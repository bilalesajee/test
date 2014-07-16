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
$id			=	$_GET['id'];
/*if(isset($_GET['id']))
{
	$shipmentlist	=	"AND fkshipmentid = '$id'";
}
else
{
	$shipmentlist	=	'';
}*/
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
//*************delete************************
$delid			=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
switch($oper)
{
	case 'del':
	{
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition ="pkorderid = '$value' ";
				$AdminDAO->deleterows('`order`',$delcondition,'1');
			}
		}
		break;
	}
}
//$deltype	=	'delwishlist';
//include_once("delete.php"); 
//$AdminDAO->deleterows("`order`","","");
$dest 	= 	'manageshiplist.php';
$div	=	'maindiv';
$form 	= 	"orderfrm";
/*
if(isset($_GET['liststatus']))
{
	$liststatus	=	$_GET['liststatus'];
}
else
{
	$liststatus	=	1;
}

if($liststatus)
{
	$_SESSION['liststatus']		=	$liststatus;
	$status	=	"AND fkstatusid = '$liststatus'";
}
// statuses
$statuses		=	$AdminDAO->getrows("orderstatuses","pkstatusid,statusname","1");
$statussel		=	"<select name=\"status\" id=\"status\" style=\"width:100px;\" onchange=\"getstatus(this.value)\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($statuses);$i++)
{
	$statusname	=	$statuses[$i]['statusname'];
	$statusid	=	$statuses[$i]['pkstatusid'];
	$select		=	"";
	if($statusid == $liststatus)
	{
		$select = "selected=\"selected\"";
	}
	$statussel2	.=	"<option value=\"$statusid\" $select>$statusname</option>";
}
$statuses			=	$statussel.$statussel2."</select>";
// end statuses
$listagent	=	$_GET['listagent'];
if($listagent)
{
	$_SESSION['listagent']		=	$listagent;
	$agentstatus	=	"AND fkagentid = '$listagent'";
}
// agents
$agents		=	$AdminDAO->getrows("supplieroragent","pksupplierid,suppliername","isagent='a'");
$agentsel		=	"<select name=\"agent\" id=\"agent\" style=\"width:100px;\" onchange=\"getagent(this.value)\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($agents);$i++)
{
	$agentname	=	$agents[$i]['suppliername'];
	$agentid	=	$agents[$i]['pksupplierid'];
	$select		=	"";
	if($agentid == $listagent)
	{
		$select = "selected=\"selected\"";
	}
	$agentsel2	.=	"<option value=\"$agentid\" $select>$agentname</option>";
}
$agent			=	$agentsel.$agentsel2."</select>";
// end agents
$listbrand	=	$_GET['listbrand'];
if($listbrand)
{
	$_SESSION['listbrand']		=	$listbrand;
	$brandstatus	=	"AND fkbrandid = '$listbrand'";
}
// brands
$brands		=	$AdminDAO->getrows("brand","pkbrandid,brandname","branddeleted<>1 ORDER BY brandname ASC");
$brandsel	=	"<select name=\"brand\" id=\"brand\" style=\"width:100px;\" onchange=\"getbrand(this.value)\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($brands);$i++)
{
	$brandname	=	$brands[$i]['brandname'];
	$brandid	=	$brands[$i]['pkbrandid'];
	$select		=	"";
	if($brandid == $listbrand)
	{
		$select = "selected=\"selected\"";
	}
	$brandsel2	.=	"<option value=\"$brandid\" $select>$brandname</option>";
}
$brand			=	$brandsel.$brandsel2."</select>";
// end brands
$listsupplier	=	$_GET['listsupplier'];
if($listsupplier)
{
	$_SESSION['listsupplier']		=	$listsupplier;
	$supplierstatus	=	"HAVING pksupplierids LIKE '%$listsupplier%'";
}
// supplier
$suppliers		=	$AdminDAO->getrows("supplier","pksupplierid,companyname","supplierdeleted<>1 ORDER BY companyname ASC");
$suppliersel	=	"<select name=\"supplier\" id=\"supplier\" style=\"width:100px;\" onchange=\"getsupplier(this.value)\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($suppliers);$i++)
{
	$suppliername	=	$suppliers[$i]['companyname'];
	$supplierid		=	$suppliers[$i]['pksupplierid'];
	$select			=	"";
	if($supplierid == $listsupplier)
	{
		$select = "selected=\"selected\"";
	}
	$suppliersel2	.=	"<option value=\"$supplierid\" $select>$suppliername</option>";
}
$supplier			=	$suppliersel.$suppliersel2."</select>";
// end supplier
$listlocation	=	$_GET['listlocation'];
if($listlocation)
{
	$_SESSION['listlocation']		=	$listlocation;
	$locationstatus	=	"AND fkstoreid = '$listlocation'";
}
// stores
$stores		=	$AdminDAO->getrows("store","pkstoreid,storename","storestatus=1");
$storesel	=	"<select name=\"store\" id=\"store\" style=\"width:100px;\" onchange=\"getlocation(this.value)\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($stores);$i++)
{
	$storename	=	$stores[$i]['storename'];
	$storeid	=	$stores[$i]['pkstoreid'];
	$select		=	"";
	if($storeid == $listlocation)
	{
		$select = "selected=\"selected\"";
	}
	$storesel2	.=	"<option value=\"$storeid\" $select>$storename</option>";
}
$store			=	$storesel.$storesel2."</select>";
// end stores
$listcountry	=	$_GET['listcountry'];
if($listcountry)
{
	$_SESSION['listcountry']		=	$listcountry;
	$countrystatus	=	"AND fkcountrylist = '$listcountry'";
}
// countries
$countries		=	$AdminDAO->getrows("countries","pkcountryid,code3","countriesdeleted<>1");
$countrysel		=	"<select name=\"country\" id=\"country\" style=\"width:100px;\" onchange=\"getcountry(this.value)\"><option value=\"\">All</option>";
for($i=0;$i<sizeof($countries);$i++)
{
	$countryname	=	$countries[$i]['code3'];
	$countryid		=	$countries[$i]['pkcountryid'];
	$select			=	"";
	if($countryid == $listcountry)
	{
		$select = "selected=\"selected\"";
	}
	$countrysel2	.=	"<option value=\"$countryid\" $select>$countryname</option>";
}
$country			=	$countrysel.$countrysel2."</select>";*/
// end countries
define(IMGPATH,'../images/');
	$query 	= 	"SELECT 
				pkorderid,
				pkorderid orderid,
				barcode,
				itemdescription,
				quantity,
				round(lastsaleprice,2) as lastsaleprice,
				CONCAT(o.weight,' ',o.unit) as weight,
				brandname,
				DATE_FORMAT(deadline,'%d-%m-%Y') deadline,
				(SELECT GROUP_CONCAT(companyname) FROM supplier,ordersupplier sl WHERE sl.fksupplierid=pksupplierid AND  fkorderid=pkorderid) as description,
				code3,				
				storecode,
				CONCAT(firstname,' ',lastname) as name,
				statusname,
				shipmentname
			FROM
				`order` o LEFT JOIN countries ON(o.fkcountryid=pkcountryid) 
				LEFT JOIN store on (fkstoreid=pkstoreid) 
				LEFT JOIN shipment ON (o.fkshipmentid=pkshipmentid)				
				LEFT JOIN addressbook ON (o.fkaddressbookid=pkaddressbookid) 
				LEFT JOIN brand ON (fkbrandid=pkbrandid), orderstatuses
			WHERE
				o.fkstatusid	=	pkstatusid  AND pkstatusid IN(1,2) $shipmentlist $status $agentstatus $brandstatus $locationstatus $countrystatus
			GROUP BY
				pkorderid
			";
		//	echo $query $supplierstatus;
