<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(30);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
$bcid		=	trim($_GET['bcid'],",");
if($bcid)
{
	//echo $bcid." is the barcodeid";
	$dest 	= 	'manageshiplist.php';
	$div	=	'maindiv2';
	$form 	= 	"frm11";
	// end statuses
	define(IMGPATH,'../images/');
	$query 	= 	"SELECT 
					pkorderid,
					barcode,
					itemdescription,
					quantity,
					round(lastsaleprice,0) as lastsaleprice,
					DATE_FORMAT(deadline,'%d-%m-%Y') deadline,
					(SELECT GROUP_CONCAT(companyname) FROM supplier,ordersupplier sl WHERE sl.fksupplierid=pksupplierid AND  fkorderid=pkorderid) as description,
					o.weight,
					brandname,
					code3,				
					storecode,
					CONCAT(firstname,' ',lastname) as name,
					statusname
				FROM
					`order` o LEFT JOIN countries ON(o.fkcountryid=pkcountryid) 
					LEFT JOIN store on (fkstoreid=pkstoreid) 
					LEFT JOIN addressbook ON (o.fkaddressbookid=pkaddressbookid) 
					LEFT JOIN brand ON (fkbrandid=pkbrandid), orderstatuses
				WHERE
					o.fkstatusid	=	pkstatusid AND
					barcode IN ($bcid)
				GROUP BY
					pkorderid
				";
	//echo $query;
	$navbtn	=	"";
	?>
	<div id="sugrid"></div>
	<div id='maindiv2'>
	<div class="breadcrumbs" id="breadcrumbs">Order History</div>
	<?php //print_r($labels);
	//print_r($fields);
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder="pkorderid DESC");
	
	?>
	<br />
	<br />
	</div>
<?php
}
else
{
	echo "Insufficient data";
}
?>