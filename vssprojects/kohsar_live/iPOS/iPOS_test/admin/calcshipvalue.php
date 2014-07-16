<?php
//include("../includes/security/adminsecurity.php");
function calcshipval($shipid)
{
	global $AdminDAO;
	//echo $query	=	"SELECT SUM(purchaseprice*quantity) as val, currencyrate FROM purchase WHERE fkshipmentid='$shipid'";
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$val	=	$AdminDAO->getrows("purchase","SUM(purchaseprice*quantity) as val, currencyrate","fkshipmentid='$shipid'");
		$value	=	$val[0]['val']*$val[0]['currencyrate'];
		return($value);
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$query					=	"SELECT 
											lastpurchaseprice,
											rate,
											sd.quantity
										FROM 
											shiplist s,currency,shiplistdetails sd
										WHERE 
											pkshiplistid=sd.fkshiplistid AND 
											s.fkshipmentid='$shipid' AND
											fkcurrencyid=pkcurrencyid
										";
		$shiplistdata			=	$AdminDAO->queryresult($query);
		if($shiplistdata)
		{
			foreach($shiplistdata as $sldata)
			{
				$sprice				=	$sldata['lastpurchaseprice'];
				$qty				=	$sldata['quantity'];
				$rate				=	$sldata['rate'];
				$price				=	$sprice*$qty;
				$totalvalue			+=	($price*$rate);
			}
			$selected_rate			=	$AdminDAO->getrows("shipment","exchangerate","pkshipmentid='$shipid'");
			$shipment_rate			=	$selected_rate[0]['exchangerate'];
			$shipvalue				=	round(($totalvalue/$shipment_rate),2);
		}
		else
		{
			$shipval	=	$AdminDAO->getrows("shipment","totalvalue","pkshipmentid='$shipid'");
			$shipvalue	=	$shipval[0]['totalvalue'];
		}
		return $shipvalue;
	}//end edit
}
?>