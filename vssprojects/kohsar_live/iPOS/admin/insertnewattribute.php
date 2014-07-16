<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
/********************************COUNTRIES***********************************/
/*print_r($_POST);
exit;*/
if(sizeof($_POST)>0)
{
	$newattribute	=	$_POST['newattribute'];
	$productid		=	$_POST['productid'];
	$barcode		=	$_POST['barcode'];
	$attributetype	=	$_POST['attributetype'];	
	if($newattribute=='n') //adding new attributes 
	{
		$attributename	=	$_POST['attributename'];
		$attnamerow		=	$AdminDAO->getrows("attribute","*"," attributename='$attributename' and pkattributeid<>'$attributeid'");
		if(count($attnamerow)>0)
		{
			print"<li>This attribute name <b><u> $attributename </u></b> already exists.</li>";	
			exit;
		}
		if($attributename!='')
		{
			$attributefield	=	array('attributename');
			$attributevalue	=	array($attributename);
			$newattribid	=	$AdminDAO->insertrow('attribute',$attributefield,$attributevalue);
			$atbindfields	=	array('fkproductid','fkattributeid','attributetype');
			$atbindvalues	=	array($productid,$newattribid,$attributetype);
			$newattribid	=	$AdminDAO->insertrow('productattribute',$atbindfields,$atbindvalues);
			echo "=".$barcode;
		}
		else
		{
			print"<li>Attribute name must be provided which was (<b><u>EMPTY</u></b>)</li>";
			exit;
		}
	}// case 1 adding new attribute done
	if($newattribute=='n2') //adding attribute with options -- new attachment with product 
	{
		$newattributes	=	$_POST['newattributes']; //attributeid
		$options		=	$_POST['option']; // Setting Up Options at runtime
		if(sizeof($options)<0)
		{
			print"<li>Please make sure you have entered at least one option.</li>";	
			exit;
		}
		$flag	=	0;
		$flagx	=	0;
		for($x=0;$x<sizeof($options);$x++)
		{
			$attnamerow		=	$AdminDAO->getrows("attributeoption","*"," attributeoptionname='$options[$x]' AND fkattributeid = '$newattributes'");
			if(count($attnamerow)>0)
			{
				$flag	= 1;
			}
			$optionsx	=	trim($options," ");
			if($options[$x]=='')
			{
				$flagx++;
			}
		}
		if($flagx==3)
		{
			print"<li>Please make sure you have entered at least one option.</li>";	
			exit;
		}
		if($flag==1)
		{
			print"<li>One of the option name already exists.</li>";	
			exit;
		}
		$optfields		=	array('attributeoptionname','fkattributeid');
		for($i=0;$i<sizeof($options);$i++)
		{
			if($options[$i]!='')
			{
				$optvalues	=	array($options[$i],$newattributes);
				$AdminDAO->insertrow('attributeoption',$optfields,$optvalues);
			}
		}
		$fields			=	array('fkproductid','fkattributeid','attributetype');
		$values			=	array($productid,$newattributes,$attributetype);
		$AdminDAO->insertrow('productattribute',$fields,$values);
		echo "=".$barcode;
	}
	if($newattribute=='n3')
	{
		$options		=	$_POST['option']; // Setting Up Options at runtime
		$newattributes	=	$_POST['oldattribute'];
		$flag	=	0;
		$flagx	=	0;
		for($x=0;$x<sizeof($options);$x++)
		{
			$attnamerow		=	$AdminDAO->getrows("attributeoption","*"," attributeoptionname='$options[$x]' AND fkattributeid = '$newattributes'");
			if(count($attnamerow)>0)
			{
				$flag	= 1;
			}
			$optionsx	=	trim($options," ");
			if($options[$x]=='')
			{
				$flagx++;
			}
		}
		if($flagx==3)
		{
			print"<li>Please make sure you have entered at least one option.</li>";	
			exit;
		}
		if($flag==1)
		{
			print"<li>One of the option name already exists.</li>";	
			exit;
		}
		$optfields		=	array('attributeoptionname','fkattributeid');
		for($i=0;$i<sizeof($options);$i++)
		{
			if($options[$i]!='')
			{
				$optvalues	=	array($options[$i],$newattributes);
				$AdminDAO->insertrow('attributeoption',$optfields,$optvalues);
			}
		}
		echo "=".$barcode;
	}
}
?>