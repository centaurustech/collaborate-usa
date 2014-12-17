<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="leftcont brdrnone bkgrnd">
    <div class="withbor brdrtop">
        <div class="tabs">
            <div class="tab">
                <a href="<?php echo base_url() . $config['new_message_url']; ?>"><label for="tab-1">New</label></a>
            </div>
            <div class="tab">
                <a href="<?php echo base_url() . $config['inbox_url']; ?>"><label for="tab-2">Inbox</label></a>
            </div>
            <div class="tab">
                <input type="radio" checked="" name="tab-group-1" id="tab-3" />
                <label for="tab-3">Invitation</label>
                <div class="content2 toptabs">
                <?php if (count($notification) > 0){ ?>
                    <ul class="msginbx">
                        
                        <?php 
                            
                             
                            foreach($notification as $notif_data){
                            
                            if($notif_data['notif_data']['template_id'] == 6){
                                
                            $image = "user_files/prof/".$notif_data['notif_data']['from_user_id'].'/'.$notif_data['notif_data']['user']['data']['profile_pic'];
                            for($i = 0; $i < 10; $i++){
                                if(!file_exists($image)){$image = "../{$image}";}else{break;}
                            }
                            if(!file_exists($image)){
                                $image = "assets/images/ep.png";
                                for($i = 0; $i < 10; $i++){
                                    if(!file_exists($image)){$image = "../{$image}";}else{break;}
                                }
                            }
                            
                            $detail = str_replace_nth("Stream", "<a href='".base_url(). $config['single_stream_url'].'/'.$notif_data['notif_data']['caller_stream']['data']['id']."'>".$notif_data['notif_data']['caller_stream']['data']['question_text']."</a>", $notif_data['notification']['notification'], 0);
                            $detail = str_replace_nth("Stream", "<a href='".base_url(). $config['single_stream_url'].'/'.$notif_data['notif_data']['receiver_stream']['data']['id']."'>".$notif_data['notif_data']['receiver_stream']['data']['question_text']."</a>", $detail, 0);
                            
                            ?>
                            
                        <li class="mrgntoptn">
                            <div class="usrimgmsg"><img alt="" src="<?php echo $image; ?>" /></div>
                            <div class="usritxtmsg"> 
                                <strong><?php echo $notif_data['notif_data']['user']['data']['name']; ?></strong><br />
                                <?php echo $detail; ?><br />
                                <strong><?php echo c_get_time_elapsed(strtotime($notif_data['notif_data']['created_on'])); ?></strong>
                                <p>
                                    <a href="javascript:void(0);" class="mainsave floatlft marginrighrspc accept-river-invite" data-csid="<?php echo $notif_data['notif_data']['caller_stream']['data']['eco_sys_id']; ?>" data-rsid="<?php echo $notif_data['notif_data']['receiver_stream']['data']['eco_sys_id']; ?>">Accept</a>
                                    <a href="javascript:void(0);" class="mainsave floatlft reject-river-invite" data-csid="<?php echo $notif_data['notif_data']['caller_stream']['data']['eco_sys_id']; ?>" data-rsid="<?php echo $notif_data['notif_data']['receiver_stream']['data']['eco_sys_id']; ?>">Reject</a>
                                    <img src="<?php echo c_get_assets_url() . 'images/voice_loader.gif' ?>" class="ar-loader" style="margin-top: 8px; display: none;" />
                                </p>
                            </div>
                            <div class="brdrall"></div>
                        </li>
                        <?php }} ?>
                    </ul>
                    <?php } ?>                                    
                </div>
            </div>
            <div class="tab">
                <a href="<?php echo base_url() . $config['notification_url']; ?>"><label for="tab-4">Notifications</label></a>
            </div>        
            <div class="tab">
            <a href="<?php echo base_url() . $config['sent_message_url']; ?>"><label for="tab-5">Sent</label></a>
            </div>
            <div class="tab brdrnonerght">
            <a href="<?php echo base_url() . $config['trash_message_url']; ?>"><label for="tab-6">Trash</label></a>
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
        $(".accept-river-invite").bind('click', function(){
            if(confirm('Are you sure you want to accept this request.')){                
                acceptRiverInvite($(this).attr('data-csid'), $(this).attr('data-rsid'), $(this));
            }
        });
        
        $(".reject-river-invite").bind('click', function(){
            if(confirm('Are you sure you want to reject this request.')){
                rejectRiverInvite($(this).attr('data-csid'), $(this).attr('data-rsid'), $(this));
            }
        });
    });
    
    function acceptRiverInvite(_csid, _rsid, _self){
        
        if(!lock){
            lock = true;
            
            _self.closest('p').find('a').css('display', 'none');
            _self.closest('p').find('.ar-loader').css('display', 'inline-block');
            
            var url = "<?php echo base_url() . $config['merger_url']; ?>/stream_to_river";
            var data = {csid: _csid, rsid: _rsid};
            
            processData(url, data, function(res){
                lock = false;
                try{                
                    res = JSON.parse(res);
                    
                    // check return status is true
                    if(res.status){
                        window.location.reload(true);
                    }
                    
                    // something wrong
                    else{
                        _self.closest('p').find('.ar-loader').css('display', 'none');
                        _self.closest('p').find('a').css('display', 'inline-block');
                        metroAlert(res.message, {theme: metroStyle.ERROR});
                    }
                }
                catch(e){
                    _self.closest('p').find('.ar-loader').css('display', 'none');
                    _self.closest('p').find('a').css('display', 'inline-block');
                    metroAlert(e, {theme: metroStyle.ERROR});
                }
            });
        }
    }
    
    function rejectRiverInvite(_csid, _rsid, _self){
        
        if(!lock){
            lock = true;
            
            _self.closest('p').find('a').css('display', 'none');
            _self.closest('p').find('.ar-loader').css('display', 'inline-block');
            
            var url = "<?php echo base_url() . $config['merger_url']; ?>/reject_river_invite";
            var data = {csid: _csid, rsid: _rsid};
            
            processData(url, data, function(res){
                lock = false;
                try{
                    res = JSON.parse(res);
                    
                    // check return status is true
                    if(res.status){
                        window.location.reload(true);                 
                    }
                    
                    // something wrong
                    else{       
                        _self.closest('p').find('.ar-loader').css('display', 'none');
                        _self.closest('p').find('a').css('display', 'inline-block');
                        
                        metroAlert(res.message, {theme: metroStyle.ERROR});
                    }
                }
                catch(e){
                    _self.closest('p').find('.ar-loader').css('display', 'none');
                    _self.closest('p').find('a').css('display', 'inline-block');
                    metroAlert(e, {theme: metroStyle.ERROR});
                }
            });   
        }        
    }
                     
</script>