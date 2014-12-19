<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

#/ get Page Info
$page_info = @mysql_exec("SELECT * FROM site_pages WHERE seo_tag_id='{$seo_tag_id}' AND is_active='1'", 'single');
if(!is_array($page_info)){redirect_me('404');}
//var_dump("<pre>", $page_info); die();

$site_menus = @get_dynamic_menu();
//var_dump("<pre>", $site_menus); die();
/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=>format_str($page_info['title']),
    'meta_keywords'=>format_str($page_info['meta_keywords']),
    'meta_descr'=>format_str($page_info['meta_descr']),
);
$page_heading = format_str($page_info['page_heading']);

include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<link type="text/css" rel="stylesheet" href="<?=DOC_ROOT?>assets/css/site_map.css" />

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

    <h1><strong><?=format_str($pg_meta['page_title'])?></strong></h1>

    <div>
        <div id="listpage_content">
            <div class="categTree">
                <div class="tree_top"><h3>User Account</h3></div>
                <ul class="tree">
                <?php if((!isset($user_idc)) || ($user_idc<=0)) { ?>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>signin">Sign-In to your Account</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>join">Join / Signup</a></li>
                <?php } else { ?>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/notification">My Notifications</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/messages">My Messages</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>member">My Profile</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>update-password">Update Password</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/logout">Signout</a></li>
                <?php } ?>
                </ul>
            </div>


            <?php if((isset($user_idc)) && ($user_idc>0)) { //EcoSystem ?>
            <div class="categTree">
                <div class="tree_top"><h3>Eco-System</h3></div>
                <ul class="tree">
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/my-voices">My Voices</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/my-votes">My Votes</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/my-streams">My Streams</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/my-rivers">My Rivers</a></li>
                    <li class="portfolio"><a href="<?=DOC_ROOT?>ecosystem/my-oceans">My Oceans</a></li>
                </ul>
            </div>
            <?php } ?>

        </div>


        <div id="listpage_content">
            <?php
            function print_maps($site_menus, $level=0)
            {
                global $consts;

                $e_cho = '';
                $e_cho2 = '';

                $fi = 0;
                $child_less = array();
                if(is_array($site_menus) && count($site_menus)>0)
                foreach($site_menus as $fm_v)
                {
                    //var_dump("<pre>",$fm_v,"</pre>");

                    #/ Process children
                    $has_child = false;
                    $temp_ec = '';
                    foreach($fm_v as $fm_k2=>$fm_v2)
                    {
                        if(!is_numeric($fm_k2) && (stristr($fm_k2, 'pg_')==false)){continue;}

                        $temp_ec.= print_maps(array(0=>$fm_v2), $level+1);
                        $has_child = true;
                    }

                    if(($level==0) && ($has_child==false)){
                    $child_less[] = $fm_v;
                    continue;
                    }


                    #/ make HTML
                    $fm_href='#_';if($fm_v['seo_tag']!='')$fm_href="{$consts['DOC_ROOT']}{$fm_v['seo_tag']}";

                    if($level==0)
                    {
                        $e_cho.= "<div class=\"categTree\">";

                        if($fm_href=='#_'){
                        $e_cho.= "<div class=\"tree_top\"><h3>{$fm_v['title']}</h3></div>";
                        }else{
                        $e_cho.= "<div class=\"tree_top\"><h3><a href=\"{$fm_href}\">{$fm_v['title']}</a></h3></div>";
                        }

                        $e_cho.= "<ul class=\"tree\">";
                    }
                    else
                    {
                        $e_cho.= "<li>";
                        $e_cho.= "<a href=\"{$fm_href}\">{$fm_v['title']}</a>";
                    }

                    if($has_child) //insert children
                    {
                        $e_cho.= "<ul>".$temp_ec."</ul>";
                    }

                    if($level==0)
                    {
                        $e_cho.= "</ul>";
                        $e_cho.= "</div>";
                    }
                    else
                    {
                        $e_cho.= "</li>";
                    }

                }//end foreach...

                if( ($level==0) && count($child_less)>0 )
                {
                    $t1 = array(0=> array_merge($child_less, array('title'=>'Misc.', 'seo_tag'=>'')));
                    //var_dump("<pre>", $level, $t1);
                    $e_cho.= print_maps($t1, 0);
                }

                return $e_cho;

            }//end func...
            echo print_maps($site_menus);
            ?>
        </div>
    </div>

</div>

</div>
<div class="clear"></div>
</div>
</div>

<?php
include_once("includes/footer.php");
?>