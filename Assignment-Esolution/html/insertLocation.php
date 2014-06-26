<?php
require_once('conn.php');
extract($_POST);
echo $query="INSERT INTO location(CODE,DETAIL,COUNTRY,CITY) VALUES('$locCode','$Detail','$country','$city')";
mysql_query($query);
$id=mysql_insert_id();
$data=compact($id,$locCode,$Detail,$country,$city,array('locCode', 'Detail', 'country', 'city'));
$rs=array('data'=>$data);
echo json_encode($rs);
?>