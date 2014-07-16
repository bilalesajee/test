<?php
include_once("AdminDAO.php");
$AdminDAO = new AdminDAO();
function getprice($id)
{
	global $AdminDAO;
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
				FROM $dbname_detail.cashpayment c 
					WHERE c.fksaleid = '$id' 
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
				FROM $dbname_detail.ccpayment cc 
					WHERE cc.fksaleid = '$id' 
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
				FROM $dbname_detail.fcpayment fc, 
					currency cr WHERE fc.fkcurrencyid = cr.pkcurrencyid AND fc.fksaleid = '$id' 
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
				FROM $dbname_detail.chequepayment ch WHERE ch.fksaleid = '$id'
				ORDER BY paytime DESC";
		$queryresult	=	$AdminDAO->queryresult($query);
		return $queryresult;
}
function getpaidamount($saleid)
{
	global $AdminDAO;
	$payments			=	getprice($saleid);
	$salesum 			=	$AdminDAO->getrows("$dbname_detail.saledetail","SUM(saleprice * quantity) as price"," fksaleid = '$saleid'");
	$price				=	$salesum[0]['price'];
	//dealing with foreign currency
	$fcurrencies		=	$AdminDAO->getrows("$dbname_detail.fcpayment","*","fksaleid = '$saleid'");
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
	$discount			=	$AdminDAO->getrows("$dbname_detail.sale","globaldiscount", " pksaleid = '$saleid'");
	$discounted			=	$discount[0]['globaldiscount'];
	$discountedprice	=	$price-$discounted;
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
	global $AdminDAO;
	$discounts	=	$AdminDAO->getrows("$dbname_detail.sale","globaldiscount","pksaleid='$id'");
	return $discounts[0]['globaldiscount'];
}
function getadjustment($id)
{
	global $AdminDAO;
	$adjustments	=	$AdminDAO->getrows("$dbname_detail.sale","adjustment","pksaleid='$id'");
	return $adjustments[0]['adjustment'];
}
function customerbalance
?>