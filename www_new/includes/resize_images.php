<?php
function make_image($cur_dir, $new_dir, $max_width, $max_height, $file_name='', $file_type, $new_imagename, $pres_ratio=true)
{
	$rt = false;
    $img_name = $file_name;
    $img_type = $file_type;


    ## get the width and height of image
	list($width, $height) = getimagesize($cur_dir.$img_name);
    //var_dump($width, $height, $file_name, $cur_dir.$img_name); die();

    if($pres_ratio)
    {
        if($width>=$height)
        {
    		$modwidth=$max_width;
    		$modheight=($height*$modwidth)/$width;
    	}
        else
        {
    		$modheight=$max_height;
    		$modwidth=($width*$modheight)/$height;
    	}
    }
    else
    {
        $modwidth = $max_width;
        $modheight = $max_height;
    }
    //var_dump($modwidth, $modheight); die();
    ##--


	$tn = imagecreatetruecolor($modwidth, $modheight);

	## check extensions of images & make files
	if ($img_type == "image/jpg" || $img_type == "image/jpeg" || $img_type == "image/pjpeg")
    {
		$image = imagecreatefromjpeg($cur_dir.$img_name);
		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
		$rt = imagejpeg($tn, $new_dir.$new_imagename.".jpg", 100);
	}

	if ($img_type == "image/gif")
    {
		$image = imagecreatefromgif($cur_dir.$img_name);
		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
		$rt = imagegif($tn, $new_dir.$new_imagename.'.gif');
	}

	if ($img_type == "image/png")
    {
		$image = imagecreatefrompng($cur_dir.$img_name);

        ## Preserve Transparancy
        imagealphablending($tn, false);
        $t_color = imagecolorallocatealpha($tn, 0, 0, 0, 127);
        imagefill($tn, 0, 0, $t_color);
        imagesavealpha($tn, true);
        ##--

		imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
		$rt = imagepng($tn, $new_dir.$new_imagename.'.png');
	}
    ##--


    return $rt;

}//end func....

/////////////////////////////////////////////////////////////////////////////

/**
 * Function upload_img_rs_ar
 * PURPOSE: upload & resize images in array[]
 *
 * $field_name = Image field name
 * $img_width = width to resize to. set to '0' to prevent resize
 * $img_height = height to resize to. set to '0' to prevent resize
 * $up_loc = upload location for the image. Like '../img/clients'
 * $img_title = Image Title (mainly used for errors)
 * $insert_name_part = Insert Part in File Name (use to create different size images like thunbnail)
 * $fix_height = fix height as well
 * $images_only = upload and return info about Images Only from $_FILES
 * $copies = array with (insert_parts, size_w, size_h) to create copies [applicable on images only]
 *
 *
 * RETURNS: Array of new names
 *
 * [USAGE example]
 *  $new_p_files = $sql_part_2 = array();

    $copy_data = array(0=>array('i_part'=>'_small', 'size_w'=>300, 'size_h'=>300),
    1=>array('i_part'=>'_t', 'size_w'=>30, 'size_h'=>30));

    $new_p_files = upload_img_rs_ar('p_file', 0, 0, $up_path, 'Image', '', '', 'CUSAADMIN_MSG_GLOBAL', false, $copy_data);
    if(is_array($new_p_files) && count($new_p_files)>0)
    {
        for($i=0; $i<count($new_p_files); $i++)
        {
            if($new_p_files[$i]=='') continue;
            $sql_part_2[] = "('{$pf_id}', '{$new_p_files[$i]['new_name']}', '{$new_p_files[$i]['type']}')";
        }
    }
 *
*/

