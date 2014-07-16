<?php
	$domain	=	"http://localhost/pos/";
	define(IMGPATH,$domain.'images/');
	define(JS,$domain.'includes/js/');
	define(CSS,$domain.'includes/css/');
	include($path."includes/classes/AdminDAO.php");
	include($path."includes/classes/Component.php");
	include_once($path."includes/classes/DiscountDAO.php");
	$AdminDAO 	= 	new AdminDAO;
	$Component 	= 	new Component;
	$DiscountDAO= 	new DiscountDAO;
?>