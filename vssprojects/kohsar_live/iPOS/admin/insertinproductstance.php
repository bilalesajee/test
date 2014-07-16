<?php
include("../includes/security/adminsecurity.php");
//error_reporting(0);
global $AdminDAO;
if(sizeof($_POST)>0)
{
	//add section
	$barcode		=	trim(filter($_POST['bc'])," ");
	$editid			=	$_POST['barcode'];
	$brandid		=	$_POST['brand'];
	$productid		=	$_POST['id'];
	$fields 		=	array('barcode','fkproductid');
	$values			=	array($barcode,$productid);
	if($barcode=="")
	{
		echo "Please enter barcode to continue.";
		exit;
	}
	if($editid)
	{
		$unique		=	$AdminDAO->isunique('barcode', 'pkbarcodeid', $editid, 'barcode', $barcode);
		if($unique)
		{
			echo "Barcode already exists, please choose a different barcode";
			exit;
		}
		else
		{
			$AdminDAO->updaterow('barcode',$fields,$values,"pkbarcodeid = '$editid'");
			$barcodeid	=	$editid;
		}
	}
	if($editid)
	{
		$AdminDAO->deleterecord("barcodebrand","fkbarcodeid='$editid'");
		
	}
	else
	{
		$unique	=	$AdminDAO->getrows('barcode','*',"barcode='$barcode'");
		if($unique)
		{
			echo "Barcode already exists, please choose a different barcode";
			exit;
		}
		else
		{
			$barcodeid		=	$AdminDAO->insertrow('barcode',$fields,$values);
		}
	}
	$barcodebrandfields		=	array('fkbarcodeid','fkbrandid');
	$barcodebrandvalues		=	array($barcodeid,$brandid);
	$barcodebrand			=	$AdminDAO->insertrow('barcodebrand',$barcodebrandfields,$barcodebrandvalues);
	$attributeoptions		=	$_POST['attributeoptions'];
	if($editid)
	{
		$AdminDAO->deleterecord("productinstance", "fkbarcodeid='$editid'");
	}
	if(count($attributeoptions)>0)
	{
		foreach($attributeoptions as $options)
		{
			$attrib_array 		=	explode("_",$options);
			$attribute 			=	$attrib_array[0];//this is not product attribute
			$attributeoptions	=	$attrib_array[1];
			$fields2 			=	array('fkproductattributeid','fkattributeoptionid','fkbarcodeid');
			$values2			=	array($attribute,$attributeoptions,$barcodeid);
			$AdminDAO->insertrow('productinstance',$fields2,$values2);
		}
	}
	else
	{
		$fields2 			=	array('fkproductattributeid','fkattributeoptionid','fkbarcodeid');
		$values2			=	array('-1','-1',$barcodeid);
		$AdminDAO->insertrow('productinstance',$fields2,$values2);
	}
	echo $barcode.'__barcode';
}
?>
