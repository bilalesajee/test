<?php
session_start();
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(isset($_POST))
{
	$lock				=	$_POST['lock'];
	$brandlock			=	$_POST['brandlock'];
	$clientlock			=	$_POST['clientlock'];
	$countrylock		=	$_POST['countrylock'];
	$deadlinelock		=	$_POST['deadlinelock'];
	$supplierlock		=	$_POST['supplierlock'];
	
	$id 				= 	$_POST['id'];
	$orderstatus		=	0;
	$fkstatusid			=	$_POST['fkstatusid'];
	if($fkstatusid==1 || $fkstatusid==2)
	{
		$orderstatus	=	1;
	}
	//status check
	if(!$fkstatusid)
	{
		$status			=	1;
	}
	else
	{
		$status			=	$fkstatusid;
	}
	$unit				=	$_POST['unit'];	
	$barcode			=	htmlentities($_POST['barcode'],ENT_QUOTES);
	$brand				=	$_POST['brand'];
	$itemdescription	=	htmlentities($_POST['itemdescription'],ENT_QUOTES);
	$supplier			=	$_POST['supplier'];
	$shipmentid			=	$_POST['fkshipmentid'];
	$description		=	htmlentities($_POST['description'],ENT_QUOTES);
	$country			=	$_POST['country'];
	$clientinfo			=	$_POST['clientinfo'];
	$quantity			=	$_POST['quantity'];
	$pricelimit			=	$_POST['pricelimit'];
	$price				=	$_POST['price'];
	$agreedprice		=	$_POST['agreedprice'];
	$weight				=	$_POST['weight'];
	$comments			=	$_POST['comments'];
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
	if($orderstatus	!=1 && $id!='-1')
	{
		$msg	.=	"<li>Order status must be Request or Final.</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	// checking if the barcode exists in db -- adjusting itemdescription
	$ifexists	=	$AdminDAO->getrows("barcode","pkbarcodeid,itemdescription","barcode='$barcode'");
	$exists		=	$ifexists[0]['itemdescription'];
	if($exists)
	{
		//changing item description with original item description and brand with original brand
		$pkbarcodeid		=	$ifexists[0]['pkbarcodeid'];
		$itemdescription	=	$exists;
		//$existingbrands		=	$AdminDAO->getrows("barcodebrand","fkbrandid","fkbarcodeid='$pkbarcodeid'");
		//$brandid			=	$existingbrands[0]['fkbrandid'];
	}
	//locking mechanism
	if($brandlock)
	{
		$lockstr.="&brandid=".$brandid;
	}
	if($clientlock)
	{
		$lockstr.="&clientid=".$clientid;
	}
	if($countrylock)
	{
		$lockstr.="&countryid=".$countryid;
	}
	if($deadlinelock)
	{
		$lockstr.="&deadline=".$deadline;
	}
	if($supplierlock)
	{
		$lockstr.="&supplierids=".implode(",",$supplierids);
	}
	//echo $addtime;
	$fields		=	array('fkaddressbookid', 'datetime', 'fkstoreid', 'fkcustomerid', 'fkshipmentid', 'barcode', 'itemdescription', 'quantity', 'deadline', 'lastsaleprice', 'pricelimit', 'agreedprice', 'weight', 'fkbrandid', 'fkcountryid', 'description', 'comments', 'productimage', 'clientinfo', 'fkstatusid','unit');
	if($id!='-1')
	{
		// updating image
		if($image!='')
		{
			@unlink('../orderimage/'.$oldimage);
		}
		else
		{
			$defaultimage	=	$oldimage;	
		}
		//updating data
		$values		=	array($addressbookid,$addtime,$store,$clientid,$shipmentid,$barcode,$itemdescription,$quantity,$deadline,$price,$pricelimit,$agreedprice,$weight,$brandid,$countryid,$description,$comments,$defaultimage,$clientinfo,$status,$unit);
		$AdminDAO->updaterow("`order`",$fields,$values,"pkorderid='$id'");
	}
	else
	{
		if($barcode!="")
		{
			$orderrec		=	$AdminDAO->getrows("`order`","pkorderid,quantity","barcode='$barcode' AND fkstatusid IN (1)");
			$pkorderid		=	$orderrec[0]['pkorderid'];
			$oldquantity	=	$orderrec[0]['quantity'];	
			$newquantity	=	$oldquantity+$quantity;	
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
				$values		=	array($addressbookid,$addtime,$store,$clientid,$shipmentid,$barcode,$itemdescription,$quantity,$deadline,$price,$pricelimit,$agreedprice,$weight,$brandid,$countryid,$description,$comments,$defaultimage,$clientinfo,$status,$unit);		
					
				$id	=	$AdminDAO->insertrow("`order`",$fields,$values);
			}			
		}
		else
		{
			$values		=	array($addressbookid,$addtime,$store,$clientid,$shipmentid,$barcode,$itemdescription,$quantity,$deadline,$price,$pricelimit,$agreedprice,$weight,$brandid,$countryid,$description,$comments,$defaultimage,$clientinfo,$status,$unit);		
				
			$id	=	$AdminDAO->insertrow("`order`",$fields,$values);
		}
	}
	// removing existing values
	$AdminDAO->deleterows("ordersupplier","fkorderid='$id'",1);
	// adding new suppliers
	$sfields	=	array('fkorderid','fksupplierid');
	foreach($supplierids as $sid)
	{
		$svalues	=	array($id,$sid);
		$AdminDAO->insertrow("ordersupplier",$sfields,$svalues);
	}
	/*****************************************************************************************/	
	//notification when adding order without barcode	
	if($_POST['barcode']=='')
	{
		echo "without barcode";
		exit;
	}	
	/*****************************************************************************************/		
	//notification when adding a same item again.
	//$prevorder	=	$AdminDAO->getrows("`order`,`orderstatuses`","pkorderid","barcode='$barcode' AND fkstatusid=pkstatusid AND statusname='Request'");
	if($quantityupdated=='1')
	{
		echo "same barcode";
		exit;
	}
	/*****************************************************************************************/	
	if($lock)
	{
		echo "alock=1".$lockstr;
	}

}
?>