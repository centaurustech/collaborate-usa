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

$my_user_id = (int)@$_SESSION["cusa_admin_usr_id"];
if($my_user_id<=0){exit;}

$btr = (int)getgpcvar("btr", "G");
$bkr = (string)getgpcvar("bkr", "G");

/////////////////////////////////////////////////////////////////////////

$ignore = array_flip(array('pageindex', 'search_it', 'invoice', 'email_add', 'amount', 'amount_dir', 'gateway_name', 'transaction_id', 'payment_status')); //for reset type func

$param = http_build_query(array_diff_key($_GET, $ignore));
$param3 = http_build_query($_GET);

if(!empty($param)) $param = '?'.$param.'&'; else $param = '?';
if(!empty($param3)) $param3 = '?'.$param3.'&'; else $param3 = '?';

/////////////////////////////////////////////////////////////////////////

if(isset($_POST['command'])) //delete
{
    $cur_page = cur_page();

    $rid = getgpcvar("RecordID", "P");
    $rid_csv = csvfromintarray($rid, "-1");

    if(!empty($rid))
    {
        #/ Delete Records
        $query = sprintf("DELETE FROM user_payments WHERE id IN (%s)", $rid_csv);
        mysql_query($query);

        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully DELETED.');
    }

    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_invoice = getgpcvar("invoice", "G");
$sr_email_add = getgpcvar("email_add", "G");
$sr_amount = getgpcvar("amount", "G");
$sr_amount_dir = getgpcvar("amount_dir", "G");
$sr_gateway_name = getgpcvar("gateway_name", "G");
$sr_transaction_id = getgpcvar("transaction_id", "G");
$sr_payment_status = getgpcvar("payment_status", "G");

$operation_page = 'user_payment_opp.php';

/////////////////////////////////////////////////////////////////////////

$load_fancy = true;

$pg_title = "Member Payments";
include_once("includes/header.php");
?>

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


<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='invoice') && (document.getElementById('invoice').value!='')){url_x+="&invoice="+escape(document.getElementById('invoice').value);}
    if((key!='email_add') && (document.getElementById('email_add').value!='')){url_x+="&email_add="+escape(document.getElementById('email_add').value);}
    if((key!='amount') && (document.getElementById('amount').value!='')){url_x+="&amount="+escape(document.getElementById('amount').value);}
    if((key!='amount_dir') && (document.getElementById('amount_dir').value!='')){url_x+="&amount_dir="+escape(document.getElementById('amount_dir').value);}
    if((key!='gateway_name') && (document.getElementById('gateway_name').value!='')){url_x+="&gateway_name="+escape(document.getElementById('gateway_name').value);}
    if((key!='transaction_id') && (document.getElementById('transaction_id').value!='')){url_x+="&transaction_id="+escape(document.getElementById('transaction_id').value);}
    if((key!='payment_status') && (document.getElementById('payment_status').value!='')){url_x+="&payment_status="+escape(document.getElementById('payment_status').value);}

    //if((url_x=='') && (val=='')){
    if(val==''){
    }
    else
    {
        var url = '<?php echo $cur_page.$param; ?>search_it=1&'+key+'='+val+url_x;
        location.href=url;
    }
}//end func.....

function update_rec(rec_id, pageindex)
{
    location.href='<?=$operation_page?><?=$param3?>&up_id='+rec_id;

}//end func......

function clear_all()
{
    location.href='<?php echo $cur_page.$param; ?>';
}

function set_this(f_id, title)
{
    if(title != ''){
    var fld = document.getElementById(f_id);
    fld.value='"'+title+'"';
    filter_x(fld);
    }

}//end func....

function move_me(loc, rec_id)
{
    if(loc=='') return false;

    var full=false;
    if(loc.search(/\.php/i)>0)
    full = true;

    if(full==false)
    location.href=loc+'.php<?=$param3?>&up_id='+rec_id;
    else
    location.href=loc;
}
</script>

<?php
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
?>

<div style="float:left;"><h1><?=$pg_title?></h1></div>
<div style="clear:both; height:1px;">&nbsp;</div>

<div style="float:left; width:100%">
    <div style="float:right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" />
    <?php /*<a class="button" href="<?=$operation_page?><?=$param3?>">&nbsp;Add New Payment&nbsp;</a>*/?></div>
    <?php if($btr>0){ ?><div style="float:right; margin-right:8px;"><input type="button" class="button" value="&laquo; Back" onclick="window.location='<?=urldecode($bkr)?>';" style="width:70px;" /></div><?php } ?>
    <div style="clear:both;"></div>
</div>

<div style="clear:both; height:20px;">&nbsp;</div>

