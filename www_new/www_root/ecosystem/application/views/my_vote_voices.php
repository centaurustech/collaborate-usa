<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="leftcont brdrnone"> 
    <span class="mainbanner">
        <img src="<?php echo c_get_assets_url(); ?>images/my_votes.jpg" alt="My Voice" />
    </span>
    <div class="withbor" style="border: none;">
      
        <!-- my voices data -->
        <div id="my_voice_data">
        </div>
        
    </div>
    <div class="widthhundr">
        <div class="voice-loader">
            <img src="<?php echo c_get_assets_url(); ?>images/voice_loader.gif" alt="Loading... Please wait." id="voc_loader" />
            <!-- <img src="<?php echo c_get_assets_url(); ?>images/view_more.png" alt="View More" id="voc_more" />-->
            <a class="bluebutton_drop" href="javascript:void(0);" style="cursor: pointer; display: none;" id="voc_more">View More</a>
        </div>        
    </div>
</div>

<!-- Right Section --> 
<?php echo $sidebar; ?>
</div>

<script type="text/javascript">

    var lock = false;
    
    $(document).ready(function(){
        
        // load my vote voices
        loadMyVoteVoices(st);
        
        // load more voice
        $("#voc_more").bind('click', function(){
            $("#voc_more").css("display", "none");
            $("#voc_loader").css("display", "inline-block");            
            loadMyVoteVoices(++st);
        });        
    });
    
    var st = 0;
    function loadMyVoteVoices(_s){
        var url = "<?php echo base_url() . $config['my_votes_url']; ?>/my_vote_voices_ajax";
        var data = {s: _s};
        
        processData(url, data, function(res){
            try{
                res = JSON.parse(res);
                
                // check return status is true
                if(res.status){
                    
                    // check data is available on response
                    if(res.is_data === true){
                        $("#voc_loader").css("display", "none");
                        $("#voc_more").css("display", "inline-block");
                        $('#my_voice_data').append(res.data);
                    }
                    
                    // no data is available
                    else{
                        $(".voice-loader").css("display", "none");                        
                    }
                }
                
                // something wrong
                else{
                    $("#voc_loader").css("display", "none");
                    $("#voc_more").css("display", "inline-block");
                    metroAlert(res.message, {theme: metroStyle.ERROR});
                }
            }
            catch(e){
                
            }
        });
    }
          
    
</script>