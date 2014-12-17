<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); 
    
    $river_id = $river['id'];
    $title = strip_tags($river['title']);
    
    $image = "../../user_files/prof/" . $river['moderator_id'] . "/ecosystem/" . $river['eco_pic'];
    //$since = c_get_time_elapsed(strtotime($stream['added_on']));
    $details = strip_tags($river["description"]);
    $details = make_url_to_link($details);
    $details = nl2br($details);
    $details = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $details);
 
    $cat_name = $category['category'];
    
    $tag = "";
    for($i = 0; $i < count($tags); $i++){
        $tag .= "<span class='bluebutton_drop' style='cursor: default;'>{$tags[$i]}</span>&nbsp;&nbsp;&nbsp;";
    }
    
    $moderator_name = (isset($moderator['name'])) ? $moderator['name'] : 'Unknown user';
    
    $follow_name = ($is_follower) ? "Unfollow" : "Follow";
    $follow_class = ($follow_name) ? "mark-unfollow" : 'mark-follow';
    
?>

<!-- invite box -->
<div id="inline1" style="width:100%;display: none; overflow:hidden;">
    <div class="persevnfif mrgnleftnone">
        <h4>Please select which river you would<br /> like to merge with your river:</h4>
        <div class="wdthhundr mrgnleftnone">
            <label class="lefttws perthir mrgntop"><strong>My Rivers</strong></label>
            <span class="leftsinptres persevntwtw mrgntop"><?php echo $title; ?></span>
            <label class="lefttws perthir mrgntop"><strong>River Merged</strong></label>
            <select class="mrgnleftnone persevntwtw monthaja mrgntop ms-dd" name="My Rivers">
                <option value="0">Select River</option>
                <?php foreach($my_rivers as $my_river){ ?>
                    <option value="<?php echo $my_river['id']; ?>"><?php echo $my_river['title']; ?></option>
                <?php } ?>
            </select>
                <div class="cont_700_inner">
                <label class="lefttws"><img src="<?php echo c_get_assets_url(); ?>images/voice_loader.gif" class="loader" style="visibility: hidden; float: right; margin-right: -45px; margin-top: 9px;" /></label>                
                <span class="leftsinptshow leftsinptres">                    
                    <a href="javascript:void(0);" class="mainsave floatlft btn-invite">Invite</a>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="leftcont brdrnone bkgrnd">
    
    <!-- river detail --> 
    <span class="mainbanner"><img alt="River" src="<?php echo $image; ?>" style="width: 100%;" /></span>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/drop.png" /></span>
            <div class="starheadmar">
                <h2><span class="star_head"><?php echo $title; ?></span></h2>
            </div>
            <div class="hoursin"><?php if($river['moderator_id'] != $user_id){ ?><a href="javascript:void(0);" class="mainsave mark-fuf" data-sid="<?php echo $river_id; ?>"><?php echo $follow_name; ?></a><?php } ?></div>
            <div class="hoursin marginrighrspc">
                 <?php if($has_ocean){ ?>
                    <a href="<?php echo base_url() . $config['single_ocean_url'] . '/' . $has_ocean['id']; ?>" class="mainsave">Goto Ocean</a>
                <?php } else if($river['moderator_id'] != $user_id){ ?>
                    <?php if($is_possible_to_create_ocean){ ?>
                        <a href="<?php echo base_url() . $config['river_ocean_url'] . '/' . $is_possible_to_create_ocean['verification_str']; ?>" class="mainsave ">Create Ocean</a> 
                    <?php }else if($is_possible_to_join){ ?>
                        <a href="#inline1" class="mainsave fancybox invite-ocean">Invite Ocean</a> 
                    <?php }else{ ?>
                         <a class="mainsave">Invited</a>
                    <?php } ?>
                <?php } ?>
            </div>
            <p>&nbsp;</p>
            <div class="starheadmar mrgnleftnone mrgntopnone">
                <h2><span class="star_head">Moderator |</span> <a href="<?php echo DOC_ROOT.'member/'.$river['moderator_id']; ?>"><?php echo $moderator_name; ?></a></h2>
                <br /><br /><br />
            </div>
            <ul class="dropcontainer">
                <?php 
                    foreach($child_streams as $stream){
                        $title = strip_tags($stream['question_text']);
                        $title = word_limiter($title, 7);
                        $str_bg_image = "/user_files/prof/" . $stream['user_id'] . "/voices/" . $stream['voice_pic'];
                        $since = c_get_time_elapsed(strtotime($stream['added_on']));
                        $detail = strip_tags($stream["voice_details"]);                                 
                        $detail = word_limiter($detail, 10);
                        
                        $user = $this->Mod_User->get_user($stream['user_id']);
                        $uid = $stream['user_id'];
                        $user_image = $user['data']['profile_pic'];
                        $single_stream_url = base_url() . $config['single_stream_url'] . '/' . $stream['id'];
                           
                        $host = str_replace('ecosystem/', '', base_url());
                        $user_image = "{$host}user_files/prof/{$uid}/{$user_image}";
                        
                        if(!remote_file_exists($user_image)){
                            $user_image = "/assets/images/ep.png";
                        }
                ?>
                    <li>
                        <div class="wwf_the_outer" style="background: url(<?php echo $str_bg_image; ?>) no-repeat scroll 0 0 / 100% 100px rgba(0, 0, 0, 0)">
                            <div class="image_wwf"><img src="<?php echo $user_image; ?>" alt="" /></div>
                            <h4><?php echo $title; ?></h4>
                            <p><?php echo $detail; ?></p>
                            <a href="<?php echo $single_stream_url; ?>" class="yellow_btn">View</a>
                            <p><?php echo $since; ?></p>
                        </div>
                    </li>
                <?php } ?>        
            </ul>
            <p><?php echo $details; ?></p>
            <p> <strong>Category</strong> : <?php echo $cat_name; ?><br />
                <?php if($tag != ''){ ?><strong>Tags</strong> : <?php echo $tag; ?> <?php } ?> 
            </p>
        </div>
        <div class="commentssection">
            <div class="leftine mcd">
                <div class="comm"><img src="<?php echo c_get_assets_url(); ?>images/comments.png" alt="" /></div>
                <div class="commhead">
                    <span class="lbl-smc" style="display: none; cursor: pointer;">Show More Comments</span>
                    <img src="<?php echo c_get_assets_url(); ?>images/voice_loader.gif" class="c-loader" style="display: inline-block;" />
                </div>                
            </div>
            <div id="comments_data">
                
            </div>
            <div class="leftine">
                <form>                
                    <textarea name="" cols="1" rows="2" placeholder="Add a Comment" class="leftstxtarea wdthnin comment-field"></textarea>                                                            
                    <a class="bluebutton_drop post-com floatrght" href="javascript:void(0);" data-sid="<?php echo $river_id; ?>">Comment</a>
                    &nbsp;&nbsp;
                    <img src="<?php echo c_get_assets_url(); ?>images/voice_loader.gif" class="pc-loader" style="display: none;" />
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Right Section --> 
<?php echo $sidebar; ?>
</div>

