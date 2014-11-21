<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="leftcont brdrnone bkgrnd"> 
    <span class="mainbanner"><img src="<?php echo c_get_assets_url(); ?>images/my_voice.jpg" alt="My Voice"  /></span>
    <div class="container_705 bkgrnd"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/voice.png"></span>
        <div class="starheadmar">
            <form name="voice_post" id="voice_post" action="<?php echo base_url(); ?>create-voice" method="post" enctype="multipart/form-data">
                <input name="voc_title" class="leftsinpt wdthhundr txt-voc-title" placeholder="Voice Title"/>                
                <textarea name="voc_desc" class="leftsinpt wdthhundr" placeholder="Voice Description" style="font-family: arial; font-size: 10pt; height: 70px; line-height: 16px; resize: none;"></textarea>
                
                <div class="voice-tags"><ul id="voice_tags" class="ul-voice-tags"></ul></div>
                <input type="hidden" name="voc_tags" class="hid-voice-tags" value="" />
                <select name="voc_cat" class="sel-voice-cats">
                    <?php foreach($voice_categories["data"] as $category){ ?>
                    <option value="<?php echo $category["id"]; ?>"><?php echo $category["category"]; ?></option>
                    <?php } ?>
                </select>
                <input name="userfile" type="file" class="floatlft" />
                <input type="button" value="POST" class="mainsave floatlft btn-create-voice" style="border: none; cursor:  pointer; float: right !important;" />
            </form>
        </div>
    </div>
    
    <!-- populate my voices data -->
    <div id="my_voice_data">
        <div class="voice-loader"><img src="<?php echo c_get_assets_url(); ?>images/voice_loader.gif" alt="Loading... Please wait." /></div>
    </div>
    
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img src="<?php echo c_get_assets_url(); ?>images/voice.png" alt=""  /></span>
            <div class="starheadmar">
                <h2><span class="star_head">Christoph Zierz</span> Posted Voice</h2>
            </div>
            <div class="hoursin">2h Ago</div>
            <div class="smallhouseimg"><img src="<?php echo c_get_assets_url(); ?>images/house.jpg" alt=""  /></div>
            <div class="smallhousetxt"> <span class="star_headdrop">It has survived not only five centuries, </span>
                <p>electronic typesetting, remaining essentially 
                unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
                passages.</p>
                <a href="my_voice_detail.php">Read More</a>
            </div>
        </div>
        <div class="brdrgratop">
            <div class="container_705 margnten">
                <div class="radio leftsinpthide">
                    <input type="radio" value="male" name="gender" id="male" />
                    <label for="male" class="test">I SEE IT</label>
                    <input type="radio" value="female" name="gender" id="female" />
                    <label for="female" class="test">I DON'T SEE IT</label>
                </div>
            </div>
        </div>
    </div>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img src="<?php echo c_get_assets_url(); ?>images/voice.png" alt=""  /></span>
            <div class="starheadmar">
                <h2><span class="star_head">Christoph Zierz</span> Posted Voice</h2>
            </div>
            <div class="hoursin">2h Ago</div>
            <div class="smallhouseimg"><img src="<?php echo c_get_assets_url(); ?>images/house.jpg" alt=""  /></div>
            <div class="smallhousetxt"> <span class="star_headdrop">It has survived not only five centuries, </span>
                <p>electronic typesetting, remaining essentially 
                unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
                passages.</p>
                <a href="my_voice_detail.php">Read More</a>
            </div>
        </div>
        <div class="brdrgratop">
            <div class="container_705 margnten">
                <div class="radio leftsinpthide">
                    <input type="radio" value="male" name="gender" id="male" />
                    <label for="male" class="test">I SEE IT</label>
                    <input type="radio" value="female" name="gender" id="female" />
                    <label for="female" class="test">I DON'T SEE IT</label>
                </div>
            </div>
        </div>
    </div>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/voice.png" /></span>
            <div class="starheadcont">
                <h2><span class="star_head">Christoph Zierz</span> posted Voice</h2>
                <p>It was popularised in the 1960s with the release of Letraset sheets
                containing Lorem Ipsum passages.</p>
            </div>
            <div class="hours">2h Ago</div>
        </div>
        <div class="brdrgratop">
            <div class="container_705 margnten">
                <div class="radio leftsinpthide">
                    <input type="radio" id="male" name="gender" value="male" />
                    <label class="test" for="male">I SEE IT</label>
                    <input type="radio" id="female" name="gender" value="female" />
                    <label class="test" for="female">I DON'T SEE IT</label>
                </div>
            </div>
        </div>
    </div>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/voice.png" /></span>
            <div class="starheadcont">
                <h2><span class="star_head">Christoph Zierz</span> posted Voice</h2>
                <p>It was popularised in the 1960s with the release of Letraset sheets
                containing Lorem Ipsum passages.</p>
            </div>
            <div class="hours">2h Ago</div>
        </div>
        <div class="brdrgratop">
            <div class="container_705 margnten">
                <div class="radio leftsinpthide">
                    <input type="radio" id="male" name="gender" value="male" />
                    <label class="test" for="male">I SEE IT</label>
                    <input type="radio" id="female" name="gender" value="female" />
                    <label class="test" for="female">I DON'T SEE IT</label>
                </div>
            </div>
        </div>
    </div>
    <div class="withbor">
        <div class="container_705"> <span class="star_vo"><img alt="" src="<?php echo c_get_assets_url(); ?>images/voice.png" /></span>
            <div class="starheadmar">
                <h2><span class="star_head">Christoph Zierz</span> Posted Voice</h2>
            </div>
            <div class="hoursin">2h Ago</div>
            <div class="smallhouseimg"><img alt="" src="<?php echo c_get_assets_url(); ?>images/house.jpg"></div>
            <div class="smallhousetxt"> <span class="star_headdrop">It has survived not only five centuries, </span>
                <p>electronic typesetting, remaining essentially 
                unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
                passages.</p>
                <a href="my_voice_detail.php">Read More</a>
            </div>
        </div>
        <div class="brdrgratop">
            <div class="container_705 margnten">
                <div class="radio leftsinpthide">
                    <input type="radio" id="male" name="gender" value="male" />
                    <label class="test" for="male">I SEE IT</label>
                    <input type="radio" id="female" name="gender" value="female" />
                    <label class="test" for="female">I DON'T SEE IT</label>
                </div>
            </div>
        </div>
    </div>
