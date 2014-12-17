<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 7; include_once('includes/check_permission.php');

/////////////////////////////////////////////////////////////////////

include_once('../../includes/format_str.php');
include_once('../../includes/func_1.php');
include_once('../../includes/db_lib.php');
include_once("../../includes/admin/functions.php");

$_POST = format_str($_POST);
$_GET = format_str($_GET);

$ignore = array_flip(array('up_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$up_id = (int)getgpcvar("up_id", "G");
$user_select = (int)getgpcvar("user_select", "G");
if($up_id<=0 && $user_select<=0){redirect_me("{$consts['DOC_ROOT_ADMIN']}404", true);} //prevent 'Add'

$read_only = (int)getgpcvar("ro", "G");
//$read_only = 1; //test

$btr = (int)getgpcvar("btr", "G");
$bkr = (string)getgpcvar("bkr", "G");

if($user_select<=0)
$back_page = "user_payments.php";
else{
$back_page = urldecode($bkr);
$param2 = '';
}

$cur_page = cur_page();

/////////////////////////////////////////////////////////////////

if($user_select>0)
if(isset($_POST['user_select']))
{
    $user_select = (int) getgpcvar("user_select", "P");

    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $rules = [
    'required' => [['invoice'], ['amount'], ['paid_on'], ['payment_status'], ['gateway_name']],
    'lengthMax' => [['invoice', 30], ['transaction_id', 150], ['gateway_name', 50], ['gateway_payer_id', 100], ['gateway_msg', 500], ['payment_status', 30]],
    'numeric' => [['amount']]
    ];

    $form_v->labels(array(
    'paid_on' => 'Payment Date',
    'gateway_name' => 'Payment Gateway',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); //die();
    #-


    #/ Check if Email Add exists
    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $chk_user = mysql_exec("SELECT invoice FROM user_payments WHERE invoice='{$_POST['invoice']}'", 'single');
        if(!empty($chk_user))
        {
            $fv_errors[] = array('This Invoice is already used, please try a different one!');
        }
    }

    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $_POST['amount'] = (float)@$_POST['amount'];

        if($up_id>0)  //Edit Mode
        {
            ////////////////-------
        }
        else //Add page
        {
            ////////////////-------

            #/ user_payments
            $sql_users = "INSERT INTO user_payments
        	(user_id, invoice, amount, transaction_id, gateway_name, gateway_payer_id, gateway_msg, payment_status, paid_on)
        	values('{$_POST['user_select']}', '{$_POST['invoice']}', '{$_POST['amount']}', '{$_POST['transaction_id']}', '{$_POST['gateway_name']}', '{$_POST['gateway_payer_id']}', '{$_POST['gateway_msg']}', '{$_POST['payment_status']}', NOW())";
            mysql_exec($sql_users, 'save');
            $up_id = (int)@mysql_insert_id();
            #-

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, "The Payment Data has been Added successfully for the user <b>{$_GET['emd']}</b>");
        }

        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);

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
if (($up_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT up.*, up.id as up_id, u.email_add
    FROM user_payments up
    LEFT JOIN users u ON u.id=up.user_id
    WHERE up.id='%d'", $up_id);

	$token  = mysql_query($query, $cn1);
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}

    $user_id = (int)$empt['user_id'];
}
else if (($user_select) && (empty($empt)) )
{
    $query  = sprintf("SELECT up.*, up.id as up_id, u.email_add
    FROM users u
    LEFT JOIN user_payments up ON u.id=up.user_id
    WHERE u.id='%d'", $user_select);

	$token  = mysql_query($query, $cn1);
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found!");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}

    if (isset($empt['up_id']) && !empty($empt['up_id']))
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Payment Data already Exists for the selected User!");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}

    $user_id = $user_select;
    $email_address = $empt['email_add'];
}

if(isset($_POST['payment_status']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////

if($read_only>0)
$no_header = true;
else
$load_fancy = true;

$pg_title = "Member Payments";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($up_id>0)? "Review ": "Add "; ?> Record</h1></div>
<?php if($read_only<=0) { ?><div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div><?php } ?>
<div style="clear:both; height:15px;">&nbsp;</div>

<?php if($read_only<=0) { ?>

<script>
$(document).ready(function() {
    $(".fbox").fancybox({
	    minWidth    : 550,
	    minHeight   : 5,
		maxWidth	: 950,
		maxHeight	: 600,
		autoSize	: true,
        fitToView	: false,
        openEffect	: 'elastic',
		closeEffect	: 'elastic',

        prevEffect	: 'none',
		nextEffect	: 'none',
        helpers	: {
			buttons	: {}
		}
	});
});
</script>

<?php if($user_select>0) { ?>
<div>[IMP] Please enter the data carefully as you wont be allowed to EDIT.</div><br />

<link rel="stylesheet" media="screen" type="text/css" href="<?=DOC_ROOT?>assets/js/jquery-ui-1.11.2.blue/jquery-ui.min.css" />
<script type="text/javascript" src="<?=DOC_ROOT?>assets/js/jquery-ui-1.11.2.blue/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
	$(function() {
        $("#paid_on").datepicker({
            dateFormat: "yy-mm-dd"
        });
    });
});
function toggle_cat(caller, div_id)
{
    if(caller.value=='other')
    {
        document.getElementById(div_id+'_div').style.display='';
        document.getElementById(div_id).disabled=false;
    }
    else
    {
        document.getElementById(div_id+'_div').style.display='none';
        document.getElementById(div_id).disabled=true;
    }

}//end func...

function check_this()
{
    var err = '';

    if(document.getElementById('invoice').value=='')
    {
        err += 'Please enter the Invoice!\n';
    }


    if(document.getElementById('amount').value=='')
    {
        err += 'Please enter the Amount!\n';
    }
    else if(document.getElementById('amount').value.search(/^[1-9][0-9]{0,}[.]{0,1}[0-9]{0,}$/)<0)
    {
        err += 'Please enter the Amount in numeric only!\n';
    }


    if(document.getElementById('paid_on').value=='')
    {
        err += 'Please set a Payment Date!\n';
    }

    if(document.getElementById('payment_status_sel').value=='-')
    {
        err += 'Please select a Payment Status!\n';
    }
    else if(document.getElementById('payment_status_sel').value=='other')
    {
        if(document.getElementById('payment_status_field').value==''){
        err += 'Please enter a Payment Status!\n';
        }
    }


    if(document.getElementById('gateway_name_sel').value=='-')
    {
        err += 'Please select a Gateway Name!\n';
    }
    else if(document.getElementById('gateway_name_sel').value=='other')
    {
        if(document.getElementById('gateway_name_field').value==''){
        err += 'Please enter a Gateway Name!\n';
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
</script>
<?php } ?>

<?php } ?>

<!-- //////////////////// -->

<?php if($read_only<=0) { ?>
<form action="" method="post" name="f2" id="f2" autocomplete="off" onsubmit="return check_this();">

<?php if($up_id){ ?>
<input type="hidden" name="up_id" id="up_id" value="<?php echo $up_id; ?>" />
<?php } else if($user_select>0) {  ?>
<input type="hidden" name="user_select" id="user_select" value="<?php echo $user_select; ?>" />
<?php } ?>

<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>PAYMENT INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:160px; float:left;">Invoice:</div>
        <?php if($up_id>0){ ?><div style="float:left; font-weight:bold;"><?=format_str(@$empt['invoice'])?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;"><input type="text" id="invoice" name="invoice" autocomplete="off" maxlength="30" value="<?=format_str(@$empt['invoice'])?>" style="width:150px; border:1px solid #000261;" />
        <span style="color:#CC0000;">&nbsp;*</span></div><?php } ?>

        <div style="clear:both; height:20px;"></div>


        <div style="width:160px; float:left;">CUSA User (Email Address):</div>
        <?php if($read_only<=0) { ?>
        <?php if($up_id>0){ ?><div style="float:left;"><a <?php if($user_id>0){?> class="fbox fancybox.ajax" href="<?php echo "{$consts['DOC_ROOT_ADMIN']}users_opp.php?u_id={$user_id}&ro=1"; ?>" <?php } ?> title="<?=format_str(@$empt['email_add'])?>"><?=format_str(@$empt['email_add'])?></a></div><?php } else if($user_select>0) { ?>
        <div style="float:left;"><input type="text" id="email_add" name="email_add" disabled="" autocomplete="off" maxlength="120" value="<?=format_str(@$email_address)?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div><?php } ?>
        <?php }else{ ?><div style="float:left;"><?=format_str(@$empt['email_add'])?></div>
        <?php } ?>


        <div style="clear:both; height:10px;"></div>

        <div style="width:160px; float:left;">Total Amount Received:</div>
        <?php if($up_id>0){ ?><div style="float:left; font-weight:bold;">$ <?=format_str(number_format((float)@$empt["amount"], 2))?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;"><input type="text" id="amount" name="amount" maxlength="3" value="<?=format_str(@$empt['amount'])?>" style="width:30px; border:1px solid #000261;" />
        $ <span style="color:#CC0000;">&nbsp;*</span></div><?php } ?>

        <div style="clear:both; height:20px;"></div>


        <div style="width:160px; float:left;">Payment Date:</div>
        <?php if($up_id>0){ ?><div style="float:left;"><?=format_str(@$empt['paid_on'])?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;"><input type="text" id="paid_on" name="paid_on" maxlength="30" value="<?=format_str(@$empt['paid_on'])?>" style="width:150px; border:1px solid #000261;" />
        <span style="color:#CC0000;">&nbsp;*</span></div><?php } ?>

        <div style="clear:both; height:10px;"></div>


        <div style="width:160px; float:left;">Payment Status:</div>
        <?php if($up_id>0){ ?><div style="float:left;"><?=format_str(@$empt['payment_status'])?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;">
            <?php
            $sql_cats = "SELECT DISTINCT payment_status FROM user_payments ORDER BY payment_status";
            $categories = mysql_exec($sql_cats);
            ?>
            <div id="payment_status_select">
                <select id="payment_status_sel" name="payment_status" onchange="toggle_cat(this, 'payment_status_field');" style="width: 160px;">
                <option value="-">-Please Select-</option>
                <option value="other">{New Value}</option>
                <?php if(@count($categories)>0)foreach($categories as $cat_v){
                echo "<option value=\"{$cat_v['payment_status']}\">{$cat_v['payment_status']}</option>";
                }
                ?>
                </select>
                <span style="color:#CC0000;">&nbsp;*</span>
                <?php if(isset($empt['payment_status'])) echo "<script>document.getElementById('payment_status_sel').value='{$empt['payment_status']}';</script>"; ?>
            </div>

            <div id="payment_status_field_div" style="display:none;"><br /><input type="text" id="payment_status_field" name="payment_status" maxlength="25" value="" style="width:150px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        </div><?php } ?>


        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


    <tr>
	<?php if($user_select<=0) { ?><th>GATEWAY RETURNED INFO</th><?php } else { ?>
    <th>GATEWAY MESSAGE</th>
    <?php } ?>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:160px; float:left;">Payment Gateway:</div>
        <?php if($up_id>0){ ?><div style="float:left;"><?=format_str(@$empt['gateway_name'])?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;">
            <?php
            //var_dump($empt['gateway_name']);
            $sql_cats = "SELECT DISTINCT gateway_name FROM user_payments ORDER BY gateway_name";
            $categories = mysql_exec($sql_cats);
            ?>
            <div id="gateway_name_select">
                <select id="gateway_name_sel" name="gateway_name" onchange="toggle_cat(this, 'gateway_name_field');" style="width: 160px;">
                <option value="-">-Please Select-</option>
                <option value="other">{New Value}</option>
                <?php if(@count($categories)>0)foreach($categories as $cat_v){
                echo "<option value=\"{$cat_v['gateway_name']}\">{$cat_v['gateway_name']}</option>";
                }
                ?>
                </select>
                <span style="color:#CC0000;">&nbsp;*</span>
                <?php if(isset($empt['gateway_name'])) echo "<script>document.getElementById('gateway_name_sel').value='{$empt['gateway_name']}';</script>"; ?>
            </div>

            <div id="gateway_name_field_div" style="display:none;"><br /><input type="text" id="gateway_name_field" name="gateway_name" maxlength="25" value="" style="width:150px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span></div>
        </div>
        <?php } ?>

        <div style="clear:both; height:20px;"></div>


        <div style="width:160px; float:left;">Gateway Transaction ID:</div>
        <?php if($up_id>0){ ?><div style="float:left; font-weight:bold;"><?=format_str(@$empt['transaction_id'])?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;"><input type="text" id="transaction_id" name="transaction_id" maxlength="150" value="<?=format_str(@$empt['transaction_id'])?>" style="width:250px; border:1px solid #000261;" />
        </div><?php } ?>


        <div style="clear:both; height:10px;"></div>


        <div style="width:160px; float:left;">Gateway Payers ID:</div>
        <?php if($up_id>0){ ?><div style="float:left;"><?=format_str(@$empt['gateway_payer_id'])?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;"><input type="text" id="gateway_payer_id" name="gateway_payer_id" maxlength="100" value="<?=format_str(@$empt['gateway_payer_id'])?>" style="width:250px; border:1px solid #000261;" />
        </div><?php } ?>

        <div style="clear:both; height:30px;"></div>

        <div style="width:160px; float:left; font-weight:bold;">Full Gateway Msg:</div>
        <?php if($up_id>0){ ?><div style="float:left;"><?=(@$empt['gateway_msg'])?></div><?php } else if($user_select>0) { ?>
        <div style="float:left;"><textarea id="gateway_msg" name="gateway_msg" rows="5" style="width:255px; border:1px solid #000261;"><?=format_str(@$empt['gateway_msg'])?></textarea>
        </div><?php } ?>


        <div style="clear:both;"></div>
    </td>
    </tr>

    <?php if($read_only<=0) { ?>
    <tr><td>&nbsp;</td></tr>

	<tr>
		<td>
			<?php if($user_select>0) { ?><input type="submit" class="button" name="sub" value="Submit" style="width:120px;" />&nbsp;&nbsp;<?php } ?>
            <input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;" />
		</td>
	</tr>
    <?php } ?>
    </table>

<?php if(($read_only<=0) && ($user_select<=0)) { ?>
</form>

<br /><br />
<div style="float:left; font-style:italic;">
Click on the <b>User Email Address</b> to review the User Details.
</div>
<?php } ?>

<?php
include_once("includes/footer.php");
?>