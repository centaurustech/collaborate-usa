<?php
require_once('../includes/config.php');

#/ Redirect if not in our IP
if(in_array($_SERVER['REMOTE_ADDR'], array('110.93.203.122', '110.93.203.14', '127.0.0.1'))==false){
@header("Location: nf.php");
echo "<script language=\"javascript\">location.href='nf.php';</script>";
exit;
}

include_once('../includes/format_str.php');
include_once('../includes/func_1.php');
include_once('../includes/db_lib.php');

include_once('includes/session_ajax.php');

/////////////////////////////////////////////////////////////////////

$allowed = array('localhost', 'www.collaborateusa.com', 'collaborateusa.com', 'new.collaborateusa.com', 'cusa-local');
if(!in_array($_SERVER['SERVER_NAME'], $allowed)) {exit;}

/////////////////////////////////////////////////////////////////////////

$user_id = (int)@$_SESSION["CUSA_Main_usr_id"];
if($user_id<=0){exit;}
$user_info = @$_SESSION["CUSA_Main_usr_info"];

$_POST = format_str($_POST);
$_GET = format_str($_GET);

/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////

include_once('../includes/patronage_points_func.php');

############/(1) Give Points ##################

//$pgiven = generate_ppoints($user_id, 'purchase', 200); //percentage based
//$pgiven = generate_ppoints($user_id, 5); //points based with direct action row `ID` instead of `key`
//$pgiven = generate_ppoints($user_id, 'access_learn'); //points based
//var_dump($pgiven); die();

/////////////////////////////////////////////////////////////////////////

############/(2) Get User Points  ##################

//$user_ppoints = get_ppoints($user_id); //all
//$user_ppoints = get_ppoints($user_id, '1'); //specific
$user_ppoints = get_ppoints($user_id, '', true); //count only
var_dump("<pre>", $user_ppoints);

############/(3)  ##################

#/ Pull Patronage Notification for this User
//include_once('../includes/notif_func.php');
//$notifs = read_notification($user_id, '19');
//var_dump("<pre>", $notifs);
?>