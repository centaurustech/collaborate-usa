var ADAPT_CONFIG = {
  path: DOC_ROOT+'assets/css/',
  callback: adapt_callback,

  // false = Only run once, when page first loads.
  // true = Change on window resize and page tilt.
  dynamic: true,

  // First range entry is the minimum.
  // Last range entry is the maximum.
  // Separate ranges by "to" keyword.
  range: [
    '0px to 752px = _empty.css',
    '752px to 20000px = _empty2.css'
  ]
};


//callback function on change of grid
function adapt_callback(i, width)
{
    if((typeof(i)=='undefined') || (i==null) || isNaN(i))
    return false;

    $(document).ready(function(){

    var loc_top_black_menu = $('#top_black_menu').detach();
    ///var device_nav = $('.dev_menu_icon').detach();

    if(i=='0') //<=770px
    {
        //alert(i+'-'+width);

        //*
        //move top menu
        if(typeof(loc_top_black_menu)!='undefined')
        $('#ins_top_menu_2').after(loc_top_black_menu);

        //move menu button
        ///if(typeof(device_nav)!='undefined')
        ///$('.top-btn .req-quote').before(device_nav);

        $('#top_black_menu .home a').html('Home'); //replace home icon
        $('#top_black_menu .cart a').html('My Cart'); //replace cart icon
        $('#top_black_menu .separatorx').css('display', 'none'); //remove separator
        //*/
    }
    else
    {
        //*
        //move top menu
        if(typeof(loc_top_black_menu)!='undefined')
        $('#ins_top_menu_1').after(loc_top_black_menu);

        //move menu button
        ///if(typeof(device_nav)!='undefined')
        ///$('.device-nav-inner .bottom-shadow').after(device_nav);

        $('#top_black_menu .home a').html('');
        $('#top_black_menu .cart a').html('');
        $('#top_black_menu .separatorx').css('display', '');

        ///*
        //fix slide height issue
        $('.main-nav ul.basic-nav > li').each(function(){
        $('.drop', this).attr('style', '');
        });
        //*/
    }

    });
}//end func..