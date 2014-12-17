<?php
//STEP 4
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


#/ Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0)){exit;}

#/ check & redirect - if already crossed Step1
if(!isset($_SESSION['signup_stage']) || empty($_SESSION['signup_stage'])){exit;}
if(($_SESSION['signup_stage']!='signup') && ($_SESSION['signup_stage']!='signup-ppe-success')){exit;}

///////////////////////////////////////////////////////////////////

function error_1($sgm = '')
{
    if(empty($sgm))
    echo 'Unable to proceed with your request at this moment! Please try again later.';
    else
    echo $sgm;
    exit;
}

#/ match pay_chk
if(!@array_key_exists('pay_chk', $_GET) || ($_GET['pay_chk']!=$_SESSION['pay_chk'])){error_1();}

#/ match Token
if(!isset($_GET['PayerID']) ||
!isset($_GET['token']) ||
($_GET['token']!=$_SESSION['TOKEN']) ||
!array_key_exists('signup_cart', $_SESSION))
{error_1();}

if(!array_key_exists($_GET['token'], $_SESSION['signup_cart']))
{error_1();}

$_SESSION['payer_id'] = $_GET['PayerID'];

///////////////////////////////////////////////////////////////////

#/ Get POSTED data
$signup_cart = @$_SESSION['signup_cart'][$_GET['token']];
$user_POST = @$signup_cart['user_post'];
if(!is_array($signup_cart) || count($signup_cart)<=0){error_1();}

//var_dump("<pre>", $signup_cart); die('x');

///////////////////////////////////////////////////////////////////

