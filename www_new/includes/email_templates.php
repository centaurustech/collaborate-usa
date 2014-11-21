<?php
function signup_success($name, $user_id, $verification_str, $insert_msg='', $admin_added=false)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
	$body_in .= "Dear <b>{$name}</b>,<br /><br />";

    if($admin_added==false)
    $body_in .= "Thank you for Signing up at <b>collaborateusa.com</b>. Your Account has been successfully created. {$insert_msg}<br /><br />";
    else
    $body_in .= "Your Account has been successfully created at <b>collaborateusa.com</b>. {$insert_msg}<br /><br />";

    $body_in .= "However, your account has NOT been activated yet. You are required to verify that you are the actual owner of this Email Address.<br /><br />";
    $body_in .= "Please click on the following link to <b>Verify</b> your email address and <b>Activate</b> your account:<br />";

    $body_in .= "<a href='{$site_url}account-confirm/?verify={$user_id}.|.{$verification_str}' target='_blank' style='color:#FB3C3C; text-decoration:none;'>";
    $body_in .= "{$site_url}account-confirm/?verify={$user_id}.|.{$verification_str}</a><br />";

	$body_in .= "";

    return $body_in;

}//end func...


function order_confirmation($first_name, $order_cart, $invoice_id)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $user_cart = @$order_cart['user_cart'];

    $body_in = "";
    $body_in .= "Dear <b>{$first_name}</b>,<br /><br />";
    $body_in .= "Thank you for your Purchases at <b>collaborateusa.com</b>, this is your Confirmation Receipt. ";
    $body_in .= "We have received your Order and will start processing it by communicating with you.<br /><br />";

    $body_in .= "However, you are required to first fill the initial Questionnaire(s) for the <b>Work Details</b> of your purchases. ";
    $body_in .= "You can find these Questionnaires at the bottom of the Checkout's Thank You page (i.e. Step 4) where you were taken after successful payment. ";
    $body_in .= "Additionally, you may also find them and Add/Review the Work Details from \"My Accounts\" menu after you login to your Account.<br /><br />";

    $body_in .= "Please note, the <b>Invoice Number</b> for this Order is <span style='color:#fb3c3c;'>{$invoice_id}</span>. ";
    $body_in .= "You can <a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>Login</a> to your account and review/track your Orders by matching it against this Invoice Number.<br /><br />";

    $body_in .= "<br />Here are your <b>Purchase details</b>:<br /><br />";

    $body_in .= '<div style="width:95%; display:inline-block; vertical-align:top; color:#777; font-size:12px;">';
    $body_in .= '<table cellspacing="0" cellpadding="0" border="0" style="width:100%; border:1px solid #eee; border-radius:5px; font-size:13px;">';
    $body_in .= '
    <tr>
        <th style="width:60%; background:#eee; font-weight:bold; padding:7px 11px; text-align:left; vertical-align:middle; font-size:13px;">Package(s)</th>
        <th style="width:15%; background:#eee; font-weight:bold; padding:7px 11px; text-align:right; vertical-align:middle; font-size:13px;">Unit&nbsp;Price</th>
        <th style="width:10%; background:#eee; font-weight:bold; padding:7px 11px; text-align:right; vertical-align:middle; font-size:13px;">Qty</th>
        <th style="width:15%; background:#eee; font-weight:bold; padding:7px 11px; text-align:right; vertical-align:middle; font-size:13px;">Total</th>
    </tr>';

    $body_in .= '<tr><td colspan="4" style="border:none; padding:7px 11px; font-size:13px;"></td></tr>';

    $i_uc = 0;
    if(is_array($user_cart) && count($user_cart)>0)
    {
        $total_quantity = 0;
        $total_amount = 0;

        foreach($user_cart as $uc)
        {
            if(stristr($uc['title'], 'package')==false)
            $uc['title'] = $uc['title'].' Package';

            $tr_class = 'border-bottom:1px solid #eee;';
            if($i_uc>=count($user_cart)-1){
            $tr_class = '';
            }

            $amount_t = number_format($uc['amount_t'], 2);

            $unit_price = $uc['unit_price'];
            $price_display = "\$".number_format($unit_price, 2);

            $disc_title = '';
            if($uc['discount']>0)
            {
                $unit_price_dis = $uc['unit_price_dis'];
                $price_display = "<span style=\"color:red; font-size:12px; text-decoration:line-through;\">\$".number_format($unit_price, 2).'</span><br />';
                $price_display.= "\$".number_format($unit_price_dis, 2);

                $disc_title = " <span style='color:red; font-size:12px; font-style:italic; font-weight:normal;'>(with a {$uc['discount']}% discount)</span>";
            }

            $total_quantity+= (int)$uc['quantity'];
            $total_amount+= (float)$uc['amount_t'];

            $body_in .= "<tr>";

                $body_in .= "<td style='border:none; {$tr_class} font-size:12px; padding:7px 11px; text-align:left; vertical-align:top;'>
                <div style='font-size:12px; font-weight:bold;'>{$uc['title']}{$disc_title}</div>
                <span style='color:red; font-style:italic; font-size:12px;'>{$uc['bt_title']}</span>
                </td>";

                $body_in .= "<td style=\"border:none; {$tr_class} font-size:12px; padding:7px 11px; vertical-align:top; text-align:right;\">
                {$price_display}
                </td>";

                $body_in .= "<td style=\"border:none; {$tr_class} font-size:12px; padding:7px 11px; vertical-align:top; text-align:right;\">
                <div>{$uc['quantity']}</div>
                </td>";

                $body_in .= "<td style=\"border:none; {$tr_class} font-size:12px; padding:7px 11px; vertical-align:top; text-align:right; color:#2c8db6; font-weight:bold;\">
                \${$amount_t}
                </td>";

            $body_in .= "</tr>";

            $i_uc++;

        }//end foreach...

        $grand_total = $total_amount;

        #/ Apply Discount
        if(array_key_exists('user_cart_coupon', $order_cart) && count($order_cart['user_cart_coupon'])>0)
        {
            $discount_title = $order_cart['user_cart_coupon']['info']['title'];
            $discount_coupon = $order_cart['user_cart_coupon']['info']['coupon_code'];
            $total_discount = (float)$order_cart['user_cart_coupon']['info']['discount_perc'];

            if($total_discount>0){
            $grand_total = round($total_amount*(1-($total_discount/100)), 2);
            }
        }
        $body_in .= '<tr><td colspan="4" style="border:none; padding:7px 11px; font-size:12px;"></td></tr>';
        $body_in .= '</table><br />';

        $body_in .= '<table cellspacing="0" cellpadding="0" border="0" style="width:100%; border:1px solid #eee; border-radius:5px; font-size:12px;">';

        $body_in .= "
        <tr>
            <td style=\"font-weight:bold; font-size:13px; border-bottom:1px solid #eee; padding:7px 11px; text-align:left; vertical-align:top;\">Sub Total -</td>
            <td style=\"width:10%; font-size:12px; border-bottom:1px solid #eee; padding:7px 11px; text-align:right; vertical-align:top;\">{$total_quantity}</td>
            <td style=\"width:15%; font-weight:bold; font-size:12px; border-bottom:1px solid #eee; padding:7px 11px; text-align:right; vertical-align:top; color:#2c8db6;\">$".@number_format($total_amount, 2)."</td>
        </tr>
        ";

        if(!array_key_exists('user_cart_coupon', $order_cart) || ($order_cart['user_cart_coupon']=='') || (empty($order_cart['user_cart_coupon'])) ) {
        } else {
        $body_in .= "
        <tr>
            <td style=\"vertical-align:middle; font-weight:bold; font-size:12px; border-bottom:1px solid #eee; padding:7px 11px; text-align:left;\">Discount Applied <br /><span style=\"font-size:11px !important; font-style:italic;\">({$discount_title}, Code: \"{$discount_coupon}\")</span></td>
            <td colspan=\"2\" style=\"color:red; vertical-align:top; font-size:12px; border-bottom:1px solid #eee; padding:7px 11px; text-align:right;\">({$total_discount}%)</td>
        </tr>";
        }

        $body_in .= "
        <tr>
            <td style=\"font-weight:bold; font-size:13px; border:none; padding:12px 11px 9px; text-align:left; vertical-align:top; color:#2c8db6;\">Grand Total -</td>
            <td colspan=\"2\" style=\"font-weight:bold; font-size:13px; border:none; padding:12px 11px 9px; text-align:right; vertical-align:middle; color:#2c8db6;\">
            <div style='text-decoration:underline;'>$".@number_format($grand_total, 2)."</div>
            <div>
                <div style=\"border-top: 1px solid #2c8db6; clear:both; float:right; height:1px; padding:2px 0; text-align:right; width:50%;\">&nbsp;</div>
            </div>
            </td>
        </tr>
        ";

    }//end if...
    else
    {
        $body_in .= '<tr><td colspan="4" style="border:none; padding:7px 11px; font-size:12px; text-align:center; color:#bc272d;">No Results Found. Your Shopping Cart is Empty!</td></tr>';
        $body_in .= '<tr><td colspan="4" style="border:none; padding:7px 11px; font-size:12px;"></td></tr>';
    }
    $body_in .= '</table><br />';
    $body_in .= '</div>';

    $body_in .= "";

    return $body_in;

}//end func...


