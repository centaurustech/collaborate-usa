<?php
/**
 * Set Sort/Display Order with Push instead of Replace
 * This will move other Records (up or down), you will have to update the current Record outside this function
 *
 * @param $table = table name
 * @param $sortField = name of the field that hold sorting number
 * @param $sortOrderExisting = existing value of sort position
 * @param $sortOrderTarget = target position of sort
 * @param $sqlVars (optional) = additional SQL to be inserted into the query.
*/
function setSortOrder($table, $sortField, $sortOrderExisting, $sortOrderTarget, $sqlVars = "")
{
    ## if both values arent the same then proceed
    if($sortOrderExisting!=$sortOrderTarget){

    ## checking sql whether the target sort order exists in database or not
    $sql = "SELECT * FROM $table WHERE $sortField='{$sortOrderTarget}' $sqlVars";
    $result = mysql_query($sql);

    ## if target sort order exists then proceed
    if(mysql_num_rows($result)>0)
    {
        ## Update All - based on conditions depending on the sort order being greater or less than target sort order
        if($sortOrderExisting > $sortOrderTarget){
        $sql = "UPDATE $table SET $sortField=$sortField+1 WHERE $sortField>=$sortOrderTarget and $sortField<$sortOrderExisting $sqlVars";
        }else{
        $sql = "UPDATE $table SET $sortField=$sortField-1 WHERE $sortField<=$sortOrderTarget and $sortField>$sortOrderExisting $sqlVars";
        }
        mysql_query($sql) or die(mysql_error());
    }
    }
}//end func....
?>