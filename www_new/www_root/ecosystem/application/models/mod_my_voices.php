<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_My_Voices extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_My_Voices loaded.");
        
        // load models
        $this->_load_models();
    }
    
    /////////////////////////////////////////////////
    // PRIVATE FUNCTIONS
    /////////////////////////////////////////////////
    
    private function _load_models(){
        $this->load->model("Mod_Vote");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function get_my_voices($bundle = array()){
        
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
            
            $query = "SELECT * FROM user_voices WHERE user_id=%d ORDER BY id DESC LIMIT %d, %d";
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
    
    public function mv_to_html($data = array()){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        // check data must be an array
        if(is_array($data)){
            
            // check data is not empty
            if(count($data) > 0){
                
                // set blank html
                $html = "";                                
                foreach($data as $voice){
                    
                    $voice_id = $voice['id'];
                    $title = strip_tags($voice['question_text']);
                    $image = "../user_files/prof/" . $this->get_logged_uid() . "/voices/" . $voice['voice_pic'];
                    $since = c_get_time_elapsed(strtotime($voice['added_on']));
                    $detail = strip_tags($voice["voice_details"]);
                    $detail = make_url_to_link($detail);
                    $detail = nl2br($detail);
                    $detail = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detail);
                    $detail = word_limiter($detail, 150);
                    
                    $html .= "
                        <div class='withbor'>
                            <div class='container_705'> <span class='star_vo'><img src='" . c_get_assets_url() . "images/voice.png' alt=''  /></span>
                                <div class='starheadmar'>
                                    <h2><span class='star_head'>{$title}</span> <!--Posted Voice--></h2>
                                </div>
                                <div class='hoursin'>{$since}</div>
                                <div class='smallhouseimg'><img src='{$image}' alt=''  /></div>
                                <div class='smallhousetxt'><!-- <span class='star_headdrop'>It has survived not only five centuries, </span>-->
                                    <p>{$detail}</p>
                                    <a href='my_voice_detail.php'>Read More</a>
                                </div>
                            </div>
                            <div class='brdrgratop'>
                                <div class='container_705 margnten'>
                                    <div class='radio leftsinpthide'>
                                        <input type='radio' value='male' name='vote{$voice_id}' id='male{$voice_id}' class='vote-up' data-vid='{$voice_id}' />
                                        <label for='male{$voice_id}' class='test'>I SEE IT</label>
                                        <input type='radio' value='female' name='vote{$voice_id}' id='female{$voice_id}' class='vote-down' data-vid='{$voice_id}' />
                                        <label for='female{$voice_id}' class='test'>I DON'T SEE IT</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ";                                        
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