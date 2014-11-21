<?php
function array2csv(array &$array, $output_key=true)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');

   if($output_key)
   fputcsv($df, array_keys(reset($array)));

   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}
?>