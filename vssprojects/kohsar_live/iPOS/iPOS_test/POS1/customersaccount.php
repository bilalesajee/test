<?php
include("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$Bill;
$Bill			=	new Bill($AdminDAO);
$rights	 	=	$userSecurity->getRights(8);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$id			=	$_GET['id'];
$printaction=	$_GET['action'];
/************* DUMMY SET ***************/
$labels = array("ID","Customer Name","Email","Title","Address","Phone","NIC","Paid","Discount","Credit","Total");
$fields = array("pkcustomerid","name","email","companyname","address","phone","nic","paid","discount","pending","total");

$dest 	= 	'customers.php';
$div	=	'mainpanel';
$form 	= 	"frm1cutomers";	
;
define(IMGPATH,'images/');
//***********************sql for record set**************************//changed $dbname_main to $dbname_detail on line 38, 39 by ahsan 22/02/2012
$query="SELECT 
			DISTINCT id as pkcustomerid,
			CONCAT(firstname,', ',lastname) as name,
			CONCAT(address1,', ',address2) as address,
			email,
			title as companyname,
			CONCAT(phone ,', ',mobile) as phone,
			nic,
			ROUND(SUM(cash)) as cash,
			ROUND(SUM(cc)) as cc,
			ROUND(SUM(fc)) as fc,
			ROUND(SUM(cheque)) as cheque,
			SUM(totalamount) as totalamount,
			SUM(globaldiscount) as discount
		FROM
			$dbname_detail.sale,
			$dbname_detail.account LEFT JOIN $dbname_detail.addressbook ON (pkaddressbookid=fkaddressbookid)
		WHERE
			fkaccountid='$id' AND
			id=fkaccountid
		";
//echo $query;
//$customer_array		=	$AdminDAO->queryresult($query);
$customer_array		=	$AdminDAO->queryresult($query);
$customername		=	$customer_array[0]['name'];
$companyname		=	$customer_array[0]['companyname'];
$total				=	$customer_array[0]['totalamount'];
$discount			=	$customer_array[0]['discount'];
$cash				=	$customer_array[0]['cash'];
$cc					=	$customer_array[0]['cc'];
$fc					=	$customer_array[0]['fc'];
$cheque				=	$customer_array[0]['cheque'];
//$totalpaid			=	floor($customer_array[0]['paid']);
$totalpaid			=	floor($cash+$cc+$fc+$cheque);
//$remainingprice		=	ceil($customer_array[0]['pending']);
$remainingprice		=	ceil($total-$totalpaid-$discount);
//*******************************************************************
?>
<link rel="stylesheet" type="text/css" href="includes/css/style.css" />
<?php
if($printaction!='')//setting up headers
{
?>
<div style="width:2.6in;padding:0px;font-size:17px;" align="center">
<b>ESAJEE'S</b><br />
<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
<b>Think globally shop locally</b>
</span>
</div>
<?php
}
?>
<div id="rightpanel" style="width:300px">
  <table  class="price" style="width:300px">
    <tr>
      <th colspan="2"><?php echo trim($customername,',');?></th>
    </tr>
    <tr>
      <th width="200">Total</th>
      <td width="150" id="aright"><?php echo numbers($total);?></td>
    </tr>
    <tr>
    	<th>
        	Discount
        </th>
        <td id="aright"><?php echo numbers($discount);?></td>
    </tr>
    <tr>
      <th>Paid</th>
      <td id="aright"><?php echo numbers($totalpaid);?></td>
    </tr>
	<tr>
      <th>Remaining Balance</th>
      <td id="aright">
	  <?php 
	  		$rem=$total-$discount-$totalpaid;
			echo numbers($rem);
		?>
      </td>
    </tr>
  </table>
  <?php
  	if($printaction=='')
	{
		?>
   <span class="buttons">
   <button type="button" name="button" id="button" onclick="printaccountinfo('<?php echo $id;?>','print');" title="Print This info">
            <img src="images/printer.png" alt=""/> 
           Print
  </button>
        <button type="button" name="button2" id="button2" onclick="hidediv('collections');" title="Cancel">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
   </span>
 	 <script language="javascript">
 function printaccountinfo(id,action)
 {
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=520,height=300,left=100,top=25';
 	window.open('customersaccount.php?id='+id+'&action=print','Customer Account',display); 	 
 }
 </script>
 <?php
	}//end of printaction
  ?>
   <div id="error"></div>
 
</div>
<?php
if($printaction=='print')
{
?>
 <script language="javascript">
 	window.print();
	window.close();
</script>
 <?php
}
?>