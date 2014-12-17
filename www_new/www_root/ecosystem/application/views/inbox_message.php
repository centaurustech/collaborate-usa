<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="leftcont brdrnone bkgrnd">
    <div class="withbor brdrtop">
        <div class="tabs">
            <div class="tab">
                <a href="<?php echo base_url() . $config['new_message_url']; ?>"><label for="tab-1">New</label></a>
            </div>
            <div class="tab">
            <input type="radio" checked="" name="tab-group-1" id="tab-2" />
            <label for="tab-2">Inbox</label>
            <div class="content2 toptabs">
                <div class="bgblue">
                    <input type="checkbox" value="" name="" class="checks" />
                    <span class="checkstrash"><strong><a href="#">Trash</a></strong></span>
                </div>
                <!--
                <ul class="msginbx">
                    <li class="mrgntoptn">
                        <div class="usrimgmsg"><a href="message_detail.php"><img alt="" src="<?php echo c_get_assets_url(); ?>images/bilal.jpg"></a></div>
                        <div class="usritxtmsg"> <strong>Aamir Khan</strong><br />
                            Joined my Network on Collaborate<br />
                            Lorem Lipsum dolor sit amit lorem lipsum dolor sit amit lorum lipsum lorem lipsum dolor sit amit lorum lipsum<br>
                            <strong>Yersterday</strong> <a href="#"><img alt="" src="<?php echo c_get_assets_url(); ?>images/delete.jpg"></a> 
                        </div>
                        <input type="checkbox" value="" name="" class="checks" />
                        <div class="brdrall"></div>
                    </li>
                </ul>
                -->
            </div>
            </div>
            <div class="tab">
                <a href="<?php echo base_url() . $config['invitation_url']; ?>"><label for="tab-3">Invitation</label></a>
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
                 
    });
                     
</script>