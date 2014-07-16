<?php

error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$customdescription		=	$_POST['customdescription'];
	$poststock				=	$_POST['stock'];
	$postquantity			=	$_POST['quantity'];
	$postcostprice			=	$_POST['costprice'];
	$postsaleprice			=	$_POST['retailprice'];
	$postquoteprice			=	$_POST['quoteprice'];
	$postexpiry				=	$_POST['expiry'];
	$postbatch				=	$_POST['batch'];
	$postpurchaseprice		=	$_POST['purchaseprice'];
	$postpriceinrs			=	$_POST['priceinrs'];
	$postshipmentcharges	=	$_POST['shipmentcharges'];
	$postfkshipmentid		=	$_POST['fkshipmentid'];
	$postfkbarcodeid		=	$_POST['fkbarcodeid'];
	$postfksupplierid		=	$_POST['fksupplierid'];
	$postfkagentid			=	$_POST['fkagentid'];
	$postfkcountryid		=	$_POST['fkcountryid'];
	$postfkbrandid			=	$_POST['fkbrandid'];
	$postshipmentpercentage	=	$_POST['shipmentpercentage'];
	$postboxprice			=	$_POST['boxprice'];
	$taxable				=	$_POST['exempt'];
	//existing units for localstock updates
	$exunits				=	$_POST['existingunits'];
	$purchaseorderid		=	$_POST['id'];
	$fkstoreid				=	$_POST['store'];
	//retrieving store info for local updates
	$detailid				=	$_POST['detailid'];
	$addressbookid			=	$_SESSION['addressbookid'];
	$fields 				= 	array('fkpurchaseorderid','fkstockid','fkbarcodeid','customdescription','addtime','costprice','saleprice','quoteprice','fkaddressbookid','taxable','status');
	for($i=0;$i<sizeof($poststock);$i++)
	{
		$stock			=	$poststock[$i];
		$quoteprice		=	$postquoteprice[$i];
		if($quoteprice=="" || $quoteprice==0)
		{
			continue;// to skip the empty units
		}
		$expiry				=	$postexpiry[$i];
		$priceinrs			=	$postpriceinrs[$i];
		$costprice			=	$postcostprice[$i];
		$saleprice			=	$postsaleprice[$i];
		
		$batch				=	$postbatch[$i];
		$purchaseprice		=	$postpurchaseprice[$i];
		$shipmentcharges	=	$postshipmentcharges[$i];
		$fkshipmentid		=	$postfkshipmentid[$i];
		$fkbarcodeid		=	$postfkbarcodeid[$i];
		$fksupplierid		=	$postfksupplierid[$i];
		$fkagentid			=	$postfkagentid[$i];
		$fkcountryid		=	$postfkcountryid[$i];
		$fkbrandid			=	$postfkbrandid[$i];
		$shipmentpercentage	=	$postshipmentpercentage[$i];
		$boxprice			=	$postboxprice[$i];
		$addtime			=	time();
		$data				=	array($purchaseorderid,$stock,$fkbarcodeid,$customdescription,$addtime,$costprice,$saleprice,$quoteprice,$addressbookid,$taxable,$status);
		
		// this is the edit section
		if($detailid!=-1)
		{
			$cfields		=	array('quoteprice','taxable','customdescription');
			$cdata			=	array($quoteprice,$taxable,$customdescription);
			$AdminDAO->updaterow("$dbname_detail.podetail",$cfields,$cdata,"pkpodetailid='$detailid'");
		}
		else
		{
			//checking previous active items for the same customer
			$customerids	=	$AdminDAO->getrows("$dbname_detail.purchaseorder","fkaccountid","pkpurchaseorderid='$purchaseorderid'");
 			$customerid		=	$customerids[0]['fkaccountid'];
			//selecting active items of the customer
			$existingitems	=	$AdminDAO->getrows("$dbname_detail.purchaseorder po,$dbname_detail.podetail pod","1","po.expired=1 AND po.fkaccountid='$customerid' AND pod.fkpurchaseorderid=po.pkpurchaseorderid AND pod.fkbarcodeid='$fkbarcodeid'");
			if(sizeof($existingitems)>0)
			{
				echo "An active Purchase Order of this customer contains the selected item, please update that item";
				exit;
			}
			else
			{
				$AdminDAO->insertrow("$dbname_detail.podetail",$fields,$data);
			}
		}
	}
}// end post
?>