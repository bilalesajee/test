<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$del;
$rights	 	=	$userSecurity->getRights(9);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];

if($groupid!=$ownergroup)
{
	//$from=" store st";
	 $where=" AND d.fkstoreid='$storeid' ";
}
//*************delete************************
$deltype	=	"deldemand";
include_once("delete.php");
$dest 	= 	'managedemands.php';
$div	=	'maindiv';
$form 	= 	"frm1demand";	
define(IMGPATH,'../images/');
/*SELECT 
							CONCAT(productname, ' | ', GROUP_CONCAT( attributeoptionname ORDER BY attributeposition	)) PRODUCTNAME
						FROM 
							productattribute pa RIGHT JOIN (product p, attribute a) ON (pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid),
							attributeoption ao,
							productinstance pi,
							barcode b
						WHERE
							pkproductid = pa.fkproductid AND
							pkattributeid = pa.fkattributeid AND
							pkproductattributeid = fkproductattributeid AND 
							pkattributeid	=	 ao.fkattributeid AND 
							pkattributeoptionid = pi.fkattributeoptionid  AND
							b.fkproductid = pkproductid AND
						pi.fkbarcodeid = b.pkbarcodeid AND
							pkbarcodeid =barcodeid*/

if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	$query 	= 	"SELECT 
 					dd.pkdemanddetailid,
					d.pkdemandid,
					d.demandname,
					s.storename,
					from_unixtime(d.demanddate,'%d-%m-%y') as demanddate,
					concat(a.firstname,' ',a.lastname) as empname,
					demandproduct
				FROM 
					$dbname_detail.demand d LEFT JOIN $dbname_detail.addressbook a ON (a.pkaddressbookid = d.fkaddressbookid) LEFT JOIN store s ON (s.pkstoreid = d.fkstoreid), $dbname_detail.demanddetails dd
				WHERE 
					d.demandstatus !='f'
					AND pkdemandid = fkdemandid
					$where
			";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$query 	= 	"SELECT 
 					dd.pkdemanddetailid,
					d.pkdemandid,
					d.demandname,
					s.storename,
					from_unixtime(d.demanddate,'%d-%m-%y') as demanddate,
					from_unixtime(d.demanddate,'%Y-%m-%d') as sortingdate,
					concat(a.firstname,' ',a.lastname) as empname,
					demandproduct
				FROM 
					$dbname_detail.demand d LEFT JOIN $dbname_detail.addressbook a ON (a.pkaddressbookid = d.fkaddressbookid) LEFT JOIN store s ON (s.pkstoreid = d.fkstoreid), $dbname_detail.demanddetails dd
				WHERE 
					d.demandstatus !='f'
					AND pkdemandid = fkdemandid
					$where
			";
}//end edit
if(in_array('30',$actions))
{

	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adddemand.php','sugrid','maindiv')\" title='Add Demand'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(in_array('31',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Demands\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
/*if(in_array('32',$actions))
{
	$navbtn .="<a class=\"button2\" id=\"editdemands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','sugrid','maindiv') title=\"Add Items\"><b>Add Items</b></a>&nbsp;| ";
}
*/
/*if(in_array('33',$actions))
{
	$navbtn .="<a class=\"button2\" id=\"editdemands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','subsection','maindiv') title=\"Add Items\"><b>Add Items</b></a>&nbsp;|";
}*/
if(in_array('34',$actions))
{
	$navbtn .="<a href=\"javascript: showpage(1,document.$form.checks,'demanddetails.php','sugrid','maindiv') \" title=\"View Demand Details\"><b>Demand Details</b></a>";
}
$navbtn .="&nbsp;| <a href=\"javascript: showpage(0,document.$form.checks,'manageinvoices.php','sugrid','maindiv') \" title=\"View Add Invoices\"><b>Invoices</b></a>";
$navbtn .="&nbsp;| <a href=\"javascript: showpage(0,'','customerdemands.php','sugrid','maindiv') \" title=\"Manage customer Demands\"><b>Customer Demands</b></a>";
/*
$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adddemand.php','subsection','maindiv')\" title='Add Demand'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Demands\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=\"editdemands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','subsection','maindiv') title=\"Add Items\"><b>Add Items</b></a>&nbsp;|
			<a href=\"javascript: loadsubgrid('sugrid',document.$form.checks,'demanddetails.php','maindiv') \" title=\"View Demand Details\"><b>Demand Details</b></a>";
			
/********** END DUMMY SET ***************/

?><head>
</head>
<div id='maindiv'>
<div id="sugrid"></div>
<div class="breadcrumbs" id="breadcrumbs">Demands</div>
	<?php 

        grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
    ?>
</div>