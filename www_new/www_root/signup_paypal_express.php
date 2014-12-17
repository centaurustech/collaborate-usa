<?php
//STEP 2
//used for Ajax processing only
//var_dump("<pre>", $url_comp, $_SERVER['HTTP_REFERER']); die();

header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 2007 05:00:00 GMT");

if(!isset($seo_tag_id) || empty($seo_tag_id)){exit;}
//var_dump($url_comp, $seo_tag_id); die();

if(!isset($_SERVER['HTTP_REFERER'])){exit;}

$allowed = array('localhost', 'www.collaborateusa.com', 'collaborateusa.com', 'new.collaborateusa.com', 'cusa-local');
if(!in_array($_SERVER['SERVER_NAME'], $allowed)) {exit;}


////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0)){exit;}

#/ check & redirect - if already crossed Step1
if(isset($_SESSION['signup_stage']) && !empty($_SESSION['signup_stage']) && ($_SESSION['signup_stage']!='signup')){
exit;
}

////////////////////##--

##/ Recalculate Amount and Recreate Cart (for security, we are recreating it again from session)
$sel_pack = (int)@$_POST['package_id'];
if($sel_pack<=0){exit;}
//var_dump($sel_pack); die();

$chk_pkg = $package_type = $POST_key = $attempted_on = '';
if($sel_pack>0)
{
    $_SESSION['signup_stage'] = 'signup';

    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    if(check_attempts(3)==false){
    update_attempt_counts();
    echo '<strong class="red-txt">Too Many Attempts!</strong> Please try again after a few minutes.';
    exit;
    }


    ##/ Validate Fields
    include_once('../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['first_name'], ['last_name'], ['package_id'], ['email_add'], ['pass_w'], ['c_pass_w'], ['secret_question_id'], ['secret_answer'], ['vercode']],
    'lengthMin' => [['pass_w', 7], ['c_pass_w', 7]],
    'lengthMax' => [['first_name', 65], ['middle_name', 20], ['last_name', 50], ['company_name', 100], ['email_add', 150], ['pass_w', 20], ['c_pass_w', 20], ['secret_answer', 190], ['vercode', 10]],
    'email' => [['email_add']],
    'equals' => [['c_pass_w', 'pass_w']],
    ];

    $form_v->labels(array(
    'package_id' => 'Membership Package',
    'email_add' => 'Email Address',
    'pass_w' => 'Password',
    'c_pass_w' => 'Confirm Password',
    'secret_question_id' => 'Secret Question',
    'vercode' => 'Verification Code',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    #/ Check Captcha Code
    if( (empty($_SESSION['cap_code'])) || (empty($_POST['vercode'])) || ($_SESSION['cap_code']!=$_POST['vercode']) )
    {
        $fv_errors[] = array('The Verification Code you entered does not match the one given in the image! Please try again.');
    }

    #/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT email_add FROM users WHERE email_add='{$_POST['email_add']}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Email Address is already used, please try a different one!');
        }
    }

    #/ Check if Package
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_pkg = mysql_exec("SELECT * FROM membership_packages WHERE id='{$_POST['package_id']}' AND is_active='1'", 'single');
        if(empty($chk_pkg))
        {
            $fv_errors[] = array('The Membership Package you selected cannot be purchased at this moment, please try a different one!');
        }
    }


    ##/ Process
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        #/ Determine Direction to move
        $chk_pkg = @format_str($chk_pkg);
        $chk_pkg['cost'] = (float)$chk_pkg['cost'];
        $package_type = 'free';
        if($chk_pkg['cost']>0){
        $package_type = 'paid';
        }
        //var_dump($chk_pkg['cost'], $package_type); die();


        #/ make a copy of submission
        $_SESSION['signup_filled']['1'] = $_POST;
        include_once('../includes/process_signup.php');

        if($package_type=='paid')
        {
            #/ Paid Package Processing
            $attempted_on = @process_signup_1($_POST, $chk_pkg, true); //$POST_key =
        }
        else
        {
            #/ Free Package Processing
            exit;
        }//end if free...
    }
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        update_attempt_counts();

        echo $fv_msg;
        exit;
    }
    #-

}//end if form post..


/////////////////////////////////////////////////////////////////////

#/ Get Final Amount to charge
//var_dump($chk_pkg, $package_type, $POST_key); die();
if(empty($chk_pkg) || ($package_type!='paid') || empty($attempted_on)){exit;}  //|| empty($POST_key)
$grand_total = (float)@$chk_pkg['cost'];

$items = array();
$items[0]['name'] = $chk_pkg['title'].' Joining Fees';
$items[0]['qty'] = 1;
$items[0]['amt'] = $grand_total;

#/ generate Hash for matching
$pay_chk = mt_rand();
$_SESSION['pay_chk'] = $pay_chk;

#/ generate Invoice Number
//$o_id = '' //??
//$invoice_str = str_pad($o_id, 5, "0", STR_PAD_LEFT);
//$invoice_num = "LDB-{$user_id}-{$invoice_str}";
#-
//die('go||||http://ldb-local/cart'); //test
///////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////

require_once("../includes/paypal/classic/express/paypalfunctions.php");

###/ Setup Paypal Parems
$ru = SITE_URL;
$paymentAmount = $grand_total;
$currencyCodeType = "USD";
$paymentType = "Sale";

$returnURL = "{$ru}signup-ppe-success/?pay_chk={$pay_chk}&";
$cancelURL = "{$ru}signup";

//$addi.= "&PAYMENTREQUEST_0_INVNUM={$invoice_num}";
$addi = "&REQCONFIRMSHIPPING=0";
$addi.= "&NOSHIPPING=1";
$addi.= "&GIFTMESSAGEENABLE=0&GIFTRECEIPTENABLE=0&GIFTWRAPENABLE=0";
$addi.= "&ALLOWNOTE=0&LOCALECODE=US&SOLUTIONTYPE=Sole&LANDINGPAGE=Billing";
//$addi.= "&PAGESTYLE=";

#/ Call Method
$resArray = CallShortcutExpressCheckout($paymentAmount, $currencyCodeType, $paymentType,
$returnURL, $cancelURL, $items, $addi);

$ack = strtoupper($resArray["ACK"]);
if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
{
    $token = urldecode($resArray["TOKEN"]);
    $_SESSION['reshash'] = $token;


    #/ Setup Signup Cart
    $_SESSION['signup_cart'] = array(
        $token => array('user_post'=>$_POST, 'paymentAmount'=>$paymentAmount, 'attempted_on'=>$attempted_on)
    );


    #/ Redirect to paypal.com here
    //RedirectToPayPal($token);
    global $PAYPAL_URL;
	$payPalURL = $PAYPAL_URL . $token;
    echo 'go||||D2CO-'.$token; //'go:'.$payPalURL

    exit;
}
else
{
    //Display a user friendly Error on the page using any of the following error information returned by PayPal
    $ErrorCode = urldecode(@$resArray["L_ERRORCODE0"]);
    $ErrorShortMsg = urldecode(@$resArray["L_SHORTMESSAGE0"]);
    $ErrorLongMsg = urldecode(@$resArray["L_LONGMESSAGE0"]);
    $ErrorSeverityCode = urldecode(@$resArray["L_SEVERITYCODE0"]);

    echo "[API CALL FAILED]\n\n";
    echo "Detailed Error Message: " . $ErrorLongMsg."\n\n";
    //echo "Short Error Message: " . $ErrorShortMsg."\n\n";
    //echo "Error Code: " . $ErrorCode."\n";
    //echo "Error Severity Code: " . $ErrorSeverityCode."\n";

    exit;
}
?>