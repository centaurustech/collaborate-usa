<?php
$seo_tag_id = 11; //home
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

////////////////////////////## Check Login
if(isset($_SESSION['CUSA_Main_usr_id']) && ($_SESSION['CUSA_Main_usr_id']>0))
{
    echo "<script language=\"javascript\">location.href='{$consts['DOC_ROOT']}home';</script>";
    exit;
}
////////////////////##--

#/ get Page Info - inherit from Home Page
$page_info = @mysql_exec("SELECT * FROM site_pages WHERE seo_tag_id='{$seo_tag_id}' AND is_active='1'", 'single');
if(!is_array($page_info)){redirect_me('404');}
//var_dump("<pre>", $page_info); die();

/////////////////////////////////////////////////////////////////////

include_once('../includes/model_package.php');


#/ Packages
$all_packages = get_all_packages();
//var_dump("<pre>", $all_packages); die();

#/ get needed site_media
$sm_array = array('free_membership_highlight');
$site_media = get_site_media($sm_array);
//var_dump("<pre>", $site_media); die();
/////////////////////////////////////////////////////////////////////

#/ Manual change
$page_info['title'] = 'Packages';

#/ Fill pg_meta
$pg_meta = array(
    'page_title'=>format_str($page_info['title']),
    'meta_keywords'=>format_str($page_info['meta_keywords']),
    'meta_descr'=>format_str($page_info['meta_descr']),
);
$page_heading = "Membership Packages";

include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<div class="website_fun">
<div class="container middle_content">
<div class="content_holder">

<div class="mid_bdy body-main" style="padding-top:30px;">

    <h1><strong><?=format_str($pg_meta['page_title'])?></strong></h1>

    <div>
    <br />
    <?php if(is_array($all_packages) && isset($all_packages[0]) && count($all_packages[0])>0) { ?>
    <div class="am_pack" style="border-radius: 5px;">
        <div class="cont" style="width: 100%;">
            <div class="amhead">We Are Crowd-Sourcing Your Ideas!</div>

            <?php
            $i = 0;
            //for($ij=0; $ij<=13; $ij++){ //debug
            foreach($all_packages[0] as $hp_k=>$hp_v)
            {
                //var_dump('<pre>', $hp_v); die();
                if(!is_array($hp_v)) continue;

                echo "<div class=\"ampckleft\">";

                $clsx='yellbro'; if(($i%2)!=0){$clsx='yellgre';}


                #/ Set title
                $cost = (float)$hp_v['cost'];
                $cost_dis = number_format($cost, 2);

                $ttle_x=''; if($cost<=0) {$ttle_x='Free';}else{
                $ttle_x="\${$cost}";
                if($hp_v['is_recursive']=='0'){$ttle_x.=" One Time";}else{$ttle_x.=" Periodic";}
                }

                echo "<div class=\"pckhead {$clsx}\">{$ttle_x}</div>";

                    echo "<div class=\"centrcont\">";

                        echo "<div class=\"centrhead\">{$hp_v['title']}</div>";
                        echo "<ul class=\"ampckul\">";

                            #/ Print Services
                            if(array_key_exists(1, $all_packages)
                            && is_array($all_packages[1])
                            && array_key_exists($hp_v['mp_id'], $all_packages[1])
                            && is_array($all_packages[1][$hp_v['mp_id']])
                            && count($all_packages[1][$hp_v['mp_id']])>0 )
                            {
                                echo "<li class=\"backgr\">Your Benefits:</li>";

                                $hp_servs = $all_packages[1][$hp_v['mp_id']];
                                $j = 0;
                                foreach($hp_servs as $hp_ser_k=>$hp_ser_v)
                                {
                                    $clsx2=''; if(($j%2)==0){$clsx2='graym';}

                                    $hp_ser_v_title = str_replace(array('[b]', '[/b]'), array('<span>', '</span>'), $hp_ser_v['title']);
                                    echo "<li class=\"{$clsx2}\">{$hp_ser_v_title}</li>";

                                    $j++;
                                }//end foreach..

                                $hp_v_intro_copy = str_replace(array('[b]', '[/b]'), array('<span>', '</span>'), $hp_v['intro_copy']);
                                $hp_v_intro_copy = nl2br($hp_v_intro_copy);
                                echo "<li class=\"backgr\">{$hp_v_intro_copy}</li>";
                            }
                        echo "</ul>";


                        #/ Special FREE membership image
                        if(($cost<=0) && (is_array($site_media) && array_key_exists('free_membership_highlight', $site_media))){
                        echo "<div class=\"freeimg\"><img
                        src=\"{$consts['DOC_ROOT']}assets/images_2/media/{$site_media['free_membership_highlight'][0]['m_file']}\"
                        title=\"{$site_media['free_membership_highlight'][0]['alt_text']}\" alt=\"{$site_media['free_membership_highlight'][0]['alt_text']}\" /></div>";
                        }

                        #/ Signup button
                        $cls_btn='yellow_btn'; if(($i%2)!=0){$cls_btn='green_btn';}
                        echo "<a href=\"{$consts['DOC_ROOT']}signup/{$hp_v['mp_id']}\" class=\"signup {$clsx} {$cls_btn}\">Signup</a>";

                    echo "</div>";
                echo "</div>";

                $i++;

            }//end foreach...
            //}
            ?>
        </div>

        <div style="clear: both;"></div><br />
        <div class="two_btns">
            <a class="white_btn" href="<?=DOC_ROOT?>learnmore">Learn More</a>
            <a class="white_btn" href="<?=DOC_ROOT?>signin">Sign In</a>
        </div>
        <div style="clear: both;"></div><br />
    </div>
    <div class="clear"></div>
    <?php } ?>
    </div>

</div>

</div>
<div class="clear"></div>
</div>
</div>

<?php
include_once("includes/footer.php");
?>