<?php
function process_signup_1($POST, $chk_pkg, $exit=false)
{
    global $seo_tag, $consts;

    #/ Setup Variables
    $email_add = @$POST['email_add'];
    if(empty($email_add))
    {
        if($exit!=false){exit;}else{
        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
        redirect_me($seo_tag);
        }
    }

    $attempted_on = date('Y-m-d H:i:s');
    $POST['attempted_on'] = $attempted_on;

    $package_title = @$chk_pkg['title'];
    if(empty($package_title))
    {
        if($exit!=false){exit;}else{
        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
        redirect_me($seo_tag);
        }
    }
    $recurring = false;
    if($chk_pkg['is_recursive']=='1' && ((float)$chk_pkg['recursive_cost']>0)){
    $recurring = true;
    }


    #/ Save Initial Data in DB temp table signup_temp
    /*
    $POST_ser = base64_encode((gzcompress(serialize($POST))));
    $POST_key = md5(microtime().rand().md5($email_add)); //uniqid(rand(20, 300), true)

    $sql_users = "INSERT INTO signup_temp (p_key, p_val, attempted_on)
    VALUES ('{$POST_key}', '{$POST_ser}', '{$attempted_on}')";
    @mysql_exec($sql_users, 'save');

    return $POST_key;
    */

    return $attempted_on;

}//end func...



function process_signup_2($POST, $chk_pkg, $redir=true, $joined_on='')
{
    global $seo_tag, $consts;

    #/ encrypt password
    include_once('../includes/func_enc.php');
    $new_pass = @$POST['pass_w'];
    $pass_w = @md5_encrypt($new_pass);

    if(empty($joined_on))
    $joined_on = date('Y-m-d H:i:s');

    #/save users
    $sql_users = "INSERT INTO users (package_id, email_add, pass_w, first_name, middle_name, last_name, company_name, joined_on)
    VALUES ('{$POST['package_id']}', '{$POST['email_add']}', '{$pass_w}', '{$POST['first_name']}', '{$POST['middle_name']}', '{$POST['last_name']}', '{$POST['company_name']}', '{$joined_on}')";
    @mysql_exec($sql_users, 'save');
    $user_id = @mysql_insert_id();
    //var_dump("<pre>", $sql_users, $user_id); die();


    if($user_id>0)
    {
        $POST['secret_question_id'] = (int)@$POST['secret_question_id'];

        #/save user_info
        $sql_user_info = "INSERT INTO user_info (user_id, secret_question_id, secret_answer)
        VALUES ('{$user_id}', '{$POST['secret_question_id']}', '{$POST['secret_answer']}')";
        @mysql_exec($sql_user_info, 'save');


        #/save acc_verifications
        $verification_str = mt_rand().md5(uniqid(rand())).mt_rand();
        $sql_veri = "INSERT INTO acc_verifications (user_id, verification_str)
        VALUES ('{$user_id}', '{$verification_str}')";
		@mysql_exec($sql_veri, 'save');


        #/save user_permissions //private fields only
        $fields_perm = 'email_add,state,city,address_ln_1,address_ln_2,zip,phone_number';
        $sql_fields_perm = "INSERT INTO user_permissions (user_id, fields_perm)
        VALUES ('{$user_id}', '{$fields_perm}')";
		@mysql_exec($sql_fields_perm, 'save');


        include_once('../includes/email_templates.php');
        include_once('../includes/send_mail.php');

        #/ Send Emails to User
        $heading = $subject = "Account Confirmation from collaborateUSA.com";
        $body_in = signup_success($POST['first_name'], $user_id, $verification_str, '', $chk_pkg);
        send_mail($POST['email_add'], $subject, $heading, $body_in, 'collaborateUSA.com', $consts['mem_support_em']);


        #/ Send Emails to Admin
        $subject = "New Member Signup Notification from collaborateUSA.com";
        $heading = "New Member Joined Us at collaborateUSA.com";
        $body_in_admin = signup_notice_to_admin($POST, $joined_on, $chk_pkg['is_basic']);
        send_mail($consts['mem_support_em'], $subject, $heading, $body_in_admin);
        //die('x');


        #/ Generate Welcome Notification
        include_once('../includes/notif_func.php');
        $notif_data = array(
        'template_id' => "5",
        'user_id' => "{$user_id}", //receiver
        'from_user_id' => "0",
        'objects' => '',
        'object_id' => '0',
        'object_location' => '',
        );
        @generate_notification($notif_data);


        if($redir!=false){
        #/ Lock & Redirect
        $_SESSION['signup_success'] = '1';
        $_SESSION['signup_stage'] = 'signup-details';
        reset_attempt_counts();
        redirect_me('signup-details');
        } else {
        return $user_id;
        }
    }
    else
    {
        if($redir!=false){
        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
        redirect_me($seo_tag);
        } else {
        return false;
        }
    }

}//end func....


