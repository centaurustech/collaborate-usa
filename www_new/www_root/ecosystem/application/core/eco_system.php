<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eco_System extends CI_Controller {
    
    /////////////////////////////////////////////////
    // VARIABLES
    /////////////////////////////////////////////////    
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->_load_library();
        $this->_load_models();
        $this->log("Controller Eco_System Loaded.");
        
        if(!is_logged_in()){
            $this->goto_login_page();
        }
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_library(){                
        $params = array('is_cusa_log' => $this->config->config["cusa_log"]);
        $this->load->library('cusa_log', $params);
    }
    
    private function _load_models(){
        $this->load->model('Mod_User');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    // write any kind of log like error, info, debug
    
    protected function log($msg, $level = 'info', $php_error = FALSE){
        $this->cusa_log->log($level, $msg, $php_error);
    }
    
    
    // load header view
    protected function load_header($data = array()){
        
        /*
        $userdata = $this->session->userdata('user_data');
        $notification = read_notification($userdata['uid']);
        $data['notification'] = $notification;
        $i = 0;
        foreach($notification as $notif_data){
            $user = $this->Mod_User->get_user($notif_data['notif_data']['from_user_id']);
            $data['notification'][$i]['notif_data']['user'] = $user;
            ++$i;
        }
        */
        $this->load->view('includes/header', $data);
    }
    
    // load footer view
    protected function load_footer($data = array()){
        $this->load->view('includes/footer', $data);
    }
    
    // redirect to login page
    protected function goto_login_page(){
        header('Location: ' . DOC_ROOT . 'signin');
    }
    
    // redirect to feed page
    protected function goto_feed_page(){
        redirect(base_url());
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
}