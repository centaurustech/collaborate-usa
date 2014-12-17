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

/////////////////////////////////////////////////////////////////////////

$ignore = array_flip(array('pageindex', 'search_it', 'email_add', 'first_name', 'last_name', 'account_activated', 'is_blocked', 'package_id')); //for reset type func

$param = http_build_query(array_diff_key($_GET, $ignore));
$param3 = http_build_query($_GET);

if(!empty($param)) $param = '?'.$param.'&'; else $param = '?';
if(!empty($param3)) $param3 = '?'.$param3.'&'; else $param3 = '?';

/////////////////////////////////////////////////////////////////////////

if(isset($_POST['command'])) //delete
{
    $cur_page = cur_page();

    $rid = getgpcvar("RecordID", "P");

    $del_id = array();
    $del_ids = '';

    $flag_1 = true;
    foreach($rid as $ridv)
    {
        #/ Check if record exists in other tables
        $user_data = mysql_exec("SELECT * FROM (
        (SELECT COUNT(*) AS cx FROM user_payments WHERE user_id = '{$ridv}')
        UNION (SELECT COUNT(*) AS cx FROM dues_installments WHERE user_id = '{$ridv}')
        UNION (SELECT COUNT(*) AS cx FROM streams_voice WHERE user_id = '{$ridv}')
        ) ax
        ORDER BY cx DESC
        LIMIT 1", 'single');
        //var_dump($user_data); die();

        if(is_array($user_data) && count($user_data)>0 && ($user_data['cx']>0)){
        $flag_1 = false;
        continue;
        }

        $del_id[] = $ridv;

    }//end foreach...

    if(!empty($del_id)) $del_ids = implode(',', $del_id);


    //if($flag_1)
    if(!empty($del_ids))
    {
        #/ Delete all Records from various tables

        #/*
        $query = sprintf("DELETE FROM users WHERE id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM user_info WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM acc_verifications WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM user_permissions WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM user_petronage_points WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM user_invitations WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM user_contacts WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM voice_cat_suggested WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM voices_votes WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM voices_votes_dump WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM eco_members WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM eco_members_requests WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM eco_discussions WHERE posted_by IN (%s)", $del_ids);
        mysql_query($query);

        $query = sprintf("DELETE FROM eco_discussion_comments WHERE posted_by IN (%s)", $del_ids);
        mysql_query($query);


        #/ Update few other tables
        $query = sprintf("UPDATE user_voices SET is_blocked='1' WHERE user_id IN (%s)", $del_ids);
        mysql_query($query);
        #*/


        #/ Delete user_files
        include_once("../../includes/file_mgt.php");
        foreach($del_id as $del_idv)
        {
            $del_idv = (int)$del_idv;
            if($del_idv<=0) continue;

            $del_path = "../user_files/prof/{$del_idv}";
            delete_files($del_path);
            @rmdir($del_path);
        }

    }//end if flag...


    $msg_del = 'The Record(s) were successfully DELETED.';
    if($flag_1==false){
    $msg_del = "Some Record(s) could not be Deleted as they have <b>Payment & Eco-system data</b> associated with them. Please delete their other data first.";
    }

    $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array($flag_1, $msg_del);
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_email_add = getgpcvar("email_add", "G");
$sr_first_name = getgpcvar("first_name", "G");
$sr_last_name = getgpcvar("last_name", "G");
$sr_account_activated = getgpcvar("account_activated", "G");
$sr_is_blocked = getgpcvar("is_blocked", "G");
$sr_package_id = getgpcvar("package_id", "G");

$operation_page = 'users_opp.php';

/////////////////////////////////////////////////////////////////////////
$load_fancy = true;

$pg_title = "Members / Users";
include_once("includes/header.php");
?>


<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='email_add') && (document.getElementById('email_add').value!='')){url_x+="&email_add="+escape(document.getElementById('email_add').value);}
    if((key!='first_name') && (document.getElementById('first_name').value!='')){url_x+="&first_name="+escape(document.getElementById('first_name').value);}
    if((key!='last_name') && (document.getElementById('last_name').value!='')){url_x+="&last_name="+escape(document.getElementById('last_name').value);}
    if((key!='package_id') && (document.getElementById('package_id').value!='')){url_x+="&package_id="+escape(document.getElementById('package_id').value);}
    if((key!='account_activated') && (document.getElementById('account_activated').value!='')){url_x+="&account_activated="+escape(document.getElementById('account_activated').value);}
    if((key!='is_blocked') && (document.getElementById('is_blocked').value!='')){url_x+="&is_blocked="+escape(document.getElementById('is_blocked').value);}

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
    location.href='<?=$operation_page?><?=$param3?>&u_id='+rec_id;

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

