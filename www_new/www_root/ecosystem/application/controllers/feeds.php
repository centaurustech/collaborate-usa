<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Feeds extends Voice {
    
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
        
        $this->log("Controller Feeds loaded.");
    }
    
    /////////////////////////////////////////////////
    // DEFAULT ACTION
    /////////////////////////////////////////////////
    
    public function index(){
        
        $data = array(
            "heading" => "NEWS FEED",
            "title" => "News Feeds"
        );
        
        $sidebar_data = $this->Mod_Sidebar->get_default_sidebar();        
        $this->page_data['sidebar'] = ($sidebar_data['status'] == true) ? $sidebar_data['data'] : '';
        
        $this->load_header($data);
        $this->load->view('feeds', $this->page_data);
        $this->load_footer();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model('Mod_Sidebar');
        $this->load->model('Mod_Feeds');
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function feeds_ajax(){
        
        $start = $this->input->post('s', true);
        $limit = (isset($this->_config['feeds_per_page_limit'])) ? $this->_config['feeds_per_page_limit'] : 10;
        
        if( ! is_numeric($start)){
            $start = 0;
        }
        
        $bundle = array("start" => $start, "limit" => $limit);
        
        $data = $this->Mod_Feeds->get_feeds($bundle);
        
        if($data["is_data"]){
            
            $data_to_html = $this->Mod_Feeds->feeds_to_html($data["data"]);
            
            if($data_to_html["status"] == true){
                $data["data"] = $data_to_html["data"];
            }
            
            $data["message"] = $data_to_html["message"];
        }
            
        echo @json_encode($data);
    }
}
