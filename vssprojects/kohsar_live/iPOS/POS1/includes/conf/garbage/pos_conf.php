<?php
//	error_reporting(7);
	//$domain	=	"http://localhost/esajee/pos/";
	$domainname	=	$_SERVER['HTTP_HOST'];
	$root		=	$_SERVER['DOCUMENT_ROOT'];
	$domaimdir	=	str_replace(realpath($root), '',$path);
	$domaimdir	=	str_replace('\\', '/',$domaimdir);
	$domain		=	"http://".$domainname.$domaimdir;
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