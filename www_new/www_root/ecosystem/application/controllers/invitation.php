<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invitation extends Voice {
    
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
        
        $this->log("Controller Invitation loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){

        $data = array(
            "heading" => "INVITATIONS",
            "title" => "Invitations"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
                
        $notification = read_notification($this->Mod_User->get_logged_uid());
        $this->page_data['notification'] = $notification;
        $i = 0;
        #echo "<pre>";print_r($notification);exit;
        foreach($notification as $notif_data){
            $user = $this->Mod_User->get_user($notif_data['notif_data']['from_user_id']);
            $this->page_data['notification'][$i]['notif_data']['user'] = $user;
            $this->page_data['notification'][$i]['notif_data']['receiver_stream'] = $this->Mod_Stream->get_request_receiver_stream($this->page_data['notification'][$i]['notif_data']['object_id']);
            $this->page_data['notification'][$i]['notif_data']['caller_stream'] = $this->Mod_Stream->get_request_caller_stream($this->page_data['notification'][$i]['notif_data']['object_id']);
            ++$i;
        }
                
        $this->load_header($data);
        $this->load->view('invitation', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){                
        $this->load->model('Mod_Sidebar');
        $this->load->model('Mod_User');
        $this->load->model('Mod_Stream');
    }
            
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
}
