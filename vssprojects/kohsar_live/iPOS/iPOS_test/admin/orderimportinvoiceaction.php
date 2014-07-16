<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$fkaddressbookid =	$_SESSION['addressbookid'];
$shipmentidd	 =	$_REQUEST['shipmentid'];
$productid		 =	$_REQUEST['productid'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";		
exit;*/
/*foreach($_POST as $key=>$value)
{
	//$_POST[$key]	=	trime($value);
	//echo '$'."$key	=	".'$_POST['."'$key'];\n";
}*/

$barcode			=	$_POST['barcode'];
$item				=	$_POST['item'];
$description		=	$_POST['description'];
$weight				=	$_POST['weight'];
$brandid			=	$_POST['brandname'];
$supplierid			=	$_POST['suppliercompanyname'];
$countryid			=	$_POST['countryname'];
$pricelimit			=	$_POST['pricelimit'];
$agreedprice		=	$_POST['agreedprice'];
$quantity			=	$_POST['quantity'];
$deadline			=	$_POST['deadline'];
$locationid			=	$_POST['locationstorename'];
$comments			=	$_POST['comments'];
$customerid			=	$_POST['customername'];
$clientinformation	=	$_POST['clientinformation'];


$purchaseprice		=	$_POST['purchaseprice'];
$batch				=	$_POST['batch'];
$expiry				=	$_POST['expiry'];
$boxno				=	$_POST['boxno'];
//var_dump($boxno);
//echo "jafer";
//exit;

for($i=0;$i<sizeof($barcode);$i++)
{
	/*******************************IF Barcode does not exist then add it first***********************/
/*	$bc	=	$barcode[$i];
	$barcodeid = $AdminDAO->getcolumn('barcode','pkbarcodeid',"barcode='$bc'");
	if(!$barcodeid)
	{
		$field		= array('barcode','itemdescription','fkproductid');
		$value	 	= array($bc,$item[$i],$productid);
		$barcodeid	= $AdminDAO->insertrow('barcode',$field,$value);
		$f2		=	array('fkbrandid','fkbarcodeid');
		$v2		=	array($brandid[$i],$barcodeid);
		$AdminDAO->insertrow('barcodebrand',$f2,$v2);
		//echo "New Barcode added...$bc<br>";
	}*/
/******************************************************************/
	if($datetime=='')
	{
		$datetime=='00-00-0000';	
	}
	$datetime		=	date("Y-m-d H:i:s");
	$deadline1		=	date("Y-m-d",strtotime($deadline[$i]));
	$expiry1		=	date("Y-m-d H:i:s",strtotime($expiry[$i]));
	
	$itemd			=	filter($item[$i]);
	$descriptiond	=	filter($description[$i]);
	
	$commentsd			=	filter($comments[$i]);
	$clientinformationd	=	filter($clientinformation[$i]);
	
	$countryidd	  	=	filter($countryid[$i]);
	$resultset		=	$AdminDAO->getrows("countries","pkcountryid", " countryname= '$countryidd' ");
	$pkcountryid	=	$resultset[0]['pkcountryid'];	
	
	$locationidd  	=	filter($locationid[$i]);
	$resultset		=	$AdminDAO->getrows("store","pkstoreid", " storename= '$locationidd' ");
	$pkstoreid		=	$resultset[0]['pkstoreid'];		
	
	$customeridd  	=	filter($customerid[$i]);
	$resultset		=	$AdminDAO->getrows("customer","pkcustomerid", " companyname= '$customeridd' ");
	$pkcustomerid	=	$resultset[0]['pkcustomerid'];	
	
	$brandidd  		=	filter($brandid[$i]);
	$resultset		=	$AdminDAO->getrows("brand","pkbrandid", " brandname= '$brandidd' ");
	$pkbrandid		=	$resultset[0]['pkbrandid'];		
		
	$tblj		 = 	"`order`";
	$field		 =	array('fkaddressbookid','datetime','fkstoreid','fkcustomerid','fkshipmentid','barcode','itemdescription','quantity','lastsaleprice','pricelimit','agreedprice','weight','fkbrandid','fkcountryid','description','comments','deadline','clientinfo','fkstatusid');
	$value		 =	array(
						$fkaddressbookid,
						$datetime,
						$pkstoreid,
						$pkcustomerid,
						$shipmentid,
						$barcode[$i],
						$itemd,
						$quantity[$i],
						$purchaseprice[$i],
						$pricelimit[$i],
						$agreedprice[$i],
						$weight[$i],
						$pkbrandid,
						$pkcountryid,
						$descriptiond,
						$commentsd,
						$deadline1,
						$clientinformationd,
						'3'
			);
			
			$orderid	 =	$AdminDAO->insertrow($tblj,$field,$value);	
	
	if($supplierid[$i]!='')
	{

		$supplieridd	  	=	filter($supplierid[$i]);
		$resultset		=	$AdminDAO->getrows("supplier","pksupplierid", " companyname= '$supplieridd' ");
		$pksupplierid	=	$resultset[0]['pksupplierid'];
						
			$tblj		 = 	"ordersupplier";
			$field		 =	array('fkorderid','fksupplierid');
			$value		 =	array(
									$orderid,
									$pksupplierid
								);
			
			$AdminDAO->insertrow($tblj,$field,$value);			
		
		
	}//if	
	
	$tblj		 = 	"orderpurchase";
	$field		 =	array('fkaddressbookid','datetime','fkshipmentid','fkorderid','fkbarcodeid','quantity','purchaseprice','weight','fksupplierid','batch','expiry');
	$value		 =	array(
							$fkaddressbookid,
							$datetime,
							$shipmentid,
							$orderid,
							$barcodeid,
							$quantity[$i],
							$purchaseprice[$i],
							$weight[$i],
							$pksupplierid,
							$batch[$i],
							$expiry1
						);
	$orderpurchaseid	=	$AdminDAO->insertrow($tblj,$field,$value);	
			
	$fields	=	array("fkaddressbookid","datetime","fkshipmentid","fkorderid","fkorderpurchaseid","fkstoreid","quantity");
	$values	=	array($fkaddressbookid,$datetime,$shipmentid,$orderid,$orderpurchaseid,$pkstoreid,$quantity[$i]);
	$AdminDAO->insertrow("orderallot",$fields,$values);
	
	$bfields	=	array('fkaddressbookid','datetime','fkshipmentid','fkorderid','packnumber','quantity');					
//	$bdata		=	array($addressbookid,$addtime,$shipmentid,$orderid,$box,$qty);
//	$AdminDAO->insertrow("orderpack",$bfields,$bdata);	
	$commapos	=	strpos($boxno[$i],",");
	$dashpos	=	strpos($boxno[$i],"-");
	if($commapos)//if comma (,) is found then split
	{
		unset($boxes);
		$boxes		=	explode(",",$boxno[$i]);
		//calculate qty per box
		if($quantity[$i]!='')
		{
			$avgqty	=	floor($quantity[$i]/sizeof($boxes));
			if($avgqty==0)
			{
				$avgqty	= 1;
			}							
		}
		// entering box data
		$quantity2	=	$quantity[$i];
		for($b=0;$b<sizeof($boxes);$b++)
		{
			$boxname	=	$boxes[$b];
			//checking existing box name for each iteration 
			if($boxname)// not needed but sometimes we have to deal with stupids
			{
				$lastiteration	=	sizeof($boxes)-1;
				if($b==$lastiteration)
				{
					$avgqty	=	$quantity2;
				}							
				$quantity2	=	$quantity2-$avgqty;
				if($quantity2<$avgqty)
				{
					$avgqty	=	$quantity2+$avgqty;
				}
				if($avgqty<0)
				{
					$avgqty	=	0;
				}						
				//$bdata		=	array($fkaddressbookid,$addtime,$shipmentid,$orderid,$boxname,$avgqty);
				$bdata		=	array($fkaddressbookid,$datetime,$shipmentid,$orderid,$boxname,$avgqty);						
				//inserting values
				$AdminDAO->insertrow("orderpack",$bfields,$bdata);
			}
		}//end entry section
	}//end comma section
	else if($dashpos)//if hyphen (-) is found then loop
	{
		unset($boxes2);
		$boxids		=	explode("-",$boxno[$i]);
		$boxstart	=	$boxids[0];
		$boxend		=	$boxids[1];
		for($j=$boxstart;$j<=$boxend;$j++)
		{
			$boxes2[]	=	$j;
		}
		//calculate qty per box
		if($quantity[$i]!='')
		{
			$avgqty	=	floor($quantity[$i]/sizeof($boxes2));
			if($avgqty==0)
			{
				$avgqty	= 1;
			}					
		}
		// entering box data
		$quantity2	=	$quantity[$i];
		for($b=0;$b<sizeof($boxes2);$b++)
		{
			$boxname	=	$boxes2[$b];
			//checking existing box name for each iteration 
			if($boxname)// not needed but sometimes we have to deal with stupids
			{
				$lastiteration	=	sizeof($boxes2)-1;						
				if($b==$lastiteration)
				{
					$avgqty	=	$quantity2;
				}						
				$quantity2	=	$quantity2-$avgqty;
				if($quantity2<$avgqty)
				{
					$avgqty	=	$quantity2+$avgqty;
				}
				if($avgqty<0)
				{
					$avgqty	=	0;
				}
				//$bdata		=	array($fkaddressbookid,$addtime,$shipmentid,$orderid,$boxname,$avgqty);
				$bdata		=	array($fkaddressbookid,$datetime,$shipmentid,$orderid,$boxname,$avgqty);						
				//inserting values
				$AdminDAO->insertrow("orderpack",$bfields,$bdata);
			}					
		}//end entry section
	}//end dash section
	else //if($commapos=='' && $dashpos=='') //one box
	{
		//update and distribute quantity among boxes
		$bdata		=	array($fkaddressbookid,$datetime,$shipmentid,$orderid,$boxno[$i],$quantity[$i]);
		//inserting values
		$AdminDAO->insertrow("orderpack",$bfields,$bdata);
	}	

	
										
}//end for
?>