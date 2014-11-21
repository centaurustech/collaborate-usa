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

$ignore = array_flip(array('pkg_id')); //for move within OPERATION page

$param2 = http_build_query(array_diff_key($_GET, $ignore));

if(!empty($param2)) $param2 = '?'.$param2.'&'; else $param2 = '?';

$pkg_id = (int) getgpcvar("pkg_id", "G");

$back_page = "packages.php";
$cur_page = cur_page();
/////////////////////////////////////////////////////////////////

if(isset($_POST['title']))
{
    $pkg_id = (int) getgpcvar("pkg_id", "P");


    ##/ Validate Fields
    include_once('../../includes/form_validator.php');
    $form_v = new Valitron\Validator($_POST);

    $_POST['is_recursive'] = (int)@$_POST['is_recursive'];

    $rules = [
    'required' => [['title'], ['cost'], ['display_order']],
    'lengthMax' => [['title', 100]],
    'integer' => [['display_order']],
    'numeric' => [['cost'], ['recursive_cost']],
    //'min' => [['cost', 1]]
    ];

    if($_POST['is_recursive']==1)
    {
        $rules['min'] = [['recursive_cost', 1]];
    }

    $form_v->labels(array(
    'recursive_cost' => 'Dues Amount',
    ));

    $form_v->rules($rules);
    $form_v->validate();
    $fv_errors = $form_v->errors();
    //var_dump("<pre>", $_POST, $fv_errors); die();
    #-


    if(!is_array($fv_errors) || empty($fv_errors) || (count($fv_errors)<=0))
    {
        $_POST['is_basic'] = (int)@$_POST['is_basic'];
        $_POST['cost'] = (float)@$_POST['cost'];

        $_POST['recursive_cost'] = (float)@$_POST['recursive_cost'];

        $_POST['display_order'] = (int)@$_POST['display_order'];
        $_POST['is_active'] = (int)@$_POST['is_active'];


        $is_update = false;
        if($pkg_id>0)  //Edit Mode
        {
            $is_update = true;

            #/ update membership_packages
    		$sql_tb1 = "UPDATE membership_packages SET
            title='{$_POST['title']}', cost='{$_POST['cost']}', intro_copy='{$_POST['intro_copy']}', is_basic='{$_POST['is_basic']}',
            is_recursive='{$_POST['is_recursive']}', recursive_cost='{$_POST['recursive_cost']}',
            display_order='{$_POST['display_order']}', is_active='{$_POST['is_active']}'
            WHERE id='{$pkg_id}'";
    		mysql_exec($sql_tb1, 'save');


            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Package has been successfully Updated');
            //redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&pkg_id={$pkg_id}", true);

            ////////////////-------

        }//end Edit..
        else //Add page
        {
            ////////////////-------

            #/ insert membership_packages
            $sql_tb1 = "INSERT INTO membership_packages
            (title, cost, intro_copy, is_basic, is_recursive, recursive_cost, display_order, is_active, added_on)
        	VALUES('{$_POST['title']}', '{$_POST['cost']}', '{$_POST['intro_copy']}', '{$_POST['is_basic']}',
            '{$_POST['is_recursive']}', '{$_POST['recursive_cost']}', '{$_POST['display_order']}', '{$_POST['is_active']}', NOW())";
            mysql_exec($sql_tb1, 'save');
            $pkg_id = (int)@mysql_insert_id();

            $_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(true, 'The Package has been successfully Added');
            //redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&pkg_id={$pkg_id}", true);

        }//end Add....



        ##/ Process benefits
        if($pkg_id>0)
        {
            ###/ Save benefits
            $benefits = @$_POST['benefits'];
            $sv_insert = $sv_update = $sv_delete = array();

            if(is_array($benefits) && count($benefits)>0)
            foreach($benefits as $sv_k=>$sv_v)
            {
                #/ make 'benefits' query parts
                if(stristr($sv_k, 'SV_')!=false)
                {
                    $upv_k = substr($sv_k, 3);
                    if($sv_v==''){
                    $sv_delete[] = $upv_k; //delete when empty
                    } else {
                    $sv_update[] = "WHEN id='{$upv_k}' THEN '{$sv_v}'";
                    }
                }
                else
                {
                    if($sv_v=='') continue;
                    $sv_insert[] = "('{$pkg_id}', '{$sv_v}')";
                }

            }
            //var_dump("<pre>", $sv_insert, $sv_update, $sv_delete); die('x');


            #/ benefits insert
            if(count($sv_insert)>0)
            {
                $sv_insert_s = implode(', ', $sv_insert);

                $sql_tb2 = "INSERT INTO package_benefits (package_id, title) VALUES
                {$sv_insert_s}";
                mysql_exec($sql_tb2, 'save');
            }

            #/ benefits update
            if(count($sv_update)>0)
            {
                $tx = "
                ";
                $sv_update_s = implode($tx, $sv_update);

                $sql_tb2 = "UPDATE package_benefits SET title=(CASE
                {$sv_update_s}
                ELSE title
                END)
                WHERE package_id='{$pkg_id}'
                ";
                mysql_exec($sql_tb2, 'save');
            }

            #/ benefits delete
            if(count($sv_delete)>0)
            {
                $sv_delete_s = '\''.implode("','", $sv_delete).'\'';
                $sql_tb2 = "DELETE FROM package_benefits WHERE id IN ({$sv_delete_s}) AND package_id='{$pkg_id}'";
                mysql_exec($sql_tb2, 'save');
            }
            ##--
        }

        //DIE('X');
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$cur_page}{$param2}&pkg_id={$pkg_id}", true);
        #-


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
if (($pkg_id) && (empty($empt)) )
{
    $query  = sprintf("SELECT lp.*, lp.id as lp_id

    FROM membership_packages lp

    WHERE lp.id='%d'", $pkg_id);

	$token  = mysql_query($query, $cn1); //or die(mysql_error($cn1));
	$empt  =  @mysql_fetch_assoc($token);

	if ($empt == false)
	{
		$_SESSION["CUSA_ADMIN_MSG_GLOBAL"] = array(false, "Record Not Found !");
        redirect_me("{$consts['DOC_ROOT_ADMIN']}{$back_page}{$param2}", true);
	}

    #/ get Benefits
    $query_2 = sprintf("SELECT * FROM package_benefits WHERE package_id='%d' ORDER BY id", $pkg_id);
	$empt_2 = @format_str(mysql_exec($query_2));
}
else
{
    #/ get max_display_order
    $query_mdo = sprintf("SELECT max(display_order) as max_display_order FROM membership_packages", $pkg_id);
	$empt_mdo = @format_str(mysql_exec($query_mdo, 'single'));

    $max_display_order = ((int)@$empt_mdo['max_display_order'])+1;
}

if(isset($_POST['title']))
{
    $empt = $_POST;
}
///////////////////////////////////////////////////////////////////
$pg_title = "Membership Packages";
include_once("includes/header.php");
?>

<div style="float:left;"><h1><?=$pg_title?> &raquo; <?php echo ($pkg_id>0)? "Edit ": "Add "; ?> Record</h1></div>
<div style="float:right; margin-top:18px;"><input type="button" class="button" value="Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:70px;" /></div>
<div style="clear:both; height:15px;">&nbsp;</div>


<script type="text/javascript">
function check_this()
{
    var err = '';

    if(document.getElementById('title').value=='')
    {
        err += 'Please enter the Package Title!\n';
    }

    if(document.getElementById('cost').value=='')
    {
        err += 'Please enter the Cost!\n';
    }
    else if(document.getElementById('cost').value.search(/^[0-9\.]{0,}$/)<0)
    {
        err += 'Please enter the Cost in Numeric only!\n';
    }

    if(document.getElementById('recursive_cost').value==''){}
    else if(document.getElementById('recursive_cost').value.search(/^[0-9\.]{0,}$/)<0)
    {
        err += 'Please enter the Dues Amount in Numeric only!\n';
    }

    if(document.getElementById('display_order').value=='')
    {
        err += 'Please enter the Display Order!\n';
    }
    else if(document.getElementById('display_order').value.search(/^[0-9]{0,}$/)<0)
    {
        err += 'Please enter the Display Order in Digits only!\n';
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


var new_smax = 0;
function add_more_sv_fields(maxv)
{
    if(document.getElementById('benefits_temp')==null)
    return false;

    if(new_smax==0)
    new_smax+= parseInt(maxv)+1;
    else
    new_smax++;

    var cl_ele = document.getElementById('benefits_temp');
    var clone_v = $(cl_ele).children().clone();
    //alert(clone_v);

    $(clone_v)
    .attr('id', 'benefits['+new_smax+']')
    .attr('name', 'benefits['+new_smax+']');

    $(clone_v).insertBefore('#last_divi_');


    var space = '<div style="clear:both; height:4px;"></div>';
    $(space).insertBefore('#last_divi_');

}//end func...
</script>
<!-- //////////////////// -->


<form action="" method="post" name="f2" id="f2" onsubmit="return check_this();">

<?php if($pkg_id){ ?>
<input type="hidden" name="pkg_id" id="pkg_id" value="<?php echo $pkg_id; ?>" />
<?php } ?>


<table border="0" cellpadding="0" cellspacing="0" class="datagrid" width="100%">
	<tr>
	<th>PACKAGE INFO</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:140px; float:left;">Title:</div>
        <div style="float:left;"><input type="text" id="title" name="title" maxlength="100" value="<?=format_str(@$empt['title'])?>" style="width:250px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        </div>

        <div style="clear:both; height:20px;"></div>

        <div style="width:140px; float:left;">Intro Copy:</div>
        <div style="float:left;"><textarea id="intro_copy" name="intro_copy" rows="8" style="width:455px; border:1px solid #000261; float:left;"><?=format_str(@$empt['intro_copy'])?></textarea>
        <div style="clear:both; height:5px;"></div>
        <span class="submsg">use [b][/b] for bold</span>
        </div>

        <div style="clear:both; height:15px;"></div>
        <div style="width:140px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:10px;"></div>


        <div style="width:140px; float:left;">Is Basic?</div>
        <div style="float:left;"><input type="checkbox" name="is_basic" id="is_basic" value="1" <?php if(@$empt['is_basic']=='1') echo "checked='checked'"; ?> />
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:140px; float:left;">Cost:</div>
        <div style="float:left;"><input type="text" id="cost" name="cost" maxlength="5" value="<?=format_str(@$empt['cost'])?>" style="width:40px; border:1px solid #000261;" />&nbsp;$
        <span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        <span class="submsg">&nbsp;(numeric only)</span>
        <span class="submsg">&nbsp;enter '0' for Free memberships</span>
        </div>


        <div style="clear:both; height:15px;"></div>
        <div style="width:140px; float:left;">&nbsp;</div><div><hr /></div>
        <div style="clear:both; height:10px;"></div>

        <div style="width:140px; float:left;">Enable Dues?</div>
        <div style="float:left;"><input type="checkbox" name="is_recursive" id="is_recursive" value="1" onclick="toggle_div(this, 'dues_div');" />
        <?php if(@$empt['is_recursive']=='1') echo '<script>$(document).ready(function(){$(\'#is_recursive\').click();});</script>'; ?>
        </div>

        <div id="dues_div" style="display: none;">
            <div style="clear:both; height:10px;"></div>

            <div style="width:140px; float:left;">Annual Dues Amount:</div>
            <div style="float:left;"><input type="text" id="recursive_cost" name="recursive_cost" maxlength="5" value="<?=format_str(@$empt['recursive_cost'])?>" style="width:40px; border:1px solid #000261;" />&nbsp;$
            <span class="submsg">&nbsp;(numeric only)</span>
            </div>
        </div>

    <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>



    <tr>
	<th>SETTINGS</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:140px; float:left;">Display Order:</div>
        <div style="float:left;"><input type="text" id="display_order" name="display_order" maxlength="3" value="<?php if(isset($empt['display_order'])){echo format_str(@$empt['display_order']);}else if(isset($max_display_order)){echo $max_display_order;}?>" style="width:40px; border:1px solid #000261;" /><span style="color:#CC0000;">&nbsp;&nbsp;*</span>
        <span class="submsg">&nbsp;&nbsp;manually manage the sorting of the Package display</span>
        </div>

        <div style="clear:both; height:10px;"></div>

        <div style="width:140px; float:left;">Is Active?</div>
        <div style="float:left;"><input type="checkbox" name="is_active" id="is_active" value="1" <?php if(@$empt['is_active']!='0') echo "checked='checked'"; ?> />
        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>



    <tr>
	<th>BENEFITS</th>
	</tr>

    <tr>
    <td valign="middle" style="padding:6px 4px;"><br />

        <div style="width:140px; float:left;">List of Benefits(s):</div>

        <div style="float:left; width:70%;">
            <?php
            //var_dump("<pre>", $empt_2);
            $total_benefits = (is_array($empt_2) && count($empt_2)>3)? count($empt_2):10;

            echo '<div>';
            for($j=0; $j<$total_benefits; $j++)
            {
                $empt_2vv = @$empt_2[$j];

                $sv_ids = $j;
                if(is_array($empt_2vv) && array_key_exists('id', $empt_2vv) && ($empt_2vv['id']!=''))
                $sv_ids = "SV_".@$empt_2vv['id'];

                echo "<input type='text' name='benefits[{$sv_ids}]' id='benefits[{$sv_ids}]' value=\"{$empt_2vv['title']}\" style=\"width:270px; border:1px solid #000261;\" />";
                echo '<div style="clear:both; height:4px;"></div>';
            }

            echo '<div id="last_divi_" style="clear:both; height:1px;"></div>';
            echo "<div><a href=\"javascript:void(0);\" onclick=\"add_more_sv_fields('".($total_benefits-1)."');\">Need more fields here? Click here...</a></div>";
            echo '</div>';
            ?>

            <div style="clear:both; height:5px;"></div>

            <div style="display: none;">
            <div id="benefits_temp">
                <input type='text' name='benefits_' id='benefits_' value="" style="width:270px; border:1px solid #000261;" />
            </div>
            </div>


            <div style="clear:both; height:5px;"></div>
            <span class="submsg">use [b][/b] for bold</span>

        </div>

        <div style="clear:both;"></div>
    </td>
    </tr>

    <tr><td>&nbsp;</td></tr>


	<tr>
		<td>
			<input type="submit" class="button" name="sub" value="Submit" style="width:120px;">
			<input type="button" class="button" value="Cancel / Back" onclick="window.location='<?=$back_page?><?=$param2?>';" style="width:120px;">
		</td>
	</tr>
    </table>

</form>

<?php
include_once("includes/footer.php");
?>