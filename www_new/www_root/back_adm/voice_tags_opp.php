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

$ignore = array_flip(array('vt_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$vt_id = (int) getgpcvar("vt_id", "G");

$back_page = "voice_tags.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['tag']))
{
    $vt_id = (int) getgpcvar("vt_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['tag']],
    'lengthMin' => [['tag', 5]],
    'lengthMax' => [['tag', 100]],
    'slug' => [['tag']],
    ];

    $form_v->labels(array(
    'tag' => 'Voice Tag',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    #/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT tag FROM voice_tags WHERE tag='{$_POST['tag']}' and id!='{$vt_id}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Voice Tag is already used, please try a different one!');
        }
    }


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        if($vt_id>0)  //Edit Mode
        {
            #/ update
    		$sql_tb1 = "UPDATE voice_tags SET
            tag='{$_POST['tag']}'
            WHERE id='{$vt_id}'";
    		mysql_exec($sql_tb1, 'save');


            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Voice Tag has been successfully Updated');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&vt_id={$vt_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            #/ insert
            $sql_tb1 = "INSERT INTO voice_tags (tag, added_on)
        	VALUES('{$_POST['tag']}', NOW())";
            mysql_exec($sql_tb1, 'save');
            $vt_id = (int)@mysql_insert_id();

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Voice Tag has been successfully Added');
            redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&vt_id={$vt_id}", true);

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
if (($vt_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT *
    FROM voice_tags vc
    WHERE id='%d'", $vt_id);

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
$pg_title = "Voice Tags";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($vt_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('tag').value=='')
    {
        err += 'Please enter the Voice Tag!\n';
    }
    else if(document.getElementById('tag').value.search(/^[a-z0-9\-_]{1,}$/i)<0)
    {
        err += 'The Voice Tag can only contain Alphanumeric values (with Dash or Underscore as separators)!\n';
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

<?php if($vt_id){ ?>
<input type="hidden" name="vt_id" id="vt_id" value="<?php echo $vt_id; ?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>TAG INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:140px; float:left;">Voice Tag:</div>
        <div style="float:left;"><input type="text" id="tag" name="tag" maxlength="100" value="<?=format_str(@$empt['tag'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
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