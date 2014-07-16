<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_GET['id'];
$quantity	=	$AdminDAO->getrows("stock","unitsremaining","pkstockid='$id'");
$qty=$quantity[0]['unitsremaining'];
print"<input type='text' id='quantity' value='$qty' size='5'>";

?>