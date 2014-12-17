<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_River extends Voice {
    
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
        
        $this->log("Controller Create_River loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index($rc_key = 0){
        
        $this->page_data["voice_categories"] = $this->Mod_Create_River->get_voice_categories();
        
        $data = array(
            "heading" => "CREATE RIVER",
            "title" => "Create River"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        $this->page_data['rc_key'] = $rc_key;
        $this->load_header($data);
        $this->load->view('create_river', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){        
        $this->load->model('Mod_Create_River');
        $this->load->model('Mod_Sidebar');
    }
            
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function create(){
        $this->Mod_Create_River->create_river();
    }
}
