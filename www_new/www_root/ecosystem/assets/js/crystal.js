// process ajax request
function processData(ajax_url, data, callback){    
    
    ajax_url  = pickParam(ajax_url, false);
    data = pickParam(data, {});
    callback  = pickParam(callback, function(res){});
    
    if(! ajax_url)
        return false;

    $.ajaxSetup({
        cache: false,
        headers: {
            'Cache-Control': 'no-cache'
        }
    });
    
    $.ajax({  
        type: 'POST',
        url: ajax_url,
        data : data,
        cache: false,        
        headers: { 
            "Cache-Control": "no-cache"
        },
        success: function(res){
            callback(res);
        },          
        error: function(e){
            metroAlert(e.status, {theme: metroStyle.ERROR, updateMetro: true});            
        }
    }); 
}

 // get parameter
function pickParam(arg, def){
    return (typeof arg == "undefined" ? def : arg);
}

// get metro loader
function getMetroLoader(){
    //return "<img src='" + site_uri + "/img/metro_loader.gif'>";
}

// get ajax url
function getAjaxUrl(){
    //return site_uri + "/includes/control.php";
}

// validate email address
function isEmail(email_addr) {
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

    if (!filter.test(email_addr)) {
        return false;
    }else{
        return true;
    }
 }