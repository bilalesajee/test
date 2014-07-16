<?php
date_default_timezone_set('Asia/karachi');
error_reporting(7);
session_start();
$date			=	date('Y-m-d h:i:s');

$headers='';
$from = "Kohsar SHOP System <kohsar@esajee.com>";
        //$to = "hesajee@gmail.com,m_esajee@hotmail.com";
$to = "hunaid@esajee.com";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$subject = "Esajee Kohsar Payout Alert on $date";


$counter 		= 	$_GET['counter'];
$closing		=	$_GET['closing'];
$cashier		=	$_GET['cashier'];
$accounttitle 	= 	$_GET['accounttitle'];
$currentpayout	=	$_GET['currentpayout'];
$body='<link rel="stylesheet" type="text/css" href="https://kohsar.esajee.com/includes/css/style.css" />
<div align="left">
<div > <img src="https://kohsar.esajee.com/images/esajeelogo.jpg" width="150" height="50"><br />
   <b>Think globally shop locally</b> <br />
 '.$storenameadd.'</span> </div>
<div > Date: '.$date.'</div>
<div > Counter: '.$counter.' </div>
<div > Closing #: '.$closing.' </div>
<div > Cashier: '.$cashier.' </div>
<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;" class="simple">
  <tr>
  	<th align="left">Account Title</th>
    <th align="right">'.$accounttitle.'</th>
  </tr>
   <tr>
    <th align="left">Current Payout</th>
    <th align="right">'.$currentpayout.'</th>
  </tr>
</table>
<div align="center">'.$date.'</div>';
//echo $body;
	 $msent=mail($to,$subject,$body,$headers);
?>