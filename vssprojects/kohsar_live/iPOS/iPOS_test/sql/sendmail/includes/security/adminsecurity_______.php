<?php
	@session_start();
	//ini_set("error_reporting",0);
	$path	=	"d:/wamp/www/pos/";
	include_once($path."includes/conf/conf.php");
	include_once($path."includes/conf/userconfig.php");
	include_once($path."includes/classes/valid.php");
	require_once($path."includes/classes/button.php");
	require_once($path."includes/classes/filter.php");
	include_once($path."includes/classes/paging.class.php");
	include_once($path."includes/classes/usersecurity.php");
	include_once($path."includes/classes/DiscountDAO.php");
	$button 		=	new button();
	$Paging			=	new PagedResults();
	$V				=	new Validator();
	$userSecurity 	=	new userSecurity();
	$DiscountDAO 	=	new DiscountDAO();
	$qs		=	"";
	$storeid			=	$_SESSION['storeid'];
	$empid				=	$_SESSION['addressbookid'];
	$closingsession		=	$_SESSION['closingsession'];
	//echo $closingsession."is the closing session";
	// This is Counter Name. To Be configured while setting POS on the Counter.
	//*************************************************************************
	$employee = $AdminDAO->getrows('employee,addressbook','*',"pkaddressbookid	=		fkaddressbookid AND `pkemployeeid`='$empid' AND employeedeleted != '1'");
	$employeename = $employee[0]['firstname'].' '.$employee[0]['lastname'];
	$store = $AdminDAO->getrows('store','*',"pkstoreid	= $storeid ");
	$storename = $store[0]['storename'];
	$pc				=	$_SESSION['pc'];
	if($pc=='pc')
	{
		$pcemployeename	=	$_SESSION['pcusername'];
		header("Location:userlogin.php?pos=$countername&e=$pc&name=$pcemployeename");
		exit;	
		
	}
?>