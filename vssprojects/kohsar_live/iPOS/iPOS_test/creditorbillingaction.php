<?php
session_start();
include_once("includes/security/adminsecurity.php");
include_once("includes/bc/barcode.php");
//include_once("includes/classes/customerbalance.php");
global $AdminDAO,$Component;
//checking closingsession
$paymentmethod	=	$_POST['paymentmethod'];
if($paymentmethod=='c')
{
	$breakmode				=	$_SESSION['breakmode'];
	if($breakmode==1)
	{
		print"Your counter is in break mode. You are unable to procceed cash sale.";
		exit;
	}	
}
if($closingsession=='')
{//changed $dbname_main to $dbname_detail on line 22, 28 by ahsan 22/02/2012
	$open_query	=	"SELECT pkclosingid,declaredamount
								FROM
									$dbname_detail.closinginfo
								WHERE 
									countername= '$countername' AND
									
									fkstoreid='$storeid' AND
									closingstatus <> 'i' AND
									closingdate = (SELECT MAX(closingdate) as mdate FROM $dbname_detail.closinginfo WHERE countername='$countername' AND fkstoreid='$storeid' AND closingstatus<>'i')";
				
	$openarray	=	$AdminDAO->queryresult($open_query);
	$declaredamount	=	$openarray[0][declaredamount];
	$field	=	array("closingdate","fkaddressbookid","countername","fkstoreid","openingbalance");
	$data	=	array(time(),$empid,$countername,$storeid,$declaredamount);
	$closingsession	=	$AdminDAO->insertrow("$dbname_detail.closinginfo",$field,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
}
$_SESSION['closingsession']=$closingsession;
//checking delete operation
$customerid		=	$_GET['customerid'];
/*****************SALE IDS should be calculated************/

$salesarray		=	$_POST['saleid'];
$ftax=array('percentage','amount','fksaleid','fktransactionid','transactiontable');
switch($paymentmethod)
{
	/************************************************Cash*********************************************************/
	case 'c':
	{
		
		//$cash	 	=	$_POST['cashamount'];
		//$cashpaid	=	$cash;
		//$price		=	$_POST['remainingprice'];
		$fields		=	array('fksaleid','paytime','amount','tendered','fkclosingid','paymenttype');
		for($i=0;$i<sizeof($salesarray);$i++)
		{
			$saleid			=	$salesarray[$i];
			$cash			=	trim(filter($_POST['amountrecieved'][$i]));
			//$paid			=	$salesarray[$i]['paid'];
			//$remainingprice	=	$saleprice	- $paid;
			//echo $cash;
			if($cash > 0)
			{
				$paid	=	$cash;
				$tendered=$cash;
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$tendered,$closingsession,'c');	
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.cashpayment",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				//inserts tax record
				$incometacamount			=	trim(filter($_POST['incometax'][$i]));
				if($incometacamount>0)
				{
					$datatax	=	array($percentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				}
				$cash		=	$cash	-	$paid;
			}//if
			
		}//for sales
		//$paymentdetails = array ("Payment Method"  => 'Cash',   "Paid" => $cashpaid);
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
		$fields	=	array('fksaleid','paytime','amount','ccno','fkcctypeid','fkbankid','charges','tendered','fkclosingid','paymenttype');
		for($i=0;$i<sizeof($salesarray);$i++)
		{
			$saleid			=	$salesarray[$i];
			$cash			=	trim(filter($_POST['amountrecieved'][$i]));
			

			//echo $cash;
			if($cash > 0)
			{
				$paid	=	$cash;
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$ccno,$cctype,$bank,$creditcharges,$tendered,$closingsession,'c');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.ccpayment",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				//inserts tax record
				$incometacamount			=	trim(filter($_POST['incometax'][$i]));
				if($incometacamount>0)
				{
					$datatax	=	array($percentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				}
				//$cash		=	$cash	-	$paid;
			}//if
			
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
		//$cash		=	$_POST['fcamount'];
		$currency	=	$_POST['currency'];
		$fcrate		=	$_POST['fcrate'];
		$fccharges	=	$_POST['fccharges'];
		//$cash		=	$cash * $fcrate;
		//$cashpaid	=	$cash;
		$fields		=	array('fksaleid','paytime','amount','fkcurrencyid','rate','charges','tendered','returned','fkclosingid','paymenttype');
		
		for($i=0;$i<sizeof($salesarray);$i++)
		{
			$saleid			=	$salesarray[$i]['id'];
			$saleprice		=	$salesarray[$i]['price'];
			$paid			=	$salesarray[$i]['paid'];
			//$remainingprice	=	$saleprice- $paid;
			//echo $cash;
			$saleid			=	$salesarray[$i];
			$cash			=	trim(filter($_POST['amountrecieved'][$i]));
			if($cash > 0)
			{
				$paid	=	$cash;
				$tendered=$cash;
				$time		=	time();
				
				//$paid		=	($paid / $fcrate);
				$data		=	array($saleid,$time,$paid,$currencyid,$fcrate,$fccharges,$tendered,$returned,$closingsession,'c');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.fcpayment",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				//inserts tax record
				$incometacamount			=	trim(filter($_POST['incometax'][$i]));
				if($incometacamount>0)
				{
					$datatax	=	array($percentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				}
				
			//	$cash		=	$cash	-	$paid;
			}//if
			
		}//for sales
		$currencyarray	=	$AdminDAO->getrows("currency","currencysymbol","pkcurrencyid = '$currencyid'");
		$symbol			=	$currencyarray[0]['currencysymbol'];
		$paymentdetails = array ("Method"  => 'Foreign Currency',   "Paid" => $cashpaid, "Currency"=>$symbol);
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
		$fields			=	array('fksaleid','paytime','amount','fkbankid','chequeno','chequedate','tendered','fkclosingid','paymenttype');
		
		for($i=0;$i<sizeof($salesarray);$i++)
		{
			$saleid			=	$salesarray[$i]['id'];
			$saleprice		=	$salesarray[$i]['price'];
			//$paid			=	$salesarray[$i]['paid'];
			//$remainingprice	=	$saleprice	- $paid;
			//echo $cash;
			$saleid			=	$salesarray[$i];
			$cash			=	trim(filter($_POST['amountrecieved'][$i]));
			
			if($cash > 0)
			{
				
				$paid	=	$cash;
				$time		=	time();
				$data		=	array($saleid,$time,$paid,$bank,$chequenumber,$chequedate,$tendered,$closingsession,'c');
				$insertid	=	$AdminDAO->insertrow("$dbname_detail.chequepayment",$fields,$data);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				//inserts tax record
				$incometacamount			=	trim(filter($_POST['incometax'][$i]));
				if($incometacamount>0)
				{
					$datatax	=	array($percentage,$incometacamount,$saleid,$insertid,$paymentmethod);
					$insertid	=	$AdminDAO->insertrow("$dbname_detail.incometax",$ftax,$datatax);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				}
				//$cash		=	$cash	-	$paid;
			}//if
			
		}//for sales
		$paymentdetails = array ("Payment Method"  => 'Cheque',   "Paid" => $cashpaid,"Cheque Number"=>$chequenumber);
		break;
	}//case cheque
}//switch
genBarCode($insertid,'collect.png');
//$_SESSION['customerid']		=	$customerid;
$_SESSION['paymentdetails'] =	$paymentdetails;
?>
<script language="javascript" type="text/javascript">
<?php /*?>printcollectionbill('<?php echo $customerid;?>');<?php */?>
//selecttab('Customers_tab','customers.php');
</script>