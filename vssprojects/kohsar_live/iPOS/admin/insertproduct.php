<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST))
{
	$multipleattributes	=	array();
	$singleattributes	=	array();
	$id 				= 	$_REQUEST['id'];
	$qs					=	$_SESSION['qstring'];
	$productname 		= 	filter($_POST['productname']);
	$categories			=	$_REQUEST['categories'];
	$singleattributes	=	$_REQUEST['singleattributes'];
	$multipleattributes	=	$_REQUEST['multipleattributes'];
	$systemattributes	=	$_REQUEST['systemattributes'];
	/*****************************************************Validation*********************************/
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		if($productname=='')
		{
			$msg	=	"<li>Product name can not be left blank.</li>";
		}
		else
		{
			$unique = $AdminDAO->isunique('product', 'pkproductid', $id, 'productname', $productname);
			if($unique=='1')
			{
					$msg.="<li>Product with this name <b><u>$newpname</u></b> already exists. Please choose another name.</li>";	
			}
			else
			{
				if((sizeof($singleattributes)==0) && sizeof($multipleattributes)==0)
				{
					$msg	.="<li>Please select at least one attribute to continue.</li>";
				}//if
			}//else
		}//else
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		if($productname=='')
		{
			$msg	=	"<li>Product name can not be left blank.</li>";
		}
	
			$unique = $AdminDAO->isunique('product', 'pkproductid', $id, 'productname', $productname);
			if($unique=='1')
			{
					$msg.="<li>Product with this name <b><u>$newpname</u></b> already exists. Please choose another name.</li>";	
			}
			else
			{
				if((sizeof($singleattributes)==0) && sizeof($multipleattributes)==0)
				{
					$msg	.="<li>Please select at least one attribute to continue.</li>";
				}//if
			}//else
	}//end edit
	if($msg)
	{
		echo $msg;
		exit;
	}
	/************************************************************************************************************************/
	$description 	=	filter($_POST['description']);
	$productname 	= 	filter($_POST['productname']);
	$fields 		=	array('productname','productdescription','defaultimage');
	$values 		=	array($productname, $description, $filename);
	$image 		 	=	$_FILES['image']['name'];
	$imagename 	 	=	explode(".",$image);
	$image2 	 	=	$imagename[0];
	$imageext		=	$imagename[1];
	$filename 		=	$image2.".".$imageext;
	/****************************************************************************************/
	if($id!="-1")
	{
		$oldimage	 	=	$_POST['oldimage'];
		if($image!='')
		{
			@unlink('../productimage/'.$oldimage);
		}
		else
		{
			$filename=$oldimage;	
		}
		$AdminDAO->deleterecord("productcategory","fkproductid","$id");
		$AdminDAO->updaterow("product",$fields,$values," pkproductid='$id' ");
		/***************************************OLD Attributes******************************************/
		$multiold	=	array();
		$singleold	=	array();
		$multi 	=	$AdminDAO->getrows("productattribute","fkattributeid","fkproductid='$id' AND attributetype='m' ");
		for($m=0;$m<sizeof($multi);$m++)
		{
			$multiold[] = $multi[$m]['fkattributeid'];
		}
		$single	=	$AdminDAO->getrows("productattribute","fkattributeid","fkproductid='$id' AND attributetype='s'");
		for($m=0;$m<sizeof($single);$m++)
		{
			$singleold[] = $single[$m]['fkattributeid'];
		}
		/******************************************DELTE OLD RECORDS which are not present now***************************************************/
		//select from productinstance where fkattributeid =
		
			$multiremoved	=	@array_diff($multiold,$multipleattributes);
			if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
				if($multipleattributes=='')
				{
					$multiremoved=$multiold;
				}
			}//end edit
			if(sizeof($multiremoved) > 0)
			{
				$mr				=	implode(',',$multiremoved);
				$query			=	"DELETE 
											productinstance.*
									FROM 
											productinstance,
											productattribute
									WHERE 
											pkproductattributeid	=	fkproductattributeid AND 
											fkproductid				=	$id AND
											fkattributeid IN  ($mr)
								";
				$AdminDAO->queryresult($query);
				
				$query2	=	"DELETE
										productattribute.*
									FROM 
										productattribute
									WHERE 
										fkproductid				=	$id AND
										fkattributeid IN  ($mr) 

								";
				$AdminDAO->queryresult($query2);
			
			}//if
			
			$singleremoved	=	@array_diff($singleold,$singleattributes);
			if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
				if($singleattributes=='')
				{
					$singleremoved=$singleold;
				}
			}//end edit
			if(sizeof($singleremoved))
			{
				$sr				=	implode(',',$singleremoved);
				$query			=	"DELETE 
											productinstance.*
									FROM 
											productinstance,
											productattribute
									WHERE 
											pkproductattributeid	=	fkproductattributeid AND 
											fkproductid				=	$id AND
											fkattributeid IN  ($sr)";
				$AdminDAO->queryresult($query);
				$query2	=	"DELETE
										productattribute.*
									FROM 
										productattribute
									WHERE 
										fkproductid			=	$id AND
										fkattributeid IN  ($sr) 
								";
				$AdminDAO->queryresult($query2);
			}//if
		/******************************ADD NEW RECORDS in produtattributes*********************************/
		$multinew	=	@array_diff($multipleattributes,$multiold);
		$singlenew	=	@array_diff($singleattributes,$singleold);
		/************************************************************************ADDING NEW ATTRIBUTES only*********************/
		if(sizeof($multinew))
		{
			foreach($multinew as $mn)
			{
				$fields	=	array('fkproductid','fkattributeid','attributetype');
				$values	=	array($id,$mn,'m');
				$AdminDAO->insertrow("productattribute",$fields,$values);
			}//foreach
		}//if
		if(sizeof($singlenew))
		{
			foreach($singlenew as $sn)
			{
				$fields	=	array('fkproductid','fkattributeid','attributetype');
				$values	=	array($id,$sn,'s');
				$AdminDAO->insertrow("productattribute",$fields,$values);
			}//foreach
		}//if
		/****************************************************************************************/
	}//if
	else
	{
		$id = $AdminDAO->insertrow("product",$fields,$values);
		/************************************************************************ADDING NEW ATTRIBUTES only*********************/
		for($m=0;$m<sizeof($multipleattributes); $m++)
		{
			$fields	=	array('fkproductid','fkattributeid','attributetype');
			$values	=	array($id,$multipleattributes[$m],'m');
			$AdminDAO->insertrow("productattribute",$fields,$values);
		}//foreach
		
		for($s=0;$s<sizeof($singleattributes); $s++)
		{
			$fields	=	array('fkproductid','fkattributeid','attributetype');
			$values	=	array($id,$singleattributes[$s],'s');
			$AdminDAO->insertrow("productattribute",$fields,$values);
		}//foreach
	}
	/*********************************************ADD CATEGORIES******************************/
	if(sizeof($categories))
	{
		$catfields = array("fkproductid","fkcategoryid");
		foreach($categories as $category)
		{
			$cats	=	$AdminDAO->getrows("subcategory"," fkcategoryid "," pksubcatid = '$category' ");
			$catvalues = array($id,$cats[0]['fkcategoryid']);
			$AdminDAO->insertrow("productcategory",$catfields,$catvalues);
		}
	}
	exit;
}//if
?>