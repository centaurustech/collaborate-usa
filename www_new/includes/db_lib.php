<?php
/**
* MySQL Execution Function

* @author Raheel Hasan
* @version 2.0

* @param $query = SQL Query
* @param $type = [single{=multiple columns of a single record}, multi{=multiple records}, save{=insert/delete/update}], default=multi

* @return array
*/

function mysql_exec($query, $type='')
{
	$data = '';

    $result = mysql_query($query);
	if($result!=false)
	{
        if($type=='single')
		{
			$data = mysql_fetch_array($result, MYSQL_ASSOC);
		}
        else if($type=='save')
		{
			$data = $result;
		}
		else
		{
			$data = array();
            while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			$data[] = $row;
		}
	}
    else{$data = false;}

	return $data;
}///////////end function ......
?>