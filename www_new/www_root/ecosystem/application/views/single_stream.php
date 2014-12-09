<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); 
    
    $stream_id = $stream['id'];
    $title = strip_tags($stream['question_text']);
    
    $image = "../../user_files/prof/" . $stream['user_id'] . "/voices/" . $stream['voice_pic'];
    //$since = c_get_time_elapsed(strtotime($stream['added_on']));
    $detail = strip_tags($stream["voice_details"]);
    $detail = make_url_to_link($detail);
    $detail = nl2br($detail);
    $detail = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detail);
 
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
    
    <!-- stream detail --> 
    <span class="mainbanner"><img alt="My Stream" src="<?php echo $image; ?>" style="width: 100%;" /></span>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/drop.png" /></span>
            <div class="starheadmar">
                <h2><span class="star_head"><?php echo $title; ?></span></h2>
            </div>
            <div class="hoursin"><a href="javascript:void(0);" class="mainsave mark-fuf" data-sid="<?php echo $stream_id; ?>"><?php echo $follow_name; ?></a></div>
            <div class="hoursin marginrighrspc"><a href="javascript:void(0);" class="mainsave">Invite</a></div>
            <p>&nbsp;</p>
            <div class="starheadmar mrgnleftnone mrgntopnone">
                <h2><span class="star_head">Moderator |</span> <a href="about_me.php"><?php echo $moderator_name; ?></a></h2>
                <br /><br /><br />
            </div>
            <p><?php echo $detail; ?></p>
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
                
                <!--
                <div class="leftine">
                    <div class="comm">&nbsp;</div>
                    <div class="commhead">&nbsp;</div>
                    <div class="smalluserimg"><img alt="" src="<?php echo c_get_assets_url(); ?>images/usercomment.jpg" /></div>
                    <div class="smallusertxt"> <span class="star_headdrop">Labinot Bytyqi</span>
                        <p>The Genius of Wearing the Same Outfit Every Day</p>
                        <a href="#">5 days</a>
                    </div>
                </div>
               -->
            </div>
            <div class="leftine">
                <form>
                    <input type="text" name="" class="inptcomm" placeholder="Add a Comment" />
                    <a class="bluebutton_drop post-com" href="javascript:void(0);" data-sid="<?php echo $stream_id; ?>">Comments</a>
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

<script type="text/javascript">

    var lock = false;
    
    $(document).ready(function(){
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
            if($('.inptcomm').val() != ''){
                $('.pc-loader').css('display', 'inline-block');
                postComment($('.inptcomm').val(), $(this).attr('data-sid'));
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
            
            var url = "<?php echo base_url() . $config['single_stream_url']; ?>/mark_fuf";
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
            $('.inptcomm').val('');
            
            var url = "<?php echo base_url() . $config['single_stream_url']; ?>/post_comment";
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
                                <div class='smalluserimg'><img alt='' src='../../user_files/prof/<?php echo $user_id; ?>/" + res.data.user.profile_pic + "' /></div> \
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
            
            var url = "<?php echo base_url() . $config['single_stream_url']; ?>/load_comments";
            var data = {lmcs: lmcs, sid: <?php echo $stream_id; ?>};            
            
            processData(url, data, function(res){
                lmc_lock = false;
                                
                try{
                    res = JSON.parse(res);
                    
                    // check return status is true
                    if(res.status){
                        
                        // check data is available on response
                        if(res.is_data === true){                            
                            $('.c-loader').css('display', 'none');
                            $(".lbl-smc").css("display", "inline-block");
                            $('#comments_data').prepend(res.data);
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