function save_user_payment($user_id, $user_POST, $save_arr, $attempted_on)
{
    global $seo_tag, $consts;

    #/ Save user_payments
    $sql_1 = "INSERT INTO user_payments (user_id, invoice, amount, transaction_id, gateway_name, gateway_payer_id, gateway_msg, payment_status, paid_on)
    values ('{$user_id}', '', '{$save_arr['amount']}', '{$save_arr['transaction_id']}', '{$save_arr['gateway_name']}', '{$save_arr['gateway_payer_id']}', '{$save_arr['gateway_msg']}', '{$save_arr['payment_status']}', '{$attempted_on}')";
    @mysql_exec($sql_1, 'save');

    $user_payment_id = (int)@mysql_insert_id();

    if($user_payment_id>0)
    {
        #/ Setup & Save Invoice ID
        $invoice_str = str_pad($user_payment_id, 4, "0", STR_PAD_LEFT);
        $user_id_str = str_pad($user_id, 3, "0", STR_PAD_LEFT);
        $invoice_id = 'CUSA-'.$user_id_str.'-'.$invoice_str;

        $sql_3 = "UPDATE user_payments SET invoice='{$invoice_id}' WHERE id='{$user_payment_id}'";
        @mysql_exec($sql_3, 'save');


        #/ Send Payment Email to User
        $subject = "[{$invoice_id}] Payment Receipt from collaborateUSA.com";
        $heading = "Payment Receipt & Invoice from collaborateUSA.com";
        $body_in = payment_receipt($invoice_id, $save_arr, $user_POST);
        send_mail($user_POST['email_add'], $subject, $heading, $body_in, 'collaborateUSA.com', $consts['mem_support_em']);


        #/ Send Payment Email to Admin
        $subject = "[{$invoice_id}] Payment Received at collaborateUSA.com";
        $heading = "Payment Invoice from collaborateUSA.com";
        $body_in = payment_invoice($invoice_id, $save_arr, $user_POST);
        send_mail($consts['mem_support_em'], $subject, $heading, $body_in);


        #/ Generate ThankYou Notification
        include_once('../includes/notif_func.php');
        $notif_data = array(
        'template_id' => "7",
        'user_id' => "{$user_id}", //receiver
        'from_user_id' => "0",
        'objects' => "{$invoice_id}",
        'object_id' => '0',
        'object_location' => '',
        );
        @generate_notification($notif_data);


        #/ Allocate & Assign Patronage Points
        include_once('../includes/patronage_points_func.php');
        @generate_ppoints($user_id, 'join_share');


        #/ Clear Sessions
        unset($_SESSION['pay_chk']);
        unset($_SESSION['reshash']);
        unset($_SESSION['signup_cart']);
        unset($_SESSION['payer_id']);
        unset($_SESSION['Payment_Amount']);


        #/ Lock & Redirect
        $_SESSION['signup_success'] = '1';
        $_SESSION['signup_stage'] = 'signup-details';
        reset_attempt_counts();
        //redirect_me('signup-details'); //ajax based not possible to redirect

        return true;
    }

}//end func....


function process_signup_3($POST, $FILES, $user_id)
{
    global $seo_tag, $consts;

    if($user_id>0)
    {
        ##/ Process Profile Pic
        include_once('../includes/resize_images.php');
        $up_path = "user_files/prof/{$user_id}/";
        if(!is_dir($up_path)){mkdir($up_path, 0705, true);}

        $sql_prt = $profile_pic = '';
        if(is_uploaded_file(@$_FILES['profile_pic']['tmp_name']))
        {
            $copy_data = array(0=>array('i_part'=>'_th', 'size_w'=>60, 'size_h'=>60));

            $profile_pic = upload_img_rs('profile_pic', 250, 250, $up_path, 'Profile Pic', '', 250, 'CUSA_MSG_GLOBAL', false, $copy_data);
            if($profile_pic!='') {
            $sql_prt.=", profile_pic='{$profile_pic}'";
            }
        }
        //die('x');
        #


        #/save users
        $sql_users = "UPDATE users SET
        screen_name='{$POST['screen_name']}', identify_by='{$POST['identify_by']}' {$sql_prt}
        WHERE id = '{$user_id}'";
        @mysql_exec($sql_users, 'save');


        #/save user_info
        $sql_user_info = "UPDATE user_info SET
        country_code = '{$POST['country_code']}', state = '{$POST['state']}', city = '{$POST['city']}',
    	address_ln_1 = '{$POST['address_ln_1']}', address_ln_2 = '{$POST['address_ln_2']}',
    	zip = '{$POST['zip']}', phone_number = '{$POST['phone_number']}'
        WHERE user_id = '{$user_id}'";
        @mysql_exec($sql_user_info, 'save');
        //die('x');


        $_SESSION['signup_success'] = '2';
        $_SESSION['signup_stage'] = 'signup-details';
        reset_attempt_counts();
        redirect_me('signup-details/success');
    }
    else
    {
        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
        redirect_me($seo_tag);
    }
}
?>