function upload_img_rs_ar($field_name, $img_width, $img_height, $up_loc, $img_title, $insert_name_part='', $fix_height='', $error_sess_name='CUSA_MSG_GLOBAL', $images_only=false, $copies=array())
{
    $rtn_ar = array();
    if(isset($_FILES[$field_name]) && @is_array($_FILES[$field_name]) && @is_array($_FILES[$field_name]['tmp_name']))
    {
        for($i=0; $i<count($_FILES[$field_name]['tmp_name']); $i++)
        {
            //var_dump("<pre>", $_FILES[$field_name]['type'][$i]);
            if($_FILES[$field_name]['tmp_name'][$i]=='')
            {$rtn_ar[$i]=''; continue;}

            #/ determine type
            $up_type = $_FILES[$field_name]['type'][$i];
            $m_type = '';
            if(stristr($up_type,'video')!=false){
            $m_type = 'video';
            } else if(stristr($up_type,'image')!=false){
            $m_type = 'image';
            }

            #/ upload files
            $new_name = '';
            if($m_type=='video')
            {
                if($images_only!=false) continue;
                $new_name = upload_vdo($field_name, $up_loc, $img_title, $insert_name_part, $error_sess_name, $i);
            }
            else if($m_type=='image') {
            $new_name = upload_img_rs($field_name, $img_width, $img_height, $up_loc, $img_title, $insert_name_part, $fix_height, $error_sess_name, $i, $copies);
            }

            if(!empty($new_name)){
            $rtn_ar[$i]['new_name'] = $new_name;
            $rtn_ar[$i]['type'] = $m_type;
            }
        }
    }
    return $rtn_ar;
}


/**
 * Function upload_img_rs
 * PURPOSE: upload & resize images
 *
 * $field_name = Image field name
 * $img_width = width to resize to. set to '0' to prevent resize
 * $img_height = height to resize to. set to '0' to prevent resize
 * $up_loc = upload location for the image. Like '../img/clients'
 * $img_title = Image Title (mainly used for errors)
 * $insert_name_part = Insert Part in File Name (use to create different size images like thunbnail)
 * $fix_height = fix height as well
 * $copies = array with (insert_parts, size_w, size_h) to create copies [applicable on images only]
 *
 * [usagage for copies]
 * $t_image = substr_replace($endors_v['t_image'], '_small.', @strrpos($endors_v['t_image'], '.'), 1);
*/

function upload_img_rs($field_name, $img_width, $img_height, $up_loc, $img_title, $insert_name_part='', $fix_height='', $error_sess_name='CUSA_MSG_GLOBAL', $array_index=0, $copies=array())
{
    global $_FILES, $_SESSION;

    $new_c_image = '';

    if(isset($_FILES[$field_name]) && @is_array($_FILES[$field_name]) && @is_array($_FILES[$field_name]['tmp_name'])){
    $tmp_name = @$_FILES[$field_name]['tmp_name'][$array_index];
    $name = @$_FILES[$field_name]['name'][$array_index];
    $type = @$_FILES[$field_name]['type'][$array_index];
    } else {
    $tmp_name = @$_FILES[$field_name]['tmp_name'];
    $name = @$_FILES[$field_name]['name'];
    $type = @$_FILES[$field_name]['type'];
    }


    if(is_uploaded_file($tmp_name))
    {
        $t1 = explode('.', format_str(format_filename($name)));
        $t1[0] = substr($t1[0], 0, 10);

        if($array_index==0){
        $tmx = time();
        $tmx = substr($tmx, 0, strlen($tmx)-2);
        } else {
        $tmx = str_replace('.', '_', microtime(true));
        }

        $new_c_image = strtolower($t1[0]).'_'.$tmx.$insert_name_part;
        //var_dump($new_c_image); die();

        list($g_width, $g_height) = @getimagesize($tmp_name);
        if(($img_width>0) && (($g_width>$img_width) || ($g_width<($img_width-1)))) $g_width = $img_width;
        if(($img_height>0) && (($g_height>$img_height) || ($g_height<($img_height-1)))) $g_height = $img_height;

        $pres_ratio = true;
        if(!empty($fix_height))
        {
            $g_height = $fix_height;
            $pres_ratio = false;
        }

        $ret = false;
        $ret = make_image($tmp_name, $up_loc, $g_width, $g_height, '', $type, $new_c_image, $pres_ratio);

        #/ make copies
        if(is_array($copies) && count($copies)>0)
        {
            foreach($copies as $cpy)
            {
                $new_copy_image = $new_c_image.$cpy['i_part'];

                $copy_width = $cpy['size_w']; $copy_height = $cpy['size_h'];
                if(($copy_width>0) && (($g_width>$copy_width) || ($g_width<($copy_width-1)))) $g_width = $copy_width;
                if(($copy_height>0) && (($g_height>$copy_height) || ($g_height<($copy_height-1)))) $g_height = $copy_height;

                make_image($tmp_name, $up_loc, $g_width, $g_height, '', $type, $new_copy_image, $pres_ratio);
            }
        }

        if($ret===false)
        {
            $new_c_image = '';
            $dname = format_str($name);
            $_SESSION[$error_sess_name] = array(false, "ERROR saving {$img_title} ({$dname})!");
        }
        else
        {
            $new_c_image.= '.'.strtolower($t1[count($t1)-1]);
        }
    }

    return $new_c_image;

}//end func....



