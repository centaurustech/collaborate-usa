<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="leftcont brdrnone"> 
    
    <div class="aboutbanner">
          <div class="abtbannurg">
          <h3>News Feed</h3>
          <br /><br /><br />
          <strong>It has survived not only five centuries,</strong> <br />
          <p>electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release. Letraset sheets containing Lorem Ipsum massages.</p>
    <a class="connbutton_drop" href="<?php echo base_url() . $config['my_voices_url']; ?>">Share Voice</a>
    </div>
    <div class="membersign"><img alt="" src="<?php echo c_get_assets_url(); ?>images/membership.jpg" /></div>
    <!--<div class="circlepic">&nbsp;</div>-->
    </div>
    
    <div class="withbor" style="border: none;">

        <!-- my streams data -->
        <div id="feeds_data">
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
        
        // load my streams
        loadFeeds(st);
        
        // load more voice
        $("#voc_more").bind('click', function(){
            $("#voc_more").css("display", "none");
            $("#voc_loader").css("display", "inline-block");            
            loadFeeds(++st);
        });
    });
    
    var st = 0;
    function loadFeeds(_s){
        var url = "<?php echo base_url(); ?>feeds_ajax";
        var data = {s: _s};
        
        processData(url, data, function(res){
            try{
                res = JSON.parse(res);
                
                // check return status is true
                if(res.status){
                    
                    // check data is available on response
                    if(res.is_data === true){
                        $("#voc_loader").css("display", "none");
                        $('#feeds_data').append(res.data);
                    }
                    
                    if(res.is_more_data === true){
                        $("#voc_more").css("display", "inline-block");
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
                metroAlert(e, {theme: metroStyle.ERROR});
            }
        });
    }
    
</script>