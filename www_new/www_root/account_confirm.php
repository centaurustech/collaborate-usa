<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}home';</script>";
    exit;
}
////////////////////##--

$success = $resend = $resent = false;
if(@in_array('success', $url_comp)!=false){
$success = true;
} else if(@in_array('resent', $url_comp)!=false){
$resent = true;
}
else if(@in_array('resend', $url_comp)!=false)
{
    if(@array_key_exists('resend_chk', $_SESSION)!=false)
    $resend = true;
    else
    redirect_me('404');
}

$show_form = true;
//die('x');

//var_dump($_GET, $url_comp); die();

/////////////////////////////////////////////////////////////////////
#/ Process Post
if(isset($_POST['email_add']) && ($resend == true))
{
    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    if(check_attempts(3)==false){
    update_attempt_counts(); redirect_me($seo_tag);
    }

    ##/ Validate Fields
    include_once('../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['email_add'], ['vercode']], //['secret_question_id'], ['secret_answer'],
    'lengthMax' => [['email_add', 150], ['vercode', 10]], //['secret_answer', 190],
    'email' => [['email_add']],
    ];

    $form_v->labels(array(
    'email_add' => 'Email Address',
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


    ##/ Find User Info
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        //$_POST['secret_question_id'] = (int)@$_POST['secret_question_id'];

        $sql_1 = "SELECT * FROM users US
        LEFT JOIN user_info UI ON US.id = UI.user_id
        WHERE US.email_add='{$_POST['email_add']}'
        ";
        //AND UI.secret_question_id='{$_POST['secret_question_id']}' AND secret_answer='{$_POST['secret_answer']}'
        $qa_res = mysql_exec($sql_1, 'single');

        if(empty($qa_res) || !is_array($qa_res))
        {
            $fv_errors[] = array("Unable to match your given info in our Records! Please try again.");
        }
        else
        {
            #/ Check if account is already activated
            if($qa_res['account_activated'] == 1){
            $fv_errors[] = array("Your Account has <b>already been ACTIVATED</b>!<br />- Please <a href='{$consts['SITE_URL']}signin' style='font-weight:bold; color:red'>Signin</a> to your Account.");
            unset($_SESSION['resend_chk']);
            $show_form = false;
            }

            #/ Check if account is blocked
            if($qa_res['is_blocked'] == 1){
            $fv_errors[] = array("Your Account has <b>BLOCKED</b> by the Admin! Consequently, you will NOT be able to Signin to your Account.");
            unset($_SESSION['resend_chk']);
            $show_form = false;
            }
        }
    }
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $user_info = $qa_res;
        if( is_array($user_info) && array_key_exists('email_add', $user_info) )
        {
            $u_id = $user_info['user_id'];
            $verification_str = mt_rand().md5(uniqid(rand())).mt_rand();


            #/delete old veri codes
            $sql_av = "DELETE FROM acc_verifications WHERE user_id='{$u_id}'";
    		@mysql_exec($sql_av, 'save');

            #/ Insert new veri code
            $sql_veri = "INSERT INTO acc_verifications (user_id, verification_str)
            VALUES ('{$u_id}', '{$verification_str}')";
    		@mysql_exec($sql_veri, 'save');
            //die('x');


            #/ Send Welcome Email to User
            include_once('../includes/email_templates.php');
            include_once('../includes/send_mail.php');

            $act_link = $consts['SITE_URL']."account-confirm/?verify={$u_id}.|.{$verification_str}";

            $subject = "Account Activation Code from collaborateUSA.com";
            $heading = "New Account Activation Code";
            $body_in = resend_activation($user_info, $act_link);
            send_mail($user_info['email_add'], $subject, $heading, $body_in);


            unset($_SESSION['resend_chk']);
            redirect_me($seo_tag.'/resent');
        }
        else
        {
            $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
            redirect_me($seo_tag);
        }

        exit;
    }
    else
    {
        $fv_msg = 'You have the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, $fv_msg);
        update_attempt_counts();
    }

}//end post.....

/////////////////////////////////////////////////////////////////////