<div>
    <script>
    function validate_f1()
    {
        if (IsChecked(document.f1["RecordID[]"], "Please select atleast 1 record to delete !") == false) return false;
        return confirm("Are you sure you want to delete the selected record(s)?");
    }
    </script>
    <form action="" method="post" name="f1" onsubmit="return validate_f1();">
    <input type="hidden" name="command" value="del" />


    <table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">

    <?php
    $recrd = false;

	#### Build SQL
	$where="";

	##### Search String
    if($search_it)
	{
       include_once("../../includes/srch_lib.php");
       $src = new srch_h();
       $get_where = '';

       if(!empty($sr_invoice)) $get_where .= $src->where_it($sr_invoice, 'invoice', 'invoice');
       if(!empty($sr_email_add)) $get_where .= $src->where_it($sr_email_add, 'email_add', 'email_add');

       $cdir = 'equals';
       if($sr_amount_dir=='lte')
       $cdir = 'less-than-equals';
       if($sr_amount_dir=='gte')
       $cdir = 'greater-than-equals';
       if($sr_amount!='') $get_where .= $src->where_it($sr_amount, 'amount', 'amount', $cdir);

       if(!empty($sr_gateway_name)) $get_where .= $src->where_it($sr_gateway_name, 'gateway_name', 'gateway_name');
       if(!empty($sr_transaction_id)) $get_where .= $src->where_it($sr_transaction_id, 'transaction_id', 'transaction_id');
       if($sr_payment_status!='') $get_where .= $src->where_it($sr_payment_status, 'payment_status', 'payment_status', 'equals');

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3, 4, 5, 6, 7)) == false) {
	$orderby = 7;
	}

	switch($orderby)
    {
		case '1': $orderby = 'invoice'; break;
        case '2': $orderby = 'email_add'; break;
		case '3': $orderby = 'amount'; break;
		case '4': $orderby = 'gateway_name'; break;
		case '5': $orderby = 'transaction_id'; break;
        case '6': $orderby = 'payment_status'; break;
		case '7': $orderby = 'paid_on'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "DESC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT up.*, up.id as up_id, u.email_add
    FROM user_payments up
    LEFT JOIN users u ON u.id=up.user_id
    WHERE 1 = 1 %s
	ORDER BY %s %s
    LIMIT %d, %d
	", $where, $orderby, $orderdi, $pageindex * $pagesize, $pagesize
	);
    //echo '<pre>'.$query; die();
    #### ---


	$token = mysql_query($query) or die(mysql_error());
    $recrd = mysql_fetch_assoc($token);

	switch($orderby)
    {
		case 'invoice': $orderby = '1'; break;
        case 'email_add':  $orderby = '2'; break;
		case 'amount':  $orderby = '3'; break;
		case 'gateway_name':   $orderby = '4'; break;
		case 'transaction_id':   $orderby = '5'; break;
        case 'payment_status':   $orderby = '6'; break;
		case 'paid_on':   $orderby = '7'; break;
	}


    #/ Fill select-options
    $pss_opts = '';
    $payment_ss = @format_str(@mysql_exec("SELECT DISTINCT payment_status FROM user_payments GROUP BY payment_status ORDER BY payment_status"));
    if(is_array($payment_ss) && count($payment_ss)>0){
    foreach($payment_ss as $cat_v){$pss_opts.= "<option value='{$cat_v['payment_status']}'>{$cat_v['payment_status']}</option>";}
    }

    #/ Fill select-options
    $gw_opts = '';
    $gateway_ss = @format_str(@mysql_exec("SELECT DISTINCT gateway_name FROM user_payments GROUP BY gateway_name ORDER BY gateway_name"));
    if(is_array($gateway_ss) && count($gateway_ss)>0){
    foreach($gateway_ss as $cat_v){$gw_opts.= "<option value='{$cat_v['gateway_name']}'>{$cat_v['gateway_name']}</option>";}
    }
	?>

    <tr>
        <th width="2%" nowrap>&nbsp;</th>

        <th valign="top" width="12%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Payment Invoice
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="20%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            User (Email Add.)
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Total Paid ($)
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 4, "orderdi" => $orderby == 4 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Gateway
            <?php if ($orderby == 4 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 4 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="13%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 5, "orderdi" => $orderby == 5 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Gateway Trans. ID
            <?php if ($orderby == 5 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 5 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>


        <th valign="top" width="11%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 6, "orderdi" => $orderby == 6 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Payment Status
            <?php if ($orderby == 6 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 6 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="13%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 7, "orderdi" => $orderby == 7 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Paid On
            <?php if ($orderby == 7 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 7 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="9%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="invoice" name="invoice" value="<?php echo $sr_invoice; ?>" style="width:90px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="email_add" name="email_add" value="<?php echo $sr_email_add; ?>" style="width:140px;" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br />
        <select id="amount_dir" name="amount_dir" style="width:40px;" nchange="filter_x(this);"><option value=""></option><option value="lte">&lt;=</option><option value="gte">&gt;=</option></select><?php echo "<script>document.getElementById('amount_dir').value='{$sr_amount_dir}';</script>"; ?>
        <input type="text" id="amount" name="amount" value="<?php echo $sr_amount; ?>" style="width:30px;" maxlength="3" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="gateway_name" name="gateway_name" style="width:70px;" onchange="filter_x(this);"><option value=""></option><?php echo $gw_opts; ?></select><?php echo "<script>document.getElementById('gateway_name').value='{$sr_gateway_name}';</script>"; ?></th>

        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="transaction_id" name="transaction_id" value="<?php echo $sr_transaction_id; ?>" style="width:100px;" onblur="filter_x(this);" /></th>

        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="payment_status" name="payment_status" style="width:70px;" onchange="filter_x(this);"><option value=""></option><?php echo $pss_opts; ?></select><?php echo "<script>document.getElementById('payment_status').value='{$sr_payment_status}';</script>"; ?></th>
        <th class="inps"></th>
        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="9" align="center">
        <br />
        <?php
        echo "No Results / Records Found !";
        if($search_it)
        {
            echo "&nbsp;Please clear the Filter and try again !";
        }
        ?>
        <br /><br />
        </td>
	    </tr>
        <?php
	}
	else
	{
        $c = 0;

        ## Customer Orders table start from here
		while($recrd)
		{
            $recrd = format_str($recrd);

            $c++;
            $skip_flag = false;

            $invoice = ($recrd["invoice"]);
            if(($sr_invoice !== false) && ($sr_invoice!='')){
            $invoice = $src->get_highlighted($invoice, 'invoice');
            if($invoice=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $email_add = ($recrd["email_add"]);
            if(($sr_email_add !== false) && ($sr_email_add!='')){
            $email_add = $src->get_highlighted($email_add, 'email_add');
            if($email_add=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $transaction_id = ($recrd["transaction_id"]);
            if(($sr_transaction_id !== false) && ($sr_transaction_id!='')){
            $transaction_id = $src->get_highlighted($transaction_id, 'transaction_id');
            if($transaction_id=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["up_id"]; ?>" /></td>

                <td valign="top"><a href="#_" onclick="update_rec('<?php echo $recrd['up_id']; ?>', '<?php echo $pageindex; ?>');"><?php echo $invoice; ?></a></td>
                <td valign="top"><a <?php if($recrd['user_id']>0){?> class="fbox fancybox.ajax" href="<?php echo "{$consts['DOC_ROOT_ADMIN']}users_opp.php?u_id={$recrd['user_id']}&ro=1"; ?>" <?php } ?> title="<?=format_str(@$recrd['email_add'])?>"><?php echo $email_add; ?></a></td>
                <td valign="top"><?php echo number_format((float)$recrd["amount"], 2); ?></td>
                <td valign="top"><?php echo $recrd["gateway_name"]; ?></td>
                <td valign="top"><?php echo $transaction_id; ?></td>
                <td valign="top"><?php echo $recrd["payment_status"]; ?></td>
                <td valign="top"><?php echo $recrd["paid_on"]; ?></td>

                <td valign="top" align="right">
                    <select class="" name="transaction_id" style="width:60px;" onchange="move_me(this.value, '<?php echo $recrd['up_id']; ?>');">
                        <option value="">--</option>
                        <option value="user_payment_opp">Review</option>
                        <?php /*<option value="user_orders.php?search_it=1&btr=1&invoice=<?=$recrd["invoice"]?>&bkr=user_payments.php<?=urlencode($param3)?>">Order Info</option>*/ ?>
                    </select>
                </td>
            </tr>
            <?php
            }//end if skip.....

            $recrd = mysql_fetch_assoc($token);
		}//end while...
    ?>

    <tr>
        <td colspan="9"><br /><input type="submit" class="button" value="Delete Selected Record(s)"/></td>
    </tr>

    <tr style="background: none !important;">
        <td colspan="9"><br />
        <?php
		#### Build Paging
		$query = sprintf("
        SELECT count(*) AS C
        FROM user_payments up
        LEFT JOIN users u ON u.id=up.user_id
        WHERE 1 = 1 %s
        ", $where
        );

        $token = mysql_query($query) or die(mysql_error());
		$recrd = mysql_fetch_assoc($token);
		$count = $recrd["C"];

        include_once('../../includes/admin/pagination_admin.php');
        echo "</div>";
        ### ---
		?>

        </td>
    </tr>

    <?php
	}//end else........
	?>
    </table>
    </form>
</div>

<br />
<div style="float: right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" /></div>
<div style="clear:both;"></div>

<div style="float:left; font-style:italic;">
Click on <b>User (Email Address)</b> to review the User Details.
</div>

<?php
include_once("includes/footer.php");
?>