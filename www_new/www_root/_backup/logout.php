<?php
require_once('../includes/config.php');

#/ Destroy User Sessions
unset($_SESSION['CUSA_Main_usr_id']);
unset($_SESSION['CUSA_Main_usr_info']);
unset($_SESSION['CUSA_MSG_GLOBAL']);

#/ Signup
unset($_SESSION['signup_stage']);
unset($_SESSION['signup_success']);
unset($_SESSION['signup_filled']);


#/ Destroy Payment Sessions
unset($_SESSION['payer_id']);
unset($_SESSION['Payment_Amount']);
unset($_SESSION['TOKEN']);
unset($_SESSION['payment_user_info']);

unset($_SESSION['resend_chk']);


@session_unset(); // unset $_SESSION variable for the run-time
@session_destroy(); // destroy session data in storage

##--


$url = "{$consts['DOC_ROOT']}signin";

@header("Location: {$url}");
echo "<script language=\"javascript\">location.href='{$url}';</script>";
exit
?>