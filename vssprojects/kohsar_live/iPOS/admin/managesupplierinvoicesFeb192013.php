<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(43);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$div		=	'maindiv';	
$query		=	"SELECT 
						pksupplierinvoiceid,
						pksupplierinvoiceid invoiceid,
						billnumber,
						FROM_UNIXTIME(datetime,'%d-%m-%y') datetime,
						description,
						image,
						companyname,
						(SELECT group_concat(DISTINCT(shipmentname)) FROM shipment,$dbname_detail.stock s WHERE fkshipmentid=pkshipmentid AND s.fksupplierinvoiceid=invoiceid) shipmentname
						
				FROM
						$dbname_detail.supplierinvoice si,supplier
				WHERE 	
						si.fksupplierid=pksupplierid
				";
$dest 	= 	"managesupplierinvoices.php";
$form 	= 	"frmsinvoice";	
define(IMGPATH,'../images/');

$navbtn	=	"";
$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addsupplierinvoice.php','subsection','$div')\" title='Add Supplier'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addsupplierinvoice.php','subsection','$div') title=\"Edit Supplier\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
$navbtn .="	<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'processreturns.php','subsection','$div') title=\"Process Returns\"><span><b>Process Returns</b></span></a>&nbsp;|";
$navbtn .="	<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'managereturns.php','subsection','$div') title=\"Manage Returns\"><span><b>Returns</b></span></a>&nbsp;|";
$navbtn .="&nbsp;<a href=\"javascript: printsupllierreport('')\" title=\"Shipment Report\"><b> Supplier Report </b></a>&nbsp;";
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
$sortorder	=	" pksupplierinvoiceid DESC";
?><head>
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
function printsupllierreport(text)
{
	var ids	=	selectedstring;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600,left=100,top=25';
 	window.open('supplierreport.php?ids='+ids+'&'+text,'Print Ship Report',display); 
}
</script>