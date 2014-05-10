<?php

class MY_Controller extends CI_Controller {
    protected $loggedIn = False;
    protected $id = False;
    protected $stdid = False;

    function __construct () {
        parent::__construct();
        $this->loggedIn = $this->session->userdata('id') !== false;
        $this->id = $this->session->userdata('id');
        $this->stdid = $this->session->userdata('stdid');
        $this->isTesting = 
            time() >= strtotime($this->config->item('start_time', 'sqljudge'))
            && time() <= strtotime($this->config->item('end_time', 'sqljudge'));
    }

    protected function is_pjax () {
        return array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'];
    }

    protected function render ($layout, $body, $params) {
        $body = $this->load->view('page/' . $body, $params, ! $this->is_pjax());
        if (! $this->is_pjax()) {
            $this->load->view('layout/' . $layout, [ 
                'body' => $body
            ]);
        }
    }
}