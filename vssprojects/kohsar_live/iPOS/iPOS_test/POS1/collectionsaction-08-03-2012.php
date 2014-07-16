<?php
session_start();
error_reporting(0);
include_once("includes/security/adminsecurity.php");
include_once("includes/bc/barcode.php");
include_once("includes/classes/bill.php");
//include_once("includes/classes/customerbalance.php");
global $AdminDAO,$Component;
$Bill			=	new Bill($AdminDAO);
//checking closingsession
if(!isset($closingsession) || $closingsession=='' || $closingsession==0)
{
	// this is where we start the closing process
	echo 1;
	exit;
}
//checking delete operation
$customerid		=	$_GET['customerid'];
/*****************SALE IDS should be calculated************/
$paymentmethod	=	$_POST['paymentmethod'];
if($paymentmethod=='c')
{
	$breakmode				=	$_SESSION['breakmode'];
	if($breakmode==1)
	{
		print"Your counter is in break mode. You are unable to procceed cash or foreign currency collection."; // changed By Yasir -- 07-07-11
		exit;
	}	
}
// addedd by Yasir -- 07-07-11
if($paymentmethod=='fc')
{
	$breakmode				=	$_SESSION['breakmode'];
	if($breakmode==1)
	{
		print"Your counter is in break mode. You are unable to procceed cash or foreign currency collection."; // changed By Yasir -- 07-07-11
		exit;
	}	
}
//
// by waqar 05-03-10
// new code to replace the query above
// Step 1. sales query

// +globaldiscount added in query by Yasir -- 19-07-11 //changed $dbname_main to $dbname_detail on line 49 by ahsan 22/02/2012
$salesquery	=	"SELECT 
					pksaleid id 
				FROM
					$dbname_detail.sale
				WHERE 
					fkaccountid = '$customerid' AND totalamount>(cash+cc+fc+cheque+globaldiscount)
				GROUP BY 
					pksaleid
				ORDER BY 
					datetime";
