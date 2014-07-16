<?php
include("../includes/security/adminsecurity.php");
//error_reporting(0);
global $AdminDAO;
if(sizeof($_POST)>0)
{
	//add section
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$brandflag			=	$_POST['brandflag'];
	}//end edit
	$barcode			=	filter($_POST['bc']);
	$editid				=	$_POST['barcode'];
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$shortdesc			=	$_POST['shortdesc'];
	}//end edit
	$brandid			=	$_POST['brand'];
	$productid			=	$_POST['id'];
	$boxstatus			=	$_POST['boxstatus'];
	$boxquantity		=	$_POST['boxqty'];
	if($boxstatus!=0)
	{
		$boxitem			=	$_POST['boxitem'];
	}
	$attributeoptions	=	$_POST['attributeoptions'];
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		$fields 			=	array('barcode','fkproductid','boxquantity','boxbarcode');
		$values				=	array($barcode,$productid,$boxquantity,$boxitem);
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$fields 			=	array('barcode','fkproductid','boxquantity','boxbarcode','shortdescription');
		$values				=	array($barcode,$productid,$boxquantity,$boxitem,$shortdesc);
	}//end edit
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
			if($boxstatus!=1)
			{
				$boxquantity	=	0;
				$boxitem		=	"";
			}
			if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
				$values				=	array($barcode,$productid,$boxquantity,$boxitem);
			}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
				$values				=	array($barcode,$productid,$boxquantity,$boxitem,$shortdesc);
			}//end edit
			$AdminDAO->updaterow('barcode',$fields,$values,"pkbarcodeid = '$editid'");
			$barcodeid	=	$editid;
		}
	}

	if($brandid=="")
	{
		echo "Please select a Brand to continue.";
		exit;
	}
	if($boxstatus==1)
	{
		if($boxquantity	<1)
		{
			echo "Please select box quantity to continue.";
			exit;
		}
	}
	if(sizeof($attributeoptions)>0)
	{
		foreach($attributeoptions as $opt)
		{
			if($opt!="")
			{
				$flag	+=	1;
			}
		}
	}
	if($flag <1)
	{
		echo "Please select at least one Option to continue.";
		exit;
	}
	if($editid)
	{
		$AdminDAO->deleterecord("barcodebrand","fkbarcodeid","$editid");
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
	if($editid)
	{
		$AdminDAO->deleterecord("productinstance", "fkbarcodeid","$editid");
	}
	if(count($attributeoptions)>0)
	{
		foreach($attributeoptions as $options)
		{
			$attrib_array 		=	explode("_",$options);
			$attribute 			=	$attrib_array[0];//this is not product attribute
			$attributeoptions	=	$attrib_array[1];
			if($attributeoptions !='')
			{
				$fields2 			=	array('fkproductattributeid','fkattributeoptionid','fkbarcodeid');
				$values2			=	array($attribute,$attributeoptions,$barcodeid);
				$AdminDAO->insertrow('productinstance',$fields2,$values2);
			}
		}
	}
//print"$productid";
}
if($barcode!='')
{
	$AdminDAO->updateproductname($barcode,'barcode');
}
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	if($brandflag)
	{
		// to retain the brands page
		echo "brand";
		exit;
	}
}//end edit

/*
SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,') ',brn.brandname) PRODUCTNAME, b.barcode as bc FROM productattribute pa RIGHT JOIN (product p, attribute a) ON ( pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, barcode b, barcodebrand bb, brand brn WHERE pkproductid = pa.fkproductid AND pkattributeid = pa.fkattributeid AND pkproductattributeid = fkproductattributeid AND pkattributeid = ao.fkattributeid AND pkattributeoptionid = pi.fkattributeoptionid AND b.fkproductid = pkproductid AND pi.fkbarcodeid = b.pkbarcodeid AND brn.pkbrandid = bb.fkbrandid AND bb.fkbarcodeid = b.pkbarcodeid AND b.barcode ='010900000840' GROUP BY bc 
*/
?>