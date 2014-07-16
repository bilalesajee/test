<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;

$brandid		=	$_REQUEST['brandid'];
$brand			=	filter($_REQUEST['brand']);
$selcountries	=	$_REQUEST['countries'];
$selsuppliers	=	$_REQUEST['suppliers'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$parentbrands	=	$_REQUEST['parentbrands'];
}//end edit
/*if($nsupplier!='' && $nsupplier!='Add New')
{
	$sfield		=	array('companyname');
	$svalue 	= 	array($nsupplier);
	$supplierid	=	$AdminDAO->insertrow('supplier',$sfield,$svalue);	
}*/
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$v				=	array("Brand Name,$brand,e","Brand Country,$selcountries,e","Brand Suppliers,$selsuppliers,e");
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$v				=	array("Brand Name,$brand,e");
}//end edit
if ($V->validate($v) ==1)
{
	echo $V->msg;
	exit;
}
else
{
	$table		=	"brand";
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
		$table2 	=	"brandsupplier";
		$field		=	array('brandname','fkcountryid');
		$value		=	array($brand,$selcountries);
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$field		=	array('brandname','fkparentbrandid','fkcountryid');
		$value		=	array($brand,$parentbrands,$selcountries);
	}//end edit
	$ifexists	=	$AdminDAO->getrows('brand','brandname',"`brandname`='$brand' AND `pkbrandid` <> '$brandid' and fkcountryid='$selcountries' ");
	if($ifexists[0]['brandname'])
	{
		echo "Brand Name already exists, please choose a different Brand Name.";
		exit;
	}
	else if($brandid=="-1")
	{
		$brandid = $AdminDAO->insertrow($table,$field,$value);
			//echo "here5";
	}
	else
	{
		if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
			$AdminDAO->deleterows($table2,"`fkbrandid`='$brandid'",1);
		}//end edit
		$AdminDAO->updaterow($table,$field,$value,"`pkbrandid`='$brandid'");
			//echo "here6";
	}
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
		$field2 = array('fkbrandid','fksupplierid');
	
			foreach($selsuppliers as $supplier)
			{
				$value2 = array($brandid,$supplier);
				if($supplier!='')
				{
					$AdminDAO->insertrow($table2,$field2,$value2);	
				}
			
			}
		if($supplierid!='')
		{
			$value2 = array($brandid,$supplierid);
			$AdminDAO->insertrow($table2,$field2,$value2);		
		}
	}//end edit
	//echo "Brand data saved successfully.";
}//else
//echo $err;
?>