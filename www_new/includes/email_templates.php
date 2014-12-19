<?php
function signup_success($name, $user_id, $verification_str, $insert_msg='', $chk_pkg, $admin_added=false)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $is_basic = (int)@$chk_pkg['is_basic'];

    $body_in = "";
	$body_in .= "Dear <b>{$name}</b>,<br /><br />";

    if($admin_added==false)
    $body_in .= "Thank you for Signing up at <b>collaborateUSA.com</b>, your Account has been successfully setup. {$insert_msg}<br /><br />";
    else
    $body_in .= "Your Account has been successfully setup at <b>collaborateUSA.com</b>. {$insert_msg}<br /><br />";

    if($admin_added==false)
    $body_in .= "However, your account has NOT been activated yet. You are required to verify that you are the actual owner of this Email Address.<br /><br />";
    else
    $body_in .= "Furthermore, your account may not have been activated yet. if so, you are required to verify that you are the actual owner of this Email Address.<br /><br />";

    $body_in .= "Please click on the following link to <b>Verify</b> your email address and <b>Activate</b> your account:<br />";

    $body_in .= "<a href='{$site_url}account-confirm/?verify={$user_id}.|.{$verification_str}' target='_blank' style='color:#2CA1F4; text-decoration:none;'>";
    $body_in .= "{$site_url}account-confirm/?verify={$user_id}.|.{$verification_str}</a>";

    $body_in .= "<br /><br />[IMP] We are continuously working on bringing you the top service you would want. Therefore, please pardon our progress while we complete our system to allow you full functionality.";

	$body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:20px 0; width:90%;' />";

    $body_in .= "As a member of <a href='{$site_url}' target='_blank' style='color:#2CA1F4; text-decoration:none;'>collaborateUSA.com</a>, you will want to get involved right away.<br /><br />";
    $body_in .= "<ol><li>First, make sure you have completed your <b>Profile</b></li>";
    if($is_basic==0){
    $body_in .= "<li>Complete your W-9, so you can EARN income from our website</li>";
    }
    $body_in .= "<li>Express your ideas, causes, etc. as a <b>VOICE</b> on the website</li>";
    $body_in .= "<li><b>VOTE</b> for other Voices</li></ol>";

    if($is_basic==1)
    {
        $body_in .= "Also, every time you use the website and our various functions you will begin to earn \"Patronage Points\". ";
        $body_in .= "However, as a <b>Basic Member</b> you cannot <b>cash</b> these in and EARN income from the website. When you budget permits, may I suggest you upgrade to Share Member and help us Crowd-Source the advertising of the website. ";
        $body_in .= "Your <b>ONETIME Share Fee</b> gets you all the benefits of our website!<br /><br />";
        $body_in .= "You can <b>Upgrade</b> at anytime by visiting your Membership Info section on the website, but you lose your points each month until you upgrade.";
    }
    else
    {
        $body_in .= "Also, every time you use the website and our various functions you will begin to earn \"Patronage Points\". ";
        $body_in .= "As a <b>Share Member</b>, your ONETIME Share Fee qualifies you to be able to EARN income from our website. You have been credited 240 points for signing up today.<br /><br />";
        $body_in .= "Even though we won't be making distributions until after March 2015, these points will help you EARN more when the big payout comes in October 2015. Share Members joining after March 1, don't get this Bonus! ";
        $body_in .= "Thank you for joining early.  New functions and features are coming online all the time, look for the changes.";
    }


    $body_in .= "";

    return $body_in;

}//end func...


function signup_notice_to_admin($POST, $joined_on, $is_basic)
{
    $body_in_admin = "Dear Admin,<br /><br />";
    $body_in_admin.= "A new Member has signup at collaborateUSA.com and their Account has been successfully created. Here are the basic <b>Account Details</b>:<br /><br />";
    $body_in_admin .= "Full Name:&nbsp;<b>{$POST['first_name']} {$POST['middle_name']} {$POST['last_name']}</b><br />";
    $body_in_admin .= "Organization Name:&nbsp;<b>{$POST['company_name']}</b><br />";
	$body_in_admin .= "Email Address:&nbsp;<b>{$POST['email_add']}</b><br />";
	$body_in_admin .= "Membership Type:&nbsp;<b>".(($is_basic=='1')? 'Basic':'Share')."</b><br />";
	$body_in_admin .= "Joined On:&nbsp;<b>{$joined_on}</b><br /><br />";

	$body_in_admin .= "You can track this customer from <b>CUSA-Admin</b> using their Email Address.";

    return $body_in_admin;

}//end func...


