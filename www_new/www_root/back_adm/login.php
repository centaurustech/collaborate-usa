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
    if(check_attempts(10, 'CUSA_ADMIN_MSG_GLOBAL')==false){
    update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}login", true);
    }
    #*/


    ##/ Check Credentials
    include_once('../../includes/func_enc.php');
    $email_add = mysql_real_escape_string($_POST['email_add']);
    $pass = md5_encrypt($_POST['pass_w']);

    $sql_ex = "SELECT * FROM admin_users WHERE email_add='{$email_add}' AND pass_w='{$pass}'"; //AND is_active='1'
    $chk_email_add = mysql_exec($sql_ex, 'single');
    //var_dump($sql_ex, $chk_email_add); die();

    #/ Check If Account Exists
    if(empty($chk_email_add))
    {
        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, '<strong>INVALID Login Credentials.</strong> &nbsp;Please try again or contact Administrator.');
        update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}login", true);
    }
    else
    {
        if($chk_email_add["is_active"]!='1')
        {
            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, '<strong>Your Account is BLOCKED.</strong> &nbsp;Please contact Administrator.');
            update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}login", true);
        }
        else
        {
            #/ Process Login
            $user_prof = format_str($chk_email_add);
            $_SESSION["cusa_admin_usr_id"] = $chk_email_add["id"];
            $_SESSION["adm_usr_info"] = $user_prof;
            $_SESSION['LAST_CUSA_Admin_ACTIVITY'] = time();

            #/ Set Permissions
            include_once("../../includes/admin/permission_manager.php");
            $adm_perm = array_keys(@cb89(@get_admin_user_perms($chk_email_add["id"], 'admin_section_id'), 'admin_section_id'));
            $_SESSION['cusa_adm_perm'] = $adm_perm;

            //var_dump("<pre>", $_SESSION); die();
            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'Welcome to the CUSA Administration Section.');
            reset_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}home", true);
            exit;

        }//end else......

    }//end if email add exists....

}//end if post......
////////////////////////////////////////////////////////////////////////

$pg_title = "Admin Login";
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

    if(document.getElementById('pass_w').value=='')
    {
        err += "Your Password cannot be empty!\n";
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
    <strong>Please enter your Login details below</strong><br /><br />

    <form action="" method="post" name="f1" id="f1" autocomplete="off" onsubmit="return validate_f1();">
    	<table align="center" border="0" cellpadding="0" cellspacing="0" class="datagrid">
        <tr>
        <th colspan="2">Login</th>
        </tr>

        <tr>
        <td>Login ID</td>
        <td><input type="text" name="email_add" id="email_add" autocomplete="off" maxlength="100" style="width:230px;" />
        <span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
        </td>
        </tr>

        <tr>
        <td>Password&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td><input type="password" name="pass_w" id="pass_w" autocomplete="off" maxlength="100" style="width:230px;" />
        <span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
        </td>
        </tr>

        <tr>
        <td colspan="2" align="right"><input type="submit" class="button" value="Login" /></td>
        </tr>
    	</table>
    </form>
    <br />

    <a href="recover_password.php">Forgot your Password? Click here to recover..</a>

</div>
</div>


<?php
include_once("includes/footer.php");
?>