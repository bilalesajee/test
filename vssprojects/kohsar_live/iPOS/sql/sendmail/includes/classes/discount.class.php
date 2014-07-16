<?php
session_start();
/* Discounts Class */
class Discount
{
	public $discountObj,$datetoday,$QoQ,$AoQ,$AoA,$PoP,$QQstat,$AQstat,$AAstat,$PPstat;
	function Discount($Obj)
	{
		$this->discountObj	=	$Obj;
		$this->datetoday	=	time();
	}
	//start getDiscount
	function setDiscount($id,$qty,$price,$saletotal,$storeid)
	{
		//fetch all discount types
		//echo $query		=	"SELECT pkdiscountid,MAX(quantity),fkdiscounttypeid FROM discount WHERE quantity<='$qty' AND fkdiscounttypeid<>2 GROUP BY fkdiscounttypeid";
		$date		=	$this->datetoday;
		/*$discountquery	=	"SELECT * FROM discount,discounttype WHERE fkdiscounttypeid=pkdiscounttypeid AND FROM_UNIXTIME( startdate, '%d-%m-%Y' ) <= '$date' AND FROM_UNIXTIME( enddate, '%d-%m-%Y' ) >= '$date' AND discountstatus='a' AND fkstoreid='$storeid' ORDER BY priority ASC";*/
		$discountquery	=	"(SELECT * FROM discount,discounttype WHERE fkdiscounttypeid=pkdiscounttypeid AND startdate <= '$date' AND enddate >= '$date' AND discountstatus='a' AND fkstoreid='$storeid' AND fkbarcodeid='$id' AND quantity<='$qty' AND fkdiscounttypeid=1 ORDER BY quantity DESC LIMIT 0,1)
		UNION 
		(SELECT * FROM discount,discounttype WHERE fkdiscounttypeid=pkdiscounttypeid AND startdate <= '$date' AND enddate >= '$date' AND discountstatus='a' AND fkstoreid='$storeid' AND fkbarcodeid='$id' AND quantity<='$qty' AND fkdiscounttypeid=2 ORDER BY quantity DESC LIMIT 0,1)
		UNION 
		(SELECT * FROM discount,discounttype WHERE fkdiscounttypeid=pkdiscounttypeid AND startdate <= '$date' AND enddate >= '$date' AND discountstatus='a' AND fkstoreid='$storeid' AND amount<='$saletotal' AND fkdiscounttypeid=3 ORDER BY amount DESC LIMIT 0,1) 
		UNION 
		(SELECT * FROM discount,discounttype WHERE fkdiscounttypeid=pkdiscounttypeid AND startdate <= '$date' AND enddate >= '$date' AND discountstatus='a' AND fkstoreid='$storeid' AND fkbarcodeid='$id' AND quantity<='$qty' AND fkdiscounttypeid=4 ORDER BY quantity DESC LIMIT 0,1)";
		//echo $discountquery;
		$discounts	=	$this->discountObj->queryresult($discountquery);
		// iterating between discounts
		for($i=0;$i<sizeof($discounts);$i++)
		{
			$setdiscounttype	=	0;
			$discounttype		=	$discounts[$i]['fkdiscounttypeid'];
			$discountid			=	$discounts[$i]['pkdiscountid'];
			$combine			=	$discounts[$i]['combine'];
			$priority			=	$discounts[$i]['priority'];
			// setting highest priority
			$setpriority		=	$discounts[0]['priority'];
			// if similar priority i.e. = highest encountered
			if($setpriority	==	$priority)
			{
				$setdiscounttype	=	$discounttype;
			}
			// setting combination
			if($combine == 1)
			{
				$setdiscounttype	=	$discounttype;
			}
			$freelimit		=	$discounts[$i]['quantity'];
			$amount			=	$discounts[$i]['amount'];
			$freeamount		=	$discounts[$i]['amountoff'];
			$amountofftype	=	$discounts[$i]['amountofftype'];
			switch ($setdiscounttype)
			{
				case 1://quantity on quantity
				$this->setQtyDiscount($id,$qty,$freelimit,$discountid,$combine,$priority);
				break;
				case 2://amount on quantity
				$this->setAmountQtyDiscount($id,$qty,$price,$freelimit,$freeamount,$amountofftype,$combine,$priority,$discountid);
				break;
				case 3://amount on amount
				$this->setAmountDiscount($saletotal,$amount,$freeamount,$amountofftype,$combine,$priority,$discountid);
				break;
				case 4://product on product
				$this->setProductDiscount($id,$qty,$freelimit,$discountid,$combine,$priority);
				break;
				default:
				// unsupported types here
				break;
			}
		}
	}//end of function getDiscount
	// start Quantity on Quantity Discount
	function setQtyDiscount($pid,$baseqty,$base,$did,$cstat,$priority)
	{
		//retrieving target quantity
		$discountdetails	=	$this->discountObj->getrows("discount d,discountdetail dt","dt.quantity quantity,d.fkbarcodeid","fkdiscountid=pkdiscountid AND pkdiscountid='$did' AND d.fkbarcodeid='$pid' ");
		$free				=	$discountdetails[0]['quantity'];
		$fkbarcodeid		=	$discountdetails[0]['fkbarcodeid'];
		//checking limits and assigning values
		if($baseqty>=$base)
		{
			$factor							=	floor($baseqty/$base);
			$this->QoQ						=	$free*$factor;
			$this->QQstat['combine']		=	$cstat;
			$this->QQstat['priority']		=	$priority;
			$this->QQstat['pkbarcodeid']	=	$fkbarcodeid;
			$this->QQstat['discountid']		=	$did;
		}
	}// End Q on Q Discount
	// start Amount on Qty Discount
	function setAmountQtyDiscount($pid,$baseqty,$price,$base,$off,$type,$cstat,$priority,$did)
	{
		$discountdetails	=	$this->discountObj->getrows("discount","1","pkdiscountid='$did' AND fkbarcodeid='$pid' ");
		//checking limits and assigning values
		$result	=	sizeof($discountdetails);
		//echo "$baseqty>=$base and the result is $result";
		if($baseqty>=$base && sizeof($discountdetails)>0)
		{
			// flat off
			if($type==1)
			{
				$this->AoQ		=	$off;
				$this->AQstat	=	$cstat;
				$this->QQstat['discountid']		=	$did;
			}
			// percentage off
			else 
			{
				$this->AoQ					=	$baseqty*$price*$off/100;
				$this->AQstat['combine']	=	$cstat;
				$this->AQstat['priority']	=	$priority;
				$this->QQstat['discountid']		=	$did;
			}
			
		}
	}// End A on Q Discount
	// start Amount on Amount Discount
	function setAmountDiscount($total,$baseamount,$off,$type,$cstat,$priority,$did)
	{
		//echo "Amount on amount is : $total>=$baseamount";
		if($total>=$baseamount)
		{
			// flat off
			if($type==1)
			{
				$this->AoA					=	$off;
				$this->AAstat['combine']	=	$cstat;
				$this->AAstat['priority']	=	$priority;
				$this->AAstat['priority']	=	$did;
			}
			// percentage off
			else
			{
				$this->AoA					=	$total*$off/100;
				$this->AAstat['combine']	=	$cstat;
				$this->AAstat['priority']	=	$priority;
				$this->AAstat['priority']	=	$did;
			}
		}
	}// End A on A Discount
	// start Product on Product Discount
	function setProductDiscount($pid,$baseqty,$limit,$did,$cstat,$priority)
	{
		//retrieving target values
		//$this->discountObj->dq	=	1;
		$discountdetails	=	$this->discountObj->getrows("discount d,discountdetail dt","dt.quantity,dt.fkbarcodeid","fkdiscountid=pkdiscountid AND fkdiscountid='$did' AND d.fkbarcodeid='$pid'");
		//$this->discountObj->dq	=	0;
		$disct	=	sizeof($discountdetails);
		//echo " product on product and the size of $disct";
		for($pro=0;$pro<sizeof($discountdetails);$pro++)
		{
			$freeqty			=	$discountdetails[$pro]['quantity'];
			$fkbarcodeid		=	$discountdetails[$pro]['fkbarcodeid'];
			//checking limits and assigning values
			if($baseqty>=$limit)
			{
				$factor							=	floor($baseqty/$limit);
				$this->PoP[$pro]['basepro']		=	$pid;
				$this->PoP[$pro]['qty']			=	$freeqty*$factor;
				$this->PoP[$pro]['pkbarcodeid']	=	$fkbarcodeid;
				$this->PPstat['combine']		=	$cstat;
				$this->PPstat['priority']		=	$priority;
				$this->PPstat['discountid']		=	$did;
			}
		}
	}// End P on P Discount
	// start Product on Product and Quantity on Quantity effect on sales
	function addPopSales($pkbarcodeid,$db,$saleid,$qty,$closingid,$did)
	{
		//flush previous discount before new insert operation
		$this->discountObj->deleterows("$db.saledetail","fksaleid='$saleid' AND fkdiscountid<>0","1");
		//setting up new values
		for($i=0;$i<sizeof($pkbarcodeid);$i++)
		{
			$barcodeid	=	$pkbarcodeid[$i]['pkbarcodeid'];
			$quantity	=	$qty[$i]['quantity'];
			$type		=	$did[$i]['type'];
			$stockquery	=	"SELECT 
								pkstockid,
								MAX(retailprice) retailprice
							FROM 
								$db.stock,barcode
							WHERE 
								fkbarcodeid = barcode.pkbarcodeid AND
								barcode.pkbarcodeid='$barcodeid'
							";
			$stockdata	=	$this->discountObj->queryresult($stockquery);
			//retrieving item details
			$stockid	=	$stockdata[0]['pkstockid'];
			$saleprice	=	$stockdata[0]['retailprice'];
			//checking existing sales
			$salesdata	=	$this->discountObj->getrows("$db.saledetail","1","fkstockid='$stockid' AND saleprice='$saleprice' AND fkclosingid='$closingid' AND quantity='$quantity' AND fksaleid='$saleid' AND fkdiscountid<>0");
			if(sizeof($salesdata)==0)
			{
				//echo "<div id=\"txxx$i\" style=\"background-color:#fff;color:#fff;\">SELECT 1 FROM $db.saledetail WHERE fkstockid='$stockid' AND saleprice='$saleprice' AND fkclosingid='$closingid' AND quantity='$quantity' AND fksaleid='$saleid'</div>";
				//checking pricechange table 
				$pricedata	=	$this->discountObj->getrows("$db.pricechange","price","fkbarcodeid='$barcodeid'");
				if(sizeof($pricedata)>0)
				{
					$saleprice	=	$pricedata[0]['price'];
				}
				$fields		=	array('fksaleid','fkstockid','quantity','saleprice','originalprice','fkdiscountid','timestamp','fkclosingid');
				$data		=	array($saleid,$stockid,$quantity,$saleprice,$saleprice,$type,time(),$closingid);
				$this->discountObj->insertrow("$db.saledetail",$fields,$data);
			}
		}// end for
	}//end P on P and Q on Q effect on sales
	//start amount on amount and amount on quantity implementation
	function addAoqSales($totalamount,$amountoff,$saleid,$db,$closingid)
	{
		//flush salediscount before new insert operation
		$this->discountObj->deleterows("$db.salediscount","fksaleid='$saleid'","1");
		for($i=0;$i<sizeof($totalamount);$i++)
		{
			$total	=	$totalamount['amountoff'][$i];
			$type	=	$totalamount['type'][$i];
			$fields	=	array('fkdiscountid','fksaleid','amount','fkclosingid');
			$data	=	array($type,$saleid,$total,$closingid);
			//checking existing sales
			$discountdata	=	$this->discountObj->getrows("$db.salediscount","1","fkdiscountid='$type' AND fksaleid='$saleid' AND fkclosingid='$closingid' AND amount='$total'");
			if(sizeof($discountdata)==0)
			{
				$this->discountObj->insertrow("$db.salediscount",$fields,$data);
			}
		}//end for
		// update sale and set total discount amount
		$sfield		=	array('amountdiscount');
		$sdata		=	array($amountoff);
		$this->discountObj->updaterow("$db.sale",$sfield,$sdata,"pksaleid='$saleid'");
	}// end A on Q and A on A
}// end of class Discount
?>