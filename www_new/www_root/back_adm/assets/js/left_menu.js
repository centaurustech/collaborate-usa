var cur_left = 1;
//localStorage.leftm_state = 1;

$(document).ready(function() {

    /////Using Web-Storage for saving left menu state
    if(typeof(Storage)!="undefined")
    {
        //alert(localStorage.leftm_state);
        if(localStorage.leftm_state)
        {
            cur_left = localStorage.leftm_state;

            if(cur_left==1) {cur_left = 0;}
            else if(cur_left==0) {cur_left = 1;}

            toggle_left(1);
        }

    }//end if...


    //## Sub-Menus
    $('ul.leftmenu span').click(function(){
        var sibl = $(this).siblings('.leftmenusub');
        if(typeof(sibl)=='undefined') return false;

        var m_id = $(this).attr('id');

        if(sibl.css('display')=='none'){
        sibl.slideDown(250, function(){process_submenu(sibl, m_id);});
        } else {
        sibl.slideUp(100, function(){process_submenu(sibl, m_id);});
        }
    });

    //localStorage.subm_state = ''; //reset

    function process_submenu(sibl, m_id)
    {
        if(typeof(Storage)!="undefined")
        {
            cur_subm = {};
            if(localStorage.subm_state)
            cur_subm = JSON.parse(localStorage.subm_state);

            cur_subm[m_id] = sibl.css('display');
            cur_subm_str = JSON.stringify(cur_subm);
            //alert(dump(cur_subm_str));

            localStorage.subm_state = cur_subm_str;
        }
    }

    // show last open ones
    if((typeof(Storage)!="undefined") && (localStorage.subm_state))
    {
        cur_subm = JSON.parse(localStorage.subm_state);
        //alert(dump(cur_subm));
        for(ix in cur_subm)
        {
            $('#'+ix).siblings('.leftmenusub').css('display', cur_subm[ix]);
        }
    }

    //show currently active one
    $('ul.leftmenu a.selected').parent().parent('.leftmenusub').show(400);

    //#- end sub menu...

});

function toggle_left(no_animate)
{
    if(cur_left==1) //Close it
    {
        cur_left = 0;
        document.getElementById('tog_it').className='tog_off';

        document.getElementById('COL1').style.width='0px';

        if(typeof(no_animate)=='undefined'){
        $(".leftmenu").animate({width: '0px'}, 900);
        }
        else{
        $(".leftmenu").css('width', '0px');
        }

        $(".leftmenu li").css('display', 'none');
        $(".leftmenu li").css('opacity', '0');
    }
    else //Open it
    {
        cur_left = 1;
        document.getElementById('tog_it').className='tog_on';

        if(typeof(no_animate)=='undefined')
        {
            $("#COL1").animate({width: '180px'}, 400);
            $(".leftmenu").animate({width: '180px'}, 400, '', function(){
              $(".leftmenu li").css('display', 'list-item');
            });
            $(".leftmenu li").animate({opacity: 1}, 2000);
        }
        else
        {
            $("#COL1").animate({width: '180px'}, 1);
            $(".leftmenu").animate({width: '180px'}, 1, '', function(){
              $(".leftmenu li").css('display', 'list-item');
            });
            $(".leftmenu li").animate({opacity: 1}, 1);
        }

    }//end else...

    if(typeof(Storage)!="undefined"){
    localStorage.leftm_state = cur_left;
    }
    //alert(localStorage.leftm_state);

}//end func.....