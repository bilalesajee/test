<?php
session_start();
include_once("includes/security/adminsecurity.php");
include_once("includes/bc/barcode.php");
include_once("includes/classes/bill.php");
//include_once("includes/classes/customerbalance.php");
global $AdminDAO,$Component;
$Bill			=	new Bill($AdminDAO);
//checking closingsession
$paymentmethod	=	$_POST['paymentmethod'];
if($paymentmethod=='c')
{
	$breakmode				=	$_SESSION['breakmode'];
	if($breakmode==1)
	{
		print"Your counter is in break mode. You are unable to proceed cash sale.";
		exit;
	}	
}
if(!isset($_SESSION['closingsession']) || $_SESSION['closingsession']=='' || $_SESSION['closingsession']==0)
	{//changed $dbname_main to $dbname_detail on line 22 by ahsan 22/02/2012
		$closingquery	=	"SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
		$closingarray	=	$AdminDAO->queryresult($closingquery);
		$closingsession	=	$closingarray[0][pkclosingid];
		if(!isset($_SESSION['closingsession']) || $_SESSION['closingsession']=='' || $_SESSION['closingsession']==0)
		{
			echo "1";
			exit;
		}
	}
/*
if($closingsession=='')
{
	
	$open_query	=	"SELECT pkclosingid,declaredamount
								FROM
									$dbname_main.closinginfo
								WHERE 
									countername= '$countername' AND
									
									fkstoreid='$storeid' AND
									closingstatus <> 'i' AND
									closingdate = (SELECT MAX(closingdate) as mdate FROM $dbname_main.closinginfo WHERE countername='$countername' AND fkstoreid='$storeid' AND closingstatus<>'i')";
				
	$openarray	=	$AdminDAO->queryresult($open_query);
	$declaredamount	=	$openarray[0][declaredamount];
	$field	=	array("closingdate","fkaddressbookid","countername","fkstoreid","openingbalance");
	$data	=	array(time(),$empid,$countername,$storeid,$declaredamount);
	$closingsession	=	$AdminDAO->insertrow("$dbname_main.closinginfo",$field,$data);
}
*/
$_SESSION['closingsession']=$closingsession;
//checking delete operation
// Changed By Yasir -- 06-07-11. $_POST from $_GET
$customerid		=	$_POST['customerid'];
/*****************SALE IDS should be calculated************/

