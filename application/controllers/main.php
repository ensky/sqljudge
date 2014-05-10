<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {
    function __construct () {
        parent::__construct();
        if (! $this->id OR ! $this->isTesting) {
            redirect('auth/logout');
        }
    }

	public function index() {
        $problems = $this->db->select('*')
            ->from('problems')
            ->order_by('id')
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
        $problem = $this->db->select('*')
            ->from('problems')
            ->where('id', $id)
            ->get()->row();
        if (!count($problem) > 0) {
            redirect('main');
        }

        $result = (object) [];
        $inputSQL = $this->input->post('query');
        if ($inputSQL) {
            $type = $this->input->post('type') === 'Test' ? 'test' : 'judge';
            $db = $this->load->database($type, True);

            $result = (object)$this->getResultNTable($id, $type, $inputSQL);
            $result->type = $type;
            if ($result->type == 'judge') {
                $answer = $this->getResultNTable($id, $type)->data;
                if (json_encode($answer) == json_encode($result->data)) {
                    $result->is_correct = '1';
                } else {
                    $result->is_correct = '0';
                }
                
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
                // clear data
                $result->data = [];
                $this->logger->log('submit an answer, result: ' . $result->is_correct, 'problem:'. $id, $inputSQL);
            } else {
                $this->logger->log('test an answer', 'problem:'. $id, $inputSQL);
            }
        }

        $answer = $this->db->select('*')
            ->from('student_answers')
            ->where('student_id', $this->id)
            ->where('problem_id', $id)
            ->get()->row();
        $answer = count($answer) > 0 ? $answer : (object) [
            'answer' => '',
            'is_correct' => false
        ];
        $solved = $answer->is_correct == '1';

        $this->render('main', 'problem', [
            'problem' => $problem,
            'answer' => $answer,
            'solved' => $solved,
            'result' => $result,
            'test' => $this->getResultNTable($id, 'test'),
            'score' => $this->db->select('score')
                ->from('students')
                ->where('id', $this->id)
                ->get()->row()->score
        ]);
    }

    private function getResultNTable ($problem_id, $database, $SQL = '') {
        if ($problem_id == false) 
            return (object)['data' => [], 'tables' => []];

        $db = $this->load->database($database, True);
        $result = (object)[];

        $problem = $this->db->select('*')
            ->from('problems')
            ->where('id', $problem_id)->get()->row();

        $SQL = empty($SQL) ? $problem->answer : $SQL;
        $query = $db->query($SQL);
        if (!$query) {
            $result->error = $db->_error_message();
            $result->data = [];
        } else {
            $result->error = false;
            $result->data = $query->result_array();
        }

        $result->tables = [];
        $tables = explode(',', $problem->tables);
        foreach ($tables as $table) {
            $result->tables[$table] = $db->select('*')->from($table)->get()->result();
        }
        return $result;
    }

    public function help () {
        $this->render('main', 'help', []);
    }
}
/* End of file main.php */
/* Location: ./application/controllers/main.php */