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
        if (! $this->input->post('stdid'))
            return '';
        $stdid = $this->input->post('stdid');
        $okay = $this->db->select('count(*) AS count')->from('students')
            ->where('stdid', $stdid)
            ->get()->row()->count;
        if ($okay != '1') {
            $this->db->insert('students', array(
                    'stdid' => $stdid,
                    'email' => '',
                    'score' => 0
                ));
            $okay = '1';
        }
        return $okay === '1' ? True : 'Wrong student ID';
    }
    private function _login () {
        $id = $this->db->select('id')->from('students')->where('stdid', $this->input->post('stdid'))->get()->row()->id;
        $this->session->set_userdata('id', $id);
        $this->session->set_userdata('stdid', $this->input->post('stdid'));
        $this->logger->log("login successfully", 'auth');
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