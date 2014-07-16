<?php
	@session_start();
	$path	=	"d:/wamp/www/esajee/pos/";
	//if (file_exists("../conf/conf.php"))
	//{
		include_once($path."includes/conf/conf.php");
		include_once($path."includes/classes/valid.php");
//	}
	//else
	//{
		//include_once("../includes/conf/conf.php");
	//}
	include($path."includes/classes/button.php");
	include($path."includes/classes/filter.php");
	include_once($path."includes/classes/paging.class.php");
	include_once($path."includes/classes/usersecurity.php");
	$button =	new button();
	$Paging	=	new PagedResults();
	$V		=	new Validator();
	$userSecurity = new userSecurity;
	$qs		=	"";
	$empid		=	1;
	$storeid	=	1;
	$_SESSION['storeid']	=	$storeid;
	$_SESSION['employeeid']	=	$empid;	
	$employee = $AdminDAO->getrows('employee,addressbook','*',"pkaddressbookid	=		fkaddressbookid AND `pkemployeeid`='$empid' AND employeedeleted != '1'");
	$employeename = $employee[0]['firstname'].' '.$employee[0]['lastname'];
	$store = $AdminDAO->getrows('store','*',"pkstoreid	=		$storeid");
	$storename = $store[0]['storename'];
	
?>