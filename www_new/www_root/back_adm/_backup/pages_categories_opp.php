<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 2; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");
//include_once("../../includes/admin/func_tree.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('pc_id')); //for back to LIST

$param2 = http_build_query(array_diff_key($_GET, $ignore));
if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$pc_id = (int) getgpcvar("pc_id", "G");

$back_page = "pages_categories.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['title']))
{
    $pc_id = (int) getgpcvar("pc_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['title']],
    'lengthMax' => [['title', 60]],
    ];


    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();

    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-

    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $is_active = (int)@$_POST['is_active'];

        if($pc_id>0)  //Edit Mode
        {
            ###/ Updating Database

            #/ page_categories
    		$sql_page_categories = "UPDATE page_categories SET title='{$_POST['title']}',
            is_active='{$is_active}' WHERE id='{$pc_id}'";
    		mysql_exec($sql_page_categories, 'save');
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Category data has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&pc_id={$pc_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            ###/ insert into Database

            #/ page_categories
            $sql_page_categories = "insert into page_categories (title, is_active)
        	values('{$_POST['title']}', '{$is_active}')";
            mysql_exec($sql_page_categories, 'save');
            $pc_id = mysql_insert_id();
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Category data has been successfully Added into the system');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&pc_id={$pc_id}", true);

        }//end Add ..


    }//end if errors...
    else
    {
        $fv_msg = 'Please clear the following Error(s):<br /><br />- '; $fv_msg_ar=array();
        foreach($fv_errors as $fv_k=>$fv_v){$fv_msg_ar = array_merge($fv_msg_ar, $fv_v);}
        $fv_msg.=@implode('<br />- ', $fv_msg_ar);
        //var_dump($fv_msg); die();

        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, $fv_msg);
    }

}////end if post.................................
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


#### Get record if EDIT Mode
$empt = array();
if (($pc_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT * FROM page_categories WHERE id='%d'", $pc_id);
	$token  = mysql_query($query, $cn1); // or die(mysql_error($cn1));
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
    $empt['is_active'] = (int)@$empt['is_active'];
}
///////////////////////////////////////////////////////////////////

$pg_title = "Page Categories";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($pc_id>0)? "Edit ": "Add "; ?> Info</h1></div>
<div style="float:right; margin-top:15px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('title').value=='')
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


<form action="" method="post" name="f2" id="f2" autocomplete="off" onsubmit="return check_this();">

<?php if($pc_id){ ?>
<input type="hidden" name="pc_id" id="pc_id" value="<?php echo $pc_id; ?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>CATEGORY INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:130px; float:left;">Title:</div>
        <div style="float:left;"><input type="text" id="title" name="title" maxlength="60" value="<?=format_str(@$empt['title'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Is Active?</div>
        <div style="float:left;"><input type="checkbox" name="is_active" id="is_active" value="1" <?php if(@$empt['is_active']!='0') echo "checked='checked'"; ?> />
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;" />
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;" />
		</td>
	</tr>
    </table>

</form>

<?php
include_once("includes/footer.php");
?>