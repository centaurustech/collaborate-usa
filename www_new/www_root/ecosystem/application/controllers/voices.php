<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Voices extends Voice {
    
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
    
    //public function index(){
        
        
    //}
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_Voices');
        $this->load->model('Mod_Tags');
        $this->load->model('Mod_Vote');
        $this->load->model('Mod_Sidebar');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function ajax_vote_action(){
        
        $voc_id = $this->input->post('vocid', true);
        $vot_va = $this->input->post('votva', true);
                
        if($vot_va == 1){
            $vot_va = $this->_config['vote_up'];
        }
        else{
            $vot_va = $this->_config['vote_down'];
        }
        
        if(! is_numeric($voc_id)){
            $voc_id = 0;
        }
        
        $result = $this->Mod_Vote->vote($voc_id, $vot_va);        
        echo @json_encode($result);                      
    }
    
    public function single_voice($id = 0){
        
        if(is_logged_in()){
            
            $result = $this->Mod_Voices->get_single_voice($id);
            $this->page_data['voice'] = $result['data'];
            $this->page_data['user_id'] = $this->Mod_Voices->get_logged_uid();            
            
            if($result['status'] == true){
            
                $cat_result = $this->Mod_Voices->get_voice_category($this->page_data['voice']['voice_cat_id']);
                $tag_result = $this->Mod_Tags->get_tags($this->page_data['voice']['voice_tag_ids']);
                $this->page_data['category'] = $cat_result['data'];
                $this->page_data['tags'] = $tag_result['data'];
                $this->page_data['total_vote_up'] = $this->Mod_Vote->count_vote($this->page_data['voice']['id'], $this->_config['vote_up']);
                $this->page_data['total_vote_down'] = $this->Mod_Vote->count_vote($this->page_data['voice']['id'], $this->_config['vote_down']);
                $this->page_data['user_vote_cast'] = $this->Mod_Vote->is_vote_cast($this->page_data['user_id'], $this->page_data['voice']['id']);
                $this->page_data['vote_up_users'] = $this->Mod_Vote->get_vote_users($this->page_data['voice']['id'], $this->_config['vote_up']);
                $this->page_data['vote_down_users'] = $this->Mod_Vote->get_vote_users($this->page_data['voice']['id'], $this->_config['vote_down']);
                
                $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
                $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
                
                $data = array(
                    "heading" => "VOICE",
                    "title" => $this->page_data['voice']['question_text']
                );
                
                $this->load_header($data);
                $this->load->view('single_voice', $this->page_data);
                $this->load_footer();
            }
            else{
                $this->goto_feed_page();
            }            
        }
        else{
            $this->goto_login_page();
        }
    }
}
