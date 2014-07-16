<?php
//include_once("AdminDAO.php");
//$AdminDAO = new AdminDAO();
function getprice($id)
{
	global $AdminDAO,$dbname_main;
	// paymenttype <> 'c' aadded by Yasir -- 05-07-11
	$query	=	"SELECT 
					pkcashpaymentid as id,
					1 as type, 
					amount, 
					paytime, 
					NULL as rate, 
					NULL as currency, 
					NULL as charges,
					tendered,
					returned
				FROM $dbname_main.cashpayment c 
					WHERE c.fksaleid = '$id'
					  AND c.paymenttype <> 'c' 
				UNION 
				SELECT 
					pkccpaymentid as id,
					2 as type, 
					amount, 
					paytime, 
					NULL as rate, 
					NULL as currency, 
					charges,
					tendered,
					returned 
				FROM $dbname_main.ccpayment cc 
					WHERE cc.fksaleid = '$id'
					  AND cc.paymenttype <> 'c' 
				UNION 
					SELECT pkfcpaymentid as id,
					3 as type, 
					amount, 
					paytime, 
					fc.rate as rate, 
					currencyname as currency,
					charges,
					tendered,
					returned
				FROM $dbname_main.fcpayment fc, 
					currency cr WHERE fc.fkcurrencyid = cr.pkcurrencyid AND fc.fksaleid = '$id' AND fc.paymenttype <> 'c' 
				UNION 
					SELECT pkchequepaymentid as id,
					4 as type,  
					amount, 
					paytime, 
					NULL as rate, 
					NULL as currency, 
					NULL as charges,
					tendered,
					returned
				FROM $dbname_main.chequepayment ch WHERE ch.fksaleid = '$id' AND ch.paymenttype <> 'c'
				ORDER BY paytime DESC";
		$queryresult	=	$AdminDAO->queryresult($query);
		return $queryresult;
}
function getpaidamount($saleid)
{
	global $AdminDAO,$dbname_main;
	$payments			=	getprice($saleid);
	$salesum 			=	$AdminDAO->getrows("$dbname_main.saledetail","SUM(saleprice * quantity) as price"," fksaleid = '$saleid' AND fkdiscountid=0");
	$price				=	$salesum[0]['price'];
	//dealing with foreign currency
	$fcurrencies		=	$AdminDAO->getrows("$dbname_main.fcpayment","*","fksaleid = '$saleid'");
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
	$discount			=	$AdminDAO->getrows("$dbname_main.sale","globaldiscount", " pksaleid = '$saleid'");	
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
	global $AdminDAO,$dbname_main;
	$discounts	=	$AdminDAO->getrows("$dbname_main.sale","globaldiscount","pksaleid='$id'");
	// first the global discount
	$globaldiscount	=	$discounts[0]['globaldiscount'];
	// second sale discount
	$sdiscounts		=	$AdminDAO->getrows("$dbname_main.salediscount","sum(amount) amount","fksaleid='$id'");
	$salediscount	=	$sdiscounts[0]['amount'];
	//total discount (sale discount + global discount)
	$totaldiscount	=	$globaldiscount+$salediscount;
	// second the amount on amount and amount on qty discount
	return $totaldiscount;
}
function getadjustment($id)
{
	global $AdminDAO,$dbname_main;
	$adjustments	=	$AdminDAO->getrows("$dbname_main.sale","adjustment","pksaleid='$id'");
	return $adjustments[0]['adjustment'];
}
?>