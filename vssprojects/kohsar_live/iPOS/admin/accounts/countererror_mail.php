<?php 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$counter=$_REQUEST['counter_'];
$er=$_REQUEST['error'];
$date=time();

$datee=date('d-m-Y H:i');
 
$from = "Kohsar Counters <kohsar@esajee.com>";

$to = "notify@esajeesolutions.com";
$body=$date;
$subject = "".$er."  Counter # $counter  $datee";
$headers = "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>