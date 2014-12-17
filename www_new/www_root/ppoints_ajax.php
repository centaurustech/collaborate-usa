<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){exit;}

include_once('includes/session_ajax.php');

#/ Check Caller
if(!isset($_SERVER['HTTP_REFERER'])){exit;}
$allowed = array('localhost', 'www.collaborateusa.com', 'collaborateusa.com', 'new.collaborateusa.com', 'cusa-local');
if(!in_array($_SERVER['SERVER_NAME'], $allowed)) {exit;}


#/ Check User
$user_id = (int)@$_SESSION["CUSA_Main_usr_id"];
if($user_id<=0){exit;}

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$action_id = (int)@$_GET['ai'];
if($action_id<=0){exit;}

/////////////////////////////////////////////////////////////////////////

include_once('../includes/patronage_points_func.php');

#/Give Points
$pgiven = generate_ppoints($user_id, $action_id);
//var_dump($pgiven); die();
?>