<?php

class Setting extends CI_Model {
    function get ($key) {
        return $this->db->select('value')->from('settings')->where('key', $key)->get()->row()->value;
    }
    function lastUpdate ($key) {
        return $this->db->select('updated_at')->from('settings')->where('key', $key)->get()->row()->updated_at;
    }
}