<?php
session_start();
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
//print_r($_SESSION);
if(sizeof($_POST)>0)
{
	$saleid			=	$_SESSION['tempsaleid'];
	$sqlamount		=	"SELECT SUM(quantity*saleprice) as totalamount FROM $dbname_detail.saledetail WHERE fksaleid='$saleid'";
	$amarr			=	$AdminDAO->queryresult($sqlamount);
	$totalamount	=	$amarr[0]['totalamount'];
	$fields			=	array('status','totalamount');
	$data			=	array('1',$totalamount);
	$insertid		=	$AdminDAO->updaterow("$dbname_detail.sale",$fields,$data, " pksaleid = '$saleid'");
	
	// for account posting
	$saledatsql		=	"SELECT globaldiscount,totalamount, cash, cc, fc, cheque FROM $dbname_detail.sale WHERE pksaleid='$saleid'";
	$saledatarr		=	$AdminDAO->queryresult($saledatsql);
	$discount		=	$saledatarr[0]['globaldiscount'];
	$cash			=	$saledatarr[0]['cash'];
	$cc				=	$saledatarr[0]['cc'];
	$fc				=	$saledatarr[0]['fc'];
	$cheque			=	$saledatarr[0]['cheque'];
	$totalamount	=	$saledatarr[0]['totalamount'];

	if ($discount > 0){
		$AdminDAO->posttransaction($discountallowedacc,$saleid,$discount,$cashacc,$saleid,$discount,"Discount on Sale"); 
	}
	
	if ($cash > 0){
		$AdminDAO->posttransaction($cashacc,$saleid,$totalamount,$cashsaleacc,$saleid,$totalamount,"Cash Sale");
	}
	
	if ($cc > 0){
		$AdminDAO->posttransaction($ccmachineaccountacc,$saleid,$totalamount,$ccsaleacc,$saleid,$totalamount,"Credit Card Sale");//changed $price to $totalamount by ahsan 07/03/2012
		
		$chargesccsql	=	"SELECT SUM(charges) as charges FROM $dbname_detail.payments WHERE fksaleid='$saleid' and paymentmethod = 'cc'";
		$chargesccarr	=	$AdminDAO->queryresult($chargesccsql);
		$chargescc		=	$chargesccarr[0]['charges'];		
		
		if ($chargescc	>	0){			
			$AdminDAO->posttransaction($ccmachinechargesacc,$saleid,$chargescc,$machinebankacc,$saleid,$chargescc,"Credit Card Sale Charges");
		}
	}
	
	if ($fc > 0){
		$AdminDAO->posttransaction($cashforeigncurrentacc,$saleid,$totalamount,$fcsaleacc,$saleid,$totalamount                              ,"Foreign Currency Sale");//changed $price to $totalamount by ahsan 07/03/2012
		
		$chargesfcsql	=	"SELECT SUM(charges) as charges FROM $dbname_detail.payments WHERE fksaleid='$saleid' and paymentmethod = 'fc'";
		$chargesfcarr	=	$AdminDAO->queryresult($chargesfcsql);
		$chargesfc		=	$chargesfcarr[0]['charges'];	
		
		if ($chargesfc	>	0){			
			$AdminDAO->posttransaction($ccmachinechargesacc,$saleid,$chargesfc,$machinebankacc,$saleid,$chargesfc,"Foreign Currency Sale Charges");
		}		
	}
	
	if ($cheque > 0){
		$AdminDAO->posttransaction($bankacc,$saleid,$totalamount,$csaleacc,$saleid,$totalamount,"Cheque Sale");//changed $price to $totalamount by ahsan 07/03/2012
	}
	
	// for return sale
	if ($totalamount < 0){
		if ($cash <	0){
			$AdminDAO->posttransaction($salereturnacc,$saleid,abs($cash),$cashacc,$saleid,abs($cash),"Cash Sale Return");
		}
		
		if ($cheque < 0){
			$AdminDAO->posttransaction($salereturnacc,$saleid,abs($cheque),$bankacc,$saleid,abs($cheque),"Cheque Sale Return");
		}
	}	
	//	
	
	//	
	
}
else
{
	echo "Insufficient data";
}
?>