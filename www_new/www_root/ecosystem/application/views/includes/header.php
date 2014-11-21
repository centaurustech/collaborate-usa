<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>My Voice</title>
		<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css' />
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo c_get_assets_url(); ?>css/style.css" />
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
				});
				var dd = new DropDown( $('#dd1') );

				$(document).click(function() {
					// all dropdowns
					$('.wrapper-dropdown-4').removeClass('active');
				});
				var dd = new DropDown( $('#dd2') );
				var dd = new DropDown( $('#dd3') );
				var dd = new DropDown( $('#dd4') );
				var dd = new DropDown( $('#dd5') );

				$(document).click(function() {
					// all dropdowns
					$('.wrapper-dropdown-5').removeClass('active');
				});
			});

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
                    <h1 class="mrgnleft">MY VOICES</h1>
                    <span class="menussigns">//</span>
                    <ul class="mnright">
                        <li><a href="my_voice.php">My Voices</a></li>
                        <li><a href="my_votes.php">My Votes</a></li>
                        <li><a href="my_streams.php">My Streams</a></li>
                        <li><a href="my_river.php">My Rivers</a></li>
                        <li class="brdrnone paddright"><a href="my_oceans.php">My Oceans</a></li>
                    </ul>
                </div>
            </div>
            <div class="wrapper">