<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ocean extends Voice {
    
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
        
        // load models
        $this->_load_models();
        
        $this->log("Controller Ocean loaded.");
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
        $this->load->model('Mod_Ocean');
        $this->load->model('Mod_Tags');
        $this->load->model('Mod_Sidebar');
        $this->load->model('Mod_User');
        $this->load->model('Mod_Eco_Member');
        $this->load->model('Mod_Comments');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function single_ocean($id = 0){
        
        if(is_logged_in()){
            
            $result = $this->Mod_Ocean->get_single_ocean($id);
            $this->page_data['ocean'] = $result['data'];
            $this->page_data['user_id'] = $this->Mod_Ocean->get_logged_uid();            
            
            if($result['status'] == true){
            
                $cat_result = $this->Mod_Ocean->get_voice_category($this->page_data['ocean']['voice_cat_id']);
                $tag_result = $this->Mod_Tags->get_tags($this->page_data['ocean']['voice_tag_ids']);
                $this->page_data['category'] = $cat_result['data'];
                $this->page_data['tags'] = $tag_result['data'];
                
                
                $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
                $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
                
                $moderator_data = $this->Mod_User->get_user($this->page_data['ocean']['moderator_id']);
                $this->page_data['moderator'] = ($moderator_data['status'] == true) ? $moderator_data['data'] : array();
                
                $this->page_data['is_follower'] = $this->Mod_Eco_Member->is_eco_member($this->page_data['ocean']['id'], $this->page_data['user_id']);
                
                $this->page_data['child_rivers'] = $this->Mod_Ocean->get_child_rivers($this->page_data['ocean']['id']);
                
                $data = array(
                    "heading" => "OCEAN",
                    "title" => $this->page_data['ocean']['title']
                );
                
                $this->load_header($data);
                $this->load->view('single_ocean', $this->page_data);
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
    
    public function mark_fuf(){
        
        $result = array("status" => false, "message" => "");
        
        if(is_logged_in()){
            
            $sid = $this->input->post('sid', true);
            if($this->Mod_Ocean->is_valid_ocean($sid)){
                
                $user_id = $this->Mod_Ocean->get_logged_uid();
                if($this->Mod_Eco_Member->mark_fuf($sid, $user_id)){
                    $result['status'] = true;
                    $result['message'] = 'Following changed.';
                }
                else{
                    $result['message'] = 'System error.';
                }
            }
            else{
                $result["message"] = "Invalid stream.";
            }            
        }
        else{
            $result["message"] = "Login first.";
        }
        
        echo @json_encode($result);
    }
    
    public function post_comment(){
        $result = array("status" => false, "message" => "", "data" => array());
        
        if(is_logged_in()){
            
            $sid = $this->input->post('sid', true);
            $com = $this->input->post('com', true);
            
            if(! empty($com)){
                if($this->Mod_Ocean->is_valid_ocean($sid)){
                    
                    $user_id = $this->Mod_Ocean->get_logged_uid();                    
                    $post_comment = $this->Mod_Comments->post_comment($sid, $user_id, $com);
                        
                    $result['status'] = $post_comment['status'];
                    $result['message'] = $post_comment['message'];
                    $result['data'] = $post_comment['data'];
                }
                else{
                    $result["message"] = "Invalid ocean.";
                }     
            }
            else{
                $result["message"] = "Comment field empty.";
            }                      
        }
        else{
            $result["message"] = "Login first.";
        }
        
        echo @json_encode($result);
    }
    
    public function load_comments(){
        $sid = $this->input->post('sid', true);
        $start = $this->input->post('lmcs', true);
        $limit = (isset($this->_config['comments_load_limit'])) ? $this->_config['comments_load_limit'] : 10;
        
        if( ! is_numeric($start)){
            $start = 0;
        }
        
        $bundle = array("start" => $start, "limit" => $limit, "id" => $sid);
        
        $comments = $this->Mod_Comments->get_eco_comments($bundle);
        
        if($comments["is_data"]){
            
            $comments_to_html = $this->Mod_Comments->cc_to_html($comments["data"]);
            
            if($comments_to_html["status"] == true){
                $comments["data"] = $comments_to_html["data"];
            }
            
            $comments["message"] = $comments_to_html["message"];
        }
            
        echo @json_encode($comments);
    }
    
    public function merge(){
                        
        echo @json_encode($this->Mod_Ocean->merge_to_ocean());
    }
}
