<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification extends Voice {
    
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
        
        $this->log("Controller Notification loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
                        
        $data = array(
            "heading" => "NOTIFICATIONS",
            "title" => "Notifications"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        
        $this->load_header($data);
        $this->load->view('notification', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){                
        $this->load->model('Mod_Sidebar');
    }
            
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
}
