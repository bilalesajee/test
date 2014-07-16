<?php
session_start();
$image	 	=	$_FILES['orderfile']['name'];
$imagename 	=	explode(".",$image);
$image2 	=	$imagename[0];
$imageext   =	$imagename[1];
$filename 	=	$_SESSION['addressbookid']."_".$image2."_".time().".".$imageext;
if($image!='')
{
	if(move_uploaded_file($_FILES['orderfile']['tmp_name'],"../invoiceimports/$filename"))
	{
		echo "The file $image has been uploaded successfully.";
		$_SESSION['orderinvoicefile']	=	$filename;
	}
	/*if($oldimage!='')
	{
		@unlink('../orderimports/'.$oldimage);
	}*/
}
?>