<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(30);
//$labels	 	=	$rights['labels'];
//$fields		=	$rights['fields'];

$fields		=	array('pkorderid','statusname','barcode','itemdescription','brandname','purchaseprice','code3','weight','lastsaleprice','quantity','deadline','description');
$labels 	=	array('ID','Status','Barcode','Item','Brand','Last Purchase Price','Country of Origin','Weight','Current Sale Price','Quantity','Deadline','Supplier');

$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
$id			=	$_GET['id'];
//get shipment name for displaying in the breadcrumb
$resultset		=	$AdminDAO->getrows("shipment,shipstatuses","shipmentname,statusname", " pkshipmentid= '$id' AND fkstatusid=pkstatusid");
$shipmentname	=	$resultset[0]['shipmentname'];	
$shipmentstatus	=	$resultset[0]['statusname'];	

if(isset($_GET['id']))
{
	$shipmentlist	=	"AND o.fkshipmentid = '$id'";
}
else
{
	$shipmentlist	=	'';
}
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
//$deltype	=	'delwishlist';
//include_once("delete.php"); 
//$AdminDAO->deleterows("`order`","","");
$dest 	= 	'vieworders.php';
$div	=	'maindiv23';
$form 	= 	"orderfrm";
define(IMGPATH,'../images/');
	$query 	= 	"SELECT 
				pkorderid,
				pkorderid orderid,
				o.barcode,
				b.itemdescription,
				o.quantity,
				round(lastsaleprice,2) as lastsaleprice,
				CONCAT(o.weight,' ',o.unit) as weight,
				brandname,
				DATE_FORMAT(deadline,'%d-%m-%Y') deadline,
				(SELECT GROUP_CONCAT(companyname) FROM supplier,ordersupplier sl WHERE sl.fksupplierid=pksupplierid AND  fkorderid=pkorderid) as description,
				code3,				
				storecode,
				CONCAT(firstname,' ',lastname) as name,
				statusname,
				(SELECT `purchaseprice` FROM $dbname_detail.stock s WHERE s.`fkbarcodeid` = b.pkbarcodeid AND pkstockid
					IN (
					SELECT MAX( pkstockid )
					FROM $dbname_detail.stock
					GROUP BY fkbarcodeid
					)
				)purchaseprice,
				shipmentname
			FROM
				`order` o LEFT JOIN countries ON(o.fkcountryid=pkcountryid) 
				LEFT JOIN barcode b ON (b.barcode=o.barcode)
				LEFT JOIN store on (fkstoreid=pkstoreid) 
				LEFT JOIN shipment ON (o.fkshipmentid=pkshipmentid)				
				LEFT JOIN addressbook ON (o.fkaddressbookid=pkaddressbookid) 
				LEFT JOIN brand ON (fkbrandid=pkbrandid), orderstatuses
			WHERE
				o.fkstatusid	=	pkstatusid $shipmentlist $status $agentstatus $brandstatus $locationstatus $countrystatus
			GROUP BY
				pkorderid
			";
		//	echo $query $supplierstatus;
$navbtn	=	"";
/*$navbtn .="&nbsp;<a class=\"button2\" id=selrecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:displayrecords('ordermove.php','subsection23','maindiv23','$_SESSION[qs]','','$formtype') title=\"Move Orders\"><b>Move to Shipment</b></a>";*/

/********** END DUMMY SET ***************/
$lock		=	$_GET['alock'];
$brandid	=	$_GET['brandid'];
$clientid	=	$_GET['clientid'];
$countryid	=	$_GET['countryid'];
$deadline	=	$_GET['deadline'];
$supplierids	=	$_GET['supplierids'];
?>
<script language="javascript" type="text/javascript">
<?php 
if ($lock==1)
{
	?>
	jQuery().ready(function() 
	{
		//showpage(0,'','orderadd.php','subsection','maindiv');
		showpage(0,0,'orderadd.php','subsection23','maindiv23','1&brandid=<?php echo $brandid;?>&clientid=<?php echo $clientid;?>&countryid=<?php echo $countryid;?>&deadline=<?php echo $deadline;?>&supplierids=<?php echo $supplierids;?>');
	});
	<?php
}
?>
function getstatus(id)
{
	jQuery('#maindiv23').load('manageshiplist.php?liststatus='+id);
}
function getagent(id)
{
	jQuery('#maindiv23').load('manageshiplist.php?listagent='+id);
}
function getbrand(id)
{
	jQuery('#maindiv23').load('manageshiplist.php?listbrand='+id);
}
function getsupplier(id)
{
	jQuery('#maindiv23').load('manageshiplist.php?listsupplier='+id);
}
function getlocation(id)
{
	jQuery('#maindiv23').load('manageshiplist.php?listlocation='+id);
}
function getcountry(id)
{
	jQuery('#maindiv23').load('manageshiplist.php?listcountry='+id);
}
function printlist(text)
{
	var ids	=	selectedstring;
	//alert(ids);
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=500,height=600,left=100,top=25';
 	window.open('printshiplist.php?ids='+ids+'&'+text,'Print Wish List',display); 
}
</script>
<div id="subsection23"></div>
<div id='maindiv23'>
<div class="breadcrumbs" id="breadcrumbs">Orders in Shipment <?php echo $shipmentname." (".$shipmentstatus.")";?></div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder="pkorderid DESC");
?>
<br />
<br />
</div>