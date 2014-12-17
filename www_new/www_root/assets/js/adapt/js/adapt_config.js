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
    '0px to 700px = _empty.css',
    '701px to 20000px = _empty2.css'
  ]
};


//callback function on change of grid
function adapt_callback(i, width)
{
    if((typeof(i)=='undefined') || (i==null) || isNaN(i))
    return false;

    $(document).ready(function(){


    if(i=='0') //<=700px
    {
        //alert(i+'-'+width);
        //$().changePromptPosition("topLeft");
    }
    else
    {
        //$().changePromptPosition("topRight");
    }

    });
}//end func..