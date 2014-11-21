<?php
require_once('../../includes/config.php');
include_once('includes/session.php');

$section_id = 1; include_once('includes/check_permission.php');

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

$ignore = array_flip(array('pageindex', 'search_it', 'email_add', 'first_name', 'last_name')); //for reset type func
//$pass = array_flip(array('pageindex', 'orderby', 'orderdi')); //to pass to next page

$param = http_build_query(array_diff_key($_GET, $ignore));
//$param2 = http_build_query(array_intersect_key($_GET, $pass));
$param3 = http_build_query($_GET);

if(!empty($param)) $param = '?'.$param.'&'; else $param = '?';
//if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';
if(!empty($param3)) $param3 = '?'.$param3.'&'; else $param3 = '?';

/////////////////////////////////////////////////////////////////////////

if(isset($_POST['command'])) //delete
{
    $cur_page = cur_page();

    $rid = getgpcvar("RecordID", "P");
    $rid_csv = csvfromintarray($rid, "-1");

    if(!empty($rid))
    {
        #/ Delete all Records and Child Records
        $query = sprintf("DELETE FROM admin_users WHERE id IN (%s)", $rid_csv);
        mysql_query($query);

        #/ Delete admin_permissions
        $query = sprintf("DELETE FROM admin_permissions WHERE admin_users_id IN (%s)", $rid_csv);
        mysql_query($query);


        $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully DELETED.');
    }
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");

$sr_first_name = getgpcvar("first_name", "G");
$sr_last_name = getgpcvar("last_name", "G");
$sr_email_add = getgpcvar("email_add", "G");

/////////////////////////////////////////////////////////////////////////

$pg_title = "Admin Users";
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

    //if((url_x=='') && (val=='')){
    if(val==''){
    }
    else
    {
        var url = '<?php echo $cur_page.$param; ?>search_it=1&'+key+'='+val+url_x;
        location.href=url;
    }
}//end func.....

function update_au(au_id, pageindex)
{
    //location.href='admin_users_opp.php?pageindex='+pageindex+'&au_id='+au_id;
    location.href='admin_users_opp.php<?=$param3?>&au_id='+au_id;

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
</script>

<?php
//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////
?>

<div style="float:left;"><h1><?=$pg_title?></h1></div>
<div style="clear:both; height:1px;">&nbsp;</div>

<div style="float:left; width:100%">
<div style="float:right;"><input type="button" class="button" value="Reset Filters" onclick="clear_all();" />
<a class="button" href="admin_users_opp.php<?=$param3?>">&nbsp;Add New Admin User&nbsp;</a></div>
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


    <table border="0" cellpadding="0" cellspacing="0" class="datagrid dg_middle" width="100%">

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

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2, 3, 4)) == false) {
	$orderby = 1;
	}

	switch($orderby)
    {
		case '1': $orderby = 'email_add'; break;
		case '2': $orderby = 'first_name'; break;
		case '3': $orderby = 'last_name'; break;
		case '4': $orderby = 'added_on'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "ASC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT *, id as au_id FROM admin_users
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
		case 'added_on':   $orderby = '4'; break;
	}
	?>

    <tr>
        <th width="2%" nowrap>&nbsp;</th>

        <th valign="top" width="28%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Email Address
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="20%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            First Name
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="20%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 3, "orderdi" => $orderby == 3 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Last Name
            <?php if ($orderby == 3 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 3 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="20%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 4, "orderdi" => $orderby == 4 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Added On
            <?php if ($orderby == 4 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 4 && $orderdi == "DESC") { ?>
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
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="first_name" name="first_name" value="<?php echo $sr_first_name; ?>" style="width:120px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="last_name" name="last_name" value="<?php echo $sr_last_name; ?>" style="width:120px;" onblur="filter_x(this);" /></th>
        <th class="inps"></th>
        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="6" align="center">
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

            $is_active = ($recrd["is_active"]);


            if($skip_flag==false){
            $tr_bg = "#FFFFFF";
            if($c%2==0)
            $tr_bg = "#F5F5F5";
            if($is_active=='0')
            $tr_bg = "#FFF0F0";
            ?>
            <tr style="background:<?php echo $tr_bg; ?>">
                <td valign="top"><?php if(($recrd["is_super_admin"]==1) || ($recrd["au_id"]==$my_user_id)){} else{ //#/prevent Super-Admin or Self-deletion ?>
                <input type="checkbox" name="RecordID[]" value="<?php echo $recrd["au_id"]; ?>" />
                <?php } ?></td>

                <td valign="top"><?php echo $email_add; ?></td>
                <td valign="top"><?php echo $first_name; ?></td>
                <td valign="top"><?php echo $last_name; ?></td>
                <td valign="top"><?php echo $recrd["added_on"]; ?></td>

                <td valign="top" align="right">
                    <?php if($is_active=='0') {?><img src="<?=DOC_ROOT_ADMIN?>/images/red.png" width="10" border="none" title="in-active" /><?php } ?>
                    <input type="button" class="button" value="Edit" onclick="update_au('<?php echo $recrd['au_id']; ?>', '<?php echo $pageindex; ?>');" />
                </td>
            </tr>
            <?php
            }//end if skip.....

            $recrd = mysql_fetch_assoc($token);
		}//end while...
    ?>

    <tr>
        <td colspan="6"><br /><input type="submit" class="button" value="Delete Selected Record(s)"/></td>
    </tr>

    <tr style="background: none !important;">
        <td colspan="6"><br />
        <?php
		#### Build Paging
		$query = sprintf("
        SELECT count(*) AS C
        FROM admin_users
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