function resend_activation($user_info, $act_link, $insert = '')
{
    $body_in = "";
    $body_in.= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";
    $body_in.= "{$insert}Here is your requested <b>Activation Code</b> for your Account.<br /><br />";

    $body_in.= "Please click on the following link to <b>Verify</b> your email address and <b>Activate</b> your account:<br />";
    $body_in.= "<a href=\"{$act_link}\" style='color:#2CA1F4; text-decoration:none;'>{$act_link}</a>";

    return $body_in;

}//end func...


function welcome_new_user($user_info, $insert = '')
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in .= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";
    $body_in .= "Your Email Address has been <b>Verified</b> and your account has been successfully <b>Activated</b>. Welcome to collaborateUSA.com<br /><br />{$insert}";
    $body_in .= "Please <a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>Click Here to Signin</a> to your account using your Email Address & Password.<br />";

    return $body_in;

}//end func...


function account_recover_access($user_info, $new_pass)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in.= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";

    $body_in.= "We have received a request from you to Recover the Access of your account. As a result, we have setup a <b>Temporary Password</b> for you. Please find the details below:<br /><br />";
    $body_in.= "<b>Signin Email Address:</b> {$user_info['email_add']}<br />";
    $body_in.= "<b>Temporary Password:</b> {$new_pass}<br /><br />";
    $body_in.= "<u>Note</u>: This is an auto-generated password. You are requested to change it after you signin.<br /><br />";

    $body_in.= "Please use the following link to <a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>Signin</a> to your account using your Email Address & Password:<br />";
    $body_in.= "<a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>{$site_url}signin</a>";

    return $body_in;

}//end func...



function payment_invoice($invoice_id, $resArray, $POST)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";

    $body_in .= "Dear Admin,<br /><br />";

    $body_in .= "Here are the <b>Payment Details</b> for the new <b>paid Membership</b> joining received at collaborateUSA.com.<br /><br />";

    $body_in .= "For this Transaction, you will also receive a separate email from <b>PayPal</b> as well - matching the <b>Transaction ID</b>. ";
    $body_in .= "Furthermore, a <b>Signup Notification</b> email has also been sent to you that contain the full Member's Info.<br /><br /><br />";

    $body_in .= "<b style='color:#2CA1F4; text-decoration:underline;'>Member's Info</b><br /><br />";
    $body_in .= "<b>Full Name:</b> {$POST['first_name']} {$POST['middle_name']} {$POST['last_name']}<br />";
    $body_in .= "<b>Email Address:</b> {$POST['email_add']}<br />";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:10px 0; width:90%;' />";

    $body_in .= "<b style='color:#2CA1F4; text-decoration:underline;'>Payment Details</b><br /><br />";
    $body_in .= "<b>Payment Gateway:</b> PayPal<br />";
    $body_in .= "<b>Invoice Number:</b> <span style='color:#2CA1F4;'>{$invoice_id}</span><br />";
    $body_in .= "<b>Transaction ID:</b> {$resArray['transaction_id']}<br />";
    $body_in .= "<b>Amount:</b> \${$resArray['amount']}<br />";
    $body_in .= "<b>Payment Status:</b> {$resArray['payment_status']}<br /><br />";
    $body_in .= "<b>Full Gateway Message (unformatted direct copy):</b><br />{$resArray['gateway_msg']}";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:10px 0 20px 0; width:90%;' />";

    $body_in .= "You can track this Payment via <b>CUSA Admin</b> with the given Invoice Number.<br />";

    return $body_in;

}//end func...


