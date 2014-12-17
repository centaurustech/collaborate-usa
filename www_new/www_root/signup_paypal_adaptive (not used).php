<?php
exit;
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

$chk_pkg = $package_type = $POST_key = '';
if($sel_pack>0)
{
    $_SESSION['signup_stage'] = 'signup';

    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    //if(check_attempts(3)==false){
    //update_attempt_counts(); redirect_me($seo_tag);
    //}


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
            $POST_key = @process_signup_1($_POST, $chk_pkg, true);
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
if(empty($chk_pkg) || ($package_type!='paid') || empty($POST_key)){exit;}
$grand_total = (float)@$chk_pkg['cost'];

#/ generate Hash for matching
$pay_chk = mt_rand();
$_SESSION['pay_chk'] = $pay_chk;

//die('x');
/////////////////////////////////////////////////////////////////////

#/ Setup Paypal Parems
$ru = SITE_URL;
$paymentAmount = $grand_total;
$currencyCodeType = "USD";
$paymentType = "Sale";

$returnURL = "{$ru}signup-paypal-success/?pay_chk={$pay_chk}&";
$cancelURL = "{$ru}signup";

/////////////////////////////////////////////////////////////////////

##/ Setup & Call PP Adaptive Payment API
require_once('../includes/paypal/PPBootStrap.php');
require_once('../includes/paypal/Common/Constants.php');

#/ Setup Receiver
$receiver = array();
$receiver = new Receiver();
$receiver->email = BUSINESS_EMAIL_ADD; //sandbox acc = JulianIWetmore@armyspy.com
$receiver->amount = $paymentAmount; //Amount to be credited to the receiver's account
#$receiver->paymentType = 'SERVICE';

$receiverList = new ReceiverList($receiver);

#/ Generate Request
$payRequest = new PayRequest(
    new RequestEnvelope("en_US"),
    'PAY',
    $cancelURL,
    $currencyCodeType,
    $receiverList,
    $returnURL
);

#/ Initiate Service Adaptor & Call Action
$service = new AdaptivePaymentsService(Configuration::getAcctAndConfig());
try {
$response = $service->Pay($payRequest);
//requestEnvelope.errorLanguage=en_US&actionType=PAY&cancelUrl=http%3A%2F%2Fcusa-local%2Fsignup&currencyCode=USD&receiverList.receiver.amount=240&receiverList.receiver.email=JulianIWetmore%40armyspy.com&returnUrl=http%3A%2F%2Fcusa-local%2Fsignup-paypal-success%2F%3Fpay_chk%3D1799557580%26
} catch(Exception $ex) {
include_once '../includes/paypal/Common/Error.php';
exit;
}
//var_dump("<pre>", $response);


#/ Process Response
$ack = strtoupper($response->responseEnvelope->ack);
if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
{
    $token = urldecode($response->payKey);
    $_SESSION['reshash'] = $token;

    #/ Setup Signup Cart
    $_SESSION['signup_cart'] = array(
        $token => array('user_cart'=>$_POST, 'paymentAmount'=>$paymentAmount)
    );

    #/ Redirect to paypal.com here
	$payPalURL = $PAYPAL_URL . $token;
    echo 'go||||D2CO-'.$token; //'go:'.$payPalURL

    exit;

}//end if success...
{
    $errors = @$response->error[0];
    //var_dump("<pre>", $errors);

    echo "Paypal was unable to process your request at this moment.";

    /*echo "The process generated the follow error:<br /><br />"
    echo "<b>Error Code</b>: ".urldecode($errors->errorId).'<br />';
    echo "<b>Severity</b>: ".urldecode($errors->severity).'<br />';
    echo "<b>Message</b>: ".urldecode($errors->message).'';*/

    exit;
}
?>