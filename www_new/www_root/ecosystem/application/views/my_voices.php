<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="leftcont brdrnone"> 
    <span class="mainbanner">
        <img src="<?php echo c_get_assets_url(); ?>images/my_voice.jpg" alt="My Voice" />
    </span>
    <div class="container_588 bkgrnd">
        <form  name="voice_post" id="voice_post" action="<?php echo base_url() . $config['voice_create_url']; ?>" method="post" enctype="multipart/form-data">
        <div class="voiceimgcont"> <span class="voiceimg"><img src="<?php echo c_get_assets_url(); ?>images/image.jpg" class="vpp" alt="" /></span> 
            <div id="file-upload-cont">
                <input id="original" name="userfile" type="file" accept="image/*" />
                <div id="my-button" class="mainbrowse">Browse</div>
            </div>    
        </div>
        <div class="starheadsev">
                              
                  <input name="voc_title" class="leftsinpt wdthnin txt-voc-title" placeholder="Voice Title"/>                   
                  <textarea name="voc_desc" cols="1" rows="2" class="leftstxtarea wdthnin" placeholder="Voice Description"></textarea>
                  <!-- <input name="" type="text"  class="leftsinpt wdthnin" placeholder='Tags' /> -->
                  <div class="voice-tags"><ul id="voice_tags" class="ul-voice-tags"></ul></div>
                <input type="hidden" name="voc_tags" class="hid-voice-tags" value="" />
                  <select name="voc_cat" class="leftsinptselct">
                <?php foreach($voice_categories["data"] as $category){ ?>
                <option value="<?php echo $category["id"]; ?>"><?php echo $category["category"]; ?></option>
                <?php } ?>
                </select>
            
            <a href="javascript:voice(0);" class="mainsave btn-create-voice">Post</a>
        </div>
        </form>
    </div>
    <div class="withbor" style="border: none;">
        <div class="brdrall"></div>
        
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

<!-- 2 CSS files are required: -->
<!--   * Tag-it's base CSS (jquery.tagit.css). -->
<!--   * Any theme CSS (either a jQuery UI theme such as "flick", or one that's bundled with Tag-it, e.g. tagit.ui-zendesk.css as in this example.) -->
<!-- The base CSS and tagit.ui-zendesk.css theme are scoped to the Tag-it widget, so they shouldn't affect anything else in your site, unlike with jQuery UI themes. -->
<link href="<?php echo c_get_assets_url(); ?>css/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="<?php echo c_get_assets_url(); ?>css/tagit.ui-zendesk.css" rel="stylesheet" type="text/css">
<!-- If you want the jQuery UI "flick" theme, you can use this instead, but it's not scoped to just Tag-it like tagit.ui-zendesk is: -->
<!--   <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/flick/jquery-ui.css"> -->

<!-- jQuery and jQuery UI are required dependencies. -->
<!-- Although we use jQuery 1.4 here, it's tested with the latest too (1.8.3 as of writing this.) -->
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>-->
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>

<!-- The real deal -->
<script src="<?php echo c_get_assets_url(); ?>js/tag-it.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">

    var lock = false;
    
    $(document).ready(function(){
        
        var tags = new Array();
        
        // setup tags
        $('#voice_tags').tagit({
            placeholderText: "Tags",
            tagLimit: 5
        });
        
        // preview voie picture
        $("#original").change(function(){
            readURL(this);
        });
        
        // create voice button action
        $('.btn-create-voice').bind('click', function(){
                           
            tags = new Array();
            $('.tagit-hidden-field').each(function(){                
                tags.push($(this).val());
            });
            
            // set tags to main tags input
            $('.hid-voice-tags').val(JSON.stringify(tags));
            
            // validate voice title
            if($('.txt-voc-title').val() == ''){
                metroAlert("Voice Title can't be blank.", {theme: metroStyle.ERROR});
            }
            
            // fild upload must
            else if($('#original').val() == ""){
                metroAlert("Select voice picture.", {theme: metroStyle.ERROR});
            }
            else{
                if(! lock){
                    lock = true;
                    $('#voice_post').submit();   
                }                
            }
        });
        
        // load my voices
        loadMyVoices(st);
        
        // load more voice
        $("#voc_more").bind('click', function(){
            $("#voc_more").css("display", "none");
            $("#voc_loader").css("display", "inline-block");            
            loadMyVoices(++st);
        });
        
        <?php if($this->session->userdata('create_voice_message')){ $msg = $this->session->userdata('create_voice_message'); ?>
            metroAlert("<?php echo $msg["message"]; ?>", {theme: "<?php echo $msg["level"] ?>"});
        <?php $this->session->unset_userdata('create_voice_message'); } ?>
    });
    
    var st = 0;
    function loadMyVoices(_s){
        var url = "<?php echo base_url() . $config['my_voices_url']; ?>/my_voices_ajax";
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
    
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();    
            reader.onload = function (e) {
                $('.vpp').attr('src', e.target.result);
            }
    
            reader.readAsDataURL(input.files[0]);
        }
    }        
    
</script>