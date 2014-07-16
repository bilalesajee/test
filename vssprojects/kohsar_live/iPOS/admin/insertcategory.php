<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_REQUEST['id'];
if(sizeof($_POST)>0)
{
	/*echo "<pre>";
	print_r($_POST);
	echo "</pre>";*/
	
	$name			=	filter($_POST['categoryname']);
	if($name=="")
	{
		echo "Category Name can not be left empty";
		exit;
	}
	if($name)
	{
		$unique = $AdminDAO->isunique('category', 'pkcategoryid', $id, 'name', $name);
		if($unique=='1')
		{
			echo"Category with this name <b><u>$name</u></b> already exists. Please choose another name.";	
			exit;
		}
	}
	$description	=	filter($_POST  ['description']);
	$categories		=	$_POST['categories'];
	$fields			=	array('name','description','categoryimage');
	$oldimage		=	$_POST['oldimage'];
	$image		 	=	$_FILES['imageField']['name'];
	$imagename 	 	=	explode(".",$image);
	$image2 	 	=	$imagename[0];
	$imageext		=	$imagename[1];
	$filename 		=	$image2.".".$imageext;
	if($image!='')
	{
		if($oldimage!='')
		{
			@unlink('../categoryimage/'.$oldimage);
		}
	}
	else
	{
		$filename=$oldimage;	
	}
	$values			=	array($name,$description,$filename);	
	
	if($id!='-1')
	{
		$AdminDAO->updaterow("category",$fields,$values,"pkcategoryid = '$id'");
		$AdminDAO->deleterecord("subcategory","fkcategoryid", "$id");
	}
	else
	{
		$id				=	$AdminDAO->insertrow("category",$fields,$values);
	}
	if(sizeof($categories) > 0)
	{
		for($i=0;$i<sizeof($categories);$i++)
		{
			$prows		=	$AdminDAO->getrows("subcategory","fkcategoryid", "pksubcatid = '$categories[$i]'");
			$parent		=	$prows[0][fkcategoryid];
			$values2	=	array($id,$parent);
			$exists		=	 $AdminDAO->getrows("subcategory","count(*) as rows", "fkparentid = '$parent' AND fkcategoryid='$id'");
			if($exists[0]['rows'] ==0)//if this relation already exists between parent and the category then don't add again
			{
				$parent_exists		=	 $AdminDAO->getrows("subcategory","count(*) as rows", "fkparentid = '$id' AND fkcategoryid='$parent'");
				if($parent_exists[0]['rows']==0)//if a reverse relation is present then don't make the entry
				{
					$fields2	=	array("fkcategoryid","fkparentid");
					$AdminDAO->insertrow("subcategory",$fields2,$values2);
				}
			}//if
		}
	}
	else//adding a  record to tell that this is parent category
	{
		$values2	=	array($id,0);
		$fields2	=	array("fkcategoryid","fkparentid");
		$AdminDAO->insertrow("subcategory",$fields2,$values2);
	}
}
else
{
	echo "false";
}
?>