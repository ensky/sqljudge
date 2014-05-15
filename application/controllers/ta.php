<?php

class TA extends MY_Controller {
    function __construct () {
        parent::__construct();
        if (!$this->isTA) {
            redirect('auth/login');
        }
    }

    function index () {
        redirect('ta/log');
    }

    public function unlock ($stdid) {
        $this->db->where('stdid', $stdid)
            ->update('students', [
                    'lock_hash' => ''
                ]);
        echo 'ok';
    }

    public function get_log ($log_id = 0) {
        $time = 0;
        do {
            if ($time != 0) {
                sleep(1);
            }
            $time++;
            $query = $this->db->select('*')->from('logs_view')->where('id > ', $log_id)
                ->order_by('id', 'desc')
                ->get();
        } while ($query->num_rows() == 0 && $time <= 20);
        echo json_encode($query->result());
    }

    public function log () {
        $this->render('main', 'log', []);
    }

    public function setting () {
        if ($this->input->post() !== false) {
            $data = $this->input->post();
            $insert = array();
            foreach ($data as $key => $val) {
                $insert['key'] = $key;
                $insert['value'] = $val;
                $str = $this->db->insert_string('settings', $insert);
                $str = str_replace('INSERT', 'REPLACE', $str);
                $this->db->query($str);
            }
        }
        $settings = $this->db->select('*')->from('settings')->order_by('id')->get()->result();
        $this->render('main', 'settings', ['settings' => $settings]);
    }

    public function problem_edit ($id = False) {
        if ($this->input->post() != false) {
            $data = [
                'order' => $this->input->post('order'),
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'score' => $this->input->post('score'),
                'tables' => $this->input->post('tables'),
                'answer' => $this->input->post('query')
            ];
            $data['description'] = $this->imageDownloader($id, $data['description']);
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
            'order' => '',
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

    private function imageDownloader ($prob_id, $html) {
        $j = 0;
        if (preg_match_all('/<img src=.([^ \'"]+)/', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $img) {
                list(, $img) = $img;
                if (strpos($img, base_url()) === false) {
                    $img_name = $prob_id . "_" . $j++ . ".jpg";
                    file_put_contents(FCPATH . 'img/' . $img_name, file_get_contents($img));
                    $html = str_replace($img, base_url('img/' . $img_name), $html);
                }
            }
        }
        return $html;
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