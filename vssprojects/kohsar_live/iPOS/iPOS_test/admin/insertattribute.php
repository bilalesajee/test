<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
	$attributeid	=	$_REQUEST['id'];
	$productid		=	$_REQUEST['productid'];
	$attributename	=	$_REQUEST['attributename'];
	$attributetype	=	$_REQUEST['attribute'];
	$attnamerow	=	$AdminDAO->getrows("attribute","*"," attributename='$attributename' and pkattributeid<>'$attributeid'");
		if(count($attnamerow)>0)
		{
			print"<li>This attribute name <b><u> $attributename </u></b> already exists.</li>";	
			exit;
		}
	if($attributename!='')
	{
		$field		=	array('attributename');
		$value		=	array($attributename);
		if($attributeid=='-1')
		{
			if($productid!='-1')
			{
				$attributefield	=	array('attributename');
				$attributevalue	=	array($attributename);
				$newattribid	=	$AdminDAO->insertrow('attribute',$attributefield,$attributevalue);
				$atbindfields	=	array('fkproductid','fkattributeid','attributetype');
				$atbindvalues	=	array($productid,$newattribid,$attributetype);
				$newattribid	=	$AdminDAO->insertrow('productattribute',$atbindfields,$atbindvalues);
			}
			else
			{
				$attribid = $AdminDAO->insertrow('attribute',$field,$value);
			}
		}
		else
		{
			$attribid = $AdminDAO->updaterow('attribute',$field,$value," pkattributeid='$attributeid' ");
			if($productid!='-1')
			{
				$atbindfields	=	array('fkproductid','fkattributeid','attributetype');
				$atbindvalues	=	array($productid,$attributeid,$attributetype);
				if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
							$productattrib	=	$AdminDAO->updaterow('productattribute',$atbindfields,$atbindvalues,"fkattributeid = '$attributeid'");
				}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
							$productattrib	=	$AdminDAO->updaterow('productattribute',$atbindfields,$atbindvalues,"fkattributeid = '$attributeid' AND fkproductid='$productid'");
				}//end edit
			}
		}
		
	}
	else
	{
	 	print"<li>Attribute name must be provided which was (<b><u>EMPTY</u></b>)</li>";
	 	exit;
	}
exit;
?>