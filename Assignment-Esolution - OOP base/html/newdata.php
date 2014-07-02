<?php
require_once('dbmanager.php');
$obj=new dbmanager("localhost","root","","esolution-assignment");
$q = "SELECT * FROM person ORDER BY id DESC ";
$array = $obj->fetch_result($q);
if (count($array)>0) 
{
    //foreach ($array as $k => $v)
    foreach ($array as $v) 
    {
        echo "<div style='font-size:40px;margin-left:200px'>{$v['ID']} of {$v['NAME']}</div>\n";
    }
  
} 
else
{
   echo 'Nothing found';   
}
?>
