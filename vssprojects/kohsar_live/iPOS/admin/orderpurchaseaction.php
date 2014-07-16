<?php
session_start();
error_reporting(7);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
*/
if(sizeof($_POST)>0)
{
	$defaultproduct		=	$_POST['productid'];
	$totalqty			=	$_POST['quantity'];
	$brand				=	$_POST['brand'];
	$productbarcode		=	$_POST['barcode'];
	$itemdescription	=	$_POST['itemdescription'];
	$productbarcodeid	=	$_POST['barcodeid'];
	$purchasequantity	=	$_POST['purchasequantity'];
	$productweight		=	$_POST['weight'];
	$productsupplier	=	$_POST['suppliers'];
	$productsupplierid	=	$_POST['supplierid'];
	$newsupp			=	$_POST['newsupp'];
	$purchaseprice		=	$_POST['purchaseprice'];
	$productbatch		=	$_POST['batch'];
	$expirydate			=	$_POST['expiry'];
	$markedas			=	$_POST['mark'];
	$productorderid		=	$_POST['orderid'];
	$shipmentid			=	$_POST['id'];
	$purchasetime		=	time();
	$fields		=	array('fkaddressbookid','datetime','fkshipmentid','fkorderid','fkbarcodeid','quantity','purchaseprice','weight','fksupplierid','batch','expiry');
	$ofields	=	array('fkstatusid');
	for($c=0;$c<sizeof($productbarcode);$c++)
	{
		// check qty and item description
		if($_POST['check'.$c]==1 && ($purchasequantity[$c]=='' || $purchaseprice[$c]==''))
		{
			$r	=	$c+1;
			$msg.=	"<li>Purchase Quantity or Purchase Price missing in $r</li>";
		}
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	for($i=0;$i<sizeof($productbarcode);$i++)
	{
		if($_POST['check'.$i]==1)
		{
			$barcode		=	$productbarcode[$i];
			$itemdesc		=	$itemdescription[$i];
			$orderquantity	=	$totalqty[$i];
			$barcodeid		=	$productbarcodeid[$i];
			$brandid		=	$brand[$i];
			if($barcodeid=='' || $barcodeid==0)// have to create a new item
			{
				$field		= array('barcode','itemdescription','fkproductid');
				$value	 	= array($barcode,$itemdesc,$defaultproduct);
				$barcodeid	= $AdminDAO->insertrow('barcode',$field,$value);
				if($brandid)
				{
					$f2			=	array('fkbrandid','fkbarcodeid');
					$v2			=	array($brandid,$barcodeid);
					$AdminDAO->insertrow('barcodebrand',$f2,$v2);
				}
			}
			$mark			=	$markedas[$i];
			$orderid		=	$productorderid[$i];
			$addressbookid	=	$_SESSION['addressbookid'];
			$addtime		=	date('Y-m-d h:i:s',time());
			$quantity		=	$purchasequantity[$i];
			$weight			=	$productweight[$i];
			$supplier		=	$productsupplier[$i];
			if($newsupp[$i]!='')
			{
				$f3			=	array('companyname','supplierdeleted');
				$v3			=	array($newsupp[$i],'0');
				$insertedid	=	$AdminDAO->insertrow('supplier',$f3,$v3);
				$supplierid	=	$insertedid;
			}
			else
				$supplierid		=	$productsupplierid[$i];
			$price			=	$purchaseprice[$i];
			$batch			=	$productbatch[$i];
			$expiry			=	implode("-",array_reverse(explode("-",$expirydate[$i])));
			if($supplierid)
			{
				$supplier	=	$supplierid;
			}
			if($expiry!='')
			{
				if($expiry<date('Y-m-d'))
				{
					$msg	.=	"<li>Please enter valid expiry date</li>";
				}
			}	
			if($msg)
			{
				echo $msg;
				exit;
			}
			else
			{
				$data	=	array($addressbookid,$addtime,$shipmentid,$orderid,$barcodeid,$quantity,$price,$weight,$supplier,$batch,$expiry);
				$orderpurchaseid	=	$AdminDAO->insertrow("orderpurchase",$fields,$data);
				//iserting default allocation by jafer balti
				$jfields	=	array("fkaddressbookid","datetime","fkshipmentid","fkorderid","fkorderpurchaseid","fkstoreid","quantity");
				//$jvalues	=	array($addressbookid,$addtime,$shipmentid,$orderid,$orderpurchaseid,'4',$quantity);
				//$AdminDAO->insertrow("orderallot",$jfields,$jvalues);
				
				$storearray	= 	$AdminDAO->getrows("store","pkstoreid", "storedeleted<>1 AND storestatus=1");
				$avg	=	floor($quantity/sizeof($storearray));
				if($avg==0)$avg=1;
				for($j=0;$j<sizeof($storearray);$j++)
				{
					$storeid		=	$storearray[$j]['pkstoreid'];
					if($j==sizeof($storearray)-1)$avg=$quantity;//last iteration
					$jvalues	=	array($addressbookid,$addtime,$shipmentid,$orderid,$orderpurchaseid,$storeid,$avg);
					$AdminDAO->insertrow("orderallot",$jfields,$jvalues);
					$quantity	=	$quantity-$avg;
					if($quantity==0)$avg=0;
				}
				
				
				
				
				
				// calculating purchases and updating order status
				$totalpurchases	=	$AdminDAO->getrows("orderpurchase","sum(quantity) quantity","fkorderid='$orderid'");
				$totalpurchased	=	$totalpurchases[0]['quantity'];
				if($orderquantity==$totalpurchased)
				{
					$status		=	3; // purchased
				}
				if($orderquantity>$totalpurchased)
				{
					$status		=	4; // partial purchase
				}
				if($orderquantity<$totalpurchased)
				{
					$status		=	5; // extra purchase
				}
				$odata			=	array($status);
				$AdminDAO->updaterow("`order`",$ofields,$odata,"pkorderid='$orderid'");
			}
		}
	}
}// end post
?>