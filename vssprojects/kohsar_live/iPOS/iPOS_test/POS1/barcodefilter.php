<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
$dest 		= 	'barcodefilter.php';
$div		=	'mainpanel';
$form 		=	"frm1bcfilter";	
define(IMGPATH,'images/');
//this block is for seraching the items in the bills
//changed $dbname_main to $dbname_detail on line 20 by ahsan 22/02/2012
   $query	=	"SELECT 
   				b.itemdescription,
				bf.pkbarcodefilterid,
				bf.currentbarcode,
				bf.fixedbarcode,
				FROM_UNIXTIME(bf.savetime,'%d-%m-%Y %h:%i:%s') as savetime,
				CONCAT(firstname,' ', lastname) employeename	
				FROM
					$dbname_detail.barcodefilter bf,
					barcode b,
					
					addressbook
					LEFT JOIN (employee ) ON ( pkaddressbookid = fkaddressbookid)
				WHERE
					b.barcode=bf.currentbarcode AND
					bf.fkaddressbookid=pkaddressbookid
";
$labels = array("ID","Current Barcode","Item Description","Fixed Barcode","Cashier","Date Time");
$fields = array("pkbarcodefilterid","currentbarcode","itemdescription","fixedbarcode","employeename","savetime");
$sortorder="pkbarcodefilterid DESC";
?>
<br />
<br />
<div id="mainpanel">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?>
</div>