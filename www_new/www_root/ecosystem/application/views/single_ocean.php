<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); 
    
    $ocean_id = $ocean['id'];
    $title = strip_tags($ocean['title']);
    
    $image = "../../user_files/prof/" . $ocean['moderator_id'] . "/ecosystem/" . $ocean['eco_pic'];
    //$since = c_get_time_elapsed(strtotime($stream['added_on']));
    $details = strip_tags($ocean["description"]);
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

<div class="leftcont brdrnone bkgrnd">
    
    <!-- ocean detail --> 
    <span class="mainbanner"><img alt="Ocean" src="<?php echo $image; ?>" style="width: 100%;" /></span>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/drop.png" /></span>
            <div class="starheadmar">
                <h2><span class="star_head"><?php echo $title; ?></span></h2>
            </div>
            <div class="hoursin"><?php if($ocean['moderator_id'] != $user_id){ ?><a href="javascript:void(0);" class="mainsave mark-fuf" data-sid="<?php echo $ocean_id; ?>"><?php echo $follow_name; ?></a><?php } ?></div>
            <div class="hoursin marginrighrspc">
                
            </div>
            <p>&nbsp;</p>
            <div class="starheadmar mrgnleftnone mrgntopnone">
                <h2><span class="star_head">Moderator |</span> <a href="<?php echo DOC_ROOT.'member/'.$ocean['moderator_id']; ?>"><?php echo $moderator_name; ?></a></h2>
                <br /><br /><br />
            </div>
            <ul class="dropcontainer">
                <?php 
                    foreach($child_rivers as $river){
                        $title = strip_tags($river['title']);
                        $title = word_limiter($title, 7);
                        $str_bg_image = "/user_files/prof/" . $river['moderator_id'] . "/ecosystem/" . $river['eco_pic'];
                        $since = c_get_time_elapsed(strtotime($river['created_on']));
                        $detail = strip_tags($river["description"]);                                 
                        $detail = word_limiter($detail, 10);
                        
                        $user = $this->Mod_User->get_user($river['moderator_id']);
                        $uid = $river['moderator_id'];
                        $user_image = $user['data']['profile_pic'];
                        $single_river_url = base_url() . $config['single_river_url'] . '/' . $river['id'];
                        
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
                            <a href="<?php echo $single_river_url; ?>" class="yellow_btn">View</a>
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
                    <a class="bluebutton_drop post-com floatrght" href="javascript:void(0);" data-sid="<?php echo $ocean_id; ?>">Comment</a>
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
        
        $('.btn-invite').bind('click', function(){
            inviteForRiver(); 
        });
    });
    
    function mark_fuf(sid){
        
        if(!lock){
            lock = true;
            
            var url = "<?php echo base_url() . $config['single_river_url']; ?>/mark_fuf";
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
            
            var url = "<?php echo base_url() . $config['single_river_url']; ?>/post_comment";
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
            
            var url = "<?php echo base_url() . $config['single_river_url']; ?>/load_comments";
            var data = {lmcs: lmcs, sid: <?php echo $ocean_id; ?>};            
            
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
    
    var invite_lock = false;
    function inviteForRiver(){
        
        var other_strm = <?php echo $ocean_id; ?>;
        var my_strm = $('.ms-dd').val();
        
        console.log(other_strm + " - " + my_strm);
        if(my_strm < 1){
            alert("Select Stream.");
            return;
        }
        
        $(".loader").css("visibility", "visible");
        $(".btn-invite").css("display", "none");
        
        if(!invite_lock){
            invite_lock = true;            
            
            var url = "<?php echo base_url() . $config['single_river_url']; ?>/merge";
            var data = {o_strm: other_strm, m_strm: my_strm};
            
            processData(url, data, function(res){
                invite_lock = false;

                try{
                    res = JSON.parse(res);
                    
                    // check return status is true
                    if(res.status){
                        
                        $('.fancybox-close').trigger('click');
                        $('.invite-river').html('Invited').removeAttr('href').removeClass('fancybox').removeClass('invite-river');
                        $('#inline1').remove();               
                    }
                    
                    // something wrong
                    else{
                        $(".loader").css("visibility", "hidden");
                        $(".btn-invite").css("display", "inline-block");
                        alert(res.message);
                    }
                }
                catch(e){
                    metroAlert(e, {theme: metroStyle.ERROR});
                }
            }); 
        }
    }
    
</script>
