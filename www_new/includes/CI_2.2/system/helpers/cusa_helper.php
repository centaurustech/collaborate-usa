<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// check is any user logged in
if( ! function_exists('is_logged_in')){
    
    function is_logged_in(){
        
        $CI =& get_instance();
    
        $is_logged_in = $CI->session->userdata('user_data');
        
        if( !isset($is_logged_in) || $is_logged_in != true){
                
            if(isset($_SESSION['CUSA_Main_usr_info'])){
                
                $logged_in_data = array(
                    "uid" => $_SESSION['CUSA_Main_usr_id'],
                    "uemail" => $_SESSION['CUSA_Main_usr_info']['email_add'], 
                    "screen_name" => $_SESSION['CUSA_Main_usr_info']['screen_name'],
                    "profile_pic" => $_SESSION['CUSA_Main_usr_info']['profile_pic'],
                    "fname" => $_SESSION['CUSA_Main_usr_info']['first_name'],
                    "mname" => $_SESSION['CUSA_Main_usr_info']['middle_name'],
                    "lname" => $_SESSION['CUSA_Main_usr_info']['last_name'],
                    "company" => $_SESSION['CUSA_Main_usr_info']['company_name']
                );
                
                $CI->session->set_userdata('user_data', $logged_in_data);
                
                return true; 
            }
            else{
                return false;   
            }
        }
        else{
            
            /*
            $dummy_logged_in_data = array(
                "uid" => 9,
                "uemail" => "dp.owaisali@gmail.com", 
                "screen_name" => "Syed Owais Ali",
                "profile_pic" => "v.jpg",
                "fname" => "Syed",
                "mname" => "Owais",
                "lname" => "Ali",
                "company" => "Meritocracy"
            );
            
            $CI->session->set_userdata('user_data', $dummy_logged_in_data);
            */
            return true;
        }
    }
}

// get assets url
if( ! function_exists('c_get_assets_url')){
    
    function c_get_assets_url(){
        return base_url() . 'assets/';
    }
}

// get now date time
if( ! function_exists('c_now')){
    
    function c_now(){
        return date("Y-m-d H:i:s");
    }
}

// get configs
if( ! function_exists('c_get_config')){
    
    function c_get_config(){
        $CI =& get_instance();
        return $CI->config->config;
    }
}

// pick param
if( ! function_exists('c_pick_param')){
    
    function c_pick_param($bundle = array(), $param, $default){
        return (isset($bundle[$param])) ? $bundle[$param] : $default;
    }
}

// find plane url and make to link
if( ! function_exists('make_url_to_link')){
    
    function make_url_to_link($text = ""){
        $text = preg_replace('$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i', ' <a href="$2" target="_blank">$2</a> ', $text." ");                    
        $text = preg_replace('$(\s|^)(www\.[a-z0-9_./?=&-]+)(?![^<>]*>)$i', '<a target="_blank" href="http://$2"  target="_blank">$2</a> ', $text." ");
        return $text;
    }
}

// since time
if(! function_exists('c_get_time_elapsed')){
    function c_get_time_elapsed($time){

        $time = time() - $time; // to get the time since that moment
    
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
    
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'') . ' ago';
        }
    }
}

// manage name
if(! function_exists('manage_name')){
    function manage_name($user){
        
        $name = $user['screen_name'];
        
        switch($user['identify_by']){
            case "full_name":
                $name = $user['first_name'] . ' ' . $user['middle_name'] . ' ' . $user['last_name'];
                break;
                
            case "screen_name":
                $name = $user['screen_name'];
                break;
                
            case "company_name":
                $name = $user['company_name'];
                break;
        }
        
        return $name;
    }
}

if(! function_exists('str_replace_nth')){
    function str_replace_nth($search, $replace, $subject, $nth)
    {
        $found = preg_match_all('/'.preg_quote($search).'/', $subject, $matches, PREG_OFFSET_CAPTURE);
        if (false !== $found && $found > $nth) {
            return substr_replace($subject, $replace, $matches[0][$nth][1], strlen($search));
        }
        return $subject;
    }
}

if(! function_exists('remote_file_exists')){
    function remote_file_exists($url) {
        $curl = curl_init($url);
    
        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);
    
        //do request
        $result = curl_exec($curl);
    
        $ret = false;
    
        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
    
            if ($statusCode == 200) {
                $ret = true;   
            }
        }
    
        curl_close($curl);
    
        return $ret;
    }   
}

// get profile picture
/*
if(! function_exists('get_profile_pic')){
    function get_profile_pic($uid = 0, $pic = "", $node = ""){
        
        $image = $node . "user_files/prof/{$uid}/{$pic}";
        
        if(! file_exists($image)){
            get_profile_pic($uid, $pic, "../".$node);
        }
        else{
            return $image;
        }
    }
}
*/