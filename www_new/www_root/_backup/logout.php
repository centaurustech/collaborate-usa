<?php
require_once('../includes/config.php');

#/ logout workaround for EcoSystem
$logout_key = '6c3ffa338864df96f306857936f708facd68454780573fa4b0b5c4eee0e7aed7';
$_SESSION['logout_key'] = $logout_key; //testing only..

if(!array_key_exists('logout_key', $_SESSION) || ($_SESSION['logout_key']!=$logout_key))
{
    //@header("Location: {$consts['DOC_ROOT']}ecosystem");exit;
    @header("Location: {$consts['DOC_ROOT']}ecosystem/logout");
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}ecosystem/logout';</script>";
    exit;
}

//////////////////////////////////////////////////////////

#/ Destroy User Sessions
unset($_SESSION['CUSA_Main_usr_id']);
unset($_SESSION['CUSA_Main_usr_info']);
unset($_SESSION['CUSA_MSG_GLOBAL']);

#/ Signup
unset($_SESSION['signup_stage']);
unset($_SESSION['signup_success']);
unset($_SESSION['signup_filled']);

#/ Destroy Payment Sessions
unset($_SESSION['signup_cart']);
unset($_SESSION['payer_id']);
unset($_SESSION['Payment_Amount']);
unset($_SESSION['TOKEN']);
unset($_SESSION['payment_user_info']);

unset($_SESSION['resend_chk']);
unset($_SESSION['logout_key']);

#/ Unset CI Vars and Vars created for CI
unset($_SESSION['global_vars']); //this is all because of CI...
unset($_SESSION['engine']); //this is all because of CI...


@session_unset(); // unset $_SESSION variable for the run-time
@session_destroy(); // destroy session data in storage

##--


$url = "{$consts['DOC_ROOT']}signin";

@header("Location: {$url}");
echo "<script language=\"javascript\">location.href='{$url}';</script>";
exit;
?>