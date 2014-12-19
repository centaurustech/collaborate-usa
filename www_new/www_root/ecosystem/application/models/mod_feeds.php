<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Feeds extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    private $_config;

    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_Feeds loaded.");
        
        $this->_config = c_get_config();
        // load models
        $this->_load_models();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){        
        $this->load->model("Mod_User");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_feeds($bundle = array()){
        
        // set return result
        $result = array("status" => true, "message" => "", "is_data" => false, "is_more_data" => false, "data" => array());
        
        
            
        $start = c_pick_param($bundle, "start", 0);
        $limit = c_pick_param($bundle, "limit", 10);
        
        // calculate start
        $next_start = ($start + 1) * $limit;
        $start = $start * $limit;
        
        $query = "
                SELECT 

                voices.`id` AS id, 
                voices.`user_id` AS user_id, 
                voices.`voice_cat_id` AS cat_id,
                voices.`voice_tag_ids` AS tag_ids,
                voices.`question_text` AS title,
                voices.`voice_details` AS details,
                voices.`voice_pic` AS eco_pic,
                voices.`is_blocked` AS is_blocked,
                voices.`added_on` AS created_on,
                'voice' AS data_type
                
                FROM user_voices AS voices
                WHERE voices.`is_blocked`=0
                
                UNION 
                SELECT 
                
                streams.`id` AS id, 
                streams.`user_id` AS user_id,
                streams.`voice_cat_id` AS cat_id,
                streams.`voice_tag_ids` AS tag_ids,
                streams.`question_text` AS title,
                streams.`voice_details` AS details,
                streams.`voice_pic` AS eco_pic,
                streams.`is_blocked` AS is_blocked,
                streams.`added_on` AS created_on,
                'stream' AS data_type
                
                FROM streams_voice AS streams
                WHERE streams.`is_blocked`=0
                
                UNION
                SELECT
                
                eco.`id` AS id,
                eco.`moderator_id` AS user_id,
                eco.`voice_cat_id` AS cat_id,
                eco.`voice_tag_ids` AS tag_ids,
                eco.`title` AS title,
                eco.`description` AS details,
                eco.`eco_pic` AS eco_pic,
                eco.`is_admin_blocked` AS is_blocked,
                eco.`created_on` AS created_on,
                CASE
                  WHEN eco.`level`=1 THEN 'stream'
                  WHEN eco.`level`=2 THEN 'river'
                  WHEN eco.`level`=3 THEN 'ocean'
                END
                AS data_type
                
                FROM eco_system AS eco
                
                WHERE eco.`level`!=1
                AND eco.`is_admin_blocked`=0
                
                ORDER BY created_on DESC
                LIMIT %d, %d";
                
        $sql = sprintf($query, $start, $limit);
        
        $bundle = array("sql" => $sql);
        
        // get feeds
        $feeds = $this->get_voices($bundle);
        
        $result["is_more_data"] = $this->is_more_data(sprintf($query, $next_start, $limit));
        $result["status"]  = $feeds["status"];
        $result["message"] = $feeds["message"];
        $result["is_data"] = $feeds["is_data"];
        $result["data"]    = $feeds["data"];
        
        // return result with info
        return $result;
    }
    
    public function feeds_to_html($data = array()){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        // check data must be an array
        if(is_array($data)){
            
            // check data is not empty
            if(count($data) > 0){
                
                // set blank html
                $group = "";
                $html = "";
                
                $control = 0;
                $serial = 0;
                                
                foreach($data as $feed){
                    
                    $feed_id = $feed['id'];
                    $title = strip_tags($feed['title']);
                    $title = word_limiter($title, 7);
                    
                    $eco_pic_dir = (($feed['data_type'] == 'voice') OR ($feed['data_type'] == 'stream')) ? "voices" : "ecosystem";
                    
                    $image = "/user_files/prof/" . $feed['user_id'] . "/{$eco_pic_dir}/" . $feed['eco_pic'];
                    $since = c_get_time_elapsed(strtotime($feed['created_on']));
                    $detail = strip_tags($feed["details"]);
                    $detail = make_url_to_link($detail);
                    $detail = str_replace('\n\r', '<br />', $detail);
                    $detail = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detail);
                    $detail = word_limiter($detail, 10);
                    $userdata = $this->session->userdata('user_data');
                    $uid = $feed['user_id'];
                    $user = $this->Mod_User->get_user($uid);                    
                    
                    $user_image = "/user_files/prof/{$uid}/".$user['data']['profile_pic'];
                    
                    if(! file_exists($_SERVER['DOCUMENT_ROOT']. $user_image)){
                        $user_image = "/assets/images/ep.png";
                    }
                                        
                    $single_feed_url = base_url() . $feed['data_type'] . '/' . $feed_id;
                    
                    $data_type = ucwords($feed['data_type']);
                    
                    ++$serial;
                    
                    if($control == 0){
                        $group = "<div class='container_705'><ul class='dropcontainer'>";
                        ++$control;
                    }
                    else{
                        $group = "";
                        ++$control;
                    }
                    
                    $text = ($feed['data_type'] == 'voice') ? 'Vote': 'View'; 
                    
                    $list = "
                            <li>
                                <div class='wwf_the_outer' style='background: url({$image}) no-repeat scroll 0 0 / 100% 100px rgba(0, 0, 0, 0)'>
                                    <div class='image_wwf'>
                                        <img src='{$user_image}' alt='' style='width: 56px; height: 56px;' />
                                    </div>
                                    <h4>{$title}</h4>
                                    <p>{$detail}</p>
                                    <a href='{$single_feed_url}' class='yellow_btn'>{$text}</a>
                                    <p>{$since}</p>
                                    <div class='srachbar'>{$data_type}</div>
                                </div>
                            </li>
                    ";
                    
                    $html .= $group . $list;
                    
                    if($control == 3){
                        $group = "</ul></div><div class='brdrall'></div>";
                        $control = 0;                        
                    }
                    else{
                        $group = "";
                    }
                    
                    if($serial == count($data))
                        $group = "</ul></div><div class='brdrall'></div>";
                    
                    $html .= $group;                                     
                }
                
                $result["status"] = true;
                $result["message"] = "Data successfully converted array to html.";
                $result["data"] = $html;
            }
            
            // else data is empty
            else{
                $result["message"] = "Data is empty.";
            }
        }
        
        // else data is not array
        else{
            $result["message"] = "Data is not array kindly pass an array.";
        }
        
        // return result
        return $result;
    }
}