<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="<?php echo c_get_assets_url(); ?>js/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="<?php echo c_get_assets_url(); ?>css/jquery.fancybox.css?v=2.1.5" media="screen" />

<script type="text/javascript">

    var lock = false;
    
    $(document).ready(function(){
        
        $('.fancybox').fancybox();
        
        $('.mark-fuf').bind('click', function(){
            if($(this).html() == 'Follow'){
                $(this).html('Unfollow');                     
            }
            else{
                $(this).html('Follow');
            }
            mark_fuf($(this).attr('data-sid'));
        });
        
        // post comment
        $('.post-com').bind('click', function(){
            if($('.comment-field').val() != ''){
                $('.pc-loader').css('display', 'inline-block');
                postComment($('.comment-field').val(), $(this).attr('data-sid'));
            }
        });
        
        // loading comments
        loadMoreComments(lmc_start);
        
        $('.lbl-smc').bind('click', function(){
            $(".lbl-smc").css("display", "none");
            $(".c-loader").css("display", "inline-block"); 
            loadMoreComments(++lmc_start);
        });
        
    });
    
    function mark_fuf(sid){
        
        if(!lock){
            lock = true;
            
            var url = "<?php echo base_url() . $config['single_ocean_url']; ?>/mark_fuf";
            var data = {sid: sid};
            
            processData(url, data, function(res){
                lock = false;
                
                try{
                    res = JSON.parse(res);
                    
                    // check return status is false
                    if(! res.status){
                        metroAlert(res.message, {theme: metroStyle.ERROR});
                    }
                }
                catch(e){
                    metroAlert(e, {theme: metroStyle.ERROR});
                }
                
            });
        }
    }
    
    var com_lock = false;
    function postComment(com, sid){
        if(!com_lock){
            com_lock = true;
            $('.comment-field').val('');
            
            var url = "<?php echo base_url() . $config['single_ocean_url']; ?>/post_comment";
            var data = {com: com, sid: sid};            
            
            processData(url, data, function(res){
                com_lock = false;
                $('.pc-loader').css('display', 'none');                
                
                try{
                    res = JSON.parse(res);
                    
                    // check return status is true
                    if(res.status){
                        var comment = "<div class='leftine'> \
                            <div class='comm'>&nbsp;</div> \
                                <div class='commhead'>&nbsp;</div> \
                                <div class='smalluserimg'><img alt='' src='" + res.data.user.profile_pic + "' /></div> \
                                <div class='smallusertxt'> <span class='star_headdrop'>" + res.data.user.name + "</span> \
                                    <p>" + res.data.comment_data.comment + "</p> \
                                    <span style='color: #646464; float: left; font-size: 13px'>" + res.data.comment_data.since + "</span> \
                                </div> \
                            </div>";
                        
                        $('#comments_data').append(comment);
                    }
                    
                    // something wrong
                    else{
                        metroAlert(res.message, {theme: metroStyle.ERROR});
                    }
                }
                catch(e){
                    metroAlert(e, {theme: metroStyle.ERROR});
                }                
            });   
        }
    }
    
    var lmc_lock = false;
    var lmc_start = 0;
    function loadMoreComments(lmcs){
        if(!lmc_lock){
            lmc_lock = true;            
            
            var url = "<?php echo base_url() . $config['single_ocean_url']; ?>/load_comments";
            var data = {lmcs: lmcs, sid: <?php echo $river_id; ?>};            
            
            processData(url, data, function(res){
                lmc_lock = false;
                                
                try{
                    res = JSON.parse(res);
                    
                    // check return status is true
                    if(res.status){
                        
                        // check data is available on response
                        if(res.is_data === true){                            
                            $('.c-loader').css('display', 'none');                            
                            $('#comments_data').prepend(res.data);
                        }
                        
                        if(res.is_more_data === true){
                            $(".lbl-smc").css("display", "inline-block");
                        }
                        
                        // no data is available
                        else{
                            $(".mcd").css("display", "none");
                        }
                    }
                    
                    // something wrong
                    else{
                        $(".c-loader").css("display", "none");
                        $(".lbl-smc").css("display", "inline-block");
                        metroAlert(res.message, {theme: metroStyle.ERROR});
                    }
                }
                catch(e){
                    metroAlert(e, {theme: metroStyle.ERROR});
                }
            }); 
        }
    }

    
</script>
