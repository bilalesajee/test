<?php

require_once('conn.php');
extract($_POST);
$query = "INSERT INTO location(CODE,DETAIL,COUNTRY,CITY) VALUES('$locCode','$Detail','$country','$city')";
mysqli_query($link,$query);
$id = mysql_insert_id();
$data = compact($id, $locCode, $Detail, $country, $city, array('id', 'locCode', 'Detail', 'country', 'city'));
$rs = array('result' => $data);
echo json_encode($rs);
?>