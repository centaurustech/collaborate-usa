function save_ls(local_pgvar, pg, pgc_key, pgc_val)
{
    if(typeof(Storage)!="undefined") //save current state of view
    {
        if(typeof(localStorage.local_pgvar)=="undefined")
        {
            localStorage.local_pgvar = '';
            var lc_ar = {};
            lc_ar[pg] = {};
        }
        else
        {
            var lc_ar = JSON.parse(localStorage.local_pgvar);
            if(typeof(lc_ar[pg])=="undefined"){
            lc_ar[pg] = {};
            }
        }

        lc_ar[pg][pgc_key] = {};
        lc_ar[pg][pgc_key]['param3'] = pgc_val;

        var js_str = JSON.stringify(lc_ar);
        localStorage.local_pgvar = js_str;

        //alert(pgc_key+'-'+pgc_val);
        //alert(localStorage.local_pgvar);
    }

}//end func....

function get_ls(local_pgvar, pg, pgc_key)
{
    var rtn = '';

    if(typeof(Storage)!="undefined") //move back to old state of view
    {
        if(typeof(localStorage.local_pgvar)!="undefined")
        {
            var lc_ar = JSON.parse(localStorage.local_pgvar);
            if((typeof(lc_ar[pg])!="undefined") && (typeof(lc_ar[pg][pgc_key])!="undefined"))
            {
                rtn = lc_ar[pg][pgc_key]['param3'];
                //alert(dump(lc_ar));
            }
        }
    }

    return rtn;

}//end func....