var old_ajaxx='';

/**
# Function: ajax_work
# Ver 3.0 [27 Dec, 2010]
# Developed by: Raheel Hasan [raheel_itstrue@yahoo.com]

# parameters:
# url     [compulsory] = complete location of contents (including query string) to fetch using the Ajax request
# divR 		[optional] = Div in which Response will be shown as innerHTML.
# loading 	[optional] = element Id of the loading image (could be div or amy element with an Id)
# mask		[optional] = element Id of the mask div
# jsString	[optional] = JavaScript in a string (without any line break) OR a JS Object, to be processed ONLY after response has been received. If its an Object, its method must be named as "runIt".
# NoTimeOut [optional] = switch Off TimeOut


# jsString as an object - Example:
# var jsString = {runIt:function(data){
# alert(data);
# }};

# JavaScript in the Resopnse: It should have comments by 3 slashes '///'
**/

function ajax_work(url, divR, loading, mask, jsString, NoTimeOut)
{

	if(	(typeof(mask) != "undefined") && (document.getElementById(mask)!=null) )
		document.getElementById(mask).style.display='';

	if(	(typeof(loading) != "undefined") && (document.getElementById(loading)!=null) )
		document.getElementById(loading).style.display='';


	////----Setting AJAX Request----////
	var xmlhttp = getHTTPObject();
	old_ajaxx = xmlhttp;
	xmlhttp.open('GET', url, true);
	//xmlhttp.setRequestHeader("Cache-Control", "no-cache");



	////----Setting TIMEOUT----////
	if(	(typeof(NoTimeOut) == "undefined") )
	{
		var t1=setTimeout(function ajaxTimeout(){
	    xmlhttp.abort();
	    old_ajaxx='';

		alert('Network Error: connection is slower than needed. Please try again !!');

		if(	(typeof(mask) != "undefined") && (document.getElementById(mask)!=null) )
			document.getElementById(mask).style.display='none';

		if(	(typeof(loading) != "undefined") && (document.getElementById(loading)!=null) )
			document.getElementById(loading).style.display='none';
		}, 20000);
	}



	////----ERROR Handling----////
	if((xmlhttp.readyState == 4) && ((xmlhttp.status != 200) && (xmlhttp.status != 0)))
	{
    	alert("Connection Error, Please try again !");

    	if(	(typeof(t1) != "undefined") )
		clearTimeout(t1);

        old_ajaxx='';

        if(	(typeof(mask) != "undefined") && (document.getElementById(mask)!=null) )
			document.getElementById(mask).style.display='none';

    	if(	(typeof(loading) != "undefined") && (document.getElementById(loading)!=null) )
			document.getElementById(loading).style.display='none';
	}


	////----Manage AJAX Response----/////
	xmlhttp.onreadystatechange=function()
	{
		if( (xmlhttp.readyState == 4) && (xmlhttp.status == 200) )
		{
			if(	(typeof(t1) != "undefined") )
    		clearTimeout(t1);

    		old_ajaxx='';


            if(	(typeof(mask) != "undefined") && (document.getElementById(mask)!=null) )
				document.getElementById(mask).style.display='none';

			if(	(typeof(loading) != "undefined") && (document.getElementById(loading)!=null) )
				document.getElementById(loading).style.display='none';

			if(	(typeof(divR) != "undefined") && (document.getElementById(divR)!=null) )
				document.getElementById(divR).innerHTML=xmlhttp.responseText;

			//-- Platform to Run JS in Ajax Response -- //
			var sc=xmlhttp.responseText;
			//links=sc.match(/[\'\"]http:\/\/.*?[\'\"]/gm); //Catching Links - Not Working
			sc=sc.replace(/[\/]{3,}.*?[\n\r\v]/gm, ''); //Remove Comments
			sc=sc.replace(/[\n\r\t\v\u00A0\u2028\u2029]{1,}/gm, ''); //Remove all types of White Space
			sc=sc.replace(/<\/script>/gm, '</script>\n'); //Insert new line to script end
			//alert(sc);

			sc=sc.match(/<.*?script.*?>.*?<\/.*?script.*>/gm); //divide into array, all the script tags
			//alert(sc[0]);

			if(sc!=null)
			for (dx=0; dx<sc.length; dx++)
			{
				nwo=sc[dx];
				abc=nwo.replace(/<.*?script.*?>(.*?)<\/script>/gm, "$1"); //remove script/open close tags and get JS only
				//alert(abc);
				eval(abc);
			}

			if(	(typeof(jsString) != "undefined") && (jsString!='') )
            {
                if (typeof(jsString) == "string")
                eval(jsString);
                else if (typeof(jsString) == "object")
                jsString.runIt(xmlhttp.responseText);
            }

		}//end if readyState=4..
	}

    xmlhttp.send(null);

}///end function...