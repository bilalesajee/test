<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$fkaddressbookid 	=	$_SESSION['addressbookid'];
$shipmentidd		=	$_GET['shipmentid'];

/*echo "<pre>";
print_r($_POST);
echo "</pre>";			exit;*/	
/*foreach($_POST as $key=>$value)
{
	$_POST[$key]	=	trim($value);
	echo '$'."$key	=	".'$_POST['."'$key'];\n";
}*/
$barcode			=	$_POST['barcode'];
$item				=	$_POST['item'];
$description		=	$_POST['description'];
$weight				=	$_POST['weight'];
$unit				=	$_POST['unit'];
$brandid			=	$_POST['brandname'];
$supplierid			=	$_POST['suppliercompanyname'];
$countryid			=	$_POST['country_name'];
$lastsaleprice		=	$_POST['lastsaleprice'];
$pricelimit			=	$_POST['pricelimit'];
$agreedprice		=	$_POST['agreedprice'];
$quantity			=	$_POST['quantity'];
$deadline			=	$_POST['deadline'];
$locationid			=	$_POST['locationstorename'];
$comments			=	$_POST['comments'];
$customerid			=	$_POST['customername'];
$clientinformation	=	$_POST['clientinformation'];

for($i=0;$i<sizeof($_POST['item']);$i++)
{
	if($deadline[$i]=='')
	{
		$deadline1='00-00-0000';
	}
	else
	{
		$deadline1		=	date("Y-m-d",strtotime($deadline[$i]));
	}
	$datetime		=	date("Y-m-d H:i:s");
	
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
	
	if($shipmentidd==-1)	
	{
		$shipmentid		=	0;
		$orderstatus	=	1;
	}
	else
	{
		$orderstatus	=	2;
	}
	
			$tblj		 = 	"`order`";
			$field		 =	array('fkaddressbookid','datetime','fkstoreid','fkcustomerid','fkshipmentid','barcode','itemdescription','quantity','lastsaleprice','pricelimit','agreedprice','weight','fkbrandid','fkcountryid','description','comments','productimage','deadline','clientinfo','fkstatusid','unit');
			$value		 =	array(
						$fkaddressbookid,
						$datetime,
						$pkstoreid,
						$pkcustomerid,
						$shipmentid,
						$barcode[$i],
						$itemd,
						$quantity[$i],
						$lastsaleprice[$i],
						$pricelimit[$i],
						$agreedprice[$i],
						$weight[$i],
						$pkbrandid,
						$pkcountryid,
						$descriptiond,
						$commentsd,
						'',
						$deadline1,
						$clientinformationd,
						$orderstatus,
						$unit[$i]
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
}//end for
//for redirecting on shipment in process screen
if($shipmentidd!='-1')
{
	$resultset		=	$AdminDAO->getrows("shipment","fkstatusid", " pkshipmentid= '$shipmentid' ");
	$status			=	$resultset[0]['fkstatusid'];		
	if($status==3 && $_REQUEST['blankrec']==0) 
	{
		echo "in process";
		exit;
	}
	else if($status==3 && $_REQUEST['blankrec']!=0) 
	{
		echo "in process but missing";
		exit;
	}	
}
if($_REQUEST['blankrec']!=0)
{
	echo "missing";
}
?>