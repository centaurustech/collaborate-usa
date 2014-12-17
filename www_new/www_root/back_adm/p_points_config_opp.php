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

$back_page = "p_points_config.php";
$cur_page = cur_page();

if($conf_id<=0){
redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true); //prevent add
}
/////////////////////////////////////////////////////////////////

if(isset($_POST['action_title']))
{
    $conf_id = (int) getgpcvar("conf_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['action_title'], ['points'], ['limits_per_day']],
    'lengthMax' => [['action_title', 70]],
    'integer' => [['points'], ['limits_per_day'], ['percentage_points']],
    //'min' => [['points', 1]]
    ];

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-

    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $_POST['is_active'] = (int)@$_POST['is_active'];
        $_POST['points'] = (int)$_POST['points'];
        $_POST['percentage_points'] = (int)$_POST['percentage_points'];
        $_POST['limits_per_day'] = (int)$_POST['limits_per_day'];

        if($conf_id>0)  //Edit Mode
        {
            ###/ Updating Database
            #/ system_config
    		$sql_tb1 = "UPDATE patronage_points_config SET category='{$_POST['category']}',
            action_title='{$_POST['action_title']}', points='{$_POST['points']}', percentage_points='{$_POST['percentage_points']}',
            limits_per_day='{$_POST['limits_per_day']}', is_active='{$_POST['is_active']}'
            WHERE id='{$conf_id}'";
    		mysql_exec($sql_tb1, 'save');
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Patronage Points data has been successfully Updated');
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
    $query  = sprintf("SELECT * FROM patronage_points_config WHERE id='%d'", $conf_id);
	$token  = mysql_query($query, $cn1); // or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}
}

if(isset($_POST['action_title']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////

$pg_action_title = "Patronage Points Config";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_action_title?> &raquo; <?php echo ($conf_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('action_title').value=='')
    {
        err += 'Please enter the Action Title!\n';
    }

    if(document.getElementById('points').value=='')
    {
        err += 'Please enter the Points!\n';
    }
    else if(document.getElementById('points').value.search(/^[0-9]{0,}$/)<0)
    {
        err += 'Please enter the Points in Numebers / Decimals only!\n';
    }

    if(document.getElementById('percentage_points').value=='')
    {
        //err += 'Please enter the Points!\n';
    }
    else if(document.getElementById('percentage_points').value.search(/^[0-9]{0,}$/)<0)
    {
        err += 'Please enter the Percentage in numeric only!\n';
    }

    if(document.getElementById('limits_per_day').value=='')
    {
        err += 'Please enter the Limits per Day!\n';
    }
    else if(document.getElementById('limits_per_day').value.search(/^[0-9]{0,}$/)<0)
    {
        err += 'Please enter the Limits per Day in Numebers / Decimals only!\n';
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
	<th>PATRONAGE POINTS CONFIG</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />
        <div style="width:130px; float:left;">Action Title:</div>
        <div style="float:left;"><input type="text" id="action_title" name="action_title" maxlength="100" value="<?=format_str(@$empt['action_title'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        <span class="submsg">&nbsp;&nbsp;for reference only</span>
        </div>

        <div style="clear:both; height:20px;"></div>

        <div style="width:130px; float:left;">Key:</div>
        <div style="float:left;"><input type="text" id="action_key" name="action_key" maxlength="50" disabled="" value="<?=format_str(@$empt['action_key'])?>" style="width:150px; border:1px solid #000261;" />
        <span class="submsg">&nbsp;&nbsp;for code</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:130px; float:left;">Category:</div>
        <div style="float:left;">
            <?php
            $sql_cats = "SELECT DISTINCT category FROM patronage_points_config ORDER BY category";
            $categories = mysql_exec($sql_cats);
            ?>
            <select id="category" name="category" style="width:160px; border:1px solid #000261;">
            <?php if(@count($categories)>0)foreach($categories as $cat_v){
            echo "<option value=\"{$cat_v['category']}\">{$cat_v['category']}</option>";
            }
            ?>
            </select>
            <?php if(isset($empt['category'])) echo "<script>document.getElementById('category').value='{$empt['category']}';</script>"; ?>
            <span class="submsg">&nbsp;&nbsp;will be shown as part of User Notification</span>
        </div>

        <div style="clear:both; height:15px;"></div>
        <div style="width:130px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:15px;"></div>


        <div style="width:130px; float:left;">Direct Points:</div>
        <div style="float:left;"><input type="text" id="points" name="points" maxlength="5" value="<?=format_str(@$empt['points'])?>" style="width:40px; border:1px solid #000261;" />
        <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        <span class="submsg">&nbsp;(numbers only)</span>
        <span class="submsg">&nbsp;Patronage Points for this Action.</span>
        </div>

        <div style="clear:both; height:10px;"></div>


        <div style="width:130px; float:left;">Percentage Points:</div>
        <div style="float:left;"><input type="text" id="percentage_points" name="percentage_points" maxlength="2" value="<?=format_str(@$empt['percentage_points'])?>" style="width:40px; border:1px solid #000261;" /> %
        <span class="submsg">&nbsp;(numeric only)</span>
        <span class="submsg">&nbsp;Percentage based Patronage Points for this Action.</span>
        <span class="submsg">&nbsp;Applied only if Direct Points is set to '0' in the above field.</span>
        </div>

        <div style="clear:both; height:20px;"></div>

        <div style="width:130px; float:left;">Limits Per Day:</div>
        <div style="float:left;"><input type="text" id="limits_per_day" name="limits_per_day" maxlength="3" value="<?=format_str(@$empt['limits_per_day'])?>" style="width:40px; border:1px solid #000261;" />
        <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        <span class="submsg">&nbsp;(numbers only)</span>
        <span class="submsg">&nbsp;Usage Limits per Day per Member</span>
        </div>

        <div style="clear:both; height:20px;"></div>


        <div style="width:130px; float:left;">Is Active?</div>
        <div style="float:left;"><input type="checkbox" name="is_active" id="is_active" value="1" <?php if(@$empt['is_active']!='0') echo "checked='checked'"; ?> />
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

<br /><br />
<div style="float:left; font-style:italic;">This Section is used to set <b>Patronage Points</b> allocation configurations.<br /><br />
[IMP]<br />
1. The "<b>Direct Points</b>" will take precedence over "<b>Percentage Points</b>", hence they must be '0' for Percentage to take effect.<br />
2. The "<b>Percentage Points</b>" will give Points against the Percentage of Sale Amount (like in BUY, SHARE, etc actions). Hence, it has no application for non-sale actions.
</div>

<?php
include_once("includes/footer.php");
?>