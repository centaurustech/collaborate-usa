<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Messages extends Voice {
    
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
        
        $this->log("Controller Messages loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){

        $data = array(
            "heading" => "INBOX",
            "title" => "Inbox"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        
        $this->load_header($data);
        $this->load->view('inbox_message', $this->page_data);
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
    
    public function create(){

        $data = array(
            "heading" => "NEW MESSAGE",
            "title" => "New Message"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        
        $this->load_header($data);
        $this->load->view('create_message', $this->page_data);
        $this->load_footer();
    }
    
    public function sent(){

        $data = array(
            "heading" => "SENT MESSAGES",
            "title" => "Sent Messages"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        
        $this->load_header($data);
        $this->load->view('sent_messages', $this->page_data);
        $this->load_footer();
    }
    
    public function trash(){

        $data = array(
            "heading" => "TRASH MESSAGES",
            "title" => "Trash Messages"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        
        $this->load_header($data);
        $this->load->view('trash_messages', $this->page_data);
        $this->load_footer();
    }
}
