<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mod_Voice_Checker extends CI_Model {
    
    /////////////////////////////////////////////////
    // PRIVATE VAR
    /////////////////////////////////////////////////
    
    private $_config;
    
    /////////////////////////////////////////////////
    // CONSTRUCTOR
    /////////////////////////////////////////////////
    
    public function __construct(){
        parent::__construct();
        #$this->log("Model Mod_Voice_Checker loaded.");
        
        $this->_config = c_get_config();
    }
    
    /////////////////////////////////////////////////
    // PUBLIC FUNCTIONS
    /////////////////////////////////////////////////
    
    public function convert_voice_to_stream($deadline = 30, $minimum_vote = 10){
        
        $sql = "SELECT ifnull( v.total_vote, 0 ) AS total_vote, voice . *
                FROM user_voices AS voice
                LEFT JOIN (
                
                SELECT vote.voice_id AS voice_id, count( * ) AS total_vote
                FROM voices_votes AS vote
                where vote_value='i_see'
                GROUP BY vote.voice_id
                ) AS v ON v.voice_id = voice.id
                WHERE DATE_SUB( CURDATE( ) , INTERVAL ? DAY ) <= voice.added_on
                AND voice.is_blocked =0";
                
        $rsl = $this->db->query($sql, array($deadline));
        
        if($rsl->num_rows() > 0){
            foreach($rsl->result_array() as $voice){
                
                $total_vote = intval($voice['total_vote']);
                
                if($total_vote >= $minimum_vote){
                    echo "----------------STREAM---------------<br />";
                    echo "<pre>";print_r($voice);echo "</pre>";
                    echo "---------------------------------<br />";
                    #$this->move_to_stream($voice);
                }
                else{
                    echo "=================PURGE=================<br />";
                    echo "<pre>";print_r($voice);echo "</pre>";
                    echo "===================================<br />";
                    #$this->purge_voice($voice);
                }
            }
        }
        else{
            return false;
        }
    }
    
    public function move_to_stream($voice = array()){
        
        // eco system data
        $data = array(
            "moderator_id" => $voice["user_id"],
            "created_on"   => c_now()
        );
        
        // insert eco system
        $insert = $this->db->insert("eco_system", $data);
        
        if($insert){
            
            // select last row from eco system table
            $sql = "SELECT * FROM eco_system ORDER BY id DESC LIMIT 1";
            $rsl = $this->db->query($sql);
            $row = $rsl->row_array();
            
            $data = array(
                "eco_sys_id"    => $row["id"],
                "user_id"       => $voice["user_id"],
                "voice_cat_id"  => $voice["voice_cat_id"],
                "voice_tag_ids" => $voice["voice_tag_ids"],
                "question_text" => $voice["question_text"],
                "voice_details" => $voice["voice_details"],
                "voice_pic"     => $voice["voice_pic"],
                "added_on"      => c_now()
            );
            
            // insert stream data
            $insert = $this->db->insert("streams_voice", $data);
            
            if($insert){
                
                $sql = "SELECT * FROM voices_votes WHERE voice_id=?";
                $rsl = $this->db->query($sql, array($voice["id"]));
                
                foreach($rsl->result_array() as $vote){
                    
                    if($vote['user_id'] != $voice["user_id"]){
                        // eco members data
                        $data = array(
                            "eco_sys_id"    => $row["id"],
                            "user_id"       => $vote["user_id"],
                            "joined_on"     => c_now()
                        );
                        
                        // insert eco members data
                        $this->db->insert("eco_members", $data);
                    }                                        
                }
                                                
                // delete vote from voices votes
                $sql = "DELETE FROM voices_votes WHERE voice_id=?";
                $this->db->query($sql, array($voice["id"]));
                
                // delete voice
                $sql = "DELETE FROM user_voices WHERE id=?";
                $this->db->query($sql, array($voice["id"]));
            }
        }
    }
    
    public function purge_voice($voice = array()){
        
        $data = array(
            "original_voice_id" => $voice["id"],
            "user_id"           => $voice["user_id"],
            "voice_cat_id"      => $voice["voice_cat_id"],
            "voice_tag_ids"     => $voice["voice_tag_ids"],
            "question_text"     => $voice["question_text"],
            "voice_details"     => $voice["voice_details"],
            "voice_pic"         => $voice["voice_pic"],
            "added_on"          => $voice["added_on"],
            "dumped_on"         => c_now()
        );
        
        // dump voice data
        $insert = $this->db->insert("voices_dump", $data);
        
        if($insert){
            
            // select voice votes by voice id
            $sql = "SELECT * FROM voices_votes WHERE voice_id=?";
            $rsl = $this->db->query($sql, array($voice["id"]));
            
            foreach($rsl->result_array() as $vote){
                
                $data = array(
                    "id"            => $vote["id"],
                    "voice_id"      => $vote["voice_id"],
                    "user_id"       => $vote["user_id"],
                    "vote_value"    => $vote["vote_value"],
                    "voted_on"      => $vote["voted_on"]
                );
                
                // insert votes dump data
                $this->db->insert("voices_votes_dump", $data);
                
                // delete vote from voices votes
                $sql = "DELETE FROM voices_votes WHERE id=?";
                $this->db->query($sql, array($vote["id"]));
            }
            
            // delete voice
            $sql = "DELETE FROM user_voices WHERE id=?";
            $this->db->query($sql, array($voice["id"]));
        }
    }
}
