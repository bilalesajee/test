<?php
include("../includes/security/adminsecurity.php");
//error_reporting(0);
global $AdminDAO;
$barcode			=	filter($_POST['bc']);
if($barcode!='')
{
	$editid				=	$_POST['barcode'];
	if($editid)
	{
		$unique		=	$AdminDAO->isunique('barcode', 'pkbarcodeid', $editid, 'barcode', $barcode);
		if($unique)
		{
			echo "Barcode already exists, please choose a different barcode";
			exit;
		}
	}
	else
	{
		$barcoderow		=	$AdminDAO->getrows('barcode', 'pkbarcodeid', " 1 and barcode='$barcode'");
		$barcodefound	=	$barcoderow[0]['pkbarcodeid'];	
		if($barcodefound!='')
		{
			echo "Barcode already exists, please choose a different barcode.";
			exit;
		}
	}
}
?>