<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 8; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('vc_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$vc_id = (int) getgpcvar("vc_id", "G");

$back_page = "voice_cats.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['category']))
{
    $vc_id = (int) getgpcvar("vc_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['category']],
    'lengthMax' => [['category', 150]],
    ];

    $form_v->labels(array(
    'category' => 'Category Title',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    #/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT category FROM voice_categories WHERE category='{$_POST['category']}' and id!='{$vc_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Category Title is already used, please try a different one!');
        }
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        if($vc_id>0)  //Edit Mode
        {
            #/ update voice_categories
    		$sql_tb1 = "UPDATE voice_categories SET
            category='{$_POST['category']}'
            WHERE id='{$vc_id}'";
    		mysql_exec($sql_tb1, 'save');


            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Voice Category has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&vc_id={$vc_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            #/ insert membership_packages
            $sql_tb1 = "INSERT INTO voice_categories (category)
        	VALUES('{$_POST['category']}')";
            mysql_exec($sql_tb1, 'save');
            $vc_id = (int)@mysql_insert_id();

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Voice Category has been successfully Added');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&vc_id={$vc_id}", true);

        }//end Add....

    }//end if errors...
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);

        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, $fv_msg);
    }

}////end if post.................................
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


#### Get record if EDIT Mode
$empt = $empt_2 = array();
if (($vc_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT *
    FROM voice_categories vc
    WHERE id='%d'", $vc_id);

	$token  = mysql_query($query, $cn1); //or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}
}

if(isset($_POST['title']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////
$pg_title = "Voice Categories";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($vc_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('category').value=='')
    {
        err += 'Please enter the Category Title!\n';
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
</script>
<!-- //////////////////// -->


<form action="" method="post" name="f2" id="f2" onsubmit="return check_this();">

<?php if($vc_id){ ?>
<input type="hidden" name="vc_id" id="vc_id" value="<?php echo $vc_id; ?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>CATEGORY INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:140px; float:left;">Category Title:</div>
        <div style="float:left;"><input type="text" id="category" name="category" maxlength="150" value="<?=format_str(@$empt['category'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

    <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;" />&nbsp;
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;">
		</td>
	</tr>
    </table>

</form>

<?php
include_once("includes/footer.php");
?>