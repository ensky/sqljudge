<?php

class Logger extends CI_Model {
    public function log ($msg, $type, $sql = '') {
        $insert = [
            'log' => "[$type] $msg",
            'student_id' => $this->session->userdata('id'),
            'ip' => $_SERVER['REMOTE_ADDR']
        ];
        if (! empty($sql))
            $insert['sql'] = $sql;
        $this->db->insert('logs', $insert);
    }
}