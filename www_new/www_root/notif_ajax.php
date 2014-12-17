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

$notif_id = (int)@$_GET['ni'];
if($notif_id<=0){exit;}

/////////////////////////////////////////////////////////////////////////

$sql_1 = "UPDATE user_notifications SET is_read='1' WHERE id='{$notif_id}' AND user_id='{$user_id}'";
$res = @mysql_exec($sql_1, 'save');
//var_dump($res);
if($res=='true')
echo '1';
?>