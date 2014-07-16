<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(43);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$div		=	'maindiv';
//(SELECT group_concat(DISTINCT(shipmentname)) FROM shipment,$dbname_detail.stock s WHERE fkshipmentid=pkshipmentid AND s.fksupplierinvoiceid=invoiceid) shipmentname
/*$query		=	"SELECT 
						pksupplierinvoiceid,
						pksupplierinvoiceid invoiceid,
						billnumber,
						FROM_UNIXTIME(datetime,'%d-%m-%y') datetime,
						description,
						companyname,
						shipmentname
				FROM
						$dbname_detail.supplierinvoice si,supplier,shipment,$dbname_detail.stock s
				WHERE 	
						si.fksupplierid=pksupplierid AND 
						s.fksupplierinvoiceid = pksupplierinvoiceid AND
						s.fkshipmentid = pkshipmentid
				GROUP BY
						pksupplierinvoiceid
				";<?php */
				
$query		=	"SELECT
					pksupplierinvoiceid,
					pksupplierinvoiceid invoiceid,
					billnumber,
					FROM_UNIXTIME(datetime,'%d-%m-%y') datetime,
					description,
					image,
					companyname,
					(SELECT group_concat(DISTINCT(shipmentname)) FROM shipment,$dbname_detail.stock s WHERE fkshipmentid=pkshipmentid AND s.fksupplierinvoiceid=invoiceid) shipmentname,
					IF(invoice_status = 1, round(sum(quantity * priceinrs),2) , '') AS   invoice_value,
					
					IF(invoice_status = 0, 'Open', IF(invoice_status = 1, 'Close', 'Void')) AS invoice_status,
					refrance_id,
					IF(accdatasent = 0, 'Pending','Confirm') AS account_status
					FROM
					$dbname_detail.supplierinvoice si left join $dbname_detail.stock s on s.fksupplierinvoiceid = pksupplierinvoiceid
					left join shipment on s.fkshipmentid = pkshipmentid,supplier
				WHERE
					si.fksupplierid=pksupplierid    GROUP BY pksupplierinvoiceid";
					
				//	si.fksupplierid=pksupplierid and datetime > 1356894000   GROUP BY pksupplierinvoiceid";

$dest 	= 	"managesupplierinvoices.php";
$form 	= 	"frmsinvoice";	
define(IMGPATH,'../images/');

$navbtn	=	"";
/*$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addsupplierinvoice.php','subsection','$div')\" title='Add Supplier'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";*/
$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addsupplierinvoice.php','subsection','$div') title=\"Edit Supplier\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp|";
if(in_array('205',$actions))
{
$navbtn .="&nbsp;<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'edit_stockfrm_inv.php','subsection','$div') title=\"Edit Stock\"><span ><b>Edit Invoice Stock</b></span></a>&nbsp;|";
}


/*$navbtn .="	<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'processreturns.php','subsection','$div') title=\"Process Returns\"><span><b>Process Returns</b></span></a>&nbsp;|";
$navbtn .="	<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'managereturns.php','subsection','$div') title=\"Manage Returns\"><span><b>Returns</b></span></a>&nbsp;|";
*/$navbtn .="&nbsp;<a href=\"javascript: printsupllierreport('')\" title=\"Shipment Report\"><b> Supplier Report </b></a>&nbsp;|";
/*if(in_array('21',$actions))
{
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addsupplier.php','subsection','$div')\" title='Add Supplier'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(in_array('22',$actions))
{
	$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addsupplier.php','subsection','$div') title=\"Edit Supplier\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('23',$actions))
{
	$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$param&brandid=$brandid') title=\"Delete Suppliers\"><span class=\"deleterecord\">&nbsp;</span></a>";
}

if(in_array('56',$actions) && $param != "brand")
{
	$navbtn .="<a class=\"button2\" id=viewbrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'managebrands.php','subsection','$div','suppliers') title=\"View Brands\">
	<b>View Brands</b>
	</a>";
}*/
if(in_array('204',$actions))
{
$navbtn .="	<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: invoicestatus('open')\" title=\"open invoice status\"><b>Open Invoice</b></a>|";
}
if(in_array('203',$actions))
{
$navbtn .="	<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: invoicestatus('close')\" title=\"close invoice status\"><b>Close Invoice</b></a>&nbsp;";
}
if(in_array('207',$actions))
{
$navbtn .="|&nbsp;<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: invoicestatus_acc()\" title=\"Resend\"><b>Resend to Accounts</b></a>&nbsp;";
}
if(in_array('209',$actions))
{
$navbtn .="|&nbsp;<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: invoicestatus('void')\" title=\"Void invoice status\"><b>Void Invoice</b></a>";
}


if($_SESSION['addressbookid']== '1888')
	{
	$navbtn .="&nbsp; | <a href=\"javascript: printhistory('')\" title=\"View History\"><b> View History </b></a>";
	}
$sortorder	=	" pksupplierinvoiceid DESC";
?><head><title>Invoices</title>
</head>

<div id="sugrid"></div>
<div id="returnsdiv"></div>
<div id='<?php echo $div;?>'>
	<div class="breadcrumbs" id="breadcrumbs">Invoices</div>
	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
</div>
<br />
<br />
<script language="javascript">
function invoicestatus(text)
{

var r = confirm("Are You Sure "+text+" This Invoice!");
if (r == true){
	
	var ids	=	selectedstring;
	invoice_status = $("#invoice_status").val(); 
	$.ajax({
type: "GET",
url: 'changestatusin.php',
success: response,
data: 'ids='+ids+'&invoice_status='+text,


});
loadsection('maindiv','managesupplierinvoices.php');
  }else{
  loadsection('maindiv','managesupplierinvoices.php');
  }

}

function response(text)

{
alert(text);
loadsection('maindiv','managesupplierinvoices.php');
}

function invoicestatus_acc()
{
var r = confirm("Are You Sure Resend This Invoice!");
if (r == true){
	
	var ids	=	selectedstring;
	invoice_status = $("#invoice_status").val(); 
	$.ajax({
type: "GET",
url: 'resend2acc_inv.php',
success: response_acc,
data: 'ids='+ids,


});
loadsection('maindiv','managesupplierinvoices.php');
}else{
  loadsection('maindiv','managesupplierinvoices.php');
  }


}

function response_acc(text)

{
alert(text);
loadsection('maindiv','managesupplierinvoices.php');
}
function printsupllierreport(text)
{
	var ids	=	selectedstring;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600,left=100,top=25';
 	window.open('supplierreport.php?ids='+ids+'&'+text,'Print Ship Report',display); 
}
function printhistory(text)
{
	var ids	=	selectedstring;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600,left=100,top=25';
 	window.open('history_print.php?screen=invoices&ids='+ids+'&'+text,'Cusromer History Report',display); 
}
</script>