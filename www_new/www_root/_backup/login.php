<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}home';</script>";
    exit;
}
////////////////////##--

/////////////////////////////////////////////////////////////////////

#/ Process Post
if(isset($_POST['email_add']))
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
    'required' => [['email_add'], ['pass_w']],
    'lengthMax' => [['email_add', 150], ['pass_w', 20]],
    'email' => [['email_add']],
    ];

    $form_v->labels(array(
    'email_add' => 'Email Address',
    'pass_w' => 'Password',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    ##/ Find User Info
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        include_once('../includes/func_enc.php');
        $pass_w = @md5_encrypt($_POST['pass_w']);

        #/ Match User
        $sql_1 ="SELECT *, US.id as user_id
        FROM users US
        LEFT JOIN user_info UI ON US.id = UI.user_id
        WHERE email_add='{$_POST['email_add']}' AND pass_w='{$pass_w}'";
        //die($sql_1);
        $chk_usr = @mysql_exec($sql_1, 'single');

        if(empty($chk_usr) || !is_array($chk_usr))
        {
            $fv_errors[] = array("Unable to <b>Authenticate</b> your given info! Please try again.<br />- If you dont have an Account setup here yet, please <b>Register</b> for an Account instead.");
        }
        else
        {
            #/ Check if account is not activated
            if($chk_usr['account_activated'] == 0)
            {
                $fv_errors[] = array("Your Account has <b>NOT been ACTIVATED</b> yet!<br />- Please follow the <b>Account Activation email</b> sent to you at the time of Signup in order to activate your Account first.");
                $fv_errors[] = array("If you have lost that email, please <a href=\"{$consts['DOC_ROOT']}account-confirm/resend\" style='font-weight:bold; color:red'>Click Here</a> to <b>RESEND</b> the Activation Email.");
                $show_form = false;
                $_SESSION['resend_chk'] = true; //allow Resend Form to be visible
            }

            #/ Check if account is blocked
            if($chk_usr['is_blocked'] == 1)
            {
                $fv_errors[] = array("Your Account has <b>BLOCKED</b> by the Admin! Consequently, you will NOT be able to Access your Account.");
                $show_form = false;
            }
        }
    }
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $user_info = format_str($chk_usr);
        if( is_array($user_info) && array_key_exists('email_add', $user_info) )
        {
            //die('x');
            #/ Setup Sessions
            $_SESSION['CUSA_Main_usr_id'] = (int)$user_info['user_id'];
            $_SESSION['CUSA_Main_usr_info'] = $user_info;
            $_SESSION['CUSA_Main_usr_info']['pass_w'] = '';//hide from session

            $_SESSION['LAST_CUSA_Main_ACTIVITY'] = time();
            reset_attempt_counts();

            if($user_info['screen_name']=='')
            {
                $_SESSION["CUSA_MSG_GLOBAL"] = array(true, 'You have not setup your <b>Screen Name</b> yet. Please complete your <b>Profile Info</b> from the Profile edit page.');
                //redirect_me('update-user-info');
                redirect_me('ecosystem/');
            }
            else
            {
                #/ take to Last Visited Page - or home page
                $last_visited_seo = @$_SESSION['last_visited_seo'];
                unset($_SESSION['last_visited_seo']);

                if(!empty($last_visited_seo))
                redirect_me($last_visited_seo);
                else{
                redirect_me('ecosystem/');
                }
            }
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
/////////////////////////////////////////////////////////////////////

$category_msg = "Please use the form below to <b>Signin</b> to your Account.";


#/ Fill pg_meta
$pg_meta = array(
    'page_title'=> "Signin to User Account",
);
$page_heading = "Signin to your User Account at collaborateUSA.com";

$load_validation = true;
include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/form.css" />
<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/login.css" />

<script>
$(document).ready(function() {
    $("#form_login").validationEngine({
    promptPosition: loc, //"topRight"
    });
});
</script>


<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main bdy_x x1" style="padding-top:30px;">
    <h3 class="red_h3"><?=($pg_meta['page_title'])?></h3><br class="dsk_br" />

    <div class="fr_grd">
    <?php if(!empty($category_msg)) { ?><div class="content"><?php echo $category_msg; ?></div><br class="dsk_br" /><?php } ?><br />

    <div class="contact-main fields-big">
    <form id="form_login" name="form_login" action="" method="POST" <?=AUTO_COMPLETE?>>

        <input type="text" name="email_add" id="email_add" class="validate[required, custom[email]]"
        value="" placeholder="Email Address" maxlength="150" style="width:88%;" tabindex="1"
        /><span class="required">*</span>
        <br />

        <input type="password" name="pass_w" id="pass_w" class="validate[required, minSize[6]]" value="" autocomplete="off"
        placeholder="Password" maxlength="20" style="width:88%;" tabindex="2"
        /><span class="required">*</span>
        <br />


        <br class="dsk_br" />
        <div style="text-aling:left;">
            <div style="argin-right:7px; display:inline-block;"><input type="submit" class="blue_btn" value="SIGNIN" /></div>
            <div style="margin-top:8px; display:inline-block; vertical-align:top; font-size:14px; font-style:italic;">&nbsp;<a href="<?=SITE_URL?>recover-access">| Forgot your Password? Click here ..</a></div>
        </div>

    </form>
    </div>
    </div>

</div>


<div class="mid_bdy body-main bdy_x x2" style="padding-top:30px; padding-left:30px;">
    <h3 class="red_h3">New User? Signup for an Account</h3><br class="dsk_br" />

    <div class="contact-main fields-big">

        <div class="content">
        Dont have an account? You are highly encouraged to <b>signup for a Membership</b> at collaborateUSA.com.<br /><br />
        Your membership will allow you to Participate (<b>Basic</b>) and Earn (<b>Share</b>) with other Members.</div>

        <br /><br class="dsk_br" />

        <div class="contact-main fields-big">
        <form id="form_reg" name="form_reg" action="<?=SITE_URL?>signup" method="POST" <?=AUTO_COMPLETE?>>

            <input type="text" name="email_add" id="email_add" class=""
            value="" placeholder="Email Address" maxlength="150" style="width:88%;"
            /><span class="required">*</span>
            <br />


            <div style="display: inline-block;">
                <input type="submit" class="blue_btn" value="JOIN" />
            </div>

        </form>
        </div>

    </div>

</div>
</div>
</div>
</div>

<?php
include_once("includes/footer.php");
?>