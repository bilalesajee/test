<?php
session_start();
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
foreach ($_POST as $key=>$val)
{
	echo '$'."$key	=	".'$_POST[\''.$key.'\'];'."\n";
}
echo "</pre>";*/
if(isset($_POST))
{
	$lock				=	$_POST['lock'];
	$id 				= 	$_POST['id'];
	$unit				=	$_POST['unit'];	
	$barcode			=	htmlentities($_POST['barcode'],ENT_QUOTES);
	$brand				=	$_POST['brand'];
	$itemdescription	=	htmlentities($_POST['itemdescription'],ENT_QUOTES);
	$supplier			=	$_POST['supplier'];
	$description		=	htmlentities($_POST['description'],ENT_QUOTES);
	$country			=	$_POST['country'];
	$clientinfo			=	$_POST['clientinfo'];
	$quantity			=	$_POST['quantity'];
	$pricelimit			=	$_POST['pricelimit'];
	$price				=	$_POST['price'];
	$agreedprice		=	$_POST['agreedprice'];
	$weight				=	$_POST['weight'];
	$comments			=	htmlentities($_POST['comments'],ENT_QUOTES);
	$deadline			=	implode("-",array_reverse(explode("-",$_POST['deadline'])));
	$defaultimage		=	$_POST['defaultimage'];
	$store				=	$_POST['store'];
	$brandid			=	$_POST['brandid'];
	$supplierid			=	$_POST['supplierid'];
	$supplierid			=	trim($supplierid,",");
	$supplieridarr		=	explode(",",$supplierid);
	$supplierids		=	array_unique($supplieridarr);
	$countryid			=	$_POST['countryid'];
	$clientid			=	$_POST['clientid'];
	$addressbookid		=	$_SESSION['addressbookid'];
	$addtime			=	date('Y-m-d h:i:s',time());
	/*****************************************************************************************/
	$image 		 		=	$_FILES['image']['name'];
	$imagename 	 		=	explode(".",$image);
	$image2 	 		=	$imagename[0];
	$imageext			=	$imagename[1];
	$defaultimage		=	$image2.".".$imageext;
	$oldimage	 		=	$_POST['oldimage'];
	/*****************************************************************************************/
	if($itemdescription == "")
	{
		$msg	=	"<li>Item Name can not be left Blank.</li>";
	}
	if($quantity=="")
	{
		$msg	.=	"<li>Quantity can not be left Blank.</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	$fields		=	array('fkaddressbookid', 'datetime', 'fkstoreid', 'fkcustomerid', 'fkshipmentid', 'barcode', 'itemdescription', 'quantity', 'deadline', 'lastsaleprice', 'pricelimit', 'agreedprice', 'weight', 'fkbrandid', 'fkcountryid', 'description', 'comments', 'productimage', 'clientinfo', 'fkstatusid','unit');
	// updating image
	if($image!='')
	{
		@unlink('../orderimage/'.$oldimage);
	}
	else
	{
		$defaultimage	=	$oldimage;	
	}

	/*$values		=	array($addressbookid,$addtime,$store,$clientid,$id,$barcode,$itemdescription,$quantity,$deadline,$price,$pricelimit,$agreedprice,$weight,$brandid,$countryid,$description,$comments,$defaultimage,$clientinfo,2);
	echo "<pre>";
	print_r($values);
	echo "</pre>";
	exit;
	$orderid	=	$AdminDAO->insertrow("`order`",$fields,$values);*/
	
	
	
	
	
	
	
	
	
	
	if($barcode!="")
	{
		$orderrec			=	$AdminDAO->getrows("`order`","pkorderid,quantity","barcode='$barcode' AND fkshipmentid='$id'");
		$pkorderid			=	$orderrec[0]['pkorderid'];
		$oldquantity		=	$orderrec[0]['quantity'];	
		$newquantity		=	$oldquantity+$quantity;	
		$quantityupdated	=	0;
		if($pkorderid!='')
		{
			$fields33	=	array('quantity');
			$values33	=	array($newquantity);
			$AdminDAO->updaterow("`order`",$fields33,$values33,"pkorderid='$pkorderid'");
			$quantityupdated	=	1;
		}
		else
		{
			$values		=	array($addressbookid,$addtime,$store,$clientid,$id,$barcode,$itemdescription,$quantity,$deadline,$price,$pricelimit,$agreedprice,$weight,$brandid,$countryid,$description,$comments,$defaultimage,$clientinfo,2,$unit);
			$orderid	=	$AdminDAO->insertrow("`order`",$fields,$values);
		}			
	}
	else
	{
		$values		=	array($addressbookid,$addtime,$store,$clientid,$id,$barcode,$itemdescription,$quantity,$deadline,$price,$pricelimit,$agreedprice,$weight,$brandid,$countryid,$description,$comments,$defaultimage,$clientinfo,2,$unit);
		$orderid	=	$AdminDAO->insertrow("`order`",$fields,$values);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/*****************************************************************************************/		
	//notification when adding a same item again.
	if($quantityupdated=='1')
	{
		echo "same barcode";
	}	
	//for redirecting on shipment in process screen
	if($id!='-1')
	{
		$resultset		=	$AdminDAO->getrows("shipment","fkstatusid", " pkshipmentid= '$id' ");
		$status			=	$resultset[0]['fkstatusid'];		
		if($status==3) 
		{
			echo "in process";
			exit;
		}
	}	
	// adding new suppliers
	$sfields	=	array('fkorderid','fksupplierid');
	foreach($supplierids as $sid)
	{
		$svalues	=	array($orderid,$sid);
		$AdminDAO->insertrow("ordersupplier",$sfields,$svalues);
	}
	echo $lock;
}
?>