#/ Process POst data from Step 1
if(isset($user_POST['vercode']))
{
    //$fv_errors = array();
    //if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    //{
        ##/ Process Checkout confirmation with Paypal
        set_time_limit(0);

        require_once("../includes/paypal/classic/express/paypalfunctions.php");

        $net_total = @$signup_cart['paymentAmount'];
        $_SESSION['Payment_Amount'] = $net_total;

        $resArray = array();
        $ack = $msgx = '';

        $resArray = ConfirmPayment($net_total);
	    $ack = strtoupper($resArray["ACK"]);

        if($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING")
        {
            $transactionId		= @$resArray["PAYMENTINFO_0_TRANSACTIONID"]; // ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs.
    		$transactionType 	= @$resArray["PAYMENTINFO_0_TRANSACTIONTYPE"]; //' The type of transaction Possible values: l  cart l  express-checkout
    		$paymentType		= @$resArray["PAYMENTINFO_0_PAYMENTTYPE"];  //' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant

            $orderTime 			= @$resArray["PAYMENTINFO_0_ORDERTIME"];  //' Time/date stamp of payment

            $amt				= @$resArray["PAYMENTINFO_0_AMT"];  //' The final amount charged, including any shipping and taxes from your Merchant Profile.
    		$currencyCode		= @$resArray["PAYMENTINFO_0_CURRENCYCODE"];  //' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
    		$feeAmt				= @$resArray["PAYMENTINFO_0_FEEAMT"];  //' PayPal fee amount charged for the transaction
    		$settleAmt			= @$resArray["PAYMENTINFO_0_SETTLEAMT"];  //' Amount deposited in your PayPal account after a currency conversion.
    		$taxAmt				= @$resArray["PAYMENTINFO_0_TAXAMT"];  //' Tax charged on the transaction.
    		$exchangeRate		= @$resArray["PAYMENTINFO_0_EXCHANGERATE"];  //' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer's account.

            $msgx1 = array("TransactionId = {$transactionId}",
            "Trans. Type = {$transactionType}",
            "PaymentType = {$paymentType}",
            "OrderTime = {$orderTime}",
            "Currency = {$currencyCode}",
            "Amount = {$amt}",
            "PayPal fee charged = {$feeAmt}",
            "Tax charged on the transaction = {$taxAmt}",
            "Exchange Rate = {$exchangeRate}",
            "Final Amount Transferred to your Paypal Account = {$settleAmt}",
            );
            $msgx1 = format_str($msgx1);

            $msgx = array();

            /*
    		' Status of the payment:
    		'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
    		'Pending: The payment is pending. See the PendingReason element for more information.
    		*/
            $paymentStatus	= $resArray["PAYMENTINFO_0_PAYMENTSTATUS"];
            if(($paymentStatus!='') && (strtolower($paymentStatus)!='completed')) $msgx[]= 'Status: '.$paymentStatus;


            /*
    		'The reason the payment is pending:
    		'  none: No pending reason
    		'  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.
    		'  echeck: The payment is pending because it was made by an eCheck that has not yet cleared.
    		'  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.
    		'  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.
    		'  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.
    		'  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service.
    		*/
            $pendingReason	= $resArray["PAYMENTINFO_0_PENDINGREASON"];
            if(($pendingReason!='') && (strtolower($pendingReason)!='none')) $msgx[]= 'Pending Reason: '.$pendingReason;


            /*
    		'The reason for a reversal if TransactionType is reversal:
    		'  none: No reason code
    		'  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer.
    		'  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee.
    		'  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer.
    		'  refund: A reversal has occurred on this transaction because you have given the customer a refund.
    		'  other: A reversal has occurred on this transaction due to a reason not listed above.
    		*/
    		$reasonCode	= $resArray["PAYMENTINFO_0_REASONCODE"];
            if(($reasonCode!='') && (strtolower($reasonCode)!='none')) $msgx[]= 'Reversal Reason: '.$reasonCode;



            $msgx = format_str($msgx);

            #/ Return if Error
            if(in_array(strtolower($paymentStatus), array('Canceled-Reversal', 'Denied', 'Expired', 'Failed', 'Partially-Refunded', 'Refunded', 'Reversed'))!=false)
            {
                if(!empty($msgx))
                error_1("Unable to proceed with your request. The following Error has been returned from payPal:<br />".implode('<br />', $msgx));
                else
                error_1();
            }


            ##/ Process Signup
            $save_arr = array(
            'amount' => format_str($amt),
            'transaction_id' => format_str($transactionId),
            'gateway_name' => "PayPal",
            'gateway_msg' => implode('<br />', $msgx1).'<br />'.implode('<br />', $msgx),
            'payment_status' => format_str($paymentStatus),
            'gateway_payer_id' => format_str($_GET['PayerID']),
            );

            $chk_pkg = mysql_exec("SELECT * FROM membership_packages WHERE id='{$user_POST['package_id']}'", 'single');


            ##/ Save User, user Info, user payment etc
            include_once('../includes/check_attempts.php');
            include_once('../includes/process_signup.php');
            $user_id = process_signup_2($user_POST, $chk_pkg, false, $signup_cart['attempted_on']);
            if($user_id==false){error_1();}

            save_user_payment($user_id, $user_POST, $save_arr, $signup_cart['attempted_on']);
            #-


            #/ Clear Sessions
            unset($_SESSION['pay_chk']);
            unset($_SESSION['reshash']);
            unset($_SESSION['signup_cart']);
            unset($_SESSION['payer_id']);
            unset($_SESSION['Payment_Amount']);


            #/ Return on Success
            if(!empty($msgx)) $_SESSION["CUSA_MSG_GLOBAL"] = array(false, implode('<br />', $msgx));
            echo 'go||||TKU-'.$_GET['token'];

            exit;

        }//end if success...
        else
        {
            //Display a user friendly Error on the page using any of the following error information returned by PayPal
    		$ErrorCode = urldecode(@$resArray["L_ERRORCODE0"]);
    		$ErrorShortMsg = urldecode(@$resArray["L_SHORTMESSAGE0"]);
    		$ErrorLongMsg = urldecode(@$resArray["L_LONGMESSAGE0"]);
    		$ErrorSeverityCode = urldecode(@$resArray["L_SEVERITYCODE0"]);

            echo "[PAYPAL CALL FAILED]\n\n";
            echo "Detailed Error Message: " . $ErrorLongMsg."\n\n";
            //echo "Short Error Message: " . $ErrorShortMsg."\n\n";
            //echo "Error Code: " . $ErrorCode."\n";
            //echo "Error Severity Code: " . $ErrorSeverityCode."\n";
            exit;
        }
        #-
    /*}
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        error_1($fv_msg);
    }*/

}//end if post...
///////////////////////////////////////////////////////////////////
?>