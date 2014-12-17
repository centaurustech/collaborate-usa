<?php
function process_signup_2($POST)
{
    global $seo_tag, $consts;

    #/ encrypt password
    include_once('../includes/func_enc.php');
    $new_pass = @$POST['pass_w'];
    $pass_w = @md5_encrypt($new_pass);

    $joined_on = date('Y-m-d H:i:s');

    #/save users
    $sql_users = "INSERT INTO users (package_id, email_add, pass_w, first_name, middle_name, last_name, company_name, joined_on)
    VALUES ('{$POST['package_id']}', '{$POST['email_add']}', '{$pass_w}', '{$POST['first_name']}', '{$POST['middle_name']}', '{$POST['last_name']}', '{$POST['company_name']}', '{$joined_on}')";
    @mysql_exec($sql_users, 'save');
    $user_id = @mysql_insert_id();
    //var_dump($user_id); die();


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


        include_once('../includes/email_templates.php');
        include_once('../includes/send_mail.php');

        #/ Send Emails to User
        $heading = $subject = "Account Confirmation from collaborateUSA.com";
        $body_in = signup_success($POST['first_name'], $user_id, $verification_str);
        send_mail($POST['email_add'], $subject, $heading, $body_in);


        #/ Send Emails to Admin
        $subject = "New Member Signup Notification from collaborateUSA.com";
        $heading = "New Member Joined Us at collaborateUSA.com";
        $body_in_admin = signup_notice_to_admin($POST, $joined_on);
        send_mail($consts['support_em'], $subject, $heading, $body_in_admin);
        //die('x');


        #/ Lock & Redirect
        $_SESSION['signup_success'] = '1';
        $_SESSION['signup_stage'] = 'signup-details';
        reset_attempt_counts();
        redirect_me('signup-details');
    }
    else
    {
        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
        redirect_me($seo_tag);
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
            $copy_data = array(0=>array('i_part'=>'_th', 'size_w'=>35, 'size_h'=>35));

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