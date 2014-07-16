<?php

if($_GET['type']=='invoice_voucher'){
	
    $invoice_link= file_get_contents("http://210.2.171.14/accounts/pos_common_entry.php?type=invoice_voucher&invoiceid=".$_GET['invoiceid']."&supplierid=".$_GET['supplierid']."&invoice_amount=".$_GET['invoice_amount']."&billnumber=".$_GET['billnumber']."&invdate=".$_GET['invdate']."&location=".$_GET['location']);
	
	}
	if($_GET['type']=='invoice_return'){
	$rdate=time();
	 $invoice_return_link= file_get_contents("http://210.2.171.14/accounts/pos_common_entry.php?type=invoice_return&return=".$_GET['return']."&fksupplierid=".$_GET['fksupplierid']."&fksupplierinvoiceid=".$_GET['fksupplierinvoiceid']."&adddate={$rdate}&location=".$_GET['location']);
	}
	
	if($_GET['type']=='supplier'){
	$supplier_link= file_get_contents("http://210.2.171.14/accounts/pos_common_entry.php?type=supplier&supplier=".$_GET['supplier']."&name=".urlencode($_GET['name'])."&gst_num=".urlencode($_GET['gst_num'])."&ntn_num=".urlencode($_GET['ntn_num'])."&address=".urlencode($_GET['address'])."");

	}
	
	if($_GET['type']=='customer'){
	$supplier_link= file_get_contents("http://210.2.171.14/accounts/pos_common_entry.php?type=customer&code=".$_GET['code']."&name=".urlencode($_GET['name'])."");

	}
	
	
?>