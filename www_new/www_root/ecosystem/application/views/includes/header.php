<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); $config = c_get_config(); ?>

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
        <div class="container">
            <div class="header">
                <div class="mainlogo"><a href="#" title="Back to Homepage"><img src="<?php echo c_get_assets_url(); ?>images/logo.jpg" alt="Back to Homepage"  /></a></div>
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
                        <li><a href="#"><div class="iconsusrnav"><img  class="mrgntop"  src="<?php echo c_get_assets_url(); ?>images/message.jpg" alt=""  /><span class="posabt">1</span></div></a></li>
                        <li><a href="#"><div class="iconsusrnav"><img src="<?php echo c_get_assets_url(); ?>images/ghanta.jpg" alt=""  /><span class="posabt">1</span></div></a></li>
                        <li><a href="#"><div class="iconsusrnav"><a href="#"><img src="<?php echo c_get_assets_url(); ?>images/user.jpg" alt=""  /></a></div></a>
                        <ul>
                            <li class="brdrnonebot"><a href="#">View About Me</a></li>
                            <li class="brdrnonebot"><a href="edit_about.php">Edit About Me</a></li>
                            <li class="brdrnonebot"><a href="#">Settings</a></li>
                            <li><a href="#">Sign Out</a></li>
                        </ul>
                    </ul>
                </div>
            </div>
            <div class="menus">
                <div class="centercontainer">
                    <h1 class="mrgnleft"><?php echo $heading; ?></h1>
                    <span class="menussigns">//</span>
                    <ul class="mnright">
                        <li class="brdrnone mrgntopnone"><a href="news_feed.php"><img src="<?php echo c_get_assets_url(); ?>images/home.png" alt="" title="" /></a></li>
                        <li><a href="<?php echo base_url() . $config['my_voices_url']; ?>">My Voices</a></li>
                        <li><a href="<?php echo base_url() . $config['my_votes_url']; ?>"">My Votes</a></li>
                        <li><a href="<?php echo base_url() . $config['my_streams_url']; ?>">My Streams</a></li>
                        <li><a href="my_river.php">My Rivers</a></li>
                        <li class="brdrnone paddright"><a href="my_oceans.php">My Oceans</a></li>
                    </ul>
                </div>
            </div>
            <div class="wrapper">