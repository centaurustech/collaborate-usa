<?php if(!isset($no_header)){ ?>
    <div class="clear"></div>

    <?php
    if(!isset($site_contact_info)){
    $site_contact_info = @cb89(@mysql_exec("SELECT * FROM site_contact_info"), 'c_key');
    }

    $foot_menu = get_dynamic_menu(true);
    //var_dump("<pre>", $foot_menu);

    $terms = array();
    $_SESSION['global_vars']['terms'] = $terms; //due to CI..
    ?>

    <div class="footer">
    <div class="container ftr_container">
        <div class="eft_nav_footer">

            <?php
            if(is_array($foot_menu) && count($foot_menu)>0){
            function print_foot_links($foot_menu, $level=0)
            {
                global $consts, $terms;

                $fi = 0;
                $child_less = array();
                if(is_array($foot_menu) && count($foot_menu)>0) {
                foreach($foot_menu as $fm_v)
                {
                    if(@in_array($fm_v['id'], array('pg_1', 'pg_2', 'pg_4'))){ //remove TOS, Privacy, IS
                    $terms[] = $fm_v;
                    $_SESSION['global_vars']['terms'] = $terms;
                    //$fi++;
                    //continue;
                    }
                    else
                    {
                        $fm_href='#_'; if($fm_v['seo_tag']!='')$fm_href="{$consts['DOC_ROOT']}{$fm_v['seo_tag']}";

                        $e_cho = "<div class='quick_links'>";

                            if($fm_href=='#_')
                            $e_cho.= "<h5>{$fm_v['title']}</h5>";
                            else
                            $e_cho.= "<h5><a href=\"{$fm_href}\">{$fm_v['title']}</a></h5>";

                            $e_cho.= "<ul>";
                            $has_child = false;
                            foreach($fm_v as $fm_k2=>$fm_v2)
                            {
                                if(!is_numeric($fm_k2) && (stristr($fm_k2, 'pg_')==false)){continue;}
                                if(($fm_v['title']=='Misc.') && ($fm_v2['seo_tag']=='')) continue;

                                $fm_href2='#_'; if(@$fm_v2['seo_tag']!='')$fm_href2="{$consts['DOC_ROOT']}{$fm_v2['seo_tag']}";
                                $fm_cls2 = ''; if(@$fm_v2['popup_only']=='1'){$fm_cls2="fbox fancybox.ajax";} //if($fm_href2!='#_')$fm_href2.="/?ro=1";

                                $e_cho.= "<li><a href=\"{$fm_href2}\" class=\"{$fm_cls2}\" title=\"{$fm_v2['title']}\">{$fm_v2['title']}</a></li>";

                                $has_child = true;
                            }
                            $e_cho.= "</ul>";

                        $e_cho.= "</div>";

                        //var_dump($e_cho); die();

                        if($level>0){
                        echo $e_cho;
                        continue;
                        }

                        if($has_child == false)
                        $child_less[] = $fm_v;
                        else
                        echo $e_cho;
                    }

                    $fi++;

                    //echo $fi.'='.count($foot_menu).'; ';
                    if($fi==count($foot_menu) && count($child_less)>0)
                    {
                        //var_dump($child_less);
                        $t1 = array(0=> array_merge($child_less, array('title'=>'Misc.', 'seo_tag'=>'')));
                        print_foot_links($t1, 1);
                    }

                }//end foreach..
                }

            }//end func.
            print_foot_links($foot_menu);
            }
            ?>

            <div class="quick_links">
            	<h5>User Account</h5>
                <ul>
                	<?php if((isset($user_idc)) && ($user_idc<=0)) { ?>
                    <li><a href="<?=DOC_ROOT?>signin">Sign-In to your Account</a></li><li>
                    <a href="<?=DOC_ROOT?>join">Join / Signup</a></li>
                    <?php } else { ?>
                    <a href="<?=DOC_ROOT?>ecosystem/logout">Signout</a>
                    <?php } ?>
                </ul>
            </div>

            <?php
            if(is_array($site_contact_info) && count($site_contact_info)>0){
            ?>
            <div class="social_links">
        	<h5>Get Connected</h5>
            <ul>
            	<li>
                	<a href="http://www.facebook.com/<?php echo @$site_contact_info['facebook']['c_value']; ?>" target="_blank"><img src="<?=DOC_ROOT?>assets/images/icon_fb.png" /></a>
                	<a href="http://www.twitter.com/<?php echo @$site_contact_info['twitter']['c_value']; ?>" target="_blank"><img src="<?=DOC_ROOT?>assets/images/icon_tw.png" /></a>
                	<a href="http://www.linkedin.com/in/<?php echo @$site_contact_info['linkedin']['c_value']; ?>" target="_blank"><img src="<?=DOC_ROOT?>assets/images/icon_li.png" /></a>
                </li>
            </ul>
            </div>
            <?php } ?>

        </div>
        <div class="clear"></div>


        <div class="terms">
        <?php
        $terms = @$_SESSION['global_vars']['terms'];
        if(is_array($terms) && count($terms)>0)
        {
            if(!isset($consts)){global $consts;} //due to CI..
            echo "<ul>";

            $tms_ar = array();
            foreach($terms as $tm_v)
            {
                $tm_href='#_'; if(@$tm_v['seo_tag']!='')$tm_href="{$consts['DOC_ROOT']}{$tm_v['seo_tag']}";
                $tm_cls = ''; if(@$tm_v['popup_only']=='1'){$tm_cls="fbox fancybox.ajax";} //if($tm_href!='#_')$tm_href.="/?ro=1";

                $tms_ar[] = "<li><a href=\"{$tm_href}\" class=\"{$tm_cls}\" title=\"{$tm_v['title']}\">{$tm_v['title']}</a></li>";
            }//end foreach..
            if(count($tms_ar)>0)echo implode('', $tms_ar);

            echo "</ul>";
        }
        ?>
        </div>

        <div class="copyright"><p>Copyright text &copy; <?=@date('Y');?> CollaborateUSA, LLC - All Rights Reserved</p></div>

        <?php if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))==false){ ?>
        <div style="text-align: center; margin-top:10px;">
            <span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=yM6Js7lyszgZ0mAs3ydPwr6ye43ODlOsJtV4zqwbozdgxJNZHGUvpwL"></script></span>
        </div>
        <?php } ?>

        <div class="clear"></div>
    </div>
    </div>
<?php } ?>
</body>
</html>