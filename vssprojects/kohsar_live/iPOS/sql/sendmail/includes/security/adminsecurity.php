<?php
	@session_start();
	//echo $_SESSION['closingsession'];
	//echo time();
	//ini_set("error_reporting",0);
	//$path	=	"d:/wamp/www/esajee/pos/";
	//$newpath	=	realpath(dirname(__FILE__));
	//$path		=	str_replace('includes\security', '', $newpath);
	$newpath	=	$_SERVER['DOCUMENT_ROOT'].'/';
	$path		=	str_replace('includes\security', '', $newpath);
	error_reporting(0);	
	include_once($path."includes/security/security.php");
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
	$empid				=	$_SESSION['addressbookid'];
	$closingsession		=	$_SESSION['closingsession'];
	
	// Date Modified : 26/01/2010
	// Checking if the empid and countername isn't missing to address duplicate closings
	$area	=	$_GET['area'];//area have the parameter from where the bill request came area=store means bill is printed by store
	
	if($_GET['param']!='admin')
	{
		if($area=='')
		{
			if((!$empid) || (!$countername))
			{
				$counter	=	$_SESSION['countername'];
				session_destroy();
				echo "<script>$(location).attr('href','userlogin.php?pos=$counter');</script>";
				exit;
			}
		}
	}
	//echo $closingsession."is the closing session";
	// This is Counter Name. To Be configured while setting POS on the Counter.
	//*************************************************************************
	if(!$empid)
	{
		$employee = $AdminDAO->getrows('employee,addressbook','*',"pkaddressbookid	=		fkaddressbookid AND `pkemployeeid`='$empid' AND employeedeleted != '1'");
		$employeename = $employee[0]['firstname'].' '.$employee[0]['lastname'];
		$store = $AdminDAO->getrows('store','*',"pkstoreid	= '$storeid' ");
		$storename = $store[0]['storename'];
	}
	$pc				=	$_SESSION['pc'];
	
	if($pc=='pc')
	{
		$pcemployeename	=	$_SESSION['pcusername'];
		header("Location:userlogin.php?pos=$countername&e=$pc&name=$pcemployeename");
		exit;	
		
	}
$closingsession	=	$_SESSION['closingsession'];
if(!isset($closingsession) || $closingsession=='' || $closingsession==0)
{			
	$closingquery	=	"SELECT pkclosingid from $dbname_main.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
	$closingarray	=	$AdminDAO->queryresult($closingquery);
	$closingsession	=	$closingarray[0][pkclosingid];

	if($closingsession!='' || $closingsession!=0)
	{
		$_SESSION['closingsession']=$closingsession;
	}

}
?>