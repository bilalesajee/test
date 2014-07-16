<?php
session_start();
include("includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
	/*echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	exit;*/
	$id				=	$_GET['new'];
	$saleid			=	$_SESSION['tempsaleid'];
	$closingsession	=	$_SESSION['closingsession'];
	$taxable		=	trim(filter($_POST['taxamount'])," ");
	if($id == 1 || $id== 2) // when new customer is added into the system
	{
		$firstname		=	trim(filter($_POST['newfname'])," ");
		$lastname		=	trim(filter($_POST['newlname'])," ");		
		$phone			=	trim(filter($_POST['newphone'])," ");
		$nicno			=	trim(filter($_POST['nicno'])," ");
		$customerid		=	$_REQUEST['customerid'];
		$addressbookid	=	$_REQUEST['addressbookid'];
		$field1			=	array('firstname','lastname','phone','nic');
		$data1			=	array($firstname,$lastname,$phone,$nicno);
		if($addressbookid !='' )
		{
			 $and=" AND pkaddressbookid<>'$addressbookid' ";	
		}
		$unique	=	$AdminDAO->getrows("$dbname_detail.addressbook","pkaddressbookid"," nic='$nicno' $and ");
		//print_r($unique);
		if(sizeof($unique)>0)
		{
			echo "A customer with this NIC # $nicno already exists";
			exit;
		}
		if($customerid!='' && $addressbookid !='')
		{
			
			$AdminDAO->updaterow("$dbname_detail.addressbook",$field1,$data1," pkaddressbookid='$addressbookid' ");
			exit;
			//$insertid2	=	$AdminDAO->insertrow('customer',$field2,$data2);
		}
		else
		{
			$insertid	=	$AdminDAO->insertrow("$dbname_detail.addressbook",$field1,$data1);
			$field2		=	array('fkaddressbookid', 'ctype');//ctype added by ahsan 03/06/2012
			$data2		=	array($insertid, 2);//ctype value added by ahsan 03/06/2012
			$insertid2	=	$AdminDAO->insertrow("$dbname_detail.account",$field2,$data2);
			
		}
		if($id== 1)
		{
			// if taxable
			if($taxable>0)
			{
				$stockdata	=	$AdminDAO->getrows("$dbname_detail.stock","pkstockid","fkbarcodeid='66422'");
				$stockid	=	$stockdata[0]['pkstockid'];
				if($stockid=='')
				{
					$sfields	=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime");
					$svalues	= 	array(0,0,0,strtotime($expiry),0,0,$newprice,$newprice,0,0,0,66422,0,$storeid,$addressbookid,$fkbrandid,time());
					// inserts records in stock table
					$stockid = $AdminDAO->insertrow("$dbname_detail.stock",$sfields,$svalues);
				}
				if($stockid)
				{
					$stfields	=	array("fksaleid","fkstockid","quantity","saleprice","originalprice","fkreasonid","fkdiscountid","counterdiscount","discountamount","timestamp","boxsize","fkclosingid","fkpodetailid","fkaccountid","taxamount","taxable");
					$stdata	=	array($saleid,$stockid,1,$taxable,$taxable,$reason,$discountid,$counterdiscount,$discountamount,time(),$boxsize,$closingsession,$pkpodetailid,$customerid,$tax,$taxable);
			$saledetailid	=	$AdminDAO->insertrow("$dbname_detail.saledetail",$stfields,$stdata);
				}
				else
				{
					echo "Errors encountered while adding tax item";
					exit;
				}
			}
			$field3		=	array('fkaccountid','status');
			$data3		=	array($insertid2,1);
			$insertid3	=	$AdminDAO->updaterow("$dbname_detail.sale",$field3,$data3," pksaleid = '$saleid'");
			
			// c code commented by Yasir 22-06-11
			// start paymenttype
			/*$pfield		=	array('paymenttype');
			$pvalue		=	array('c');*/
			// cashpayment
			//$pidc		=	$AdminDAO->updaterow("$dbname_main.cashpayment",$pfield,$pvalue," fksaleid = '$saleid'");
			// ccpayment
			//$pidcc		=	$AdminDAO->updaterow("$dbname_main.ccpayment",$pfield,$pvalue," fksaleid = '$saleid'");
			// fcpayment
			//$pidfc		=	$AdminDAO->updaterow("$dbname_main.fcpayment",$pfield,$pvalue," fksaleid = '$saleid'");
			// chequepayment
			//$pidch		=	$AdminDAO->updaterow("$dbname_main.chequepayment",$pfield,$pvalue," fksaleid = '$saleid'");
		}
	}
	else
	{
		$customerid	=	$_POST['customerid'];
		
		// get customer balance at sale time . Added by Yasir -- 05-07-11
		$query_balance	=	"SELECT							  
							  ROUND(SUM(cash)) as cash,
							  ROUND(SUM(cc)) as cc,
							  ROUND(SUM(fc)) as fc,
							  ROUND(SUM(cheque)) as cheque,
							  SUM(totalamount) as totalamount,
							  SUM(globaldiscount) as discount
						  FROM
							  $dbname_detail.sale							  
						  WHERE
							  fkaccountid	=	'$customerid' AND
							  pksaleid < '$saleid'
						  ";
		
		$balance_array		=	$AdminDAO->queryresult($query_balance);		
		$total				=	$balance_array[0]['totalamount'];
		$discount			=	$balance_array[0]['discount'];
		$cash				=	$balance_array[0]['cash'];
		$cc					=	$balance_array[0]['cc'];
		$fc					=	$balance_array[0]['fc'];
		$cheque				=	$balance_array[0]['cheque'];
		$totalpaid			=	floor($cash+$cc+$fc+$cheque);
		$customerbalance	=	$total-$discount-$totalpaid;
		//
		
		
		if($customerid=='')
		{
			echo "Customer not found, please make sure you have selected the customer account properly.";
			exit;
		}
		if($customerid==56)
		{
			// sale amount
			$totalamount	=	$_POST['remamount'];
			$customername	=	$_POST['gencreditor'];
			/*if($totalamount>99)
			{
				echo "This customer is not allowed more than Rs. 99";
				exit;
			}*/
			if($customername=='')
			{
				echo "Please enter customer name";
				exit;
			}
		}
		// if taxable
		if($taxable>0)
		{
			$stockdata	=	$AdminDAO->getrows("$dbname_detail.stock","pkstockid","fkbarcodeid='66422'");
			$stockid	=	$stockdata[0]['pkstockid'];
			if($stockid=='')
			{
				$sfields	=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime");
				$svalues	= 	array(0,0,0,strtotime($expiry),0,0,$newprice,$newprice,0,0,0,'66422',0,$storeid,$addressbookid,$fkbrandid,time());
				// inserts records in stock table
				$stockid = $AdminDAO->insertrow("$dbname_detail.stock",$sfields,$svalues);
			}
			if($stockid)
			{
				$stfields	=	array("fksaleid","fkstockid","quantity","saleprice","originalprice","fkreasonid","fkdiscountid","counterdiscount","discountamount","timestamp","boxsize","fkclosingid","fkpodetailid","fkaccountid","taxamount","taxable");
				$stdata	=	array($saleid,$stockid,1,$taxable,$taxable,$reason,$discountid,$counterdiscount,$discountamount,time(),$boxsize,$closingsession,$pkpodetailid,$customerid,$tax,$taxable);
		$saledetailid	=	$AdminDAO->insertrow("$dbname_detail.saledetail",$stfields,$stdata);
			}
			else
			{
				echo "Errors encountered while adding tax item";
				exit;
			}
		}
		$fields		=	array('fkaccountid','customername','status','customerbalance');
		$data		=	array($customerid,$customername,1,$customerbalance);
		$insertid	=	$AdminDAO->updaterow("$dbname_detail.sale",$fields,$data," pksaleid = '$saleid'");
		
		// c code Commented by Yasir 22-06-11		
		// start paymenttype
		/*$pfield		=	array('paymenttype');
		$pvalue		=	array('c');*/
		// cashpayment
		//$pidc		=	$AdminDAO->updaterow("$dbname_main.cashpayment",$pfield,$pvalue," fksaleid = '$saleid'");
		// ccpayment
		//$pidcc		=	$AdminDAO->updaterow("$dbname_main.ccpayment",$pfield,$pvalue," fksaleid = '$saleid'");
		// fcpayment
		//$pidfc		=	$AdminDAO->updaterow("$dbname_main.fcpayment",$pfield,$pvalue," fksaleid = '$saleid'");
		// chequepayment
		//$pidch		=	$AdminDAO->updaterow("$dbname_main.chequepayment",$pfield,$pvalue," fksaleid = '$saleid'");
	
	}
	$sqlamount		=	"SELECT SUM(quantity*saleprice) as totalamount FROM $dbname_detail.saledetail WHERE fksaleid='$saleid'";
	$amarr			=	$AdminDAO->queryresult($sqlamount);
	$totalamount	=	$amarr[0]['totalamount'];
	$fields			=	array('totalamount');
	$data			=	array($totalamount);
	$insertid		=	$AdminDAO->updaterow("$dbname_detail.sale",$fields,$data, " pksaleid = '$saleid'");
	
	//add accounts entry
	$AdminDAO->posttransaction($customerid,$saleid,$totalamount,$csaleacc,$saleid,$totalamount,"Credit Sale");
}
else
{
	echo "insufficient data";
	exit;
}
?>