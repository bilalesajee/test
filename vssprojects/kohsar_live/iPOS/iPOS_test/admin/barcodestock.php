<?php
exit;
include_once("../includes/security/adminsecurity.php");

include_once("dbgrid.php");

global $AdminDAO,$Component;

//*************delete************************

$id			=	$_REQUEST['id'];

$labels = array("ID","Brand","Units","Units Remaining","Damaged","Retail Price","Trade Price","Shipment Date","Charges","Expiry","Supplier","Location");

$fields = array("pkstockid","brandname","quantity","unitsremaining","dquantity","retail","priceinrs","shipmentdetails","shipmentcharges","expirydate","supplier","storename");

$param	=	$_REQUEST['param'];

if($_REQUEST['param']=="brand" )

{

	$brandid = $_REQUEST['id'];

	$where=" AND s.fkbrandid = '$brandid'";

	//$brandarray	=	$AdminDAO->getrows("brand ","brandname "," pkbrandid = '$brandid' ");

	//$brandname	=	$brandarray[0]['brandname'];

	$labels = array("ID","Barcode","Units","Units Remaining","Damaged","Retail Price","Trade Price","Shipment Date","Charges","Expiry","Supplier","Location");

	$fields = array("pkstockid","barcode","quantity","unitsremaining","dquantity","retail","priceinrs","shipmentdetails","shipmentcharges","expirydate","supplier","storename");

	//$page="Brand >> <b> $brandname </b>";

}

elseif($_REQUEST['param']=="stock")

{

	$barcodeid = $_REQUEST['id'];	

	$where=" AND s.fkbarcodeid = '$barcodeid'";

}

else

{

	$where	=	" AND fkbarcodeid = '$id' ";

	//$productinfoarray	=	$AdminDAO->getrows("barcode , product ","productname,  barcode"," fkproductid = pkproductid AND pkbarcodeid = '$id' ");

	//$productname		=	$productinfoarray[0]['productname'];

	//$barcode			=	$productinfoarray[0]['barcode'];

	//$page= "Barcode >> <b> $barcode </b> >> $productname";

}



/************* DUMMY SET ***************/

$dest 	= 	'barcodestock.php';



$div	=	'sugrid';



$form 	= 	"frm1stockship";	

define(IMGPATH,'../images/');

//geting product info productname barcode for heading

// this is the main query for grid

 $query	=	"SELECT 

					s.pkstockid,

					s.fkbarcodeid,

					s.batch,

					b.barcode,

					

					br.brandname,

					s.quantity,

					s.unitsremaining,

					s.retailprice as retail,

					format(s.priceinrs,2) as priceinrs, 

					format(s.shipmentcharges,4) as shipmentcharges,

					IF(shipmentdate='0','--------',FROM_UNIXTIME(sh.shipmentdate, '%d-%m-%y')) as shipmentdetails,

					IF(expiry='0','--------',FROM_UNIXTIME(s.expiry,'%d-%m-%y')) as expirydate,

					st.storename,

					sg.shipmentgroupname,

					(SELECT SUM(quantity) from $dbname_detail.damages d WHERE s.pkstockid = d.fkstockid) as dquantity,

					(select companyname from supplier,brandsupplier bs,$dbname_detail.stock st where bs.fksupplierid=pksupplierid and bs.fkbrandid=br.pkbrandid AND st.fksupplierid = pksupplierid AND pkstockid = s.pkstockid) as supplier

				FROM 

					$dbname_detail.stock s LEFT JOIN barcode b ON (s.fkbarcodeid 	= b.pkbarcodeid) LEFT JOIN store st ON (s.fkstoreid 	= st.pkstoreid) LEFT JOIN shipment sh ON (s.fkshipmentid 	= sh.pkshipmentid) LEFT JOIN 					brand br ON (br.pkbrandid 	= s.fkbrandid) LEFT JOIN shipmentgroups sg ON (s.fkshipmentgroupid = sg.pkshipmentgroupid), barcode b2 LEFT JOIN product p ON (b2.fkproductid 	= p.pkproductid)

				WHERE 1

					

					 $where

				GROUP BY

					s.pkstockid

					 ";





$navbtn="<a href=\"javascript: loadsubgrid('movestock',document.$form.checks,'movestock.php','sugrid') \" title=\"Move Stock\"><b>Move Stock</b></a>&nbsp;

			&nbsp;";			

/********** END DUMMY SET ***************/



?><head>

</head>

<div id="movestock"></div>

<div id="sugrid"></div>

<div id='stockdivshipdiv'>

<div class="breadcrumbs" id="breadcrumbs">

<?php if($_REQUEST['param']=="brand" ) {echo "Brand Stocks";} else {echo "Stock Shipments";}?>

</div>

<?php 

	//$button->makebutton("View Stocks","javascript: showpage(0,'','instancestock.php','maindiv')");

	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray);

?>

</div>

<br />

<br />