function payment_invoice($invoice_id, $resArray)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";

    $body_in .= "Dear Admin,<br /><br />";

    $body_in .= "Here are the <b>Payment Details</b> for the Order <span style='color:#fb3c3c;'>{$invoice_id}</span> from collaborateusa.com.<br /><br />";
    $body_in .= "For this Order, you will also receive a separate email from <b>PayPal</b> as well - matching the <b>Transaction ID</b>. ";
    $body_in .= "Furthermore, an Admin's copy of the <b>Order Confirmation Receipt</b> has also been sent to you.<br />";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:20px 0; width:90%;' />";

    $body_in .= "<b>Transaction ID: </b>{$resArray['transaction_id']}<br />";
    $body_in .= "<b>Amount: </b>\${$resArray['amount']}<br />";
    $body_in .= "<b>Payment Status: </b>{$resArray['payment_status']}<br /><br />";
    $body_in .= "<b>Full Gateway Message: </b><br />{$resArray['gateway_msg']}";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:20px 0; width:90%;' />";

    $body_in .= "You can track this Customer/Order on <b>LDB Admin</b> via the given Invoice Number.<br />";

    return $body_in;

}//end func...


function welcome_new_user($user_info, $insert = '')
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in .= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";
    $body_in .= "Your Email Address has been successfully <b>Verified</b> and your account has been <b>Activated</b>. Welcome to collaborateusa.com<br /><br />{$insert}";
    $body_in .= "Please <a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>Click Here to Login</a> to your account using your Email Address & Password.<br />";

    return $body_in;

}//end func...


