<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends MY_Controller {
    public function index () {
        show_404();
    }

    private function _login_check () {
        if (! $this->isTesting) {
            $startTime = $this->config->item('start_time', 'sqljudge');
            $endTime = $this->config->item('end_time', 'sqljudge');
            return "out of testing time ($startTime - $endTime)";
        }
        if (! $this->input->post('stdid') OR ! $this->input->post('email'))
            return '';
        $stdid = $this->input->post('stdid');
        $email = $this->input->post('email');
        $okay = $this->db->select('count(*) AS count')->from('students')
            ->where('stdid', $stdid)
            ->where('email', $email)
            ->get()->row()->count;
        if ($okay != '1') {
            $this->logger->log("login failed, stdid: $stdid, email: $email", 'auth');
        }
        return $okay === '1' ? True : 'Wrong student ID or Email';
    }

    private function _login () {
        $id = $this->db->select('id')->from('students')->where('stdid', $this->input->post('stdid'))->get()->row()->id;
        $this->session->set_userdata('id', $id);
        $this->session->set_userdata('stdid', $this->input->post('stdid'));
        $this->logger->log("login successfully", 'auth');

        // lock insert
        if ($this->session->userdata('lock_hash') === False) {
            $this->session->set_userdata('lock_hash', sha1(uniqid()));
        }
        $this->db->where('id', $id)
            ->where('lock_hash', '')
            ->update('students', ['lock_hash' => $this->session->userdata('lock_hash')]);
    }

    public function login () {
        if ($this->loggedIn) {
            redirect('main/help');
        }

        if (True === ($result = $this->_login_check())) {
            $this->_login();
            redirect('main/help');
        } else {
            $this->render('main', 'login', ['errors' => $result]);
        }
    }

    public function logout () {
        $this->logger->log("logged out", 'auth');
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
/* End of file auth.php */
/* Location: ./application/controllers/auth.php */