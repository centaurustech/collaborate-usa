function change_state(val, no_set_sz)
{
    if(val=='') return false;

    $(document).ready(function() {

    if(typeof(no_set_sz)=='undefined')
    $('#state').val('');

    if(val!='US')
    {
        $('#us_states_div').css('display', 'none');
        $('#intr_states_div').css('display', '');
    }
    else
    {
        $('#us_states_div').css('display', '');
        $('#intr_states_div').css('display', 'none');
    }

    });
}//end func.....

function set_state(val)
{
    if(val=='') return false;

    $(document).ready(function() {
    var cntry = $('#country_code').val();
    if(cntry=='US')
    {
        $('#state').val(val);
    }
    });

}//end func....

$(document).ready(function() {
    change_state($('#country_code').val(), 1);
    set_state($('#us_state').val());
});