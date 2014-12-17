<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

#/ Setup variables & check empty
$my_user_id = (int)@$_SESSION["cusa_admin_usr_id"];
if($my_user_id<=0){exit;}

$_GET['id'] = $my_user_id;
/////////////////////////////////////////////////////////////////

if(isset($_POST['first_name']))
{

    #/ Check Attempts
    include_once('../../includes/check_attempts.php');
    #/*
    if(check_attempts(3, 'CUSA_ADMIN_MSG_GLOBAL')==false){
    update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}logout", true);
    }
    #*/


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);


    if(array_key_exists('update_acc_info', $_POST) && ($_POST['update_acc_info']=='1')){
    $rules = [
    'required' => [['first_name'], ['last_name'], ['current_password'], ['new_pass'], ['vercode']],
    'lengthMax' => [['first_name', 60], ['last_name', 60], ['current_password', 20], ['new_pass', 20]],
    'lengthMin' => [['current_password', 7], ['new_pass', 7]],
    //'equals' => [['current_password', 'new_pass']],
    ];

    } else {

    $rules = [
    'required' => [['first_name'], ['last_name']],
    'lengthMax' => [['first_name', 60], ['last_name', 60]],
    ];
    }
    $form_v->rules($rules);

    $form_v->labels(array(
    'new_pass' => 'New Password',
    ));


    $form_v->validate();
    $fv_errors = $form_v->errors();


    #/ verify verification code
    if(array_key_exists('update_acc_info', $_POST) && ($_POST['update_acc_info']=='1'))
    {
        #// Check Captcha Code
        if( (empty($_SESSION['cap_code'])) || (empty($_POST['vercode'])) || ($_SESSION['cap_code']!=$_POST['vercode']) )
        {
            $fv_errors[] = array('The Verification Code you entered does not match the one given in the image! Please try again.');
        }
    }//end if update_acc_info..

    //var_dump("<pre>", $_POST, $fv_errors); //die();
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {

        if(array_key_exists('update_acc_info', $_POST) && ($_POST['update_acc_info']=='1'))
        {
            include_once('../../includes/func_enc.php');
            $pass = md5_encrypt($_POST['current_password']);

            #/ Current Password check in db
        	$chk_pwd = mysql_exec("SELECT * FROM admin_users WHERE pass_w='{$pass}' AND id='{$my_user_id}'", 'single');
            if(empty($chk_pwd))
            {
        		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, 'The Current Password you entered is Incorrect! Please try again.');
        		update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}my_settings_opp.php", true);
        	}


            if(!empty($chk_pwd) && ($my_user_id>0))
            {
                #/ User New Password
        		$new_pwd = $_POST['new_pass'];
        		$new_password = md5_encrypt($new_pwd);


                //$_POST_sv = format_str($_POST, 0, false, true);

                #/ Updating Data
        		$sql = "UPDATE admin_users SET first_name='{$_POST['first_name']}', last_name='{$_POST['last_name']}', pass_w='{$new_password}'
                WHERE id='{$my_user_id}' AND pass_w='{$pass}'";
        		mysql_query($sql);

                $_SESSION["adm_usr_info"]['first_name'] = $_POST['first_name'];
                $_SESSION["adm_usr_info"]['last_name'] = $_POST['last_name'];

                $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Admin data successfully Updated.');
                reset_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}my_settings_opp.php", true);

            }//end else..

        }//end if update password....
        else
        {
            #/ Updating Data
    		$sql = "UPDATE admin_users SET first_name='{$_POST['first_name']}', last_name='{$_POST['last_name']}'
            WHERE id='{$my_user_id}'";
    		mysql_query($sql);

            $_SESSION["adm_usr_info"]['first_name'] = $_POST['first_name'];
            $_SESSION["adm_usr_info"]['last_name'] = $_POST['last_name'];

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Admin data successfully Updated.');
            reset_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}my_settings_opp.php", true);
        }

    }//end if errors...
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);
        //var_dump($fv_msg); die();

        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, $fv_msg);
        update_attempt_counts();
    }

}////end if post.................................
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


#### Get record if EDIT Mode
$empt = array();
$id = (int) getgpcvar("id", "G");
if ($id)
{
    $query  = sprintf("SELECT * FROM admin_users WHERE id='%d'", $id);
	$token  = mysql_query($query, $cn1); // or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
		redirect_me("{$consts['DOC_ROOT_ADMIN']}home", true);
	}
}

