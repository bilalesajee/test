<?php
//include_once("AdminDAO.php");
//$AdminDAO = new AdminDAO();
function getprice($id)
{
	global $AdminDAO,$dbname_detail;
	// paymenttype <> 'c' aadded by Yasir -- 05-07-11
	$query	=	"SELECT 
					pkpaymentid as id,
					paymentmethod as type, 
					amount, 
					paytime, 
					(IF(p.rate IS NULL,0,p.rate)) as rate, 
					currencyname as currency, 
					(IF(charges IS NULL,0,charges)) as charges,
					tendered,
					(IF(returned IS NULL,0,returned)) as returned
				FROM $dbname_detail.payments p
				LEFT JOIN currency
					   ON fkcurrencyid = pkcurrencyid
					WHERE fksaleid = '$id'
					  AND paymenttype <> 'c'					  
				 ORDER BY paytime DESC";
		$queryresult	=	$AdminDAO->queryresult($query);		
		return $queryresult;
}
function getpaidamount($saleid)
{
	global $AdminDAO,$dbname_detail;
	$payments			=	getprice($saleid);
	$salesum 			=	$AdminDAO->getrows("$dbname_detail.saledetail","SUM(saleprice * quantity) as price"," fksaleid = '$saleid' AND fkdiscountid=0");
	$price				=	$salesum[0]['price'];
	//dealing with foreign currency
	$fcurrencies		=	$AdminDAO->getrows("$dbname_detail.payments","*","fksaleid = '$saleid' AND paymentmethod = 'fc'");
	for($i=0;$i<sizeof($payments);$i++)
	{
		$returnedamount		+=	$payments[$i]['returned'];
		if($payments[$i]['type']!=3)
		{
			$totalpaid				+=	$payments[$i]['amount'];
			$tenderedamountrs		+=	($payments[$i]['tendered']);
			$returnedamount			+=	($payments[$i]['returned']);
		}
		else
		{
			$totalpaid			+=	($payments[$i]['amount'])*($payments[$i]['rate']);
			$tenderedamountrs	+=	($payments[$i]['tendered'])*($payments[$i]['rate']);
			$returnedamount		+=	($payments[$i]['returned'])*($payments[$i]['returned']);
		}
	}
	// getting global discount
	$discount			=	$AdminDAO->getrows("$dbname_detail.sale","globaldiscount", " pksaleid = '$saleid'");	
	//$discounted			=	$discount[0]['globaldiscount'];
	$discountedprice	=	$price-$discounted;
	// getting salediscount for Amount on Amount and Amount on Quantity
	//$salediscount		=	$AdminDAO->getrows("$dbname_main.salediscount","sum(amount) amount", " fksaleid = '$saleid'");
	//$salediscountval	=	$salediscount[0]['amount'];
	//$discountedprice	=	$price-($discounted+$salediscountval);
	if($totalpaid<0)
	{
		if($discountedprice<0)
		{
			$remainingprice		=	$totalpaid-$discountedprice;
		}
		else
		{
			$remainingprice		=	$discountedprice+$totalpaid;
		}
	}
	else
	{
		$remainingprice		=	$discountedprice-$totalpaid;
	}
	
	$remainingprice		=	round($remainingprice,2);
	return $remainingprice."_".$totalpaid."_".$tenderedamountrs."_".$returnedamount."_".$discountedprice;
}
function getdiscount($id)
{
	global $AdminDAO,$dbname_detail;
	$discounts	=	$AdminDAO->getrows("$dbname_detail.sale","globaldiscount","pksaleid='$id'");
	// first the global discount
	$globaldiscount	=	$discounts[0]['globaldiscount'];
	// second sale discount
	$sdiscounts		=	$AdminDAO->getrows("$dbname_detail.salediscount","sum(amount) amount","fksaleid='$id'");
	$salediscount	=	$sdiscounts[0]['amount'];
	//total discount (sale discount + global discount)
	$totaldiscount	=	$globaldiscount+$salediscount;
	// second the amount on amount and amount on qty discount
	return $totaldiscount;
}
function getadjustment($id)
{
	global $AdminDAO,$dbname_detail;
	$adjustments	=	$AdminDAO->getrows("$dbname_detail.sale","adjustment","pksaleid='$id'");
	return $adjustments[0]['adjustment'];
}
?>