function resend_activation($user_info, $act_link, $insert = '')
{
    $body_in = "";
    $body_in.= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";
    $body_in.= "{$insert}Here is your requested <b>Activation Code</b>.<br /><br />Please visit the following link to Activate your Account:<br />";
    $body_in.= "<a href=\"{$act_link}\" style='color:#FB3C3C; text-decoration:none;'>{$act_link}</a>";

    return $body_in;

}//end func...


function account_recover_access($user_info, $new_pass)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in.= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";

    $body_in.= "We have received a request from you to Recover the Access to your account. As a result, we have setup a <b>Temporary Password</b> for you, please find the details below:<br /><br />";
    $body_in.= "<b>Login Email Address:</b> {$user_info['email_add']}<br />";
    $body_in.= "<b>Password:</b> {$new_pass}<br /><br />";
    $body_in.= "<u>Note</u>: This is an auto-generated password. You are requested to change it after you login.<br /><br />";

    $body_in.= "Please use the following link to <a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>Login</a> to your account using your Email Address & Password:<br />";
    $body_in.= "<a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>{$site_url}login</a>";

    return $body_in;

}//end func...


function password_updated($user_info, $new_pass)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $updated_on = date('Y-m-d H:i:s');

    $body_in = "";
    $body_in.= "Dear <b>{$user_info['first_name']}</b>,<br /><br />";

    $body_in.= "This is a confirmation that you have successfully <b>Updated</b> your Account Password at collaborateusa.com. However, if the Password Change was not initiated by you, please feel free to contact the Admin.<br /><br />";
    $body_in.= "Please find the details below:<br /><br />";
    $body_in.= "<b>New Password:</b> {$new_pass}<br />";
    $body_in.= "<b>Updated On:</b> {$updated_on}<br /><br />";

    $body_in.= "Please use the following link to <a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>Login</a> to your account using your Email Address & Password:<br />";
    $body_in.= "<a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>{$site_url}login</a>";

    return $body_in;

}//end func...


