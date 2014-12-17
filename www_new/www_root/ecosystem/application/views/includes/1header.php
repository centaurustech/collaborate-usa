<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
    
    $config = c_get_config();
    $logged_user = $this->session->userdata('user_data');
    $header_image = DOC_ROOT."user_files/prof/".$logged_user["uid"]."/".$logged_user["profile_pic"];
    
    
    for($i = 0; $i < 10; $i++){
        //if(!file_exists($header_image)){$header_image = "../{$header_image}";}else{break;}
    }
    
    if(!file_exists($header_image)){
        $header_image = DOC_ROOT."assets/images/ep.png";
        for($i = 0; $i < 10; $i++){
            //if(!file_exists($header_image)){$header_image = "../{$header_image}";}else{break;}
        }                                    
    }
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $title; ?></title>
		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css' />
        <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />
        <link rel="shortcut icon" href="https://www.collaborateusa.com/favicon.ico" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo c_get_assets_url(); ?>css/style.css" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo c_get_assets_url(); ?>css/header_footer.css" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo c_get_assets_url(); ?>js/crystal-metro-alert-1.0.js"></script>
        <script type="text/javascript" src="<?php echo c_get_assets_url(); ?>js/crystal.js"></script>                                                                
        <script type="text/javascript">
        			
            function DropDown(el) {
            	this.dd = el;
            	this.placeholder = this.dd.children('span');
            	this.opts = this.dd.find('ul.dropdown > li');
            	this.val = '';
            	this.index = -1;
            	this.initEvents();
            }
            
            DropDown.prototype = {
            	initEvents : function() {
            		var obj = this;
            
            		obj.dd.on('click', function(event){
            			$(this).toggleClass('active');
            			return false;
            		});
            
            		obj.opts.on('click',function(){
            			var opt = $(this);
            			obj.val = opt.text();
            			obj.index = opt.index();
            			obj.placeholder.text(obj.val);
            		});
            	},
            	getValue : function() {
            		return this.val;
            	},
            	getIndex : function() {
            		return this.index;
            	}
            }
            
            $(function() {
            
            	var dd = new DropDown( $('#dd') );
            
            	$(document).click(function() {
            		// all dropdowns
            		$('.wrapper-dropdown-3').removeClass('active');
            		$('.wrapper-dropdown-4').removeClass('active');
            		$('.wrapper-dropdown-5').removeClass('active');
            		$('.wrapper-dropdown-4cat').removeClass('active');
            		
            	});
            	var dd = new DropDown( $('#dd1') );
            	var dd = new DropDown( $('#dd2') );
            	var dd = new DropDown( $('#dd3') );
            	var dd = new DropDown( $('#dd4') );
            	var dd = new DropDown( $('#dd5') );
            	var dd = new DropDown( $('#dd6') );
            
            	$(document).click(function() {
            		// all dropdowns
            		
            	});
            	
            	
            });
        								
            function show_hide_div(div_id){
        	
            	if(div_id == "t1"){
            		
            		$("#t1").css('display','block');
            		$("#t2").css('display','none');
            		}else{
            		$("#t1").css('display','none');
            		$("#t2").css('display','block');
            	}	
            }
        
        </script>
    </head>
	<body>
    <?php #echo "<pre>"; print_r($notification);exit; ?>
        <div class="container">
            <div class="header">
                <div class="mainlogo"><a href="<?php echo DOC_ROOT; ?>" title="Back to Homepage"><img src="<?php echo c_get_assets_url(); ?>images/logo.jpg" alt="Back to Homepage"  /></a></div>
                <div class="searchhead">
                    <div class="seartxt">Search</div>
                    <div class="searinpt">
                        <input name="" type="text" />
                    </div>
                    <div class="seargo"><a href="#">Go</a></div>
                </div>
                <div class="usericons"> </div>
                <div id="nav">
                    <ul>
                    <li>
                        <div class="iconsusrnav">
                            <a href="message.php">
                                <img alt="" src="<?php echo c_get_assets_url(); ?>images/message.jpg" class="mrgntoptn" />
                            </a>
                            <!--<span class="posabt">1</span>-->
                        </div>
                        <!--
                        <ul class="marginulin marginuliniph">
                            <li class="brdrnonebot width_390"><h2 class="colorblack">Messages</h2></li>
                            <li class="brdrnonebot width_390">
                            <div class="notsmall"><a href="message_detail.php"><img alt="" src="<?php echo c_get_assets_url(); ?>images/notsmall.jpg"></a></div>
                            <div class="notsmallp"><strong><a href="message_detail.php">Collaborate : Christoph Zierz send you a request.</a></strong></div>
                            <div class="notsmallp">Today at 9:05 am</div>
                            </li>
                            <li class="brdrnonebot width_390">
                            <div class="notsmall"><a href="message_detail.php"><img alt="" src="<?php echo c_get_assets_url(); ?>images/notsmall.jpg"></a></div>
                            <div class="notsmallp"><strong><a href="message_detail.php">Collaborate : Christoph Zierz send you a request.</a></strong></div>
                            <div class="notsmallp">Today at 9:05 am</div>
                            </li>
                            <li class="brdrnonebot width_390">
                            <div class="notsmall"><a href="message_detail.php"><img alt="" src="<?php echo c_get_assets_url(); ?>images/notsmall.jpg"></a></div>
                            <div class="notsmallp"><strong><a href="message_detail.php">Collaborate : Christoph Zierz send you a request.</a></strong></div>
                            <div class="notsmallp">Today at 9:05 am</div>
                            </li>
                            <li class="brdrnonebot width_390">
                            <div class="notsmall"><a href="message_detail.php"><img alt="" src="<?php echo c_get_assets_url(); ?>images/notsmall.jpg"></a></div>
                            <div class="notsmallp"><strong><a href="message_detail.php">Collaborate : Christoph Zierz send you a request.</a></strong></div>
                            <div class="notsmallp">Today at 9:05 am</div>
                            </li>
                            <li class="brdrnonebot width_390">
                            <a class="notbt" href="message.php">View More</a>
                            </li>
                        </ul>
                        -->
                    </li>
                    <li>
                        <div class="iconsusrnav">
                            <a href="<?php echo base_url() . $config['notification_url']; ?>">
                                <img alt="" src="<?php echo c_get_assets_url(); ?>images/ghanta.jpg" class="mrgntop" />
                            </a>
                            <span class="posabt"><?php if (count($notification) > 0){ echo count($notification); } ?></span>
                        </div>
                        <?php if (count($notification) > 0){ ?>
                        <ul class="marginulin marginuliniph">
                            
                            <?php 
                                
                                $sh = true; 
                                foreach($notification as $notif_data){
                                $image = "user_files/prof/".$notif_data['notif_data']['from_user_by'].'/'.$notif_data['notif_data']['user']['data']['profile_pic'];
                                for($i = 0; $i < 10; $i++){
                                    if(!file_exists($image)){$image = "../{$image}";}else{break;}
                                }
                                if(!file_exists($image)){
                                    $image = "assets/images/ep.png";
                                    for($i = 0; $i < 10; $i++){
                                        if(!file_exists($image)){$image = "../{$image}";}else{break;}
                                    }                                    
                                }
                                if($sh){ $sh = false; ?><li class="brdrnonebot width_390"><h2 class="colorblack">Notifications</h2></li><?php } ?>
                            <li class="brdrnonebot width_390">
                                <div class="notsmall"><img alt="" src="<?php echo $image; ?>" /></div>
                                <div class="notsmallp"><strong><a href="<?php echo DOC_ROOT . $notif_data['notif_data']['visit_url']; ?>"><?php echo word_limiter($notif_data['notification'], 30); ?></a></strong></div>
                                <div class="notsmallp"><?php echo c_get_time_elapsed(strtotime($notif_data['notif_data']['created_on'])); ?></div>
                            </li>
                            <?php } ?>                            
                            <li class="brdrnonebot width_390">
                                <a class="notbt" href="<?php echo base_url() . $config['notification_url']; ?>">View More</a>
                            </li>
                        </ul>
                        <?php } ?>
                        
                    </li>
                    <li>
                        <div class="iconsusrnav"><a href="#"><img alt="" src="<?php echo $header_image; ?>" style="width: 35px; height: 35px;" /></a></div>
                        
                        <ul>
                        <li><h2 class="colorblack">My Account</h2></li>
                        <li class="brdrnonebot"><a href="my_member.php">My Members</a></li>
                        <li class="brdrnonebot"><a href="about_me.php">View About Me</a></li>
                        <li class="brdrnonebot"><a href="edit_about.php">Edit About Me</a></li>
                        <li class="brdrnonebot"><a href="#">Settings</a></li>
                        <li><a href="<?php echo base_url(); ?>logout">Sign Out</a></li>
                        </ul>
                    </li>
</ul>
              </div>
                
            </div>
            <div class="menus">
                <div class="centercontainer">
                    <h1 class="mrgnleft"><?php echo $heading; ?></h1>
                    <span class="menussigns">//</span>
                    <ul class="mnright">
                        <li class="brdrnone mrgntopnone"><a href="<?php echo base_url(); ?>"><img src="<?php echo c_get_assets_url(); ?>images/home.png" alt="" title="" /></a></li>
                        <li><a href="<?php echo base_url() . $config['my_voices_url']; ?>">My Voices</a></li>
                        <li><a href="<?php echo base_url() . $config['my_votes_url']; ?>">My Votes</a></li>
                        <li><a href="<?php echo base_url() . $config['my_streams_url']; ?>">My Streams</a></li>
                        <li><a href="<?php echo base_url() . $config['my_rivers_url']; ?>">My Rivers</a></li>
                        <li class="brdrnone paddright"><a href="<?php echo base_url() . $config['my_oceans_url']; ?>">My Oceans</a></li>
                    </ul>
                </div>
            </div>
            <div class="wrapper">