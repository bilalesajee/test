<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<link href="../includes/css/style.css" rel="stylesheet" type="text/css" />
<script src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js"></script>
<script src="../includes/js/common.js"></script>
<body style="background-color:#FFF;">
<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_REQUEST['id'];
$storeid	=	$_SESSION['storeid'];
//selecting customers
	/*$customerinfo	=	$AdminDAO->getrows("$dbname_detail.purchaseorder,$dbname_detail.customer,$dbname_detail.addressbook LEFT JOIN city ON (fkcityid=pkcityid)","concat(firstname,' ',lastname) as name,concat(address1,' ',address2) as address,cityname","pkpurchaseorderid='$id' AND fkcustomerid=pkcustomerid AND customer.fkaddressbookid=pkaddressbookid");
	$customername	=	$customerinfo[0]['name'];
	$address		=	$customerinfo[0]['address'];
	$cityname	=	$customerinfo[0]['cityname'];*/
  
	$customerinfo	=	$AdminDAO->getrows("addressbook LEFT JOIN city ON (fkcityid=pkcityid)","concat(firstname,' ',lastname) as name,concat(address1,' ',address2) as address,cityname","pkaddressbookid='$empid'");
	$username		=	$customerinfo[0]['name'];
	$useraddress	=	$customerinfo[0]['address'];
	$usercity		=	$customerinfo[0]['cityname'];
	$storeaddress	=	$AdminDAO->getrows("store LEFT JOIN city ON (fkcityid=pkcityid)","storename,storephonenumber,storeaddress","pkstoreid='$storeid'");
	$storename		=	$storeaddress[0]['storename'];
	$phonenumber	=	$storeaddress[0]['storephonenumber'];
	$storeaddress	=	$storeaddress[0]['storeaddress'];
	$queryforinvoice	=	"SELECT 
						pksupplierinvoiceid invoiceid,billnumber,
						companyname
	
				FROM
						$dbname_detail.supplierinvoice si,supplier
				WHERE 	
						pksupplierinvoiceid='$id'
						AND si.fksupplierid=pksupplierid
				";  
	$invoicedata	=	$AdminDAO->queryresult($queryforinvoice);	
	$invoicenumber	=	$invoicedata[0]['billnumber'];
	$suppliername	=	$invoicedata[0]['companyname'];
?>
<div style="width:6.0in;padding:0px;font-size:18px; font-weight:bold;" align="center">
	Esajee & co. Islamabad
<br />
<span style="font-size:14px;">
<strong><?php echo $suppliername;?></strong>
	
<br /></span>
<br />
<span style="font-size:14px;">
<strong>Return Invoice</strong>
	
<br /></span>
	<div style="font:Arial, Helvetica, sans-serif;font-size:11px;" align="left">
    	<br />Invoice  Trader Bill # (When we purchased): <strong><?php echo $invoicenumber;?></strong>
     
	
    </div>
<table style="width:6.0in; margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse;" align="left">
<tr>
  
    <th style="padding:5px;">Barcode</th>
    <th style="padding:5px;">Descritpion</th>
    <th style="padding:5px;">Qty</th>
    <th style="padding:5px;">T/P rate</th>     
    <th style="padding:5px;">Amount</th>  
</tr>
  <?php 
 $query 	= 	"SELECT 
				pkreturnid,
				barcode,
				priceinrs,
				itemdescription,
				r.quantity qty,
				s.quantity quantity,
				s.unitsremaining,
				returntype,
				IF(returnstatus='p','Pending','Confirmed') status
			FROM 
				$dbname_detail.returns r,$dbname_detail.stock s,returntype,barcode
			WHERE
				r.fkstockid				=	pkstockid AND
				r.fkreturntypeid		=	pkreturntypeid AND
				fkbarcodeid				=	pkbarcodeid AND
				s.fksupplierinvoiceid	=	'$id'
			";  
			
  $data	=	$AdminDAO->queryresult($query);
  $tmt=0;
  for($i=0;$i<sizeof($data);$i++)
  {
	  	$barcode 		=	$data[$i]['barcode'];
		$item			=	$data[$i]['itemdescription'];
		$qty			=	$data[$i]['qty'];
		$quantity		=	$data[$i]['quantity'];
		$unitsremaining	=	$data[$i]['unitsremaining'];
		$tp		=	$data[$i]['priceinrs'];
		$status			=	$data[$i]['status'];
		$amt=$tp*$qty;
		$tmt+=$amt;
		if($i%2==0)
		{
			$color	=	"#F8F8F8";
		}
		else
		{
			$color	=	"#ECECFF";
		}
  ?>
  <tr>
    <td style="padding:3px;"><?php echo $barcode;?></td>
    <td style="padding:3px;"><?php echo $item;?></td>
    <td style="padding:3px;"><?php echo $qty;?></td>
    <td style="padding:3px;"><?php echo $tp;?></td>
    <td style="padding:3px;"><?php echo $amt;?></td>
   </tr>
  <?php
  }//for
  ?>
   <tr>
    <td style="padding:3px;"></td>
    <td style="padding:3px;"></td>
    <td style="padding:3px;"></td>
    <td style="padding:3px;">Total</td>
    <td style="padding:3px;"><?php echo $tmt;?></td>
   </tr>
</table><br />
	<div style="font:Arial, Helvetica, sans-serif;font-size:11px;;" align="left">
  		<strong><?php echo $username;?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   Received By<br /><?php echo date("D, M m, Y");?>
    </div>
</div>
</body>
</html>
<script language="javascript">
	window.print();
	//window.close();
</script>