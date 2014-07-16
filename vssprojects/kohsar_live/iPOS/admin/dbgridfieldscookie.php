<?php
ob_start();
//prepares the list of unchecked fields and saves it in a cookie
$fields		=	trim($_GET['field'],',');
$screen		=	$_GET['screen'];
//$status	=	$_GET['status'];

	setcookie("datafields$screen","$fields");

ob_end_flush();
?>