function payment_receipt($invoice_id, $resArray, $POST)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";

    $body_in .= "Dear <b>{$POST['first_name']}</b>,<br /><br />";
    $body_in .= "Thank you for your <b>Payment at collaborateUSA.com</b>. We have received your Payment Details and this is your Confirmation Receipt.<br /><br /><br />";

    $body_in .= "<b style='color:#2CA1F4; text-decoration:underline;'>Member's Info</b><br /><br />";
    $body_in .= "<b>Full Name:</b> {$POST['first_name']} {$POST['middle_name']} {$POST['last_name']}<br />";
    $body_in .= "<b>Email Address:</b> {$POST['email_add']}<br />";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:10px 0; width:90%;' />";

    $body_in .= "<b style='color:#2CA1F4; text-decoration:underline;'>Payment Details</b><br /><br />";
    $body_in .= "<b>Payment Gateway:</b> PayPal<br />";
    $body_in .= "<b>Invoice Number:</b> {$invoice_id}<br />";
    $body_in .= "<b>Transaction ID:</b> {$resArray['transaction_id']}<br />";
    $body_in .= "<b>Amount:</b> \${$resArray['amount']}<br />";
    $body_in .= "<b>Payment Status:</b> {$resArray['payment_status']}";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:10px 0 20px 0; width:90%;' />";

    $body_in .= "Once again, we would like to thank you for your time & efforts in joining us at collaborateUSA.com.<br /><br />";
    $body_in .= "Please <a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>Click Here to Signin</a> to your account using your Email Address & Password.<br />";

    return $body_in;

}//end func...


function notification_email($from_usr, $to_usr, $notification, $created_on, $visit_url='', $notif_details='')
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    #/ Set from-DP
    if(!empty($from_usr))
    {
        $prf_pic = "{$site_url}assets/images/ep_th.png";
        if(!empty($from_usr['profile_pic'])){
        $prf_pic = "{$site_url}user_files/prof/{$from_usr['user_id']}/{$from_usr['profile_pic']}";
        }
    }

    $body_in = "";
    $body_in.= "Dear <b>{$to_usr['user_ident']}</b>,<br /><br />";
    $body_in.= "You have received the following <b>Notification</b> from collaborateUSA.com:<br />";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:20px 0 10px 0; width:90%;' />";
    $body_in.= "<table style='padding-top:2px;' cellpadding='0' cellspacing='0'><tr>";

        if(!empty($from_usr)){
        $body_in.= "<td style=\"width:58px; height:52px; vertical-align:top;\"><img src=\"{$prf_pic}\" style='width:50px; height:50px; border-radius:5px;' /></td>";
        }

        $body_in.= "<td style=\"color:color:#464646; font-family:Arial, Helvetica, sans-serif; font-size:13px; border-left:dotted 1px #eee; padding-left:5px; vertical-align:top;\">
            <div style=\"color:#666; font-style:italic; font-size:11px; margin-bottom:2px;\">{$created_on}</div>
            <div style=\"margin-bottom:2px;\"><a href=\"{$site_url}{$visit_url}\" style='color:#2CA1F4; text-decoration:none;'>{$notification}.</a></div>";

        if(!empty($notif_details)) {
        $body_in.= "<br />{$notif_details}";
        }

        $body_in.= "</td>";

    $body_in.= "</tr></table>";
    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:10px 0 20px 0; width:90%;' />";


    $body_in.= "In order to review the details, please <a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>Signin</a> to your account and see the notification area / dropdown. ";
    $body_in.= "If you wish not to receive this notification in future, you may change the <b>Notification Settings</b> after you login.<br />";

    //die($body_in);
    return $body_in;

}//end func...


function password_updated($user_info, $new_pass)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $updated_on = date('Y-m-d H:i:s');

    $body_in = "";
    $body_in.= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";

    $body_in.= "This is a confirmation that you have successfully <b>Updated</b> your Account Password at collaborateUSA.com. However, if the Password Change was not initiated by you, please feel free to contact the Admin.<br /><br />";
    $body_in.= "Please find the details below:<br /><br />";
    $body_in.= "<b>New Password:</b> {$new_pass}<br />";
    $body_in.= "<b>Updated On:</b> {$updated_on}<br /><br />";

    $body_in.= "Please use the following link to <a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>Signin</a> to your account using your Email Address & Password:<br />";
    $body_in.= "<a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>{$site_url}signin</a>";

    return $body_in;

}//end func...

