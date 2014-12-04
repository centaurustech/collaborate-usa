<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voice_Checker extends Voice {
    
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
        
        $this->log("Controller Voices loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
        $this->Mod_Voice_Checker->convert_voice_to_stream($this->_config['voice_deadline'], $this->_config['voice_minimum_vote']);
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_Voice_Checker');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
}