/**
 * Function upload_vdo
 * PURPOSE: upload & rename Videos
 *
 * $field_name = file name
 * $up_loc = upload location for the file. Like '../img/clients'
 * $img_title = File Title (mainly used for errors)
 * $insert_name_part = Insert Part in File Name at the end (forexample '_t1')
*/

function upload_vdo($field_name, $up_loc, $img_title, $insert_name_part='', $error_sess_name='CUSA_MSG_GLOBAL', $array_index=0)
{
    global $_FILES, $_SESSION;

    $new_c_image = '';

    if(isset($_FILES[$field_name]) && @is_array($_FILES[$field_name]) && @is_array($_FILES[$field_name]['tmp_name'])){
    $tmp_name = @$_FILES[$field_name]['tmp_name'][$array_index];
    $name = @$_FILES[$field_name]['name'][$array_index];
    } else {
    $tmp_name = @$_FILES[$field_name]['tmp_name'];
    $name = @$_FILES[$field_name]['name'];
    }

    if(is_uploaded_file($tmp_name))
    {
        $t1 = explode('.', format_str(format_filename($name)));
        $t1[0] = substr($t1[0], 0, 10);

        if($array_index==0){
        $tmx = time();
        $tmx = substr($tmx, 0, strlen($tmx)-2);
        } else {
        $tmx = str_replace('.', '_', microtime(true));
        }

        $new_c_image = strtolower($t1[0]).'_'.$tmx.$insert_name_part.'.'.strtolower($t1[count($t1)-1]);

        $ret = false;
        $ret = move_uploaded_file($tmp_name, $up_loc.$new_c_image);
        if($ret===false)
        {
            $new_c_image = '';
            $dname = format_str($name);
            $_SESSION[$error_sess_name] = array(false, "ERROR saving {$img_title} ({$dname})!");
        }
        else
        {
            //$new_c_image.= '.'.strtolower($t1[count($t1)-1]);
        }
    }

    return $new_c_image;

}//end func....



/**
 * Function upload_img_external
 * PURPOSE: upload & resize images from external URL
 *
 * $external_url = Image URL
 * $save_as = save image as File Name
 * $img_width = width to resize to
 * $up_loc = upload location for the image. Like '../img/clients'
 * $img_title = Image Title (mainly used for errors)
 * $insert_name_part = Insert Part in File Name (use to create different size images like thunbnail)
 * $fix_height = fix height as well
*/

function upload_img_external($external_url, $save_as, $img_width, $up_loc, $img_title, $insert_name_part='', $fix_height='', $error_sess_name='CUSA_MSG_GLOBAL')
{
    global $_SESSION;

    $file_pth = $up_loc.$save_as.'.jpg';

    $file = @file_get_contents($external_url);
    $saved = @file_put_contents($file_pth, $file);
    //var_dump($saved, strlen($file)); die('x');

    $new_c_image = '';
    if($saved == strlen($file))
    {
        $t1 = $save_as;

        $tmx = time();
        $tmx = substr($tmx, 0, strlen($tmx)-2);

        $new_c_image = strtolower($t1).'_'.$tmx;

        list($g_width, $g_height) = @getimagesize($file_pth);
        if(($g_width>$img_width) || ($g_width<($img_width-1))) $g_width = $img_width;

        $pres_ratio = true;
        if(!empty($fix_height))
        {
            $g_height = $fix_height;
            $pres_ratio = false;
        }

        $ret = false;
        $ret = make_image($file_pth, $up_loc, $g_width, $g_height, '', 'image/jpg', $new_c_image.$insert_name_part, $pres_ratio);
        if($ret===false)
        {
            $new_c_image = '';
            $_SESSION[$error_sess_name] = array(false, "ERROR saving {$img_title}!");
        }
        else
        {
            $new_c_image.= '.jpg';
        }
    }

    return $new_c_image;

}//end func....
?>