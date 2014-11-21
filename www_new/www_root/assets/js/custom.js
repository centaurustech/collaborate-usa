function time_id(div_id, type, time)
{
    var t1=setTimeout(function ajaxTimeout(){
    if(type=='div'){
    //document.getElementById(div_id).style.display='none';
    $('#'+div_id).fadeOut('slow');
    }
    else if(type=='field'){
    document.getElementById(div_id).value='';
    }
    },time);
}//end func...........


//Preview image before upload
//please validate befor calling this function
function preview_img(input, display_ele_id)
{
    //var files = input.files ? input.files : input.currentTarget.files;
    //alert(input);

    //var browser = navigator.userAgent;
    //if(browser.indexOf('MSIE')>=0)
    //{
        /*
        var newPreview = document.getElementById("profile_thumb_ie");
        newPreview.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = input.value;
        newPreview.style.width = "50px";
        newPreview.style.height = "50px";
        return true;
        //*/
    //}

    if(input.files && input.files[0])
    {
        if(typeof(FileReader)!='undefined')
        {
            var reader = new FileReader();

            reader.onload = function(e){
            $('#'+display_ele_id).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

}//end func...........


// JS equivalent of PHP's in_array
function in_array(needle, haystack, argStrict)
{
    var key = '', strict = !!argStrict;

    if (strict)
    {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else
    {
        for (key in haystack) {
            if (haystack[key] == needle) {
            return true;
            }
        }
    }
    return false;
}//end func...