<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 4; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('conf_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$conf_id = (int) getgpcvar("conf_id", "G");

$back_page = "system_config.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['title']))
{
    $conf_id = (int) getgpcvar("conf_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['title'], ['c_value']],
    'lengthMax' => [['title', 100], ['c_value', 50]],
    ];

    $form_v->labels(array(
    'title' => 'Title',
    'c_value' => 'Value',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-

    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        if($conf_id>0)  //Edit Mode
        {
            ###/ Updating Database
            #/ system_config
    		$sql_tb1 = "UPDATE system_config SET title='{$_POST['title']}', c_value='{$_POST['c_value']}'
            WHERE id='{$conf_id}'";
    		mysql_exec($sql_tb1, 'save');
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Site data has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&conf_id={$conf_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {

        }//end Add ..


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
$empt = array();
if (($conf_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT * FROM system_config WHERE id='%d'", $conf_id);
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
}
///////////////////////////////////////////////////////////////////

$pg_title = "System Configurations";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($conf_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('title').value=='')
    {
        err += 'Please enter the Title!\n';
    }

    if(document.getElementById('c_value').value=='')
    {
        err += 'Please enter the Value!\n';
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

<?php if($conf_id){ ?>
<input type="hidden" name="conf_id" id="conf_id" value="<?php echo $conf_id; ?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>SYSTEM CONFIG</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:130px; float:left;">Title:</div>
        <div style="float:left;"><input type="text" id="title" name="title" maxlength="100" value="<?=format_str(@$empt['title'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        <span class="submsg">&nbsp;&nbsp;for reference only</span>
        </div>

        <div style="clear:both; height:20px;"></div>

        <div style="width:130px; float:left;">Key:</div>
        <div style="float:left;"><input type="text" id="c_key" name="c_key" maxlength="50" disabled="" value="<?=format_str(@$empt['c_key'])?>" style="width:250px; border:1px solid #000261;" />
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Value:</div>
        <div style="float:left;"><input type="text" id="c_value" name="c_value" maxlength="50" value="<?=format_str(@$empt['c_value'])?>" style="width:100px; border:1px solid #000261;" />
        <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;">&nbsp;&nbsp;
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;">
		</td>
	</tr>
    </table>

</form>

<?php
include_once("includes/footer.php");
?>