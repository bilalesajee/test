<?php
error_reporting(7);
session_start();

$date			=	date('Y-m-d h:i:s');
$counter 		= 	$_GET['counter'];
$closing		=	$_GET['closing'];
$cashier		=	$_GET['cashier'];
$accounttitle 	= 	$_GET['accounttitle'];
$currentpayout	=	$_GET['currentpayout'];
?>
<link rel="stylesheet" type="text/css" href="https://kohsar.esajee.com/includes/css/style.css" />
<div align="left">
<div > <img src="https://kohsar.esajee.com/images/esajeelogo.jpg" width="150" height="50"><br />
   <b>Think globally shop locally</b> <br />
  <?php echo $storenameadd;?></span> </div>
<div > Date: <?php echo $date; ?> </div>
<div > Counter: <?php echo $counter;?> </div>
<div > Closing #: <?php echo $closing;?> </div>
<div > Cashier: <?php echo $cashier; ?> </div>
<table width="300" style="font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;" class="simple">
  <tr>
  	<th align="left">Account Title</th>
    <th align="right"><?php echo $accounttitle;?></th>
  </tr>
   <tr>
    <th align="left">Current Payout</th>
    <th align="right"><?php echo $currentpayout;?></th>
  </tr>
</table>
<div align="center">
  <?php 
echo $date; 
?>
</div>