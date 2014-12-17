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

include_once('../includes/notif_func.php');

############/(1) Create Notification & Emails ##################

/* [Place holder]
{USER} //sender
{OBJECT}

{OBJECT2}
{USER2} //receiver
*/


#/ example = Welcome
$notif_data = array(
'template_id' => "5",
'user_id' => "23", //receiver
'from_user_id' => "0",
'objects' => '',
'object_id' => '0',
'object_location' => '',
'visit_url' => '',
);

//generate_notification($notif_data, true); die();

//-------------

#/ example = Payment Success
$notif_data = array(
'template_id' => "7",
'user_id' => "{$user_id}", //receiver
'from_user_id' => "0",
'objects' => "CUSA-654643643",
'object_id' => '0',
'object_location' => '',
'visit_url' => '',
);

//generate_notification($notif_data, true); die();

//-------------

#/ example = FB Share
$notif_data = array(
'template_id' => "4",
'user_id' => "{$user_id}", //receiver
'from_user_id' => "23",
'objects' => "Voice",
'object_id' => '11',
'object_location' => 'user_voices',
'visit_url' => 'ecosystem/11',
);

generate_notification($notif_data, true); die();

//-------------

#/ example = Reply to Discussion
$notif_details = "<span style=\"color:#2CA1F4;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</span><br />
<div>JoanJGarcia: \"Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat\"</div>";

$notif_data = array(
'template_id' => "1",
'user_id' => "{$user_id}", //receiver
'from_user_id' => "3",
'objects' => "Stream",
'object_id' => '1',
'object_location' => 'eco_discussion_comments',
'visit_url' => 'ecosystem/stream/1', //to visit after click from top menu and from emails
'notif_details' => $notif_details, //details for emails
);

//generate_notification($notif_data, true); die();

//-------------

#/ example = merger invitation
$notif_details = "<span style='color:#464646;'>Your Stream: <a href=\"my_stream_url\" style=\"color:#2CA1F4; text-decoration:none;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</a> (Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat).</span><br /><br />
<span style='color:#464646;'>Their Stream: <a href=\"user_stream_url\" style=\"color:#2CA1F4; text-decoration:none;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</a> (Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat).</span><br />";

$notif_data = array(
'template_id' => "6",
'user_id' => "{$user_id}", //receiver
'from_user_id' => "3",
'objects' => "Stream:Stream",
'object_id' => '1',
'object_location' => 'eco_merge_requests',
'visit_url' => 'ecosystem/invitations', //to visit after click from top menu and from emails
'notif_details' => $notif_details, //details for emails
);

//generate_notification($notif_data, true); die();

//-------------

#/ example = merger accepted
$notif_details = "<span style='color:#464646;'>Your Stream: <a href=\"my_stream_url\" style=\"color:#2CA1F4; text-decoration:none;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</a> (Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat).</span><br /><br />
<span style='color:#464646;'>Their Stream: <a href=\"user_stream_url\" style=\"color:#2CA1F4; text-decoration:none;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</a> (Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat).</span><br />";

$notif_data = array(
'template_id' => "8",
'user_id' => "{$user_id}", //receiver
'from_user_id' => "3",
'objects' => "Stream:Stream",
'object_id' => '1',
'object_location' => 'eco_merge_requests',
'visit_url' => 'ecosystem/invitations', //to visit after click from top menu and from emails
'notif_details' => $notif_details, //details for emails
);

//generate_notification($notif_data, true); die();

//-------------

#/ example = River formation
$notif_details = "<span style='color:#464646;'>Collaborated River: <a href=\"my_stream_url\" style=\"color:#2CA1F4; text-decoration:none;\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit</a> (Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat).</span><br />";

$notif_data = array(
'template_id' => "9",
'user_id' => "{$user_id}", //receiver
'from_user_id' => "8",
'objects' => "River:Stream",
'object_id' => '1',
'object_location' => 'eco_system',
'visit_url' => 'ecosystem/river/1', //to visit after click from top menu and from emails
'notif_details' => $notif_details, //details for emails
);

//generate_notification($notif_data, true); die();

/////////////////////////////////////////////////////////////////////////

############/(2) Pull Notification(s) for a User ##################

#/ Testing notification read function
$notification_id = '';
$notifs = read_notification($user_id, $notification_id);
var_dump("<pre>", $notifs);
?>