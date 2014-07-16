<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$barcodeid 	=	$_REQUEST['id'];
$barcodeid	=	explode(',',$barcodeid);
$productid 	=	$_REQUEST['productid'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	foreach($barcodeid as $bc)
	{
		if($bc!='' && $productid!='')
		{
			//$sql="UPDATE barcode set fkproductid='$productid' where pkbarcodeid='$bc'";
			//$AdminDAO->queryresult($sql);
			
			$fields		=	array('fkproductid');
			$values		=	array($productid);
			$table		=	"barcode";
			$AdminDAO->updaterow($table,$fields,$values,"pkbarcodeid='$bc'");		
		}
	}
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$productname	=	$_REQUEST['productname'];
	$result		=	$AdminDAO->getrows("product","pkproductid","productname='$productname'");
	if(isset($result) && count($result)==0)
	{
		echo "There is no any product with this name. Please take help with autocomplete box...";
		exit;	
	}
	else
	{
		foreach($barcodeid as $bc)
		{
			if($bc!='')
			{
				$prodata	=	$AdminDAO->getrows("barcode","fkproductid","pkbarcodeid='$bc'");
				$oldproid	=	$prodata[0]['fkproductid'];
				//1. Removing item related data from Product Instance
				//a. Fetch old product attributeid
				$productattributes	=	$AdminDAO->getrows("productinstance,attributeoption,attribute","fkproductattributeid,fkattributeoptionid","fkbarcodeid='$bc' AND fkattributeid=pkattributeid AND fkattributeoptionid=pkattributeoptionid ORDER BY attributeposition ASC");
				$AdminDAO->deleterecord("productinstance", "fkbarcodeid","$bc");
				//2. Fetch product related data
				for($i=0;$i<sizeof($productattributes);$i++)
				{
					$productattributeid		=	$productattributes[$i]['fkproductattributeid'];
					$attributeoptionid		=	$productattributes[$i]['fkattributeoptionid'];
					//fetch attributeoption id
					$attributeoptions		=	$AdminDAO->getrows("attributeoption ao,productattribute pa","pkattributeoptionid,attributeoptionname","ao.fkattributeid='$productattributeid' AND ao.fkattributeid=pa.fkattributeid AND pa.fkproductid='$oldproid'");
					$fields2 				=	array('fkproductattributeid','fkattributeoptionid','fkbarcodeid');
					$values2				=	array($productattributeid,$attributeoptionid,$bc);
					$AdminDAO->insertrow('productinstance',$fields2,$values2);
				}
				if($bc!='' && $productid!='')
				{
					///$sql="UPDATE barcode set fkproductid='$productid' where pkbarcodeid='$bc'";
		
					$tblj	= 	'barcode';
					$field	=	array('fkproductid');
					$value	=	array($productid);
					
					$AdminDAO->updaterow($tblj,$field,$value,"pkbarcodeid='$bc'");					
		
					///$AdminDAO->queryresult($sql);
					//3. Updating Product name ...
					$productnameis	=	$AdminDAO->updateproductname($bc);
					echo $productnameis;
				}
			}
		}
	}
}//end edit
exit;
?>