$(document).ready(function(){
$.move_me = function(loc, rec_id, me)
{
    if(loc=='') return false;

    if(loc.search(/pmt_/i)>=0){
        $('#'+loc).click();
        $(me).val('');
        return false;
    }

    var full=false;
    if(loc.search(/\.php/i)>0)
    full = true;

    if(full==false)
    location.href=loc+'.php<?=$param3?>&u_id='+rec_id;
    else
    location.href=loc;
};
});

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

<?php
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
?>

<div style="float:left;"><h1><?=$pg_title?></h1></div>
<div style="clear:both; height:1px;">&nbsp;</div>

<div style="float:left; width:100%">
<div style="float:right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" />&nbsp;
<a class="button" href="<?=$operation_page?><?=$param3?>">&nbsp;Add New User&nbsp;</a></div>
<div style="clear:both;"></div>
</div>

<div style="clear:both; height:20px;">&nbsp;</div>

<div>
    <script>
    function validate_f1()
    {
        if (IsChecked(document.f1["RecordID[]"], "Please select atleast 1 record to delete !") == false) return false;
        return confirm("Are you sure you want to delete the selected record(s)?\nThey could have Payment & Eco-System data associated with them.");
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

       if(!empty($sr_email_add)) $get_where .= $src->where_it($sr_email_add, 'email_add', 'email_add');
       if(!empty($sr_first_name)) $get_where .= $src->where_it($sr_first_name, 'first_name', 'first_name');
       if(!empty($sr_last_name)) $get_where .= $src->where_it($sr_last_name, 'last_name', 'last_name');
       if($sr_package_id!='') $get_where .= $src->where_it($sr_package_id, 'package_id', 'package_id', 'equals');
       if($sr_account_activated!='') $get_where .= $src->where_it($sr_account_activated, 'account_activated', 'account_activated', 'equals');
       if($sr_is_blocked!='') $get_where .= $src->where_it($sr_is_blocked, 'is_blocked', 'is_blocked', 'equals');

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3, 4, 5, 6, 7)) == false) {
	$orderby = 7;
	}

	switch($orderby)
    {
		case '1': $orderby = 'email_add'; break;
		case '2': $orderby = 'first_name'; break;
		case '3': $orderby = 'last_name'; break;
		case '4': $orderby = 'package_id'; break;
        case '5': $orderby = 'account_activated'; break;
		case '6': $orderby = 'is_blocked'; break;
		case '7': $orderby = 'joined_on'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "DESC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT u.*, u.id AS u_id, up.invoice, up.id as up_id
    FROM users u
    LEFT JOIN user_payments up ON up.user_id=u.id
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
		case 'email_add':  $orderby = '1'; break;
		case 'first_name': $orderby = '2'; break;
		case 'last_name':  $orderby = '3'; break;
		case 'package_id':   $orderby = '4'; break;
		case 'account_activated':   $orderby = '5'; break;
		case 'is_blocked':   $orderby = '6'; break;
		case 'joined_on':   $orderby = '7'; break;
	}


    #/ Fill select-options
    $package_opts = '';
    $packages = @format_str(@mysql_exec("SELECT * FROM membership_packages ORDER BY title"));
    $packages_lst = cb89($packages, 'id');
    if(is_array($packages) && count($packages)>0){
    foreach($packages as $cat_v){
        $cat_v['cost'] = (float)$cat_v['cost'];
        $cost_dv = number_format($cat_v['cost'], 2);
        $package_opts.= "<option value='{$cat_v['id']}'>{$cat_v['title']}".($cat_v['cost']>0? " (\${$cost_dv})":'')."</option>";
    }
    }
	?>

    <tr>
        <th width="2%" nowrap>&nbsp;</th>

        <th valign="top" width="20%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Email Address
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="11%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            First Name
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="12%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Last Name
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="15%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 4, "orderdi" => $orderby == 4 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Membership Package
            <?php if ($orderby == 4 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 4 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="8%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 5, "orderdi" => $orderby == 5 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Activated
            <?php if ($orderby == 5 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 5 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>


        <th valign="top" width="8%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 6, "orderdi" => $orderby == 6 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Blocked
            <?php if ($orderby == 6 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 6 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="14%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 7, "orderdi" => $orderby == 7 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Joined On
            <?php if ($orderby == 7 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 7 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="email_add" name="email_add" value="<?php echo $sr_email_add; ?>" style="width:190px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="first_name" name="first_name" value="<?php echo $sr_first_name; ?>" style="width:80px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="last_name" name="last_name" value="<?php echo $sr_last_name; ?>" style="width:80px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="package_id" name="package_id" style="width:110px;" onchange="filter_x(this);"><option value=""></option><?php echo $package_opts; ?></select><?php echo "<script>document.getElementById('package_id').value='{$sr_package_id}';</script>"; ?></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="account_activated" name="account_activated" style="width:50px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('account_activated').value='{$sr_account_activated}';</script>"; ?></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_blocked" name="is_blocked" style="width:50px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_blocked').value='{$sr_is_blocked}';</script>"; ?></th>
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

            $email_add = ($recrd["email_add"]);
            if(($sr_email_add !== false) && ($sr_email_add!='')){
            $email_add = $src->get_highlighted($email_add, 'email_add');
            if($email_add=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $first_name = ($recrd["first_name"]);
            if(($sr_first_name !== false) && ($sr_first_name!='')){
            $first_name = $src->get_highlighted($first_name, 'first_name');
            if($first_name=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $last_name = ($recrd["last_name"]);
            if(($sr_last_name !== false) && ($sr_last_name!='')){
            $last_name = $src->get_highlighted($last_name, 'last_name');
            if($last_name=='--continue--continue--continue--continue--') {$skip_flag = true;}
            }

            $account_activated = ($recrd["account_activated"]);
            $is_blocked = ($recrd["is_blocked"]);

            $special_anchor = "";
            if( ((float)@$packages_lst[$recrd["package_id"]]['cost']>0) && !empty($recrd["invoice"]) ){
            $special_anchor = "<a href=\"{$consts['DOC_ROOT_ADMIN']}user_payment_opp.php?&up_id={$recrd["up_id"]}&ro=1\"
            id=\"pmt_{$recrd["up_id"]}\" class=\"fbox fancybox.ajax\" title=\"{$recrd["email_add"]}\">&nbsp;</a>";
            }


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            if($is_blocked=='1')
            $tr_bg = "#FFF0F0";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["u_id"]; ?>" /></td>

                <td valign="top"><a href="#_" onclick="update_rec('<?php echo $recrd['u_id']; ?>', '<?php echo $pageindex; ?>');"><?php echo $email_add; ?></a></td>
                <td valign="top"><?php echo $first_name; ?></td>
                <td valign="top"><?php echo $last_name; ?></td>
                <td valign="top"><?php echo @$packages_lst[$recrd["package_id"]]['title'].$special_anchor; ?></td>
                <td valign="top"><?php echo ($account_activated)?'Yes':'No'; ?></td>
                <td valign="top"><?php echo ($is_blocked)?'Yes':'No'; ?></td>
                <td valign="top"><?php echo $recrd["joined_on"]; ?></td>

                <td valign="top" align="right">
                    <?php if($is_blocked=='1') {?><img src="<?=DOC_ROOT_ADMIN?>/images/red.png" width="10" border="none" title="in-active" /><?php } ?>

                    <select class="" name="package_id" style="width:60px;" onchange="$.move_me(this.value, '<?php echo $recrd['u_id']; ?>', this);">
                        <option value="">--</option>
                        <option value="users_opp">Edit</option>
                        <?php
                        if((float)@$packages_lst[$recrd["package_id"]]['cost']>0)
                        {
                            if(empty($recrd["invoice"])){ ?>
                            <option value="user_payment_opp.php?user_select=<?=$recrd["u_id"]?>&emd=<?=$recrd["email_add"]?>&bkr=users.php<?=urlencode($param3)?>">Add Payment</option>
                            <?php } else { ?>
                            <?php /*<option value="user_payments.php?search_it=1&btr=1&email_add=<?=$recrd["email_add"]?>&bkr=users.php<?=urlencode($param3)?>">Review Payments</option>*/ ?>
                            <option value="pmt_<?=$recrd["up_id"]?>">Review Payment</option>
                            <?php
                            }
                        }
                        ?>
                        <option value="voices.php?search_it=1&btr=1&email_add=<?=$recrd["email_add"]?>&bkr=users.php<?=urlencode($param3)?>">Review Voices</option>
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
        //LEFT JOIN user_info ui ON u.id=ui.user_id
		$query = sprintf("
        SELECT count(*) AS C
        FROM users u
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

<?php
include_once("includes/footer.php");
?>