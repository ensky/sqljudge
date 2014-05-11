<?php

class TA extends MY_Controller {
    function __construct () {
        parent::__construct();
        if (!$this->isTA()) {
            redirect('auth/login');
        }
    }

    private function isTA () {
        return $this->id;
    }

    public function problem_edit ($id = False) {
        if ($this->input->post() != false) {
            $data = [
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'score' => $this->input->post('score'),
                'tables' => $this->input->post('tables'),
                'answer' => $this->input->post('query')
            ];
            if ($id === False) {
                $this->db->insert('problems', $data);
                $id = $this->db->insert_id();
                redirect('ta/problem_edit/' . $id);
            } else {
                $this->db->where('id', $id)
                    ->update('problems', $data);
            }
        }
        $problem = (object)[
            'id' => '',
            'title' => '',
            'description' => '',
            'score' => '',
            'tables' => '',
            'answer' => ''
        ];
        $result = (object) [];
        if ($id !== false) {
            $problem = $this->db->select('*')
                ->from('problems')
                ->where('id', $id)->get()->row();
        }

        $this->render('main', 'new_problem', [
            'problem' => $problem,
            'test' => $this->getResultNTable($id, 'test'), 
            'judge' => $this->getResultNTable($id, 'judge')
        ]);
    }

    public function log () {
        // TODO: view log
    }

    public function student () {
        // TODO: view stdid and email
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

}