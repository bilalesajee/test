<?php ob_start();
$from = "POS Online Check <kohsar@esajee.com>";
//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";
//$to = "fahadbuttqau@gmail.com,accounts@esajee.com,hesajee@gmail.com";
$to = "fahadbuttqau@gmail.com";
$cuptame=$_GET['uptime'];
$cdowntame=$_GET['downtime'];
$headers='';
if($cuptame!=''){
echo $subject = "Esajee Kohsar Counter ".$_GET['counter']." is now ONline ";	
	
echo $body="Esajee Kohsar Counter ".$_GET['counter']." ONline time is ".$cuptame."";	
	}else{
$subject = "Esajee Kohsar Counter ".$_GET['counter']." is now Offline ";		
$body="Esajee Kohsar Counter ".$_GET['counter']." Offline time is ".$cdowntame."";
	}
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
echo $headers .= "From:" . $from;
echo $msent=mail($to,$subject,$body,$headers);
?>