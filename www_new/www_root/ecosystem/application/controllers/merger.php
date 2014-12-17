<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Merger extends Voice {
    
    /////////////////////////////////////////////////
    // CONSTANTS
    /////////////////////////////////////////////////

    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        
        // load all my voice models
        $this->_load_models();
        
        $this->log("Controller Merger loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
        redirect(base_url());
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_Merger');
    }
            
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function stream_to_river(){                        
        echo @json_encode($this->Mod_Merger->stream_to_river());
    }
    
    public function river_to_ocean(){
        
    }
    
    public function reject_river_invite(){                        
        echo @json_encode($this->Mod_Merger->reject_river_invite());
    }
}
