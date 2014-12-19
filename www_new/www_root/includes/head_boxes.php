<link rel="stylesheet" href="<?=DOC_ROOT?>assets/css/post_login.css" type="text/css" />

<div class="head_boxes">

<div class="searchhead">
    <form name="search_form" id="search_form" action="<?php echo base_url(); ?>search" method="post">
        <div class="seartxt">Search</div>    
        <div class="searinpt"><input name="sk" type="text" /></div>
        <div class="seargo"><a href="javascript:search_form.submit();">Go</a></div>
    </form>
</div>

<?php /*<div class="usericons"></div>*/ ?>

<div id="nav">
<ul>
    <!-- Messages -->
    <li>
        <?php
        if(function_exists('time_elapsed_string')!=true){
            include_once('../includes/func_time.php');
        }

        #/ Get list of messages to display
        $msgs_count = 0;
        $my_msgs = array();
        ?>

        <div class="iconsusrnav">
            <a href="#_">
            <img src="<?=DOC_ROOT?>ecosystem/assets/images/message.jpg" /></a>
            <?php if($msgs_count>0){ ?>
            <span class="posabt" id="msgs_count" style=""><?=$msgs_count?></span>
            <?php } ?>
        </div>

        <ul class="marginulin marginuliniph pad_1 nav_1">
            <li class="title width_390"><h2>Messages ::</h2></li>

            <?php
            if(is_array($my_msgs) && count($my_msgs)>0){
            } else {
            echo "<li class=\"width_390\">";
            echo "<i>you have no new messages / none found ..</i>";
            echo "</li>";
            }
            ?>

            <?php /* ?>
            <li class="brdrnonebot width_390">
                <div class="notsmall"><a href="#_"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></a></div>
                <div class="notsmallp"><strong><a href="#_">Collaborate: Christoph Zierz send you a request. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat</a></strong></div>
                <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>

            <li class="brdrnonebot width_390">
            <div class="notsmall"><a href="#_"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></a></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate: Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><a href="#_"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></a></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate: Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>

            <li class="width_390" style="background: #f4f6f8; padding:0px 0px 0 10px;">
                <a href="<?=DOC_ROOT?>ecosystem/messages" class="active">View More ...</a>
            </li>
            <?php #*/ ?>
        </ul>
    </li>



    <!-- Notifications -->
    <li>
        <?php
        #/ Get list of notifications to display
        if(function_exists('generate_notification')!=true){
        include_once('../includes/notif_func.php');
        }
        $notifs_count = (int)@count_notification($user_idc);
        $notifs = @read_notification($user_idc);
        //var_Dump("<pre>", $notifs); die();
        ?>

        <script>
        $(document).ready(function(){
            $.notifs_count = parseInt(<?=$notifs_count?>);

            $.process_notifi = function(notif_id)
            {
                if(typeof(notif_id)=='undefined'){return false;}

                if($('#notif_'+notif_id).hasClass('unread'))
                {
                    $('#notif_'+notif_id).removeClass('unread');

                    <?php #/ Mark as Read ?>
                    $.ajax({
                    cache: false,
                    type : 'Get',
                    dataType: 'text',
                    url: '<?=DOC_ROOT?>notif?ni='+notif_id,
                    }).done(function(msg){
                        if(msg=='1')
                        {
                            <?php #/ Change Count ?>
                            $.notifs_count--;
                            if($.notifs_count>0)
                            $('#notif_count').html($.notifs_count);
                            else
                            $('#notif_count').html('');
                        }
                    });
                }//end if unread....

                <?php #/ Take to Destination ?>
                var target = $('#notif_'+notif_id).attr('target');
                //alert(target);
                if(target!=''){
                location.href=target;
                return true;
                }
            }
        });
        </script>

        <div class="iconsusrnav">
            <a href="#_">
                <img src="<?=DOC_ROOT?>ecosystem/assets/images/ghanta.jpg" />
            </a>
            <?php if($notifs_count>0){ ?>
            <span class="posabt" id="notif_count" style="margin-left:-10px;"><?=$notifs_count?></span>
            <?php } ?>
        </div>

        <ul class="marginulin marginuliniph pad_1 nav_2">
            <li class="title width_390"><h2>Notifications ::</h2></li>

            <?php
            if(is_array($notifs) && count($notifs)>0){
            foreach($notifs as $notif_v)
            {
                if(empty($notif_v['notification'])){continue;}

                $notif_data = @$notif_v['notif_data'];
                $notif_data2 = @$notif_v['notification'];

                if(empty($notif_data)){continue;}
                if(!is_array($notif_data2) || !array_key_exists('notification', $notif_data2)){continue;}
                //var_dump("<pre>", $notif_data2); die();

                #/ Set Vars
                $is_read = '';
                if($notif_data['is_read']=='0'){
                $is_read = 'unread';
                }

                $target = '';
                if(strlen($notif_data['visit_url'])>3){
                $target = DOC_ROOT.$notif_data['visit_url'];
                }

                $img_v = '';
                if(!empty($notif_data['from_user_id']) && isset($notif_data2['users_info'][$notif_data['from_user_id']])){
                $img_vp = $notif_data2['users_info'][$notif_data['from_user_id']]['profile_pic'];
                $img_vp = @substr_replace($img_vp, '_th.', @strrpos($img_vp, '.'), 1);
                $img_v = DOC_ROOT."user_files/prof/{$notif_data['from_user_id']}/{$img_vp}";
                }

                $notif_dtx = '';
                if($notif_data['template_id']!='5'){ //hide time for certain templates
                $notif_dtx = @time_elapsed_string(@strtotime($notif_data['created_on'])).' -';
                }


                #/ Display notif
                echo "<li id=\"notif_{$notif_data['id']}\" class=\"brdrnonebot width_390 hand {$is_read}\"
                onclick=\"$.process_notifi('{$notif_data['id']}');\" target=\"{$target}\">";

                if(!empty($img_v))
                echo "<div class=\"notsmall\"><img src=\"{$img_v}\" /></div>";

                echo "<div class=\"notsmallp\">{$notif_data2['notification']}</div>";
                echo "<div class=\"notsmallp dtx\">{$notif_dtx}</div>";
                echo "</li>";
            }

            echo "<li class=\"width_390\" style=\"background: #f4f6f8; padding:0px 0px 0 10px;\">";
            echo "<a href=\"".DOC_ROOT."ecosystem/notification\" class=\"active\">View More ...</a>";
            echo "</li>";
            } else {
            echo "<li class=\"width_390\">";
            echo "<i>you have no new notifications / none found ..</i>";
            echo "</li>";
            }
            ?>

            <?php /* ?>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate : Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <li class="brdrnonebot width_390">
            <div class="notsmall"><img src="<?=DOC_ROOT?>assets/images/ep_th.png" /></div>
            <div class="notsmallp"><strong><a href="#_">Collaborate : Christoph Zierz send you a request.</a></strong></div>
            <div class="notsmallp dtx">Today at 9:05 am</div>
            </li>
            <?php #*/ ?>

        </ul>
    </li>


    <!-- Accounts -->
    <li>
        <div class="iconsusrnav">
            <a href="#_">
            <img src="<?=@$prf_pic_th?>" style="width:35px; height:35px;" class="round_borders" /></a>
        </div>

        <ul class="marginuliniph pad_2 width_300 nav_3">
            <?php #/* ?><li class="title"><h2>My Account ::</h2></li>
            <li class="brdrnonebot"><a href="<?=DOC_ROOT?>ecosystem/">My Eco-System</a></li>
            <li class="brdrnonebot"><a href="<?=DOC_ROOT?>member">My Profile</a></li>
            <li class="brdrnonebot"><a href="<?=DOC_ROOT?>update-password">Update Password</a></li><?php #*/ ?>
            <li><a href="<?=DOC_ROOT?>ecosystem/logout">Sign Out</a></li>
        </ul>
    </li>
</ul>
</div>
</div>