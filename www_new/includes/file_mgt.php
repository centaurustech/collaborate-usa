<?php
/**
 * Function delete_files
 * Purpose: delete all files & folders with a given directory
*/
function delete_files($dirname)
{
   if (is_dir($dirname))
      $dir_handle = opendir($dirname);
   if (!$dir_handle)
      return false;

   while($file = readdir($dir_handle))
   {
      if ($file != "." && $file != "..")
      {
         //var_dump($dirname.'/'.$file);
         if (!is_dir($dirname."/".$file))
         {
            @unlink($dirname."/".$file);
         }
         else
         {
            delete_files($dirname."/".$file);
            @rmdir($dirname."/".$file);
         }
      }
   }
   closedir($dir_handle);
   return true;
}//end func...
?>