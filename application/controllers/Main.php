<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {
	private $problem;

    function __construct () {
        parent::__construct();
        if (! $this->id OR ! $this->isTesting) {
            redirect('auth/logout');
        }
    }

	public function index() {
        $problems = $this->db->select('P.*, PA.correct, PA.total_submit')
            ->from('problems P')
            ->join('(SELECT problem_id, SUM(is_correct) AS correct, COUNT(*) AS total_submit FROM student_answers GROUP BY problem_id) AS PA', 'P.id = PA.problem_id', 'LEFT')
            ->order_by('order')
            ->get()->result();
        
        $answers = $this->db->select('*')
            ->from('student_answers')
            ->where('student_id', $this->id)
            ->get()->result();

        $corrects = [];
        foreach ($answers as $answer) {
            $corrects[$answer->problem_id] = $answer->is_correct;
        }

        $score = $this->db->select('score')
            ->from('students')
            ->where('id', $this->id)
            ->get()->row()->score;

        $this->render('main', 'main', [
            'problems' => $problems,
            'answers' => $corrects,
            'score' => $score
        ]);
	}

	public function problem ($id) {
		if(!$this->loadProblem($id)){
            redirect('main');
		}

		$problem = $this->problem;
		$inputSQL = $this->input->post('query');

        $result = (object) [
            'error' => '',
			'type' => '',
			'data' => []
        ];
        if ($inputSQL) {
            set_time_limit(10);
            $result->type = $this->input->post('type') === 'Test' ? 'test' : 'judge';

			if ($this->input->post('type') !== 'Test') {
				$result->type = 'judge';
				$result->is_correct = $this->judge($inputSQL) ? '1' : '0';

				// update student's answer
                $this->db->where('student_id', $this->id)
                    ->where('problem_id', $id)
					->delete('student_answers');

                $update = [
                    'student_id' => $this->id,
                    'problem_id' => $id,
                    'answer' => $inputSQL,
                    'is_correct' => $result->is_correct
                ];
				$this->db->insert('student_answers', $update);

				$this->logger->log('submit an answer, result: ' . $result->is_correct, 'problem:'. $id, $inputSQL);
			} else {
				$result = $this->getUserResult($inputSQL);
                $this->logger->log('test an answer', 'problem:'. $id, $inputSQL);
			}
        }

        $answer = $this->db->select('*')
            ->from('student_answers')
            ->where('student_id', $this->id)
            ->where('problem_id', $id)
			->get()->row();

		if(count($answer) == 0)
			$answer = (object) [
				'answer' => '',
				'is_correct' => false];

        $solved = $answer->is_correct == '1';
        $this->render('main', 'problem', [
            'problem' => $problem,
			'answer' => $answer,
			'query' => ($inputSQL) ? $inputSQL : $answer->answer,
            'solved' => $solved,
			'result' => $result,
			'test_tables' => $this->getTestData(),
			'test_result' => $this->getReferenceResultData(),
            'score' => $this->db->select('score')
                ->from('students')
                ->where('id', $this->id)
                ->get()->row()->score
        ]);
	}

	public function cleanUp(){
		if(!$this->isTA)
			die('No permission');

		$pdo = $this->db->conn_id;
		$temp_databases = $pdo->query('SHOW DATABASES LIKE "sqljudge\_tmp\_%";')->fetchAll(PDO::FETCH_COLUMN, 0);
		foreach($temp_databases as $db){
			$pdo->query("DROP DATABASE `$db`");
		}

		$temp_users = $pdo->query('SELECT CONCAT("`", user, "`@`", host,"`") FROM  mysql.user WHERE user LIKE "sqljudge_t%";')
			->fetchAll(PDO::FETCH_COLUMN, 0);
		foreach($temp_users as $temp_user){
			$pdo->query("DROP USER $temp_user");
		}
		echo "done";
	}

	// TODO: these should be put in a model
	static function santanize_identifier($string){
		return preg_replace('/[^A-Za-z0-9\_]/', '', $string);
	}

	private function loadProblem($id){
		if(!($this->problem = $this->db->select('*')
            ->from('problems')
            ->where('id', $id)
			->get()->row()))
			return false;

		// dbname should be safe, santanize anyway
		$this->problem->test_db = $this->santanize_identifier("sqljudge_{$this->problem->dbname}_test");
		$this->problem->judge_db = $this->santanize_identifier("sqljudge_{$this->problem->dbname}_judge");

		// auto load table names if not probided by question entry
		if(!empty($this->problem->tables)){
			$this->problem->tables = array_map(array(self, 'santanize_identifier'), explode(',', $this->problem->tables));
		}else{
			$pdo = $this->db->conn_id;
			if(($stmt = $pdo->query("SHOW TABLES IN {$this->problem->test_db}")) === False)
				trigger_error(var_dump($pdo->errorInfo()));
			
			$this->problem->tables = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
		}

		foreach($this->problem->tables as $table){
			// tables should have a safe name
			assert($table == $this->santanize_identifier($table));
		}

		return true;
	}
	// return all test table data
	//
	private function getTestData(){
		if(!$this->problem)
			throw new Exception("No problem selected");

		$result = array();
		foreach ($this->problem->tables as $table) {
			$from = "`{$this->problem->test_db}`.`$table`";
            $result[$table] = $this->db->select('*')->from($from)->get()->result();
		}

		return $result;
	}

	private function createTempUser(){
		$pdo = new PDO($this->db->dsn, $this->db->username, $this->db->password);
		$trial = 0;

		do{
			$username = 'sqljudge_t'.substr(uniqid(), -6);
			$sql = "CREATE USER '$username'@'localhost';";
		}while($pdo->exec($sql) === FALSE || (($trial++ > 10) && die('cannot create temp user')));

		return $username;
	}

	// if sql is from TA, it's executed with test user,
	// if by student, a temp user is passed in to grant permission
	private function createTempDatabase($template, $student_temp_user = false){
		assert($this->problem, 'problem should be loaded');
		// we will switch DB, so don't reuse PDO from $this->db
		$pdo = new PDO($this->db->dsn, $this->db->username, $this->db->password);

		$prefix = ($student_temp_user)? "sqljudge_tmp_usr_": "sqljudge_tmp_ta_";

		// loop until get a new db
		while(1){
			$dbname = uniqid($prefix);
			if($this->db->query("CREATE DATABASE $dbname"))
				break;			
		}

		if($student_temp_user){
			$escaped_db_name = str_replace('_', '\_', $dbname);
			if($pdo->exec("GRANT ALL PRIVILEGES ON `$escaped_db_name`.* TO '$student_temp_user'@'localhost'") === FALSE)
				die('cannot grant priv to temp user');
		}

		// copy database
		foreach($this->problem->tables as $table){
			assert($pdo->query("USE $template"));
			$stmt = $pdo->query("SHOW CREATE TABLE $table");
			$sql = $stmt->fetchColumn(1);

			// Do the copy
			assert($pdo->query("USE $dbname; $sql; INSERT INTO `$table` SELECT * FROM `$template`.`$table` "));
		}

		// TODO: register temp database, for cron delete
		return $dbname;
	}

	private function dropDatabase($dbname){
		return $this->db->query("DROP DATABASE $dbname;");
	}

	private function dropUser($username){
		return  $this->db->query("DROP USER '$username'@'localhost';");
	}

	private function getReferenceResultData($judge_mode = false){
		$template = ($judge_mode)? $this->problem->judge_db : $this->problem->test_db;

		// create test database
		$temp_db = $this->createTempDatabase($template);

		// run correct answer with test user
		$pdo = new PDO('mysql:host='.SQLJUDGE_DB_HOST.';dbname='.$temp_db, SQLJUDGE_DB_TEST_USER, SQLJUDGE_DB_TEST_PASS);
		$result = $this->getResult($pdo);

		// delete temp database
		$this->dropDatabase($temp_db);

		return $result->data;
	}

	private function getUserResult($sql, $judge_mode = false){
		$template = ($judge_mode)? $this->problem->judge_db : $this->problem->test_db;
		$temp_user = $this->createTempUser();
		$temp_db = $this->createTempDatabase($template, $temp_user);

		$pdo = new PDO('mysql:host='.SQLJUDGE_DB_HOST.';dbname='.$temp_db, $temp_user, '');
		$result = $this->getResult($pdo, $sql);
		$result->type = ($judge_mode)? 'judge' : 'test';

		$this->dropDatabase($temp_db);
		$this->dropUser($temp_user);

		return $result;
	}

	//XXX: We currently compare all results in PHP, this would be too slow if we have too many results
	// alternative method is to create a view in user's temp database (run by user temp account) 
	// and reference temp database (run by test account)
	// then compare the the views with UNION like ensky's previous implementation
	// the challenge would be find out correct SELECT statement in the query
	// XXX: we should also impose ORDER BY requirements, to avoid using intersect
	private function judge($sql){
		$user_result_data = $this->getUserResult($sql, true)->data;
		$correct_result_data = $this->getReferenceResultData(true);
		$correct_row_count = count($correct_result_data);

		return (!empty($user_result_data) &&
				count($user_result_data[0]) == count($correct_result_data[0]) && // same col count
				count($user_result_data) == $correct_row_count &&
				count(array_uintersect($user_result_data, $correct_result_data, function($a,$b){
					$a = array_values($a) ;
					$b = array_values($b);
					return ($a == $b)? 0 : (($a > $b)? 1 : -1);
				})) == $correct_row_count);
	}

	// numeric_keys is used under judge mode
	private function getResult($pdo, $sql = ''){
		$result = (object)['data' => [], 'error' => false];
		$i = -1; // query line
		if(empty($sql))
			$sql = $this->problem->answer;

		$pdo->exec("SET SESSION sql_mode='ANSI,STRICT_ALL_TABLES';");
		$stmt = $pdo->query($sql);
		$error = $pdo->errorInfo();

		if($stmt !== FALSE){
			$result->affected = [];
			do{
				$i++;
				$result->affected []= $stmt->rowCount();

				if(empty($this->problem->verifier))
					$result->data = $stmt->fetchAll(PDO::FETCH_ASSOC);

			}while($stmt->nextRowset());// loop through each query

			$error = $stmt->errorInfo();
			if($error[0] === "00000"){ // all query success
				if($this->problem->verifier){
					$stmt = $pdo->query($this->problem->verifier);
					$result->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
				}

				return $result;
			}
		}

		$result->error = "$error[1]: $error[2]";
		return $result;
	}

    public function help () {
        $this->render('main', 'help', []);
    }
}
/* End of file main.php */
/* Location: ./application/controllers/main.php */
