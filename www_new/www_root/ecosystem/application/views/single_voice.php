<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); 
    
    $voice_id = $voice['id'];
    $title = strip_tags($voice['question_text']);
    
    $image = "../../user_files/prof/" . $voice['user_id'] . "/voices/" . $voice['voice_pic'];
    //$since = c_get_time_elapsed(strtotime($voice['added_on']));
    $detail = strip_tags($voice["voice_details"]);
    $detail = make_url_to_link($detail);
    $detail = nl2br($detail);
    $detail = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $detail);
 
    $cat_name = $category['category'];
    
    $tag = "";
    for($i = 0; $i < count($tags); $i++){
        $tag .= "<span class='bluebutton_drop' style='cursor: default;'>{$tags[$i]}</span>&nbsp;&nbsp;&nbsp;";
    }
      
?>

<div class="leftcont brdrnone bkgrnd">
    
    <!-- voice detail --> 
    <span class="mainbanner"><img alt="My Voice" src="<?php echo $image; ?>" style="width: 100%;" /></span>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/voice.png" /></span>
            <div class="starheadmar">
                <h2><span class="star_head"><a href="#"><?php echo $title; ?></a></span></h2>
            </div>
            <div class="hourseye">
                
                    <div class="eyeimg"><img style="cursor:pointer" onclick="show_hide_div('t1');" alt="" src="<?php echo c_get_assets_url(); ?>images/drop_color.png" /></div>
                    <div class="eyetxt"><?php echo $total_vote_up; ?></div>
                
                    <div class="eyeimg"><img style="cursor:pointer" onclick="show_hide_div('t2');" alt="" src="<?php echo c_get_assets_url(); ?>images/drop_wtcolor.png" /></div>
                    <div class="eyetxt"><?php echo $total_vote_down; ?></div>
                
            </div>
            <p><?php echo $detail; ?></p>
            <p> 
                <strong>Category</strong> : <?php echo $cat_name; ?><br />
                <?php if($tag != ''){ ?><strong>Tags</strong> : <?php echo $tag; ?> <?php } ?> 
            </p>
        </div>
        
        <?php if($user_vote_cast['status'] == true){ ?>
        
        <!-- voting area -->
        <div class="brdrgratop">
            <div class="container_705 margnten">
                <div class="radio leftsinpthide vudc">
                    <input type="radio" id="male" class="vote-up" data-vid="<?php echo $voice['id']; ?>" value="" />
                    <label class="test" for="male">I SEE IT</label>
                    <input type="radio" id="female" class="vote-down" data-vid="<?php echo $voice['id']; ?>" value="" />
                    <label class="test" for="female">I DON'T SEE IT</label>
                </div>
                <div class="vc-loader" style="display: none;"><img src="<?php echo c_get_assets_url(); ?>images/voice_loader.gif" /></div>
            </div>
        </div>
        
        <?php } ?>
        
        <?php if($total_vote_up > 0){ ?>
        <!-- vote up list -->
        <div style="display:none;" id="t1" class="withbor bggray">
            <div class="container_705 bggray">
                <?php foreach($vote_up_users["data"] as $data){ ?>
                    <div class="smalldetimg"><img alt="" src="<?php echo get_profile_pic($data['id'], $data['profile_pic']); ?>" /></div>
                    <div class="starheadmardet">
                        <h2><span class="star_head"><?php echo $data['name']; ?></span> Voted i see it</h2>
                    </div>
                <?php } ?>                
            </div>
        </div>
        <?php } ?>
        
        <?php if($total_vote_down > 0){ ?>
        <!-- vote down list -->
        <div style="display:none;" id="t2" class="withbor bggray">
            <div class="container_705 bggray">
                <?php foreach($vote_down_users["data"] as $data){ ?>
                    <div class="smalldetimg"><img alt="" src="<?php echo get_profile_pic($data['id'], $data['profile_pic']); ?>" /></div>
                    <div class="starheadmardet">
                        <h2><span class="star_head"><?php echo $data['name']; ?></span> Voted i don't see it</h2>
                    </div>
                <?php } ?>  
            </div>
        </div>
        <?php } ?>
        
    </div>
</div>

<!-- Right Section --> 
<?php echo $sidebar; ?>
</div>

<script type="text/javascript">

    var lock = false;
    
    $(document).ready(function(){
        // vote up
        $('.vote-up').unbind('click').bind('click', function(){
            voteUp($(this).attr('data-vid'));
        });
        
        // vote down
        $('.vote-down').unbind('click').bind('click', function(){
            voteDown($(this).attr('data-vid'));
        });
    });
    
    function voteUp(vid){
        vote(vid, 1);
    }
    
    function voteDown(vid){
        vote(vid, 0);
    }
    
    function vote(vid, vac){
        
        if(!lock){
            lock = true;
            
            $('.vudc').css('display', 'none');
            $('.vc-loader').css('display', 'inline-block');
            
            var url = "<?php echo base_url() . $config['voices_url']; ?>/ajax_vote_action";
            var data = {vocid: vid, votva: vac};
            
            processData(url, data, function(res){
                try{
                    res = JSON.parse(res);
                    console.log(res);
                    // check return status is true
                    if(res.status){
                        window.location.reload(true);
                    }
                    
                    // something wrong
                    else{
                        metroAlert(res.message, {theme: metroStyle.ERROR});
                    }  
                }
                catch(e){
                    
                }
            });   
        }        
    }
    
</script>