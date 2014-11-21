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

$my_user_id = (int)@$_SESSION["cusa_admin_usr_id"];
if($my_user_id<=0){exit;}

/////////////////////////////////////////////////////////////////////////

$ignore = array_flip(array('pageindex', 'search_it', 'title', 'is_active')); //for reset type func
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


    #/ Delete pages_categories (parent and child)
    $query = sprintf("DELETE FROM page_categories WHERE (id IN (%s))", $rid_csv, $rid_csv);
    mysql_query($query);

    #/ Delete site_pages ??
    //$query = sprintf("DELETE FROM site_pages WHERE cat_id IN (%s)", $rid_csv);
    //mysql_query($query);

    $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Record(s) were successfully DELETED.');
    redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param3}", true);

}//end if delete..

/////////////////////////////////////////////////////////////////////////

$search_it = (int)getgpcvar("search_it", "G");
$sr_title = getgpcvar("title", "G");
$sr_is_active = (string)getgpcvar("is_active", "G");

$operation_page = 'pages_categories_opp.php';
/////////////////////////////////////////////////////////////////////////

$pg_title = "Page Categories";
include_once("includes/header.php");
?>


<!-- Custom Functions -->
<script>
function filter_x(fld)
{
    var key = fld.name;
    var val = escape(fld.value);

    var url_x = '';
    if((key!='title') && (document.getElementById('title').value!='')){url_x+="&title="+escape(document.getElementById('title').value);}
    if((key!='is_active') && (document.getElementById('is_active').value!='')){url_x+="&is_active="+escape(document.getElementById('is_active').value);}

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
    location.href='<?=$operation_page?><?=$param3?>&pc_id='+rec_id;

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

<div style="float:left;"><h1>Page Categories</h1></div>
<div style="clear:both; height:1px;">&nbsp;</div>

<div style="float:left; width:100%">
    <div style="float:right;">
        <input type="button" class="button" value="Reset Filters" onclick="clear_all();" />
        <a class="button" href="<?=$operation_page?><?=$param3?>">&nbsp;Add New&nbsp;</a>
    </div>
    <div style="clear:both;"></div>
</div>

<div style="clear:both; height:20px;">&nbsp;</div>

<div>
    <script>
    function validate_f1()
    {
        if (IsChecked(document.f1["RecordID[]"], "Please select atleast 1 record to delete !") == false) return false;
        return confirm("Are you sure you want to delete the selected record(s)?\nThis may also effect the display of Pages within these Categories!");
    }
    </script>
    <form action="" method="post" name="f1" onsubmit="return validate_f1();">
    <input type="hidden" name="command" value="del" />


    <table border="0" cellpadding="0" cellspacing="0" class="datagrid dg_middle" width="100%">

    <?php
    $recrd = false;

	#### Build SQL
	$where = "";

	##### Search String
    if($search_it)
	{
       include_once("../../includes/srch_lib.php");
       $src = new srch_h();
       $get_where = '';

       if(!empty($sr_title)) $get_where .= $src->where_it($sr_title, 'title', 'title');
       if($sr_is_active!='') $get_where .= $src->where_it($sr_is_active, 'is_active', 'is_active', 'equals');

       $where .= $get_where;
	}
    #####--

	$orderby = (int) getgpcvar("orderby", "G");
	if (in_array($orderby, array(1, 2)) == false) {
	$orderby = 1;
	}

	switch($orderby)
    {
		case '1': $orderby = 'title'; break;
		case '2': $orderby = 'is_active'; break;
	}

	$orderdi   = getgpcvar("orderdi",   "G");
    if (in_array($orderdi, array("ASC", "DESC")) == false) { $orderdi = "ASC"; }

	$pageindex = (int) getgpcvar("pageindex", "G"); if ($pageindex < 0) { $pageindex = 0; }
	$pagesize  = 30;


    $query = sprintf("SELECT id, title, is_active FROM page_categories
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
		case 'title':  $orderby = '1'; break;
		case 'is_active': $orderby = '2'; break;
	}
	?>

    <tr>
        <th width="4%" nowrap>&nbsp;</th>

        <th valign="top" width="56%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 1, "orderdi" => $orderby == 1 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Title
            <?php if ($orderby == 1 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 1 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="30%" nowrap><a href="<?php echo rebuildurl(array("orderby" => 2, "orderdi" => $orderby == 2 && $orderdi == "ASC" ? "DESC" : "ASC")); ?>">
            Is Active
            <?php if ($orderby == 2 && $orderdi == "ASC" ) { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sorta.gif" alt="Asc"  width="9" height="9" border="0">
            <?php } ?>
            <?php if ($orderby == 2 && $orderdi == "DESC") { ?>
            <img src="<?=DOC_ROOT_ADMIN?>/images/sortd.gif" alt="Desc" width="9" height="9" border="0">
            <?php } ?>
            </a>
        </th>

        <th valign="top" width="10%" nowrap></th>
    </tr>


    <!-- Filter -->
    <tr>
        <th class="inps"></th>
        <th class="inps"><i style="font-size: 10px;">Contains</i><br /><input type="text" id="title" name="title" value="<?php echo $sr_title; ?>" style="width:250px;" onblur="filter_x(this);" /></th>
        <th class="inps"><i style="font-size: 10px;">Is</i><br /><select id="is_active" name="is_active" style="width:150px;" onchange="filter_x(this);"><option value=""></option><option value="1">Yes</option><option value="0">No</option></select><?php echo "<script>document.getElementById('is_active').value='{$sr_is_active}';</script>"; ?></th>
        <th class="inps"></th>
    </tr>


    <?php
	if($recrd == false)
	{
		?>
        <tr>
        <td colspan="4" align="center">
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

            $title = ($recrd["title"]);
            if(($sr_title !== false) && ($sr_title!='')){
            $title = $src->get_highlighted($title, 'title');
            if($title=='--continue--continue--continue--continue--') {$skip_flag = true;}
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
                <td valign="top"><input type="checkbox" name="RecordID[]" value="<?php echo $recrd["id"]; ?>" /></td>

                <td valign="top"><?php echo $title; ?></td>
                <td valign="top"><?php echo ($is_active)?'Yes':'No'; ?></td>

                <td valign="top" align="right">
                    <?php if($is_active=='0') {?><img src="<?=DOC_ROOT_ADMIN?>/images/red.png" width="10" border="none" title="in-active" /><?php } ?>
                    <input type="button" class="button" value="Edit" onclick="update_rec('<?php echo $recrd['id']; ?>', '<?php echo $pageindex; ?>');" />
                </td>
            </tr>
            <?php
            }//end if skip.....

            $recrd = mysql_fetch_assoc($token);
		}//end while...
    ?>

    <tr>
        <td colspan="4"><br /><input type="submit" class="button" value="Delete Selected Record(s)"/></td>
    </tr>


    <tr style="background: none !important;">
        <td colspan="4"><br />
        <?php
		#### Build Paging
		$query = sprintf("
        SELECT count(*) AS C
        FROM page_categories
        WHERE
        1 = 1 %s
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

<div style="float: right;">
<input type="button" class="button" value="Reset Filters" onclick="clear_all();" />
</div>
<div style="clear:both;"></div>

<?php
include_once("includes/footer.php");
?>