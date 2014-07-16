<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_REQUEST['sid'];
if($id)
{
	// selecting invoices
	$invoice		=	$AdminDAO->getrows("$dbname_detail.supplierinvoice","pksupplierinvoiceid,concat(pksupplierinvoiceid,' (',billnumber,')') billnumber","fksupplierid='$id' and invoice_status=0");
	$invoicesel		=	"<select name=\"invoice\" id=\"invoice\" style=\"width:150px;\" ><option value=\"\">Select Invoice</option>";
	for($i=0;$i<sizeof($invoice);$i++)
	{
		$billnumber				=	$invoice[$i]['billnumber'];
		$pksupplierinvoiceid	=	$invoice[$i]['pksupplierinvoiceid'];
		$invoicesel2			.=	"<option value=\"$pksupplierinvoiceid\" $select>$billnumber</option>";
	}
	$invoices		=	$invoicesel.$invoicesel2."</select>";
	// end invoices
	echo $invoices;
}
?>