/////////////////////////////////////////////////////



function order_status_updated($first_name, $invoice, $order_status)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in.= "Dear <b>{$first_name}</b>,<br /><br />";
    $body_in.= "This is a notification to inform you that the <b>Status</b> for your <b style='color:#2CA1F4;'>Order {$invoice}</b> has been updated. Please find the details below:<br /><br />";
    $body_in.= "Order Invoice Number: <b>{$invoice}</b><br />";
    $body_in.= "Order Status: <b style='color:#2CA1F4;'>{$order_status}</b><br /><br /><br />";

    $body_in.= "You can review full Details of your Order from \"My Accounts\" menu at the collaborateUSA.com after you signin. ";
    $body_in.= "Please use the following link to <a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>Signin</a> to your account:<br />";
    $body_in.= "<a href='{$site_url}signin' target='_blank' style='color:#2CA1F4; text-decoration:none;'>{$site_url}signin</a>";

    return $body_in;

}//end func....


function subscription_request_received($email_add, $nl_subscriber_id, $verification_str)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in.= "Dear User,<br /><br />";
    $body_in.= "This is a confirmation that we have successfully received your <b>Subscription</b> request for our periodic <b>Newsletter Service</b>.<br /><br />";

    $body_in.= "The following is your Email Address with which you have been subscribed:<br />";
    $body_in.= "<b>{$email_add}</b><br /><br />";

    $body_in .= "However, you are required to verify that you are the actual owner of this Email Address.<br /><br />";
    $body_in .= "Please click on the following link to <b>Verify</b> your Email Address and <b>Confirm</b> your Subscription:<br />";

    $body_in .= "<a href='{$site_url}newsletter-confirm/?verify={$nl_subscriber_id}.|.{$verification_str}' target='_blank' style='color:#2CA1F4; text-decoration:none;'>";
    $body_in .= "{$site_url}newsletter-confirm/?verify={$nl_subscriber_id}.|.{$verification_str}</a><br />";

    return $body_in;

}//end func....


function subscription_confirmation($email_add)
{
    $body_in = "";
    $body_in.= "Dear User,<br /><br />";
    $body_in.= "This is a confirmation that you have successfully <b>Subscribed</b> to our <b>Newsletter Service</b> and your Email Address has been successfully Confirmed. Thank you for joining our service.<br /><br />";

    $body_in.= "The following is your Email Address with which you have been subscribed:<br />";
    $body_in.= "<b>{$email_add}</b><br /><br />";

    $body_in.= "Periodically, we send interesting & informative Newsletters within the domain of our business (i.e. Logo & Associated Designs). ";
    $body_in.= "Please make sure our email address is added into your Safe List and our Emails are not going to your Junk mail.<br /><br />";

    $body_in.= "Once again, we would like to thank you for your support.<br />";

    return $body_in;

}//end func....


function unsubscription_confirmation($email_add)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in.= "Dear User,<br /><br />";
    $body_in.= "This is a confirmation that we have received your <b>unsubscription</b> request from our <b>Newsletter Service</b> against your Email Address. ";
    $body_in.= "Please allow us <b>7 working days</b> to completely remove you from our email Services.<br /><br />";

    $body_in.= "Though, we are sad to see you leave, you are always welcome to <b>Subscribe back</b> again to our Newsletters by following the <a href='{$site_url}newsletter-subscription' target='_blank' style='color:#2CA1F4; text-decoration:none;'>Subscription link</a> given below:<br /><br />";
    $body_in .= "<a href='{$site_url}newsletter-subscription' target='_blank' style='color:#2CA1F4; text-decoration:none;'>{$site_url}newsletter-subscription</a><br /><br />";

    $body_in.= "Once again, we would like to thank you for your support.<br />";

    return $body_in;
}
?>