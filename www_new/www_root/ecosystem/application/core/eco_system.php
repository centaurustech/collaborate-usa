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
        $this->log("Controller Eco_System Loaded.");
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_library(){                
        $params = array('is_cusa_log' => $this->config->config["cusa_log"]);
        $this->load->library('cusa_log', $params);
    }
    
    /////////////////////////////////////////////////
    // PROTECTED FUNCTIONS
    /////////////////////////////////////////////////
    
    // write any kind of log like error, info, debug
    protected function log($msg, $level = 'info', $php_error = FALSE){
        $this->cusa_log->log($level, $msg, $php_error);
    }
    
    
    // load header view
    protected function load_header($data = array()){
        $this->load->view('includes/header', $data);
    }
    
    // load footer view
    protected function load_footer($data = array()){
        $this->load->view('includes/footer', $data);
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
}