$salesarray[]		=	$_POST['saleid'];
$ftax=array('percentage','amount','fksaleid','fktransactionid','transactiontable');
switch($paymentmethod)
{
	/************************************************Cash*********************************************************/
	case 'c':
	{
		
		//$cash	 	=	$_POST['cashamount'];
		//$cashpaid	=	$cash;
		//$price		=	$_POST['remainingprice'];
		$fields		=	array('fksaleid','paytime','amount','tendered','fkclosingid','paymenttype','paymentmethod');
		/*for($i=0;$i<sizeof($salesarray);$i++)
		{*/ //loop commented by Yasir -- 20-07-11
		
		// replaced $salesarray[$i] by $_POST['saleid'] by Yasir -- 20-07-11
		
		// removed [$i] from $_POST['amountrecieved'], $_POST['incometax'] by Yasir -- 20-07-11
		 
			$saleid			=	$_POST['saleid'];
			$cash			=	trim(filter($_POST['amountrecieved']));
			//$paid			=	$salesarray[$i]['paid'];
			//$remainingprice	=	$saleprice	- $paid;
			//echo $cash;
			if($cash > 0)
			{
				$paid	=	$cash;
				$tendered=$cash;
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$tendered,$closingsession,'c','c');	
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				
				//add accounts entry
				$AdminDAO->posttransaction($cashacc,$saleid,$paid,$customerid,$saleid,$paid,"Cash Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($paid,$paymentmethod,$saleid); // added by Yasir - 06-07-11
				//inserts tax record
				$incometacamount			=	trim(filter($_POST['incometax']));
				if($incometacamount>0)
				{
					$datatax	=	array($percentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				}
				$cash		=	$cash	-	$paid;
			}//if
			
		//} //for sales //loop commented by Yasir -- 20-07-11
		// un commented by Yasir 21-07-11
		$paymentdetails = array ("Payment Method"  => 'Cash',   "Paid" => $paid);
		break;
	}//case cash
	/************************************************Credit Card*********************************************************/
	case 'cc':
	{
		$creditcharges	=	$_POST['creditcharges'];
		//$price			=	$_POST['remainingprice'];
		$newcard		=	$_POST['newcard'];
		$newbank		=	$_POST['newbank'];
		//$cash			=	trim(filter($_POST['creditamount'])," ");
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
		/*for($i=0;$i<sizeof($salesarray);$i++)
		{*/ //loop commented by Yasir -- 20-07-11
		
		// replaced $salesarray[$i] by $_POST['saleid'] by Yasir -- 20-07-11
		
		// removed [$i] from $_POST['amountrecieved'], $_POST['incometax'] by Yasir -- 20-07-11
		
			$saleid			=	$_POST['saleid'];
			$cash			=	trim(filter($_POST['amountrecieved']));
			

			//echo $cash;
			if($cash > 0)
			{
				$paid	=	$cash;
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$ccno,$cctype,$bank,$creditcharges,$tendered,$closingsession,'c','cc');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);
				
				//add accounts entry
				$AdminDAO->posttransaction($ccmachineaccountacc,$saleid,$paid,$customerid,$saleid,$paid,"Credit Card Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($paid,$paymentmethod,$saleid); // added by Yasir - 06-07-11
				//inserts tax record
				$incometacamount			=	trim(filter($_POST['incometax']));
				if($incometacamount>0)
				{
					$datatax	=	array($percentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);
				}
				//$cash		=	$cash	-	$paid;
			}//if
			
		//}//for sales //loop commented by Yasir -- 20-07-11 
		$paymentdetails = array ("Method"  => 'Credit Card',   "Paid" => $paid,"CC #"=>$ccno);
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
				$currencyid	=	$AdminDAO->insertrow("$dbname_detail.currency",$f,$v);
			}
		}
		if($currencyid == "")
		{
			echo "Please select at least one currency.";
			exit;
		}
		//$cash		=	$_POST['fcamount'];
		$currency	=	$_POST['currency'];
		$fcrate		=	$_POST['fcrate'];
		$fccharges	=	$_POST['fccharges'];
		//$cash		=	$cash * $fcrate;
		//$cashpaid	=	$cash;
		$fields		=	array('fksaleid','paytime','amount','fkcurrencyid','rate','charges','tendered','fcamount','fctendered','returned','fkclosingid','paymenttype','paymentmethod');
		
		/*for($i=0;$i<sizeof($salesarray);$i++)
		{*/ //loop commented by Yasir -- 20-07-11
		
		// replaced $salesarray[$i] by $_POST['saleid'] by Yasir -- 20-07-11
		
		// removed [$i] from $_POST['amountrecieved'], $_POST['incometax'] by Yasir -- 20-07-11
		
			$saleid			=	$_POST['saleid'];
			//$saleprice		=	$salesarray[$i]['price']; // Commented by Yasir -- 20-07-11
			//$paid			=	$salesarray[$i]['paid']; // Commented by Yasir -- 20-07-11
			//$remainingprice	=	$saleprice- $paid;
			//echo $cash;
			//$saleid			=	$salesarray[$i];
			$amount = trim(filter($_POST['amountrecieved'])); // added by Yasir -- 20-078-11
			$cash			=	trim(filter($_POST['amountrecieved']))*$fcrate; // added *$fcrate by Yasir -- 20-07-11
			
			$remainingprice =   trim($_POST['remainingprice']);
			
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
				
				$tendered=$amount;
				
				$time		=	time();
				
				//$paid		=	($paid / $fcrate);
				
				$data		=	array($saleid,$time,$cash,$currencyid,$fcrate,$fccharges,$cash,$amount,$tendered,$returned,$closingsession,'c','fc');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);
				
				//add accounts entry
				$AdminDAO->posttransaction($cashforeigncurrentacc,$saleid,$cash,$customerid,$saleid,$cash,"Foreign Currency Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($cash,$paymentmethod,$saleid);
				//inserts tax record
				$incometacamount			=	trim(filter($_POST['incometax']));
				if($incometacamount>0)
				{
					$datatax	=	array($percentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);
				}
				
				// if paid is greater than remaining -- added by Yasir -- 18-07-11
				if ( $cash > $remainingprice ){
					$adjust_amount	=	$remainingprice	-	$cash;
				 
					$fields_ =	array('fksaleid','paytime','amount','tendered','fkclosingid', 'paymenttype','paymentmethod');
					$tendered_ = 0;
					$data_	=	array($saleid,$time,$adjust_amount,$tendered_,$closingsession,'c','c');
				 	$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields_,$data_);
					
					//add accounts entry
				    $AdminDAO->posttransaction($cashacc,$saleid,$adjust_amount,$customerid,$saleid,$adjust_amount,"Cash Collection");
					
					//will adjust the collection in the sale table
				    $Bill->updatepayment($adjust_amount,'c',$saleid);
					
					// adjustment
					$fields__		=	array('adjustment');
					$values__		=	array(-($adjust_amount));
					$AdminDAO->updaterow("$dbname_detail.sale",$fields__,$values__,"pksaleid='$saleid'");
					//	
					//echo $remainingprice.' - '.$paid*$fcrate.' - '.$adjust_amount.' - '.$cash." - $saleid <br/>"; 			   
				}
				//
				
				
				
			//	$cash		=	$cash	-	$paid;
			}//if
			
		// }//for sales //loop commented by Yasir -- 20-07-11 
		$currencyarray	=	$AdminDAO->getrows("currency","currencysymbol","pkcurrencyid = '$currencyid'");
		$symbol			=	$currencyarray[0]['currencysymbol'];
		$paymentdetails = array ("Method"  => 'Foreign Currency',   "Paid" => $amount, "Currency"=>$symbol);
		break;
	}
	/************************************************Cheque*********************************************************/
	case 'ch':
	{
		$cash			=	trim(filter($_POST['chequeamount'])," ");
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
		
		/*for($i=0;$i<sizeof($salesarray);$i++)
		{*/ //loop commented by Yasir -- 20-07-11
		
		// replaced $salesarray[$i] by $_POST['saleid'] by Yasir -- 20-07-11
		
		// removed [$i] from $_POST['amountrecieved'], $_POST['incometax'] by Yasir -- 20-07-11
		
			$saleid			=	$_POST['saleid'];
			//$saleprice		=	$salesarray[$i]['price']; // Commented by Yasir -- 20-07-11
			//$paid			=	$salesarray[$i]['paid'];
			//$remainingprice	=	$saleprice	- $paid;
			//echo $cash;
			//$saleid			=	$salesarray[$i];
			$cash			=	trim(filter($_POST['amountrecieved']));
			
			if($cash > 0)
			{
				
				$paid	=	$cash;
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$bank,$chequenumber,$chequedate,$tendered,$closingsession,'c','ch');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.payments",$fields,$data);
				
				//add accounts entry
				$AdminDAO->posttransaction($machinebankacc,$saleid,$paid,$customerid,$saleid,$paid,"Cheque Collection");
				
				//will adjust the collection in the sale table
				$Bill->updatepayment($paid,$paymentmethod,$saleid); // added by Yasir - 06-07-11
				//inserts tax record
				$incometacamount		=	trim(filter($_POST['incometax']));
				if($incometacamount>0)
				{
					$datatax	=	array($taxpercentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);
				}
				//$cash		=	$cash	-	$paid;
			}//if
			
		//}//for sales //loop commented by Yasir -- 20-07-11
		$paymentdetails = array ("Payment Method"  => 'Cheque',   "Paid" => $paid,"Cheque Number"=>$chequenumber);
		break;
	}//case cheque
}//switch

// added by Yasir -- 06-07-11
$fcol	=	array("amount","paymentmethod","fkaccountid","fkaddressbookid","datetime","fkclosingid");
$vcol	=	array($paid,$paymentmethod,$customerid,$empid,time(),$closingsession);
$collectionid	=	$AdminDAO->insertrow("$dbname_detail.collection",$fcol,$vcol);
$saleid	=	$_POST['saleid'];
$fcol	=	array("fkcollectionid","fksaleid");
$vcol	=	array($collectionid,$saleid);
$AdminDAO->insertrow("$dbname_detail.collectionbills",$fcol,$vcol);
// 
genBarCode($insertid,'collect.png');
$_SESSION['customerid']		=	$customerid;
$_SESSION['paymentdetails'] =	$paymentdetails;
echo $customerid;
?>