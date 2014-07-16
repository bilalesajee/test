<?php
if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){//edit by ahsan 20/02/2012
	@session_start();
	//echo $_SESSION['closingsession'];
	//echo time();
	//ini_set("error_reporting",0);
	//$path	=	"d:/wamp/www/esajee/pos/";
	//$newpath	=	realpath(dirname(__FILE__));
	//$path		=	str_replace('includes\security', '', $newpath);
	$newpath	=	$_SERVER['DOCUMENT_ROOT'].'/';
	$path		=	str_replace('includes\security', '', $newpath);
		$dbname_detail			=	"kohsar_test";//this is table name for detail of the
	//start add from security.php by ahsan 03/02/2012
	if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){//added if condition by ahsan 16/02/2012, 
	//security.php code block for POS
		if (!isset($_SESSION['countername']) || $_SESSION['countername'] == '' || $_SESSION['countername'] == 0  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' || $_SESSION['addressbookid'] == 0 ){
		 /*$counter	=	$_SESSION['countername'];
		 $msg = 'Please login';	 
		 header("Location:userlogin.php?pos=$counter&msg=$msg");*/
		 echo '<script>location.reload();</script>';
		 exit;
		}
	}else{
		//security.php code block for main/store
					//start code from store_secuity.php, add by ahsan 15/02/2012
		if (!isset($_SESSION['admin_section']) || !isset($_SESSION['storeid']) || $_SESSION['storeid'] == ''  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' ){
		 header("Location:../admin/userlogin.php");
		 exit;
		}//end add code
	}//end add from security.php
		$dbname_detail			=	"kohsar_test";//this is table name for detail of the
	include_once($path."includes/conf/conf.php");
//	include_once($path."includes/conf/userconfig.php");//add comment by ahsan 22/02/2012
	include_once($path."includes/conf/accountconfig.php");
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
	$dbname_detail			=	"kohsar_test";//this is table name for detail of the
	
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
	$closingquery	=	"SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
	$closingarray	=	$AdminDAO->queryresult($closingquery);
	$closingsession	=	$closingarray[0][pkclosingid];

	if($closingsession!='' || $closingsession!=0)
	{
		$_SESSION['closingsession']=$closingsession;
	}

}
$dbname_detail			=	"kohsar_test";//this is table name for detail of the
}else{//end if request_uri, edit by ahsan 
	//error_reporting(7);
	@session_start();
	/*echo "<pre>";
	print_r($_SESSION);
	echo "</pre>";*/
	//error_reporting(7);
	//$path	=	"/home/esajeeso/public_html/test/";
	//$newpath	=	realpath(dirname(__FILE__));//for windows server
		$dbname_detail			=	"kohsar_test";//this is table name for detail of the
	$newpath	=	$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;//for linux server
	$path		=	str_replace('includes/security', '', $newpath);
	//start add from security.php by ahsan 03/02/2012
	if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){//added if condition by ahsan 16/02/2012, 
	//security.php code block for POS
		if (!isset($_SESSION['countername']) || $_SESSION['countername'] == '' || $_SESSION['countername'] == 0  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' || $_SESSION['addressbookid'] == 0 ){
		 /*$counter	=	$_SESSION['countername'];
		 $msg = 'Please login';	 
		 header("Location:userlogin.php?pos=$counter&msg=$msg");*/
		 echo '<script>location.reload();</script>';
		 exit;
		}
	}else{
		//security.php code block for main/store
					//start code from store_secuity.php, add by ahsan 15/02/2012
		if (!isset($_SESSION['admin_section']) || !isset($_SESSION['storeid']) || $_SESSION['storeid'] == ''  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' ){
		 header("Location:../admin/userlogin.php");
		 exit;
		}//end add code
	}//end add from security.php
	
	require_once($path."includes/conf/conf.php");
	//$dbname_detail.'this is dbname_detail';
	include_once($path."includes/classes/valid.php");	
	include_once($path."includes/classes/button.php");
	include_once($path."includes/classes/actions.php");//line added from main by ahsan 22/02/2012
	include_once($path."includes/classes/filter.php");
	include_once($path."includes/classes/paging.class.php");
	include_once($path."includes/classes/usersecurity.php");
	include_once($path."includes/classes/del.php");
		$dbname_detail			=	"kohsar_test";//this is table name for detail of the
	//include_once($path."pos/includes/classes/DiscountDAO.php");
	$button =	new button();
	$Paging	=	new PagedResults();
	$V		=	new Validator();
	//$AdminDAO=	new AdminDAO();
	$del	=	new del();
	$userSecurity = new userSecurity;
	$qs		=	"";
	$empid		=	$_SESSION['addressbookid'];
	$storeid	=	$_SESSION['storeid'];
	if($storeid	!=	 '')
	{
		$companyid	=	0;
		$prefixid	=	'esajee_';
		//$_SESSION['storeid']	=	$storeid;
		//***********Groupid and owner id here for permission**
		$groupid		=	$_SESSION['groupid'];
		$storename		=	$_SESSION['storename'];
		$ownergroup		=7;
		/******************************************************
		/*$_SESSION['employeeid']	=	$empid;	
		$employee = $AdminDAO->getrows('employee,addressbook','*',"pkaddressbookid	=		fkaddressbookid AND `pkemployeeid`='$empid' AND employeedeleted != '1'");
		$employeename = $employee[0]['firstname'].' '.$employee[0]['lastname'];*/
		if($storename=='')
		{
			$store = $AdminDAO->getrows('store','*',"pkstoreid	=		'$storeid'");
			$storename = $store[0]['storename'];
			$_SESSION['storename']=$storename;
		}
	}
	else
	{
		header("Location:../admin/userlogin.php");
		exit;
	}
	if(!function_exists(dump))
	{
		function dump($var)
		{
			print"<pre>";	
			print_r($var);
			print"</pre>";
		}
	}
	//$rights	=	$userSecurity->getRights(17);
	//print_r($rights);
	//print"<br>=====================================";
	//dump($_SESSION);
	//echo $dbname_detail;
		$dbname_detail			=	"kohsar_test";//this is table name for detail of the
}//end else
?>