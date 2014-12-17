<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Comments extends Mod_Voice {
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        $this->log("Model Mod_User loaded.");
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function post_comment($sid = 0, $uid = 0, $com = ""){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => array());                
        
        $user = $this->is_valid_user($uid);
        
        if($user){
            
            if($this->is_valid_eco_system_id($sid)){
                
                if(! empty($com)){
                    
                    $data = array(
                        'eco_sys_id' => $sid,
                        'posted_by'  => $uid,
                        'comment'    => $com,
                        'added_on'   => c_now()
                    );
                    
                    if($this->db->insert('eco_discussion_comments', $data)){
                        $since = c_get_time_elapsed(strtotime(c_now()));
                        
                        
                        
                        $host = str_replace('ecosystem/', '', base_url());
                        $uid = $user['id'];
                        $user_image = $user['profile_pic'];
                        $user_image = "{$host}user_files/prof/{$uid}/{$user_image}";
                        
                        if(!remote_file_exists($user_image)){
                            $user_image = "/assets/images/ep.png";
                        }
                        
                        $result['status'] = true;
                        $result['message'] = "Comment posted successfully.";
                        $result['data']['user']['name'] = $user['name'];
                        $result['data']['user']['profile_pic'] = $user_image;
                        $result['data']['comment_data'] = array();
                        $result['data']['comment_data']['comment'] = $com;
                        $result['data']['comment_data']['added_on'] = c_now();
                        $result['data']['comment_data']['since'] = (is_null($since)) ? '2 seconds ago' : $since;    
                    }
                    else{
                        $result['message'] = 'System error.';
                    }
                }
                else{
                    $result['message'] = "Empty comment.";   
                }                
            }
            else{
                $result['message'] = "Invalid eco system id.";
            }            
        }
        else{
            $result['message'] = "Invalid user.";
        }
        
        return $result;
    }
    
    public function get_comments($bundle = array()){
        
        // set return result
        $result = array("status" => true, "message" => "", "is_data" => false, "is_more_data" => false, "data" => array());
                           
        $stream_id = c_pick_param($bundle, "stream_id", 0);
        $start = c_pick_param($bundle, "start", 0);
        $limit = c_pick_param($bundle, "limit", 10);
        
        if($this->is_valid_stream($stream_id)){
            
            $stream_data = $this->get_single_stream($stream_id);
            if($stream_data['status'] == true){
                
                $stream = $stream_data['data'];                    
                $eco_system_id = $stream['eco_sys_id'];
                
                if($this->is_valid_eco_system_id($eco_system_id)){
                    
                    // calculate start
                    $next_start = ($start + 1) * $limit;
                    $start = $start * $limit;                    
                    
                    $query = "SELECT * FROM eco_discussion_comments WHERE eco_sys_id=%d ORDER BY id DESC LIMIT %d, %d";
                    $sql = sprintf($query, $eco_system_id, $start, $limit);
                    
                    $bundle = array("sql" => $sql);
                    
                    // get comments
                    $comments = $this->get_comments_data($bundle);
                    
                    $result["is_more_data"] = $this->is_more_data(sprintf($query, $eco_system_id, $next_start, $limit));
                    $result["status"]  = $comments["status"];
                    $result["message"] = $comments["message"];
                    $result["is_data"] = $comments["is_data"];
                    $result["data"]    = $comments["data"];            
                }
                else{
                    $result['status'] = false;
                    $result['message'] = "Invalid eco system id.";
                }
            }
            else{
                $result['status'] = false;
                $result['message'] = "Invalid stream.";
            }
        }
        else{
            $result['status'] = false;
            $result['message'] = "Invalid stream id.";
        }
                                
        // return result with info
        return $result;
    }
    
    public function get_eco_comments($bundle = array()){
        
        // set return result
        $result = array("status" => true, "message" => "", "is_data" => false, "is_more_data" => false, "data" => array());
                           
        $id    = c_pick_param($bundle, "id", 0);
        $start = c_pick_param($bundle, "start", 0);
        $limit = c_pick_param($bundle, "limit", 10);
        
        if($this->is_valid_river($id)){
            
            // calculate start
            $next_start = ($start + 1) * $limit;
            $start = $start * $limit;                    
            
            $query = "SELECT * FROM eco_discussion_comments WHERE eco_sys_id=%d ORDER BY id DESC LIMIT %d, %d";
            $sql = sprintf($query, $id, $start, $limit);
            
            $bundle = array("sql" => $sql);
            
            // get comments
            $comments = $this->get_comments_data($bundle);
            
            $result["is_more_data"] = $this->is_more_data(sprintf($query, $id, $next_start, $limit));
            $result["status"]  = $comments["status"];
            $result["message"] = $comments["message"];
            $result["is_data"] = $comments["is_data"];
            $result["data"]    = $comments["data"];  
        }
        else{
            $result['status'] = false;
            $result['message'] = "Invalid river id.";
        }
                                
        // return result with info
        return $result;
    }
    
    public function cc_to_html($data = array()){
        
        // set return result
        $result = array("status" => false, "message" => "", "data" => "");
        
        // check data must be an array
        if(is_array($data)){
            
            // check data is not empty
            if(count($data) > 0){
                
                // set blank html                
                $html = "";
                
                $data = array_reverse($data);
                foreach($data as $comment){
                    
                    $detail = strip_tags($comment["comment"]);
                    $detail = make_url_to_link($detail);
                    $detail = str_replace('\n\r', '<br />', $detail);
                    $detail = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detail);
                    
                    $since = c_get_time_elapsed(strtotime($comment['added_on']));
                    $user = $this->is_valid_user($comment['posted_by']);
                    
                    $name  = ($user) ? $user['name'] : 'Unknow user';
                    $uid   = ($user) ? $comment['posted_by'] : 0;
                    $image = ($user) ? $user['profile_pic'] : ''; 
                    
                    $html .= "
                    <div class='leftine'>
                        <div class='comm'>&nbsp;</div>
                        <div class='commhead'>&nbsp;</div>
                        <div class='smalluserimg'><img alt='' src='../../user_files/prof/$uid/$image' /></div>
                        <div class='smallusertxt'> <span class='star_headdrop'>$name</span>
                            <p>$detail</p>
                            <span style='color: #646464; float: left; font-size: 13px'>$since</span>
                        </div>
                    </div>";
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