if(isset($_POST['first_name']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////

$pg_title = "Admin Settings";
include_once("includes/header.php");
?>

<h1>Edit My Account Info</h1><br />

<!-- //////////////////// CSS & JS -->
<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('first_name').value=='')
    {
        err += 'Please enter the First name!\n';
    }

    if(document.getElementById('last_name').value=='')
    {
        err += 'Please enter the Last name!\n';
    }

    if(document.getElementById('update_acc_info').checked)
    {
        if(document.getElementById('current_password').value=='')
        {
            err += 'Please enter the Current Password!\n';
        }

        if(document.getElementById('new_pass').value=='')
        {
            err += 'Please enter the New Password!\n';
        }

        if(document.getElementById('vercode').value=='')
        {
            err += 'Please enter the Security Code!\n';
        }
    }

    if(err!='')
    {
        alert("Please clear the following ERROR(s):\n\n"+err);
        return false;
    }
    else
    {
        return true;
    }

    return false;
}//end func....


<?php if(array_key_exists('update_acc_info', $_POST) && ($_POST['update_acc_info']=='1')){ ?>
$(document).ready(function(){
    $('#update_acc_info').click();
});
<?php } ?>
</script>
<!-- //////////////////// -->


<form action="" method="post" name="f2" id="f2" autocomplete="off" onsubmit="return check_this();">

<? if($id){ ?>
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
<? } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>PERSONAL INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;">
        <div style="width:130px; float:left;">First Name:</div>
        <div style="float:left;"><input type="text" id="first_name" name="first_name" maxlength="60" value="<?=format_str(@$empt['first_name'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Last Name:</div>
        <div style="float:left;"><input type="text" id="last_name" name="last_name" maxlength="60" value="<?=format_str(@$empt['last_name'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>LOGIN INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;">
        <?php /*<div style="width:130px; float:left;">Email/Login:</div>
        <div style="float:left;"><input type="text" id="Email" name="Email" autocomplete="off" disabled="" maxlength="20" value="<?=format_str(@$empt['email_add'])?>" style="width:250px; border:1px solid #000261;" />
        </div>
        <div style="clear:both; height:5px;"></div>*/?>

        <div style="width:130px; float:left;">Update Login Info?</div>
        <div style="float:left;"><input type="checkbox" name="update_acc_info" id="update_acc_info" value="1" onclick="toggle_div(this, 'acc_info_div');" />
        </div>

        <div id="acc_info_div" style="display: none;">
            <div style="clear:both; height:15px;"></div>

            <div style="width:130px; float:left;">Current Password:</div>
            <div style="float:left;"><input type="password" id="current_password" name="current_password" autocomplete="off" maxlength="20" value="" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
            </div>

            <div style="clear:both; height:10px;"></div>


            <div style="width:130px; float:left;">New Password:</div>
            <div style="float:left;"><input type="password" id="new_pass" name="new_pass" autocomplete="off" maxlength="20" value="" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
            </div>

            <div style="clear:both; height:25px;"></div>

            <div style="width:130px; float:left;">Security Code:</div>
            <div>
                    <?php $title = 'Please enter this Verification Code you see in the box into the security code field. If you have trouble reading the code, click on REFRESH to re-generate it.'; ?>
                    <div style="float:left; padding-right:10px;"><input name="vercode" id="vercode" type="text" maxlength="10" placeholder="security code" style="width:90px; border:1px solid #000261; height:16px;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
                    <div style="float:left; padding:0 5px;"><img src='<?=DOC_ROOT?>secure-captcha' id='secure_image' border='0' height='20' width='67' style="height:20px; width:67px;" /></div>
                    <div style="float:left; padding:0 7px 0 3px;"><a href="javascript:void(0)" style="margin-top:4px;" onclick="document.getElementById('secure_image').src=document.getElementById('secure_image').src+'?<?php echo time(); ?>';">Refresh</a></div>
                    <div style="float:left; padding:1px 7px 0 0;"><img src='<?=DOC_ROOT?>assets/images/tip2.gif' border='0' class="toolIT" title='<?php echo $title; ?>' style="cursor:help;" /></div>
                    <div style="clear: both;">&nbsp;</div>
    		</div>
        </div>


        <div style="clear:both;"></div>
    </td>
    </tr>



    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;">
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='home';" style="width:120px;">
		</td>
	</tr>
    </table>

</form>

<?php
include_once("includes/footer.php");
?>