#/ Process verify
if(isset($_GET['verify']))
{
    $fv_errors = array();

    #/ Check Attempts
    include_once('../includes/check_attempts.php');
    if(check_attempts(3)==false){
    update_attempt_counts(); redirect_me($seo_tag);
    }


    #/ Get Verification Str and User Id
    $t1 = explode('.|.', $_GET['verify']);
    $u_id = (int) @$t1[0];
    $verification_str = (string) @$t1[1];

    #/ fix spoace issues added by email clients
    $verification_str = preg_replace('/[!\s]{0,}/m', '', $verification_str);


    #/ match Verification Str from the DB
    $sql_1 = "SELECT * FROM users US
    LEFT JOIN user_info UI ON US.id = UI.user_id
    INNER JOIN acc_verifications AV ON US.id = AV.user_id
    WHERE AV.user_id='{$u_id}' AND verification_str='{$verification_str}'";
    //die($sql_1);
    $ver_res = mysql_exec($sql_1, 'single');

    if(empty($ver_res) || !is_array($ver_res))
    {
        $fv_errors[] = array("The Verification Code does not match (or has expired)! &nbsp;<a href=\"{$consts['DOC_ROOT']}account-confirm/resend\" style='font-weight:bold; color:red'>Click Here</a> to RESEND the Activation Email.");
        $_SESSION['resend_chk'] = true; //allow Resend Form to be visible
    }
    else
    {
        #/ Check if account is already activated
        if($ver_res['account_activated'] == 1){
        $fv_errors[] = array("Your Account has <b>already been ACTIVATED</b>!<br />- Please <a href='{$consts['SITE_URL']}signin' style='font-weight:bold; color:red'>Signin</a> to your Account.");
        unset($_SESSION['resend_chk']);
        $show_form = false;
        }

        #/ Check if account is blocked
        if($ver_res['is_blocked'] == 1){
        $fv_errors[] = array("Your Account has <b>BLOCKED</b> by the Admin! Consequently, you will NOT be able to Signin to your Account.");
        unset($_SESSION['resend_chk']);
        $show_form = false;
        }
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        #/delete from acc_verifications
        $sql_av = "DELETE FROM acc_verifications WHERE user_id='{$u_id}' AND verification_str='{$verification_str}'";
		@mysql_exec($sql_av, 'save');

        #/update users
        $sql_users = "UPDATE users SET account_activated='1' WHERE id='{$u_id}'";
		@mysql_exec($sql_users, 'save');


        #/ Get user info
        $user_info = $ver_res;

        if( is_array($user_info) && array_key_exists('email_add', $user_info) )
        {
            include_once('../includes/email_templates.php');
            include_once('../includes/send_mail.php');

            #/ Send Welcome Email to User
            $subject = "Welcome to collaborateUSA.com | Account Activated Successfully";
            $heading = "Account Successfully Activated | Welcome to collaborateUSA.com";

            $insert_bdy = '';
            if($user_info['screen_name']=='')
            {
                $insert_bdy = "<u>Do Note</u>: You are required to setup your <b>Profile Info</b> including your Screen Name (if you have not done that already).<br /><br />";
            }

            $body_in = welcome_new_user($user_info, $insert_bdy);
            send_mail($user_info['email_add'], $subject, $heading, $body_in);


            #/ redirect
            reset_attempt_counts();
            unset($_SESSION['resend_chk']);
            redirect_me($seo_tag.'/success');
        }
        else
        {
            $_SESSION["CUSA_MSG_GLOBAL"] = array(false, 'Unable to process your request at this moment! Please try again later.');
            redirect_me($seo_tag);
        }

        exit;
    }
    else
    {
        $fv_msg = 'You have the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        $_SESSION["CUSA_MSG_GLOBAL"] = array(false, $fv_msg);
        update_attempt_counts();
    }

}//end if form post..

/////////////////////////////////////////////////////////////////////

$category_msg2 = "If you already have Activated your Account previously,
please <b><a href=\"{$consts['DOC_ROOT']}signin\">Click Here to Signin</a></b> instead.";

if($resend==true)
{
    //$secret_questions = @format_str(@mysql_exec("SELECT * FROM secret_questions ORDER BY question"));
    $category_msg = "Please fill the form below to <b>Resend the Activation Code</b> for your User Account at collaborateUSA.com. ".$category_msg2;
}

/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Account Activation",
);
$page_heading = "Account Activation at collaborateUSA.com";

$load_validation = true;
include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />

<?php if($resend==true){ ?>
<script>
$(document).ready(function() {
    $("#form_act").validationEngine({
    promptPosition: loc,//"topRight"
    });
});
</script>
<?php } ?>


<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">
<div class="mid_bdy body-main" style="padding-top:30px;">

