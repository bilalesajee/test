<?php
error_reporting(7);
include_once("../includes/security/adminsecurity.php");
include_once("../includes/classes/exportcsv.php");
global $AdminDAO;
$id		=	$_GET['id'];
$query	=	"SELECT
				barcode,
				itemdescription,
				sd.quantity,
				sl.lastpurchaseprice,
				weight,
				GROUP_CONCAT(companyname) companyname
			FROM 
				shiplistdetails sd,shiplist sl LEFT JOIN shiplistsupplier LEFT JOIN supplier ON (fksupplierid=pksupplierid) ON (fkshiplistid=pkshiplistid)
			WHERE
				pkshiplistid	=	sd.fkshiplistid	AND
				sd.fkshipmentid		=	'$id'
			GROUP BY pkshiplistdetailsid
			";
$result		=	$AdminDAO->queryresult($query);
// calling export function
$headcols	=	array('Barcode','Item','Qty','Price','Weight','Supplier');
$filename	=	'Shopping_List';
$type		=	'xls';//csv
exportcsv($result,$headcols,$filename,$type);
?>