$navbtn	=	"";
if(in_array('69',$actions))
{
	/*$navbtn .= "<a class='button2' id='addshiplist' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addshiplist.php','subsection','maindiv')\" title='Add Order'>
				<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";*/
	$navbtn .="	<a class=\"button2\" id=\"editaddshiplist\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'orderadd.php','subsection','maindiv','','$formtype') title=\"Add Order\"><span class=\"addrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('70',$actions))
{
	/*$navbtn .="	<a class=\"button2\" id=\"editaddshiplist\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addshiplist.php','subsection','maindiv') title=\"Edit Order\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";*/
	$navbtn .="	<a class=\"button2\" id=\"editaddshiplist\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'orderadd.php','subsection','maindiv','','$formtype') title=\"Edit Order\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('71',$actions))
{
	$navbtn .=" <a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Orders\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
$navbtn .="	| <a class=\"button2\" id=selrecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'ordereorder.php','subsection','maindiv')  title=\"Import Orders\"><b>Reorder</b></a>";
/*
if(in_array('74',$actions))
{
	$qstring	=	$_SERVER['QUERY_STRING'];
	$navbtn .="	<a href=\"javascript: printlist('$qstring') \" title=\"Print Wish List\"><span class=\"printrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('78',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:displayrecords('packinglist.php','subsection','$div','$_SESSION[qs]') title=\"Packing List\"><b>Packing List</b></a>&nbsp;";
}*/
if(in_array('79',$actions))
{
/*$navbtn .="	<a class=\"button2\" id=selrecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:displayrecords('movelist.php','subsection','$div','$_SESSION[qs]') title=\"Finalize Orders\"><b>Move to Shipment</b></a>";*/
$navbtn .=" | <a class=\"button2\" id=selrecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:displayrecords('ordermove.php','subsection','$div','$_SESSION[qs]','','$formtype') title=\"Move Orders\"><b>Move to Shipment</b></a>";
}
/*
$navbtn .="	| <a class=\"button2\" id=selrecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'orderimport.php','subsection','maindiv','','$formtype')  title=\"Import Orders\"><b>Import Order</b></a>";
*/
/*if(in_array('98',$actions))
{*/
//$navbtn .=" | <a class=\"button2\" id=selrecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:displayrecords('changeliststatus.php','subsection','$div','$_SESSION[qs]') title=\"Change Order Status\"><b>Order Status</b></a>";
/*}*/
// if
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
		showpage(0,0,'orderadd.php','subsection','maindiv','1&brandid=<?php echo $brandid;?>&clientid=<?php echo $clientid;?>&countryid=<?php echo $countryid;?>&deadline=<?php echo $deadline;?>&supplierids=<?php echo $supplierids;?>');
	});
	<?php
}
?>
function getstatus(id)
{
	jQuery('#maindiv').load('manageshiplist.php?liststatus='+id);
}
function getagent(id)
{
	jQuery('#maindiv').load('manageshiplist.php?listagent='+id);
}
function getbrand(id)
{
	jQuery('#maindiv').load('manageshiplist.php?listbrand='+id);
}
function getsupplier(id)
{
	jQuery('#maindiv').load('manageshiplist.php?listsupplier='+id);
}
function getlocation(id)
{
	jQuery('#maindiv').load('manageshiplist.php?listlocation='+id);
}
function getcountry(id)
{
	jQuery('#maindiv').load('manageshiplist.php?listcountry='+id);
}
function printlist(text)
{
	var ids	=	selectedstring;
	//alert(ids);
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=500,height=600,left=100,top=25';
 	window.open('printshiplist.php?ids='+ids+'&'+text,'Print Wish List',display); 
}
</script>
<div id="subsection"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs"><?php if($id==''){?>Orders<?php }else{?>Orders in Shipment<?php }?></div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder="pkorderid DESC");
?>
<br />
<br />
</div>