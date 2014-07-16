<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
//$rights	 	=	$userSecurity->getRights(30);
//$labels	 	=	$rights['labels'];
//$fields		=	$rights['fields'];
$labels			=	array("ID","Serial #","Invoice Date","From Date","To Date","Added by","Customer","Account Title","Added On");
$fields			=	array("pkcreditinvoiceid","serialno","invoicedate","fromdate","todate","name","cust","title","datetime");
//$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
$dest 	= 	'managecreditinvoices.php';
$div	=	'maindiv';
$form 	= 	"frmcreditinvoices";
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkcreditinvoiceid,
				serialno,
				FROM_UNIXTIME(datetime,'%d-%m-%Y') datetime,
				FROM_UNIXTIME(fromdate,'%d-%m-%Y') fromdate,
				FROM_UNIXTIME(todate,'%d-%m-%Y') todate,
				FROM_UNIXTIME(invoicedate,'%d-%m-%Y') invoicedate,
				CONCAT(a.firstname,' ',a.lastname) name,
				companyname title,
				CONCAT(ct.firstname,' ',ct.lastname) cust,
				taxpercentage
			FROM
				$dbname_detail.creditinvoices ci,addressbook a,customer ct
			WHERE	
				ci.fkaddressbookid 	= 	a.pkaddressbookid AND
				fkaccountid		=	pkcustomerid and location=3 ";
$navbtn	=	"";
$navbtn .= "<a class='button2' id='addcinvoice' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addcreditinvoice.php','subsection','maindiv')\" title='Add Creditor Invoice'>
				<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
$navbtn .="	<a class=\"button2\" id=\"editcinvoice\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addcreditinvoice.php','subsection','maindiv') title=\"Edit Creditor Invoice\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
$navbtn .="	<a class=\"button2\" id=deleterecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Creditor Invoice\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
$navbtn	.="  <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void()\"; onclick= \"printinvoicereport();return false;\" title=\"Posted Invoices\"><span class=\"addbrands\"><b>Posted Invoices</b></span></a>&nbsp;";
/********** END DUMMY SET ***************/
?>
<script language="javascript" type="text/javascript">
function printinvoicereport()
{
	var sel	=	getselected('maindiv');
	var sb;
	if (sel.length > 1)
	{
		for (i=1; i < sel.length; i++)
		{
			 sb	=	sel[i];
		} 
		var sb1	=	sb.split('maindiv');
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
 	window.open('printcreditorinvoice.php?id='+sb1,display); 
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
		return false;
	}
}
</script>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Credit Invoices</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionarray,"  name ASC ");
?>
<br />
<br />
</div>