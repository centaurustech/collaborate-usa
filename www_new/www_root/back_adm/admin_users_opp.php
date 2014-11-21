<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 1; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");
include_once("../../includes/admin/permission_manager.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('au_id')); //for move within OPERATION page
//$pass = array_flip(array('pageindex', 'orderby', 'orderdi')); //to pass to next page

$param2 = http_build_query(array_diff_key($_GET, $ignore));
//$param2 = http_build_query(array_intersect_key($_GET, $pass));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$au_id = (int) getgpcvar("au_id", "G");

$my_user_id = (int)@$_SESSION["cusa_admin_usr_id"];
if($my_user_id<=0){exit;}
/////////////////////////////////////////////////////////////////

if(isset($_POST['first_name']))
{

    $au_id = (int) getgpcvar("au_id", "P");

    #/ Check Attempts
    include_once('../../includes/check_attempts.php');
    #/*
    if(check_attempts(5, 'CUSA_ADMIN_MSG_GLOBAL')==false){
    update_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}logout", true);
    }
    #*/


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);


    if ( ($au_id<=0) || (array_key_exists('update_acc_info', $_POST) && ($_POST['update_acc_info']=='1')) )
    {
        $rules = [
        'required' => [['first_name'], ['last_name'], ['email_add'], ['new_pass']], //['current_password'],
        'lengthMax' => [['first_name', 60], ['last_name', 60], ['email_add', 100], ['new_pass', 20]], //['current_password', 20],
        'lengthMin' => [['new_pass', 7]], //['current_password', 7],
        'email' => [['email_add']],
        ];
    }
    else
    {
        $rules = [
        'required' => [['first_name'], ['last_name'], ['email_add']],
        'lengthMax' => [['first_name', 60], ['last_name', 60], ['email_add', 100]],
        'email' => [['email_add']],
        ];
    }

    $form_v->labels(array(
    'new_pass' => 'Password',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    ##/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT email_add FROM admin_users WHERE email_add='{$_POST['email_add']}' and id!='{$au_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Email Address / Login ID is already used, please try a different Login Id!');
        }
    }
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $is_active = (int)@$_POST['is_active'];
        $permissions = @$_POST['permissions'];

        if($au_id>0)  //Edit Mode
        {
            #/ encrypt password
            $p_string = '';
            if(array_key_exists('update_acc_info', $_POST) && ($_POST['update_acc_info']=='1'))
            {
                include_once('../../includes/func_enc.php');
                $new_pass = (string)$_POST['new_pass'];
        		$new_password = md5_encrypt($new_pass);
                $p_string = "pass_w='{$new_password}', ";
            }


            ###/ Updating Database

            #/ admin_users
    		$sql_admin_users = "UPDATE admin_users SET first_name='{$_POST['first_name']}', last_name='{$_POST['last_name']}',
            email_add='{$_POST['email_add']}', {$p_string} is_active='{$is_active}'
            WHERE id='{$au_id}'";
    		mysql_exec($sql_admin_users, 'save');


            #/ admin_permissions
            update_permissions($au_id, $permissions);
            #-
            //die(mysql_error());

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Admin User data successfully Updated');
            reset_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}admin_users_opp.php{$param2}&au_id={$au_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {

            ////////////////-------

            #/ encrypt password
            include_once('../../includes/func_enc.php');
            $new_pass = (string)$_POST['new_pass'];
    		$new_password = md5_encrypt($new_pass);

            ###/ Updating Database

            #/ admin_users
            $sql_admin_users = "insert into admin_users
        	(email_add, first_name, last_name, is_active, pass_w, added_on)
        	values('{$_POST['email_add']}', '{$_POST['first_name']}', '{$_POST['last_name']}', '{$is_active}', '{$new_password}', now())";
            mysql_exec($sql_admin_users, 'save');
            $au_id = mysql_insert_id();


            if($au_id>0)
            {
                #/ admin_permissions
                insert_permissions($au_id, $permissions);
            }
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Admin User added successfully into the system.');
            reset_attempt_counts(); redirect_me("{$consts['DOC_ROOT_ADMIN']}admin_users_opp.php{$param2}&au_id={$au_id}", true);

        }//end Add ..


    }//end if errors...
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, $fv_msg);
        update_attempt_counts();
    }

}////end if post.................................
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


#### Get record if EDIT Mode
$empt = array();
if (($au_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT * FROM admin_users WHERE id='%d'", $au_id);
	$token  = mysql_query($query, $cn1); // or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}admin_users.php{$param2}", true);
	}
}

if(isset($_POST['first_name']))
{
    $empt = $_POST;
    $empt['is_active'] = (int)@$empt['is_active'];
}
///////////////////////////////////////////////////////////////////

$pg_title = ($au_id>0)? "Edit Admin User": "Add Admin User";
include_once("includes/header.php");
?>

