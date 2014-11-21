<?php
if(!isset($seo_tag_id) || empty($seo_tag_id)){redirect_me('404');}

include_once('../includes/model_home.php');

#/ get Page Info
$page_info = @mysql_exec("SELECT * FROM site_pages WHERE seo_tag_id='{$seo_tag_id}' AND is_active='1'", 'single');
if(!is_array($page_info)){redirect_me('404');}
//var_dump("<pre>", $seo_tag_id, $page_info); die();


$user_idc = (int)@$_SESSION['CUSA_Main_usr_id'];

#/ get needed site_media
$sm_array = array('free_membership_highlight');
$site_media = get_site_media($sm_array);
//var_dump("<pre>", $site_media); die();

#/ get site_misc_data
$smd = array('website_functions_copy', 'home_sliders', 'why_collaborate_copy', 'learn_functions', 'home_vide'); //'home_video' when live
$site_misc_data = get_site_misc_data($smd);
//var_dump("<pre>", $site_misc_data); die();

#/ Packages
$home_packages = false;
if($user_idc<=0){
$home_packages = get_home_packages();
//var_dump("<pre>", $home_packages); die();
}

/////////////////////////////////////////////////////////////////////

#/ Fill pg_meta
$pg_meta = array(
    //'page_title'=>format_str($page_info['title']),
    'meta_keywords'=>format_str($page_info['meta_keywords']),
    'meta_descr'=>format_str($page_info['meta_descr']),
);

$current_pgx = 'home';
$load_bx_slider = true;
$load_owl_carousel = true;
include_once("includes/header.php");
/////////////////////////////////////////////////////////////////////
?>

<script src="<?=DOC_ROOT?>assets/js/func_home.js"></script>

<?php ##------------------------------- Section 1 -------------------------------## ?>
<div class="website_fun">
<div class="container">
<div class="content_holder">

    <div class="website_functions">
        <?php
        if(is_array($site_misc_data) && array_key_exists('website_functions_copy', $site_misc_data))
        {
            echo "<h2>{$site_misc_data['website_functions_copy'][0]['title']}</h2>
            <p>{$site_misc_data['website_functions_copy'][0]['m_value']}</p>";
        }
        ?>
        <div class="circle_image">
        	<img src="<?=DOC_ROOT?>assets/images/website_functions.png"  usemap="#Map" border="0" />

            <map name="Map" id="Map">
                <area shape="poly" coords="124,269" href="#" />
                <area shape="poly" coords="76,320,42,284,26,258,47,226,80,230,110,268,78,283" href="#learn_about_fund" title="About FUND" class="fbox" />
                <area shape="poly" coords="19,237,12,213,11,176,11,161,43,146,71,170,76,215,40,212" href="#learn_about_refer" title="About REFER" class="fbox" />
                <area shape="poly" coords="17,146,24,121,37,96,52,75,87,80,98,114,84,136,78,153,48,131" href="#learn_about_learn" title="About Learn" class="fbox" />
                <area shape="poly" coords="66,59,83,44,106,33,128,22,154,45,149,78,128,89,107,105,96,67"  href="#learn_about_earn" title="About Earn" class="fbox" />
                <area shape="poly" coords="150,18,169,17,208,18,222,22,234,54,205,83,183,76,160,81,171,41" href="#learn_about_join" title="ABOUT JOIN (MEMBERSHIP)" class="fbox" />
                <area shape="poly" coords="221,87,247,63,238,28,264,41,284,56,295,66,302,75,291,105,259,116,224,89" href="#learn_about_share" title="About SHARE" class="fbox" />
                <area shape="poly" coords="269,126,273,141,283,162,282,170,315,184,341,161,329,121,320,99,312,88,303,120" href="#learn_about_voice" title="About VOICE" class="fbox" />
                <area shape="poly" coords="282,184,281,201,278,217,272,230,296,260,327,257,338,229,342,213,343,185,341,179,318,200" href="#learn_about_vote" title="About VOTE" class="fbox" />
                <area shape="poly" coords="266,244,255,258,233,276,237,314,266,331,299,301,319,271,288,275" href="#learn_about_buy" title="About BUY" class="fbox" />
                <area shape="poly" coords="222,284,203,290,178,293,160,324,176,353,207,354,229,345,251,336,225,323" href="#learn_about_sell" title="About SELL" class="fbox" />
                <area shape="poly" coords="163,291,146,286,128,279,121,275,87,293,87,329,112,342,127,348,160,352,146,322" href="#learn_about_give" title="About GIVE" class="fbox" />
            </map>
        </div>

        <?php
        if(is_array($site_misc_data) && array_key_exists('learn_functions', $site_misc_data))
        {
            foreach($site_misc_data['learn_functions'] as $lf_v)
            {
                echo "<div id='learn_{$lf_v['content_settings']}' class='popup_bdy' style='display:none; max-width:500px;'>
                <div class='header2 hdx'><div class='head_ttles'>{$lf_v['title']}</div></div>
                <div class='content'>{$lf_v['m_value']}</div>
                </div>
                ";
            }
        }
        ?>
    </div>


    <div class="why_collaborate">
        <?php
        if(is_array($site_misc_data) && array_key_exists('why_collaborate_copy', $site_misc_data))
        {
            echo "<h2>{$site_misc_data['why_collaborate_copy'][0]['title']}</h2>
            <p>{$site_misc_data['why_collaborate_copy'][0]['m_value']}</p>";
        }
        ?>

        <div class="the_personality">
        <?php if(is_array($site_misc_data) && array_key_exists('home_video', $site_misc_data)) { ?>
    	<div class="video_thumb">
        	<div class="video_display" style="height: 326px; width:385px;">
            <iframe width="385" height="326" src="<?php echo $site_misc_data['home_video'][0]['m_value']; ?>" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
        <?php } ?>
        </div>
    </div>

</div>
<div class="clear"></div>
</div>
</div>


<?php ##------------------------------- Section 2 -------------------------------## ?>
<div class="container">
<div class="your_opinion">
    <h2>Your Ideas and Opinion Matters<br />Express Your VOICE<br />VOTE on What You Care About</h2>

    <div class="wwf_boxes">
    <ul class="bxslider2">

    </ul>
    </div>
</div>
</div>


<?php ##------------------------------- Section 3 = Packages -------------------------------## ?>
<?php if(is_array($home_packages) && isset($home_packages[0]) && count($home_packages[0])>0) { ?>
<div class="am_pack">
    <div class="cont">
        <div class="amhead">We Are Crowd-Sourcing Your Ideas!</div>

        <?php
        $i = 0;
        //for($ij=0; $ij<=13; $ij++){ //debug
        foreach($home_packages[0] as $hp_k=>$hp_v)
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
                        if(array_key_exists(1, $home_packages)
                        && is_array($home_packages[1])
                        && array_key_exists($hp_v['mp_id'], $home_packages[1])
                        && is_array($home_packages[1][$hp_v['mp_id']])
                        && count($home_packages[1][$hp_v['mp_id']])>0 )
                        {
                            echo "<li class=\"backgr\">Your Benefits:</li>";

                            $hp_servs = $home_packages[1][$hp_v['mp_id']];
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

<?php
include_once("includes/footer.php");
?>