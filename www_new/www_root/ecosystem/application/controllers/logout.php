<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends Voice {
    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    private $page_data = array();
    private $_config;
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        
        // get config and store to private var
        $this->_config = c_get_config();
        
        // load all my voice models
        $this->_load_models();
        
        $this->log("Controller Logout loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
        
        $is_logged_in = $this->session->userdata('user_data');
        $_SESSION['logout_key'] = $this->_config['logout_key'];
        
        if(isset($is_logged_in)){
            $this->session->unset_userdata('user_data');
            header('Location: /logout');
        }
        else{
            header('Location: /logout');
        }
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
}
