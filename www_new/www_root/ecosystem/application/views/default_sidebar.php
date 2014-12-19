<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="rightcont">
    <div class="headblack">Voices You Might Like To <br />HEAR</div>
    <ul class="headlarge">
        <?php
            foreach($recent_users as $rec_user){                
                $profile_image = get_profile_pic($rec_user['id'], $rec_user['profile_pic']);
                
                
        ?>
        <li><a href="<?php echo DOC_ROOT.'member/'.$rec_user['id']; ?>"><img class="mrgntop" src="<?php echo $profile_image; ?>" alt="" style="width: 57px; height: 57px; border-radius: 100%;" /></a></li>
        <?php } ?>
    </ul>
    <div class="brdrall"></div>
    
    <!--
    <div class="headblack mrgntopnone">Collaborate with these <br />Members</div>
    <ul class="headlarge">
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
        <li><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/img_right.jpg" alt="" /></a></li>
    </ul>
    <div class="brdrall"></div>
    -->
    
    
    
    <?php if($is_or_stream){
        $os_title = strip_tags($or_stream['question_text']);
        $os_title = word_limiter($os_title, 7);
        $os_str_bg_image = "/user_files/prof/" . $or_stream['user_id'] . "/voices/" . $or_stream['voice_pic'];
        $os_since = c_get_time_elapsed(strtotime($or_stream['added_on']));
        $os_detail = strip_tags($or_stream["voice_details"]);                                 
        $os_detail = word_limiter($os_detail, 10);
        
        $user = $this->Mod_User->get_user($or_stream['user_id']);                
        $os_single_stream_url = base_url() . $config['single_stream_url'] . '/' . $or_stream['id'];
        
        $os_user_image = get_profile_pic($user['data']['id'], $user['data']['profile_pic']);
          
    ?>
    <div class="headblack mrgntopnone">Streams you might<br /> want to JOIN</div>    
    <div class="wwf_the_outer rightpan" title="" style="background: url(<?php echo $os_str_bg_image; ?>) no-repeat scroll 0 0 / 100% 100px rgba(0, 0, 0, 0)">
        <div class="image_wwf"><img src="<?php echo $os_user_image; ?>" alt="" style="width: 65px; height: 65px;" /></div>
        <h4><?php echo $os_title; ?></h4>
        <p style="padding:0px;"><?php echo $os_detail; ?></p>
        <a class="yellow_btn" href="<?php echo $os_single_stream_url; ?>">View</a>
        <p><?php echo $os_since; ?></p>
    </div>
    <div class="brdrall"></div>
    <?php } ?>
    
    <?php if($is_or_river){
        $or_title = strip_tags($or_river['title']);
        $or_title = word_limiter($or_title, 7);
        $or_str_bg_image = "/user_files/prof/" . $or_river['moderator_id'] . "/ecosystem/" . $or_river['eco_pic'];
        $or_since = c_get_time_elapsed(strtotime($or_river['created_on']));
        $or_detail = strip_tags($or_river["description"]);                                 
        $or_detail = word_limiter($or_detail, 10);
        
        $user = $this->Mod_User->get_user($or_river['moderator_id']);
        $or_single_river_url = base_url() . $config['single_river_url'] . '/' . $or_river['id'];
                        
        $or_user_image = get_profile_pic($user['data']['id'], $user['data']['profile_pic']);
    ?>
    <div class="headblack mrgntopnone">Rivers you might want<br /> to Follow</div>
    <div class="wwf_the_outer rightpan" title="" style="background: url(<?php echo $or_str_bg_image; ?>) no-repeat scroll 0 0 / 100% 100px rgba(0, 0, 0, 0)">
        <div class="image_wwf"><img src="<?php echo $or_user_image; ?>" alt="" style="width: 65px; height: 65px;" /></div>
        <h4><?php echo $or_title; ?></h4>
        <p style="padding:0px;"><?php echo $or_detail; ?></p>
        <a class="yellow_btn" href="<?php echo $or_single_river_url; ?>">View</a>
        <p><?php echo $or_since; ?></p>
    </div>
    <div class="brdrall"></div>
    <?php } ?>
    
    <div class="headblack mrgntopnone">PREMIUM ADS</div>
    <div class="headlarge bkgrnd"><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/ad1.jpg" alt="" /></a></div>
    <div class="headsky"><a href="#"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/ad2.jpg" alt="" /></a></div>
</div>