</div>    

<!-- Right Section -->
    
<div class="rightcont">
    <div class="headblack">Relationships</div>
    <div class="headlarge">51</div>
    <div class="headsky">Voices Posted</div>
    <div class="headblack">&nbsp;</div>
    <div class="headlarge">05</div>
    <div class="headsky">Stream Followed</div>
    <div class="headblack">&nbsp;</div>
    <div class="headlarge">03</div>
    <div class="headsky">Rivers Joined</div>
    <div class="headblack">PREMIUM ADS</div>
    <div class="headlarge"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/ad1.jpg" alt="" /></div>
    <div class="headsky"><img class="mrgntop" src="<?php echo c_get_assets_url(); ?>images/ad2.jpg" alt="" /></div>
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
            else if($('.floatlft').val() == ""){
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
        loadMyVoices();
        
        <?php if($this->session->userdata('create_voice_message')){ $msg = $this->session->userdata('create_voice_message'); ?>
            metroAlert("<?php echo $msg["message"]; ?>", {theme: "<?php echo $msg["level"] ?>"});
        <?php $this->session->unset_userdata('create_voice_message'); } ?>
    });
    
    var s = 0;
    function loadMyVoices(){
        var url = "<?php echo base_url() . $config['my_voice_url']; ?>/my_voices_ajax";
        var data = {s: s};
        
        processData(url, data, function(res){
            metroAlert(res, {theme: metroStyle.SUCCESS, updateMetro: true});
        });
    }
</script>