$salesres	=	$AdminDAO->queryresult($salesquery);
//performing operations on sales query
//setting variable for the results array
$j=0;
//working with sales
for($i=0;$i<sizeof($salesres);$i++)
{
	//	the sales id
	$id				=	$salesres[$i]['id'];
	// sales query
	
	// totalamount replaced by (totalamount - globaldiscount) by Yasir -- 19-07-11
	//changed $dbname_main to $dbname_detail on line 76 by ahsan 22/02/2012
	$pricequery		=	"SELECT 
								(totalamount - globaldiscount) as price,
								cash,
								cc,
								fc,
								cheque 
							FROM 
								$dbname_detail.sale
								
							WHERE
							
								fkaccountid = '$customerid' AND
								pksaleid	=	'$id' 
						";
	$priceresult	=	$AdminDAO->queryresult($pricequery);
	//sales total
	$price			=	$priceresult[0]['price'];
	$cash			=	$priceresult[0]['cash'];
	$cc				=	$priceresult[0]['cc'];
	$fc				=	$priceresult[0]['fc'];
	$cheque			=	$priceresult[0]['cheque'];
	// cash paid for the sale
	/*$cashquery		=	"SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as cashamount
						FROM 
							$dbname_main.cashpayment,
							$dbname_main.sale
						WHERE 
							fkcustomerid	=	'$customerid' AND
							pksaleid 		= 	fksaleid AND
							fksaleid 		=	'$id'
						";
	$cashresult	=	$AdminDAO->queryresult($cashquery);*/
	// cash total
	//$cash			=	$cashresult[0]['cashamount'];
	// cc paid for the sale
	/*$ccquery		=	"SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as ccamount
						FROM 
							$dbname_main.ccpayment,
							$dbname_main.sale
						WHERE 
							fkcustomerid	=	'$customerid' AND
							pksaleid 		= 	fksaleid AND
							fksaleid 		=	'$id'
						";
	$ccresult	=	$AdminDAO->queryresult($ccquery);*/
	// cc total
	//$cc			=	$ccresult[0]['ccamount'];
	// fc paid for the sale
	/*$fcquery		=	"SELECT (IF (sum(amount*rate) IS NULL,0,sum(amount*rate))) as fcamount
						FROM 
							$dbname_main.fcpayment,
							$dbname_main.sale
						WHERE 
							fkcustomerid	=	'$customerid' AND
							pksaleid 		= 	fksaleid AND
							fksaleid 		=	'$id'
						";
	$fcresult	=	$AdminDAO->queryresult($fcquery);*/
	// fc total
	//$fc			=	$fcresult[0]['fcamount'];
	// cheque paid for the sale
	/*$chequequery		=	"SELECT (IF (sum(amount) IS NULL,0,sum(amount))) as chequeamount
						FROM 
							$dbname_main.chequepayment,
							$dbname_main.sale
						WHERE 
							fkcustomerid	=	'$customerid' AND
							pksaleid 		= 	fksaleid AND
							fksaleid 		=	'$id'
						";
	$chequeresult	=	$AdminDAO->queryresult($chequequery);*/
	// cheque total
	//$cheque			=	$chequeresult[0]['chequeamount'];
	
	$paid			=	$cash+$cc+$fc+$cheque;
	
	$total			=	$price-$totalamount;
	if($total>0)
	{
		$salesarray[$j]['id']		=	$id;
		$salesarray[$j]['price']	=	$price;
		$salesarray[$j]['paid']		=	$paid;
		$j++;
	}
}
switch($paymentmethod)
{
	/************************************************Cash*********************************************************/
	case 'c':
	{
		$cash	 	=	$_POST['amount1'];
		
		// added by Yasir 25-07-11
		if ($cash == '' || $cash == 0)
		{
		 	echo 'Please enter amount';
			exit;
		}
		//
		
		
		$cashpaid	=	$cash;
		$price		=	$_POST['remainingprice'];
		$fields		=	array('fksaleid','paytime','amount','tendered','fkclosingid','paymenttype', 'paymentmethod');
		for($i=0;$i<sizeof($salesarray);$i++)
		{
			$saleid			=	$salesarray[$i]['id'];
			$saleprice		=	$salesarray[$i]['price'];
			$paid			=	$salesarray[$i]['paid'];
			$remainingprice	=	$saleprice	- $paid;
			//echo $cash;
			if($cash > 0)
			{
				if($cash > $remainingprice)
				{
					$paid	=	$remainingprice;
				}
				else
				{
					$paid	=	$cash;
					
				}
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$tendered,$closingsession,'c','c');	
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				
				//add accounts entry
				$AdminDAO->posttransaction($cashacc,$saleid,$paid,$customerid,$saleid,$paid,"Cash Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($paid,$paymentmethod,$saleid);
				$cash		=	$cash	-	$paid;
				//echo $cash;
			}//if
			else
			{
				break;
			}
		}//for sales
		$paymentdetails = array ("Payment Method"  => 'Cash',   "Paid" => $cashpaid);
		break;
	}//case cash
	/************************************************Credit Card*********************************************************/
	case 'cc':
	{
		$creditcharges	=	$_POST['creditcharges'];
		$price			=	$_POST['remainingprice'];
		$newcard		=	$_POST['newcard'];
		$newbank		=	$_POST['newbank'];
		$cash			=	trim(filter($_POST['amount2'])," ");
		
		// added by Yasir 25-07-11
		if ($cash == '' || $cash == 0)
		{
		 	echo 'Please enter amount';
			exit;
		}
		//
		
		$cashpaid		=	$cash;
		if($newcard!='')
		{
			$field	=	array("typename");
			$value	=	array($newcard);
			$cctype	=	$AdminDAO->insertrow("cctype",$field,$value);
		}
		else
		{
			$cctype					=	$_POST['card'];
		}
		if($cctype=='' && $newcard=='')
		{
			echo "Please select credit card type.";
			exit;
		}
		if($newbank!='')
		{
			$field	=	array("	bankname");
			$value	=	array($newbank);
			$bank	=	$AdminDAO->insertrow("bank",$field,$value);
		}
		else
		{
			$bank					=	$_POST['banks'];
		}
		$ccno				=	trim(filter($_POST['ccnumber'])," ");
		if($ccno=='')
		{
			echo "Please enter credit card number.";
			exit;
		}
		$fields	=	array('fksaleid','paytime','amount','ccno','fkcctypeid','fkbankid','charges','tendered','fkclosingid','paymenttype','paymentmethod');
		for($i=0;$i<sizeof($salesarray);$i++)
		{
			$saleid			=	$salesarray[$i]['id'];
			$saleprice		=	$salesarray[$i]['price'];
			$paid			=	$salesarray[$i]['paid'];
			$remainingprice	=	$saleprice	- $paid;
			//echo $cash;
			if($cash > 0)
			{
				if($cash > $remainingprice)
				{
					$paid	=	$remainingprice;
				}
				else
				{
					$paid	=	$cash;
				}
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$ccno,$cctype,$bank,$creditcharges,$tendered,$closingsession,'c','cc');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				
				//add accounts entry
				$AdminDAO->posttransaction($ccmachineaccountacc,$saleid,$paid,$customerid,$saleid,$paid,"Credit Card Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($paid,$paymentmethod,$saleid);
				$cash		=	$cash	-	$paid;
			}//if
			else
			{
				break;
			}
		}//for sales
		$paymentdetails = array ("Method"  => 'Credit Card',   "Paid" => $cashpaid,"CC #"=>$ccno);
		break;
	}
	/************************************************Foreign Currency*********************************************************/
	case 'fc':
	{
		$currencyid				=	$_POST['currency'];
		$newfc					=	trim($_POST['newfc']," ");
		$fcrate					=	$_POST['fcrate'];
		if($newfc!='')
		{
			if($fcrate=='')
			{
				print"Please enter Currency exchange rate to continue.";
				exit;
			}
			else
			{
				$f=array("currencysymbol","rate");	
				$v=array($newfc,$fcrate);	
				$currencyid	=	$AdminDAO->insertrow("$dbname_detail.currency",$f,$v);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
			}
		}
		if($currencyid == "")
		{
			echo "Please select at least one currency.";
			exit;
		}
		$cash		=	$_POST['amount3'];
		
		// added by Yasir 25-07-11
		if ($cash == '' || $cash == 0)
		{
		 	echo 'Please enter amount';
			exit;
		}
		//
		
		$currency	=	$_POST['currency'];
		$fcrate		=	$_POST['fcrate'];
		$fccharges	=	$_POST['fccharges'];
		$cash		=	$cash * $fcrate;
		$cashpaid	=	$cash;
		$fields		=	array('fksaleid','paytime','amount','fkcurrencyid','rate','charges','tendered','fcamount','fctendered','returned','fkclosingid','paymenttype','paymentmethod');
		
		for($i=0;$i<sizeof($salesarray);$i++)
		{ //echo $cash.' - ';
			$saleid			=	$salesarray[$i]['id'];
			$saleprice		=	$salesarray[$i]['price'];
			$paid			=	$salesarray[$i]['paid'];
			$remainingprice	=	$saleprice - $paid;
			//echo $cash;
			if($cash > 0)
			{
				if($cash > $remainingprice)
				{
					$paid	=	$remainingprice;
				}
				else
				{
					$paid	=	$cash;
				}
				$time		=	time();
				
				$fcpaid		=	ceil(round(($paid / $fcrate),2));
				
				$data		=	array($saleid,$time,$paid,$currencyid,$fcrate,$fccharges,$tendered,$fcpaid,$fcpaid,$returned,$closingsession,'c','fc');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				
				//add accounts entry
				$AdminDAO->posttransaction($cashforeigncurrentacc,$saleid,$paid,$customerid,$saleid,$paid,"Foreign Currency Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($paid*$fcrate,$paymentmethod,$saleid); // $paid*$fcrate replaced from $paid by Yasir -- 06-07-11				
				$cash	=	$cash-($paid*$fcrate); // $paid replaced by $paid*$fcrate by Yasir -- 19-07-11
				
				// if paid is greater than remaining -- added by Yasir -- 18-07-11
				if ( ($paid*$fcrate) > $remainingprice ){
					$adjust_amount	=	$remainingprice	-	($paid*$fcrate);
				 
					$fields_ =	array('fksaleid','paytime','amount','tendered','fkclosingid', 'paymenttype','paymentmethod');
					$data_	=	array($saleid,$time,$adjust_amount,$tendered,$closingsession,'c','c');
				 	$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields_,$data_);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
					
					//add accounts entry
				    $AdminDAO->posttransaction($cashacc,$saleid,$adjust_amount,$customerid,$saleid,$adjust_amount,"Cash Collection");
					
					//will adjust the collection in the sale table
				    $Bill->updatepayment($adjust_amount,'c',$saleid);
					
					// adjustment
					$fields__		=	array('adjustment');
					$values__		=	array(-($adjust_amount));
					$AdminDAO->updaterow("$dbname_detail.sale",$fields__,$values__,"pksaleid='$saleid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
					//	
					//echo $remainingprice.' - '.$paid*$fcrate.' - '.$adjust_amount.' - '.$cash." - $saleid <br/>"; 			   
				}
				//
				
				//$cash	=($cash*$fcrate);
			}//if
			else
			{
				break;
			}
		}//for sales
		$currencyarray	=	$AdminDAO->getrows("currency","currencysymbol","pkcurrencyid = '$currencyid'");
		$symbol			=	$currencyarray[0]['currencysymbol'];
		$paymentdetails = array ("Method"  => 'Foreign Currency',   "Paid" => $cashpaid, "Currency"=>$symbol);
		break;
	}
	/************************************************Cheque*********************************************************/
	case 'ch':
	{
		$cash			=	trim(filter($_POST['amount4'])," ");
		
		// added by Yasir 25-07-11
		if ($cash == '' || $cash == 0)
		{
		 	echo 'Please enter amount';
			exit;
		}
		//
		
		$cashpaid		=	$cash;
		$chequenumber	=	$_POST['chequenumber'];
		$bank			=	$_POST['bank'];
		$newbank		=	$_POST['newbank'];
		if($newbank!='')
		{
			$field	=	array("	bankname");
			$value	=	array($newbank);
			$bank	=	$AdminDAO->insertrow("bank",$field,$value);
		}
		if($chequenumber=='')
		{
			echo "Please enter cheque number.";
			exit;
		}
		if($newbank=='' && $bank=='')
		{
			echo "Please select a bank.";
			exit;
		}
		
		$chequedate		=	$_POST['chequedate'];
		$fields			=	array('fksaleid','paytime','amount','fkbankid','chequeno','chequedate','tendered','fkclosingid','paymenttype','paymentmethod');
		for($i=0;$i<sizeof($salesarray);$i++)
		{
			$saleid			=	$salesarray[$i]['id'];
			$saleprice		=	$salesarray[$i]['price'];
			$paid			=	$salesarray[$i]['paid'];
			$remainingprice	=	$saleprice	- $paid;
			//echo $cash;
			if($cash > 0)
			{
				if($cash > $remainingprice)
				{
					$paid	=	$remainingprice;
				}
				else
				{
					$paid	=	$cash;
				}
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$bank,$chequenumber,$chequedate,$tendered,$closingsession,'c','ch');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.chequepayment",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				
				//add accounts entry
				$AdminDAO->posttransaction($machinebankacc,$saleid,$paid,$customerid,$saleid,$paid,"Cheque Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($paid,$paymentmethod,$saleid);
				$cash		=	$cash	-	$paid;
			}//if
			else
			{
				break;
			}
		}//for sales
		$paymentdetails = array ("Payment Method"  => 'Cheque',   "Paid" => $cashpaid,"Cheque Number"=>$chequenumber);
		break;
	}//case cheque
}//switch
//$paymentmethod	=	$_POST['paymentmethod'];
$fcol	=	array("amount","paymentmethod","fkaccountid","fkaddressbookid","datetime","fkclosingid");
$vcol	=	array($cashpaid,$paymentmethod,$customerid,$empid,time(),$closingsession);
$collectionid	=	$AdminDAO->insertrow("$dbname_detail.collection",$fcol,$vcol);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
for($i=0;$i<sizeof($salesarray);$i++)
{
	$saleid	=	$salesarray[$i]['id'];
	$fcol	=	array("fkcollectionid","fksaleid");
	$vcol	=	array($collectionid,$saleid);
	$AdminDAO->insertrow("$dbname_detail.collectionbills",$fcol,$vcol);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
}
genBarCode($insertid,'collect.png');
$_SESSION['customerid']	=	$customerid;
$_SESSION['paymentdetails'] =	$paymentdetails;
?>