function questionnaire_filled($first_name, $questionnaires, $POST, $cart_details, $quest_form)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in.= "Dear <b>{$first_name}</b>,<br /><br />";

    $body_in.= "Thank you for filling up the <b>Work Details Questionnaire</b> at collaborateusa.com. This is a confirmation that you have successfully submitted it. We will now review your submission and contact you back via email.<br /><br />";

    $body_in.= "Here is the Order Details for this submission:<br /><br />";

    $body_in.= "Invoice Number: <b style='color:#fb3c3c;'>{$cart_details['invoice']}</b><br />";
    $body_in.= "Package: <b style='color:#fb3c3c;'>{$cart_details['title']} ({$cart_details['bt_title']})</b><br />";

    $body_in.= "<hr style='text-align:left; border:none; background:none; height:1px; border-bottom:solid 1px #ddd; margin:20px 0; width:90%;' />";
    $body_in.= "Here is the copy of your submission:<br /><br />";

    $i_qc = 1;
    foreach($questionnaires as $qc)
    {
        $c_qc_id = "qfield_{$quest_form}_{$qc['qf_id']}";

        if(!array_key_exists($c_qc_id, $POST) || empty($POST[$c_qc_id])){$POST[$c_qc_id]='-';}

        $label = $qc['label'];
        if((strrpos($label, '?')!=(strlen($label)-1)) && (strrpos($label, ':')!=(strlen($label)-1)))
        $label.= ':';

        if(is_array($POST[$c_qc_id])) $POST[$c_qc_id] = implode(', ', $POST[$c_qc_id]);

        if($qc['field_type']=='textarea')
        $POST[$c_qc_id] = ucfirst($POST[$c_qc_id]);
        else
        $POST[$c_qc_id] = ucwords($POST[$c_qc_id]);

        $body_in.= "<b>{$i_qc}) {$label}</b><br /><span style='color:#2c8db6;'>{$POST[$c_qc_id]}</span><br /><br />";

        $i_qc++;
    }

    return $body_in;

}//end func....


function order_status_updated($first_name, $invoice, $order_status)
{
    global $consts;
    $site_url = $consts['SITE_URL'];

    $body_in = "";
    $body_in.= "Dear <b>{$first_name}</b>,<br /><br />";
    $body_in.= "This is a notification to inform you that the <b>Status</b> for your <b style='color:#fb3c3c;'>Order {$invoice}</b> has been updated. Please find the details below:<br /><br />";
    $body_in.= "Order Invoice Number: <b>{$invoice}</b><br />";
    $body_in.= "Order Status: <b style='color:#fb3c3c;'>{$order_status}</b><br /><br /><br />";

    $body_in.= "You can review full Details of your Order from \"My Accounts\" menu at the collaborateusa.com after you login. ";
    $body_in.= "Please use the following link to <a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>Login</a> to your account:<br />";
    $body_in.= "<a href='{$site_url}login' target='_blank' style='color:#FB3C3C; text-decoration:none;'>{$site_url}login</a>";

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

    $body_in .= "<a href='{$site_url}newsletter-confirm/?verify={$nl_subscriber_id}.|.{$verification_str}' target='_blank' style='color:#FB3C3C; text-decoration:none;'>";
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

    $body_in.= "Though, we are sad to see you leave, you are always welcome to <b>Subscribe back</b> again to our Newsletters by following the <a href='{$site_url}newsletter-subscription' target='_blank' style='color:#FB3C3C; text-decoration:none;'>Subscription link</a> given below:<br /><br />";
    $body_in .= "<a href='{$site_url}newsletter-subscription' target='_blank' style='color:#FB3C3C; text-decoration:none;'>{$site_url}newsletter-subscription</a><br /><br />";

    $body_in.= "Once again, we would like to thank you for your support.<br />";

    return $body_in;
}
?>