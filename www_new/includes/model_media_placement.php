<?php
function get_media($pg_media)
{
    $pg_media_csv = '';
    if(@!empty($pg_media) && @is_array($pg_media))
    {
        $pg_media_csv = "'".implode("','", $pg_media)."'";
    }
    if(empty($pg_media_csv)) return false;

    $sql_1 = "SELECT * FROM site_media WHERE placement_tag IN ({$pg_media_csv})";
    $media = @mysql_exec($sql_1);
    //var_dump("<pre>", $sql_1, $media); die();

    if(is_array($media) && count($media)>0)
    {
        $media = cb89($media, 'placement_tag');
        //var_dump("<pre>", $media); die();
        return $media;
    }
    return false;

}//end func....


/**
 * {media_image:TAG}
 * {media_video:TAG}

 * [usage]
 * if(stristr($to_v['content'], '{media_')!=false){ //for {media___:__} placeholders
   $to_v['content'] = $this->main_manager->place_media($to_v['content']);
   }
 *
**/
function place_media($pg_content)
{
    preg_match_all("/\{media_.{3,}?\:(.{1,}?)\}/ims", $pg_content, $pg_media);
    //var_dump("<pre>", $pg_media); die();

    if(@!empty($pg_media[1]) && @is_array($pg_media[1]))
    {
        global $consts;

        $pg_media_v = get_media($pg_media[1]);
        //var_dump("<pre>", $consts, $pg_media_v); die();

        $ar_srch = $ar_repl = array();
        $video_inc = false;

        if(is_array($pg_media_v) && count($pg_media_v)>0)
        foreach($pg_media_v as $pmk=>$pmv)
        {
            if($pmv['m_type']=='image')
            {
                $ar_srch[]="/\{media_image?\:$pmk\}/ims";

                $src = "{$consts['SITE_URL']}assets/images_2/media/{$pmv['m_file']}"; ///\{media_.{3,5}?\:$pmk\}/ims
                $repx = "<img src='{$src}' class='place_media' />";

                $ar_repl[]=$repx;
            }
            else if($pmv['m_type']=='video')
            {
                $ar_srch[]="/\{media_video?\:$pmk\}/ims";

                $repx = '';
                /*
                if($video_inc==false)
                {
                    $video_inc = true;
                    $repx = "";
                }
                */

                $repx.= "<video preload='metadata' controls>
                <source src=\"{$consts['SITE_URL']}assets/images_2/media/{$pmv['m_file']}\" type=\"video/mp4\">
                Your browser does not support the video tag.
                </video>";

                $ar_repl[]=$repx;

            }
        }
        //var_dump("<pre>", $pg_media_v,$ar_srch,$ar_repl); //die();

        $pg_content = preg_replace($ar_srch, $ar_repl, $pg_content);
        //var_dump("<pre>", $pg_content); die();
    }

    return $pg_content;

}//end func....
?>