<?php
require_once('../../includes/config.php');

////////////////////////////////////////////////////////////////////////
////////////////////////////## Check Login
if(isset($_SESSION['cusa_admin_usr_id']) && ($_SESSION['cusa_admin_usr_id']>0))
{
    @header("location: {$consts['DOC_ROOT_AUTOADMIN']}home");
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT_AUTOADMIN']}home';</script>";
    exit;
}

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

////////////////////////////////////////////////////////////////////////

if(isset($_POST['email_add']))
{
    #/ Check Attempts
    include_once('../../includes/check_attempts.php');
    #/*
    if(check_attempts(3, 'CUSA_ADMIN_MSG_GLOBAL')==false){
    update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}login", true);
    }
    #*/


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['email_add']],
    'lengthMax' => [['email_add', 100]],
    'email' => [['email_add']],
    ];

    $form_v->labels(array(
    'email_add' => 'Login ID',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();


    #// Check Captcha Code
    if( (empty($_SESSION['cap_code'])) || (empty($_POST['vercode'])) || ($_SESSION['cap_code']!=$_POST['vercode']) )
    {
        $fv_errors[] = array('The Verification Code you entered does not match the one given in the image! Please try again.');
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        ##/ Check Credentials
        include_once('../../includes/func_enc.php');
        $email_add = mysql_real_escape_string($_POST['email_add']);

        $sql_ex = "SELECT * FROM admin_users WHERE email_add='{$email_add}'"; //AND is_active='1'
        $chk_email_add = mysql_exec($sql_ex, 'single');
        //var_dump($chk_email_add); die();

        #/ Check If Account Exists
        if(empty($chk_email_add))
        {
            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, '<strong>INVALID Login Credentials.</strong> &nbsp;Please try again or contact Administrator.');
            update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}recover_password.php", true);
        }
        else
        {
            if($chk_email_add["is_active"]!='1')
            {
                $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, '<strong>Your Account is BLOCKED.</strong> &nbsp;Please contact Administrator.');
                update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}recover_password.php", true);
            }
            else
            {
                #/ Process recovery
                $user_prof = format_str($chk_email_add);

                $pass = md5_decrypt($user_prof['pass_w']);
                $site_url = SITE_URL.'back_adm/';

                include_once('../../includes/send_mail.php');
        		$heading = "CUSA Admin - Password Recovery";

        		$body_in = "";
        		$body_in .= "Dear <b>{$user_prof['first_name']}</b>,<br /><br />";
        		$body_in .= "Your password has been recovered. Please use the following info to login to your account:<br /><br />";
        		$body_in .= "Password:&nbsp; {$pass}<br /><br />";
        		$body_in .= "You can use the following link to go to the Login page directly:<br />";
        		$body_in .= "<a href='{$site_url}login' target='_blank' style='color:#2CA1F4; text-decoration:none;'>{$site_url}login</a><br />";
        		$body_in .= "<br /><b>IMPORTANT</b>: ";
                $body_in .= "Please update your Password after you Login.";
                $body_in .= "";
                //echo $body_in; die();

                $to = $user_prof['email_add'];
        		$subject = "Password Recovery from CUSA Admin";
        		send_mail($to, $subject, $heading, $body_in);

                $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'Your Login Info has been sent to your Email Address. Please check your Email.');
                reset_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}login", true);
                exit;

            }//end else......

        }//end if email add exists....

    }
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);
        //var_dump($fv_msg); die();

        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, $fv_msg);
        update_attempt_counts();
    }

}//end if post......
////////////////////////////////////////////////////////////////////////

$pg_title = "Recover Password";
include_once("includes/header.php");
?>

<script type="text/javascript">
function validate_f1()
{
    var err = '';

    if(document.getElementById('email_add').value=='')
    {
        err += "Your Login / Email Address cannot be empty!\n";
    }

    if(document.getElementById('vercode').value=='')
    {
        err += "The Security Code cannot be empty!\n";
    }

    if(err!='')
    {
        alert("Please clear the following ERROR(s):\n\n"+err);
        return false;
    }
    else
    {
       document.getElementById('f1').submit();
       return true;
	}

    return false;
}//end func....
</script>

<br />
<div style="text-align:center;">
<div style="text-align:left; display:inline-block;">
    <strong>Please enter your details below</strong><br /><br />

    <form action="" method="post" name="f1" id="f1" autocomplete="off" onsubmit="return validate_f1();">
    	<table border="0" cellpadding="0" cellspacing="0" class="datagrid">
        <tr>
        <th colspan="2">Login Info</th>
        </tr>

        <tr>
        <td>Login ID</td>
        <td><input type="text" name="email_add" id="email_add" autocomplete="off" maxlength="100" style="width:237px;" />
        <span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
        </td>
        </tr>

        <tr>
        <td>Security Code&nbsp;&nbsp;</td>
        <td>
            <div style="">
            <?php $title = 'Please enter this Verification Code you see in the box into the security code field. If you have trouble reading the code, click on REFRESH to re-generate it.'; ?>
            <div style="float:left; padding-right:10px;"><input name="vercode" id="vercode" type="text" maxlength="10" placeholder="security code" style="width:90px;" /><span style="color:#53A9E9;">&nbsp;&nbsp;*</span></div>
            <div style="float:left; padding:0 5px;"><img src='<?=DOC_ROOT?>secure-captcha' id='secure_image' border='0' height='20' width='67' style="height:20px; width:67px;" /></div>
            <div style="float:left; padding:2px 7px 0 3px;"><a href="javascript:void(0)" style="margin-top:4px;" onclick="document.getElementById('secure_image').src=document.getElementById('secure_image').src+'?<?php echo time(); ?>';">Refresh</a></div>
            <div style="float:left;"><img src='<?=DOC_ROOT?>assets/images/tip2.gif' border='0' class="toolIT" title='<?php echo $title; ?>' style="cursor:help;" /></div>
            </div>
        </td>
        </tr>

        <tr>
        <td colspan="2" align="right"><input type="submit" class="button" value="Submit" /></td>
        </tr>
    	</table>
    </form>
    <br />

    <a href="login.php">Back to login ..</a>

</div>
</div>

<?php
include_once("includes/footer.php");
?>