<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_My_Vote_Voices extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    private $_config;

    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_My_Voices loaded.");
        
        $this->_config = c_get_config();
        // load models
        $this->_load_models();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model("Mod_Vote");
        $this->load->model("Mod_User");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_my_vote_voices($bundle = array()){
        
        // set return result
        $result = array("status" => true, "message" => "", "is_data" => false, "data" => array());
        
        // get valid logging id
        $uid = $this->get_logged_uid();
        
        // first user is valid
        if($uid){
            
            $start = c_pick_param($bundle, "start", 0);
            $limit = c_pick_param($bundle, "limit", 10);
            
            // calculate start
            $start = $start * $limit;
            
            $query = "SELECT voice.* FROM user_voices AS voice
                     INNER JOIN voices_votes AS vote
                     ON voice.id = vote.`voice_id`
                     WHERE vote.`user_id`=%d 
                     AND voice.is_blocked=0 
                     ORDER BY voice.id DESC 
                     LIMIT %d, %d";
                    
            $sql = sprintf($query, $uid, $start, $limit);
            
            $bundle = array("sql" => $sql);
            
            // get voices
            $voices = $this->get_voices($bundle);
            
            $result["status"]  = $voices["status"];
            $result["message"] = $voices["message"];
            $result["is_data"] = $voices["is_data"];
            $result["data"]    = $voices["data"];
        }
        
        // user is not valid
        else{
            $result["status"] = false;
            $result["message"] = "User ID is not valid";
        }
        
        // return result with info
        return $result;
    }
    
    public function mvv_to_html($data = array()){
        
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
                                
                foreach($data as $voice){
                    
                    $voice_id = $voice['id'];
                    $title = strip_tags($voice['question_text']);
                    $title = word_limiter($title, 7);
                    $image = "../user_files/prof/" . $this->get_logged_uid() . "/voices/" . $voice['voice_pic'];
                    $since = c_get_time_elapsed(strtotime($voice['added_on']));
                    $detail = strip_tags($voice["voice_details"]);
                    $detail = make_url_to_link($detail);
                    $detail = str_replace('\n\r', '<br />', $detail);
                    $detail = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detail);                    
                    $detail = word_limiter($detail, 10);
                    $userdata = $this->session->userdata('user_data');
                    $uid = $userdata['uid'];
                    $user = $this->Mod_User->get_user($uid);                    
                    $user_image = $user['data']['profile_pic'];
                    
                    $single_voice_url = base_url() . $this->_config['single_voice_url'] . '/' . $voice_id;
                    
                    ++$serial;
                    
                    if($control == 0){
                        $group = "<div class='container_705'><ul class='dropcontainer'>";
                        ++$control;
                    }
                    else{
                        $group = "";
                        ++$control;
                    }
                    
                    $list = "
                            <li>
                                <div class='wwf_the_outer' style='background: url({$image}) no-repeat scroll 0 0 / 100% 100px rgba(0, 0, 0, 0)'>
                                    <div class='image_wwf'>
                                        <img src='../user_files/prof/{$uid}/{$user_image}' alt='' style='width: 56px; height: 56px;' />
                                    </div>
                                    <h4>{$title}</h4>
                                    <p>{$detail}</p>
                                    <a href='{$single_voice_url}' class='yellow_btn'>View</a>
                                    <p>{$since}</p>
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