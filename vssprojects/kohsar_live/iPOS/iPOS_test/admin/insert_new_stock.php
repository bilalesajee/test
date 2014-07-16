<?php
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;



	

		
	
	for($i=0;$i<sizeof($_POST);$i++)
	{
		/*if($_POST['tradeprice_'.$i]	== 0 || $_POST['tradeprice_'.$i] == '')
		{
			continue;
		}*/
		
		 $stockid = $_POST['stock1_'.$i];
		/*echo  $_POST['newstock_'.$i]; echo "-----";
		echo  $_POST['tradeprice_'.$i];echo "-----";
		echo  $_POST['retailprice_'.$i];echo "-----";
		 exit;*/
		if($_POST['newstock_'.$i] !="")
		{ 
    $fields		=	array('quantity');
	$values		=	array($_POST['newstock_'.$i]);
	$table		=	"$dbname_detail.stock";
	$AdminDAO->updaterow($table,$fields,$values,"pkstockid='$stockid'");
		}
	if($_POST['tradeprice_'.$i] !="")
		{ 
	$fields		=	array('priceinrs','retailprice');
	$values		=	array($_POST['tradeprice_'.$i]);
	$table		=	"$dbname_detail.stock";
	$AdminDAO->updaterow($table,$fields,$values,"pkstockid='$stockid'");
		}
		if($_POST['retailprice_'.$i] !="")
		{ 
	$fields		=	array('retailprice');
	$values		=	array($_POST['retailprice_'.$i]);
	$table		=	"$dbname_detail.stock";
	$AdminDAO->updaterow($table,$fields,$values,"pkstockid='$stockid'");

		}
/*if($val != '' and is_integer($val))
{
	$rep = " retailprice='{$val}' ";
}

$q = " update stock set qty = $qty {$rep} {$trad} where pkstockid = $stockid ";*/


	
	
}
   
			
	

?>