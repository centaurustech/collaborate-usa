<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

<div class="leftcont brdrnone bkgrnd">
    <div class="withbor brdrtop">
        <div class="tabs">
            <div class="tab">
                <input type="radio" checked="" name="tab-group-1" id="tab-1" />
                <label for="tab-1">New</label>
                <div class="content2 toptabs">
                    <div class="starheamsg">
                        <input type="text" placeholder="To" class="leftsinpt wdthnin" name="" />
                        <input type="text" placeholder="Subject" class="leftsinpt wdthnin" name="" />
                        <textarea class="leftstxtarea wdthnin" placeholder="Type your message" rows="2" cols="1" name=""></textarea>
                        <a class="mainbrowse marginrighrspc" href="#">Send Message</a> <a class="mainbrowse " href="#">Cancel</a>
                    </div>
                </div>
            </div>
            <div class="tab">
                <a href="<?php echo base_url() . $config['inbox_url']; ?>"><label for="tab-2">Inbox</label></a>
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