<?php if(($resend==true) || ($success==true) || ($resent==true)){ ?>

    <?php if($show_form==true){ ?><h1><strong><?php if($resend==true){echo "Resend Account Activation Code";}else{echo $page_heading;} ?> </strong></h1><br /><?php } ?>

    <?php if($success==true){ ?>

        <div>Dear User,<br /><br />Thank you for Verifying your Email Address. Your Account has been activated and a
        <b>Welcome Email</b> has been sent to you at your registered Email Address. You can now easily
        <b><a href="<?=DOC_ROOT?>signin">Signin</a></b> to your Account.<br /><br />

        <b style="font-size: 1.2em !important%;">
        Best Regards,<br />
        <span style="color:#2CA1F4;">collaborateUSA.com</span></b>
        </div>

    <?php }else if ($resent==true){ ?>

        <div>Dear User,<br /><br />A message has been sent to your provided Email Address with a new <b>Account Activation Link</b>.
        Please follow that email in order to verify your Email Address and <b>Activate</b> your Account.<br /><br />

        <?php if(!empty($category_msg2)) { ?><?php echo $category_msg2; ?><br /><br /><?php } ?>

        <b style="font-size: 1.2em !important%;">
        Best Regards,<br />
        <span style="color:#2CA1F4;">collaborateUSA.com</span></b>
        </div>

    <?php }else if (($resend==true) && ($show_form==true)){ ?>

    <?php if(!empty($category_msg)) { ?><div class="content"><?php echo $category_msg; ?></div><br /><?php } ?><br />

    <?php $tabindex=1; ?>
    <div class="span10" style="margin-left:0;">
    <div class="contact-main fields-big">
    <form id="form_act" name="form_act" action="" method="POST" <?=AUTO_COMPLETE?>>


        <div class="">
            <input type="text" name="email_add" id="email_add" class="validate[required, custom[email]]"
            value="<?php if(isset($_POST['email_add'])){echo $_POST['email_add'];} ?>"
            placeholder="Email Address" maxlength="150" style="width:88%;" tabindex="<?=$tabindex++?>"
            /><span class="required">*</span>
        </div>

        <div style="clear: both;"></div>
        <br />

        <?php /*
        <br /><br />
        <select name="secret_question_id" id="secret_question_id" class="validate[required]" style="width:88%;">
            <option value="">Select Secret Question</option>
            <?php foreach ($secret_questions as $secret_question) { ?>
            <option value='<?php echo $secret_question['id']; ?>'><?php echo $secret_question['question']; ?></option>
            <?php } ?>
        </select>
        <span class="required">&nbsp;&nbsp;*</span>
        <?php if(isset($_POST['secret_question_id'])){echo "<script>document.getElementById('secret_question_id').value='{$_POST['secret_question_id']}';</script>";} ?>
        <br />

        <input type="text" name="secret_answer" id="secret_answer" class="validate[required]" value=""
        placeholder="Secret Answer" maxlength="190" style="width:88%;" />
        <span class="required">&nbsp;&nbsp;*</span>
        <br />
        */ ?>

        <hr style="width:68%; display:inline-block;" />
        <div style="clear: both;"></div>
        <br />


        <div class="right_fl veri_block">
            <div style="font-size:12px; margin-bottom:10px;">Please enter the Code you see in the box into this field:</div>
            <?php $title = 'Please enter this Verification Code you see in the box into the security code field. If you have trouble reading the code, click on REFRESH to re-generate it.'; ?>
            <div style="display:inline-block; padding-right:5px; vertical-align:top;"><input name="vercode" id="vercode" class="validate[required]" type="text" maxlength="10" placeholder="Code" style="width:150px;" tabindex="<?=$tabindex++?>" /><span class="required no_pos">*</span></div>
            <div style="display:inline-block; padding:0 5px; vertical-align:top;"><img src='<?=DOC_ROOT?>secure-captcha' id='secure_image' border='0' class="round_borders" style="" /></div>
            <div style="display:inline-block; padding:0 5px 0 0; vertical-align:middle;"><a href="javascript:void(0)" style="font-size:12px; " onclick="document.getElementById('secure_image').src=document.getElementById('secure_image').src+'?<?php echo time(); ?>';">Refresh</a></div>
            <div style="display:inline-block; vertical-align:middle;"><img src='<?=DOC_ROOT?>assets/images/tip2.gif' border='0' class="toolIT" title='<?php echo $title; ?>' style="cursor:help;" /></div>
        </div>
        <div style="clear: both;"></div>

        <br />
        <div style="display: inline-block;">
            <input type="submit" class="blue_btn" value="SUBMIT" style="width:120px;" />
        </div>
        <div style="clear: both;"></div>
        <br />

        <hr style="width:68%; display:inline-block;" /><br />
        <div style="clear: both;"></div>
        <br />

        <?php if(!empty($category_msg2)) { ?><div><?php echo $category_msg2; ?></div><?php } ?>

    </form>
    <br /><br />
    </div>
    </div>

    <?php } //else success..
    else if(!empty($fv_msg) || !empty($fv_errors))
    {
        ?>
        <br />
        <div style="text-align:center;">
        <div style="display:inline-block;"><img src="<?=SITE_URL?>assets/images/err.jpg" style="max-width:150px;" /></div>
        </div>
        <?php
    }
}
else if(!empty($fv_msg) || !empty($fv_errors))
{
    ?>
    <br />
    <div style="text-align:center;">
    <div style="display:inline-block;"><img src="<?=SITE_URL?>assets/images/err.jpg" style="max-width:150px;" /></div>
    </div>
    <?php
} ?>
</div>
</div>
</div>
</div>


<?php
include_once("includes/footer.php");
?>