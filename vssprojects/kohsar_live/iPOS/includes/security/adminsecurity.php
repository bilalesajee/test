<?php
@session_start();

    $posfolder	="vssprojects".DIRECTORY_SEPARATOR."kohsar_live".DIRECTORY_SEPARATOR."iPOS";  //Only Enter Folder Name
    
	$Comment_Print_=0; //For Disable Print
    $newpath	=	$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.$posfolder.DIRECTORY_SEPARATOR;
    $path		=	str_replace('includes'.DIRECTORY_SEPARATOR.'security', '', $newpath);

	
	include_once($path."includes/conf/conf.php");
	include_once($path."includes/conf/accountconfig.php");
	include_once($path."includes/classes/valid.php");
	require_once($path."includes/classes/button.php");
	require_once($path."includes/classes/filter.php");
	include_once($path."includes/classes/paging.class.php");
	include_once($path."includes/classes/usersecurity.php");
	include_once($path."includes/classes/DiscountDAO.php");
   	include_once($path."includes/classes/actions.php");
	include_once($path."includes/classes/del.php");

	
 	$button 		=	new button();
	$Paging			=	new PagedResults();
	$V				=	new Validator();
	$userSecurity 	=	new userSecurity();

	
	
if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){

		if (!isset($_SESSION['countername']) || $_SESSION['countername'] == '' || $_SESSION['countername'] == 0  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' || $_SESSION['addressbookid'] == 0){
		 echo '<script>location.reload();</script>';
		 exit;
		}
	
	$DiscountDAO 	=	new DiscountDAO();
	$qs		=	"";
 $empid		=	$_SESSION['addressbookid'];
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
	$closingquery	=	"SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
	$closingarray	=	$AdminDAO->queryresult($closingquery);
	$closingsession	=	$closingarray[0][pkclosingid];

	if($closingsession!='' || $closingsession!=0)
	{
		$_SESSION['closingsession']=$closingsession;
	}

}

   }else{ 

    $_SESSION['countername']=-1;
     	
	if (!isset($_SESSION['admin_section']) || !isset($_SESSION['storeid']) || $_SESSION['storeid'] == ''  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' ){
		 header("Location:../admin/userlogin.php");
		 exit;
		}//end add code
		$del	=	new del();
	$qs		=	"";
	$empid		=	$_SESSION['addressbookid'];
	$storeid	=	$_SESSION['storeid'];
	if($storeid	!=	 '')
	{
		$companyid	=	0;
		$prefixid	=	'esajee_';
	
		//***********Groupid and owner id here for permission**
		$groupid		=	$_SESSION['groupid'];
		$storename		=	$_SESSION['storename'];
		$ownergroup		=7;
	
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
}//end else

?>