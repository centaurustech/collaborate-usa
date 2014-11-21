function IsChecked( f, n )
{
	if ( f.length )
    {
		for ( var i = 0; i < f.length; i++ )
        {
			if ( f[ i ].checked == true )
            {
				return true;
			}
		}
		alert( n ); f[ 0 ].focus( ); return false;
	}
	else if ( f.checked == false )
    {
		alert( n ); f.focus( ); return false;
	}
	return true;

}///end func.........


function TrimString(sInString)
{
	sInString = sInString.replace( /^\s+/g, "" );// strip leading
	return sInString.replace( /\s+$/g, "" );// strip trailing
}//end func...


function toggle_div(caller, div_id, close_div)
{
    if(caller.checked) {
    document.getElementById(div_id).style.display='';
    } else {
    document.getElementById(div_id).style.display='none';
    }

    if(typeof(close_div)!="undefined"){
    document.getElementById(close_div).style.display='none';
    }

}//end func...