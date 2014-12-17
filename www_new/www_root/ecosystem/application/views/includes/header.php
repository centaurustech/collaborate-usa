<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

///////////////////////////////////////////////////////////////////
##/ Integrate Generic HEader

#/ get Page Info
$page_info = @mysql_exec("SELECT * FROM site_pages WHERE seo_tag_id='11'", 'single');

#/ Fill pg_meta
if(isset($title) && !empty($title))
$page_title = $title;
else
$page_title = format_str(@$page_info['title']);

$pg_meta = array(
    'page_title'=>$page_title,
    'meta_keywords'=>format_str(@$page_info['meta_keywords']),
    'meta_descr'=>format_str(@$page_info['meta_descr']),
);
$page_heading = format_str(@$page_info['page_heading']);

require_once(CONTEXT_DOCUMENT_ROOT.'/includes/header.php');

///////////////////////////////////////////////////////////////////

$config = c_get_config();
?>

<link rel="stylesheet" type="text/css" media="all" href="<?php echo c_get_assets_url(); ?>css/style.css" />
<?php /*<link rel="stylesheet" type="text/css" media="all" href="<?php echo c_get_assets_url(); ?>css/header_footer.css" />*/ ?>

<?php /*<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>*/ ?>

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



<div class="container2" style="padding-top:5px;">

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