<div style="float:left;"><h1>Admin Users &raquo; <?=$pg_title?></h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='admin_users.php<?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('first_name').value=='')
    {
        err += 'Please enter the First Name!\n';
    }

    if(document.getElementById('last_name').value=='')
    {
        err += 'Please enter the Last Name!\n';
    }

    if(document.getElementById('email_add').value=='')
    {
        err += 'Please enter the Email Address!\n';
    }

    <?php if($au_id>0) { //edit page ?>
    if(document.getElementById('update_acc_info').checked)
    {
        /*if(document.getElementById('current_password').value=='')
        {
            err += 'Please enter the Current Password!\n';
        }*/

        if(document.getElementById('new_pass').value=='')
        {
            err += 'Please enter the New Password!\n';
        }
    }
    <?php } else { //add page ?>
    if(document.getElementById('new_pass').value=='')
    {
        err += 'Please enter the Password!\n';
    }
    <?php } ?>

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

<?php if($au_id){ ?>
<input type="hidden" name="au_id" id="au_id" value="<?php echo $au_id; ?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>BASIC INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;">
        <div style="width:130px; float:left;">First Name:</div>
        <div style="float:left;"><input type="text" id="first_name" name="first_name" maxlength="60" value="<?=format_str(@$empt['first_name'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Last Name:</div>
        <div style="float:left;"><input type="text" id="last_name" name="last_name" maxlength="60" value="<?=format_str(@$empt['last_name'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
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
        <div style="width:130px; float:left;">Email/LoginId:</div>
        <div style="float:left;"><input type="text" id="email_add" name="email_add" autocomplete="off" maxlength="100" value="<?=format_str(@$empt['email_add'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:10px;"></div>


        <?php if($au_id>0) { //edit page ?>

        <div style="width:130px; float:left;">Update Password?</div>
        <div style="float:left;"><input type="checkbox" name="update_acc_info" id="update_acc_info" value="1" onclick="toggle_div(this, 'acc_info_div');" />
        </div>

        <div id="acc_info_div" style="display: none;">
            <div style="clear:both; height:15px;"></div>

            <?php /*
            <div style="width:130px; float:left;">Current Password:</div>
            <div style="float:left;"><input type="password" id="current_password" name="current_password" autocomplete="off" maxlength="20" value="" style="width:250px; border:1px solid #000261;" /><span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
            </div>

            <div style="clear:both; height:10px;"></div>
            */ ?>

            <div style="width:130px; float:left;">New Password:</div>
            <div style="float:left;"><input type="password" id="new_pass" name="new_pass" autocomplete="off" maxlength="20" value="" style="width:250px; border:1px solid #000261;" /><span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
            </div>

            <div style="clear:both; height:10px;"></div>

        </div>

        <?php } else { //add page ?>

        <div style="width:130px; float:left;">Password:</div>
        <div style="float:left;"><input type="password" id="new_pass" name="new_pass" autocomplete="off" maxlength="20" value="" style="width:250px; border:1px solid #000261;" /><span style="color:#53A9E9;">&nbsp;&nbsp;*</span>
        </div>

        <?php } ?>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Is Active?</div>
        <div style="float:left;"><input type="checkbox" name="is_active" id="is_active" value="1" <?php if(@$empt['is_active']=='1') echo "checked='checked'"; ?> />
        </div>


        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<th>ADMIN PERMISSIONS</th>
	</tr>


    <tr>
    <td valign="middle" style="padding:6px 4px;">

        <div style="width:130px; float:left;">Allowed Sections:</div>
        <div style="float:left;">
        <?php
        $admin_sections = get_admin_sections();

        $admin_user_perms = array();
        if($au_id>0){
        $admin_user_perms = cb89(get_admin_user_perms($au_id), 'admin_section_id');
        //var_dump("<pre>", $admin_user_perms);
        }

        foreach($admin_sections as $admin_sec)
        {
            #/ Prevent self-lockout, and Prevent Super-Admin locout
            $disabled='';
            if((($au_id==$my_user_id) || (@$empt['is_super_admin']=='1')) && ($admin_sec['id']==1)){
            $disabled="disabled=''";
            echo "<input type='hidden' name='permissions[]' value='{$admin_sec['id']}' />";
            }


            echo '<input type="checkbox" name="permissions[]" '.$disabled.' value="'.$admin_sec['id'].'"';
            if(isset($admin_user_perms[$admin_sec['id']])) echo "checked=\"checked\"";
            echo '>';
            echo $admin_sec['label'].'</br>';
        }
        ?>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>



    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;">
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='admin_users.php<?=$param2?>';" style="width:120px;">
		</td>
	</tr>
    </table>

</form>

<?php
include_once("includes/footer.php");
?>