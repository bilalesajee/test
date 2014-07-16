<?php
	include("../includes/security/adminsecurity.php");

	global $AdminDAO;

 $id	=	$_REQUEST['ids'];

	$id	=	trim($id,',');

 $idarr	=	explode(',',$id);
	$newid	=	$idarr[(sizeof($idarr)-1)];

////////////////////////////////////////////////////master table query///////////////////////////////////////
 $query = "select ad.phone,ad.zip,ad.address1,c.cityname ,st.statename,o.*,s.companyname,FROM_UNIXTIME(o.addtime,'%Y-%m-%d') as addtime from $dbname_detail.purchase_order o 
 
 left join supplier s on s.pksupplierid = o.fksupplierid
 left join addressbook ad on ad.pkaddressbookid= o.fksupplierid
 left join city c on c.pkcityid = ad.fkcityid
left join state st on st.pkstateid = ad.fkstateid
 
 where pkpurchaseorderid = '$newid'";
	$purchase_order=	$AdminDAO->queryresult($query);
	$supplierid 	= 	$purchase_order[0]['fksupplierid'];
	$addtime		=	$purchase_order[0]['addtime'];
	$remarks			=	$purchase_order[0]['remarks'];
	$status			=	$purchase_order[0]['status'];
	$ship_to			=	$purchase_order[0]['ship_to'];
	
 $pkpurchaseorderid			=	$purchase_order[0]['pkpurchaseorderid'];
 $companyname			=	$purchase_order[0]['companyname'];
  $phone			=	$purchase_order[0]['phone'];
 $add=	$purchase_order[0]['address1'].', '.$purchase_order[0]['cityname'].', '.$purchase_order[0]['statename'].', '.$purchase_order[0]['zip'];
 ///////////////////////////////////////////////////////////////////////
 $query_detail = "select p.*,b.itemdescription from $dbname_detail.purchase_order_detail p 
 
 left join barcode b on b.pkbarcodeid = p.fkbarcodeid

 where fkpurchaseorderid = '$newid'";
	$reportresult=	$AdminDAO->queryresult($query_detail);
 ////////////////////////////////////////////detail table query////////////////////////////////////////////////////
/**************************************************************/
$sql="SELECT 	b.firstname,b.zip,b.address1,c.cityname ,email,st.statename,b.phone	from addressbook b
left join city c on c.pkcityid = b.fkcityid
left join state st on st.pkstateid = b.fkstateid

where b.pkaddressbookid='28'";

$storearray=	$AdminDAO->queryresult($sql);

$storenameadd=	$storearray[0]['address1'].', '.$storearray[0]['cityname'].', '.$storearray[0]['statename'].', '.$storearray[0]['zip'];
$phone=	$storearray[0]['phone'];
$email=	$storearray[0]['email'];
$firstname=	$storearray[0]['firstname'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Purchase Order Print</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<link rel="stylesheet" type="text/css" href="../includes/css/style.css">
<style type="text/css">
.style1 {
        font-size: 16px;

        font-weight: bold;
}
.style1 {
	font-size: 16px;

	font-weight: bold;
}
.style4 {
        font-size: 14px;

        font-weight: bold;
}
.style4 {font-size: 14px; font-weight: bold; }
</style>
</head>
<script>
function printpage()
{
window.print();
}
</script>
<body>
<br />
<table width="100%" class="simple" style="background-color:#E6EFF7">
 

	
  <tr>
		<td>
		<table width="100%"  border="0" style="border:none; background-color:#E6EFF7" >
		  <tr style="border:none;">
		    <td align="center" style="border:none; font-size:14px; font-weight:bold"><a href="#" onClick="printpage()"><img src="images/printer.png" alt="ff" width="20" height="20" border="0" /></a></td>
		    <td align="center" style="border:none; font-size:14px; font-weight:bold">&nbsp;</td>
		    <td align="center" style="border:none; font-size:14px; font-weight:bold">Purchase Order</td>
		    <td align="center" style="border:none; font-size:14px; font-weight:bold">&nbsp;</td>
		    <td align="center" style="border:none; font-size:14px; font-weight:bold">&nbsp;</td>
		    <td align="center" style="border:none; font-size:14px; font-weight:bold">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="6" style="border:none;">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td align="center" style="border:none;">&nbsp;</td>
		    <td align="center" style="border:none;"><span style="width:8in;padding:0px;margin-left:-200px;font-size:17px;"><img src="../images/esajeecologo.jpg" alt="" width="286" height="77"></span></td>
		    <td align="center" style="border:none;"><span style="font-size:11px; line-height:15px"><span class="style1">Think globally shop locally</span><br />
            <span class="style4">Importers & General Order Suppliers </span></span></td>
		    <td colspan="3" style="border:none;">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="2" align="center" style="border:none; font-size:14px; font-weight:bold">&nbsp;</td>
		    <td align="center" style="border:none; font-size:14px; font-weight:bold"><?php echo $firstname;?></td>
		    <td colspan="3" align="center" style="border:none; font-size:14px; font-weight:bold">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="2" style="border:none; font-weight:bold">Company Phone:&nbsp;&nbsp;<?php echo $phone; ?>&nbsp;</td>
		    <td colspan="2" align="right" style="border:none;font-size:12px; font-weight:bold">&nbsp;</td>
		    <td colspan="2" align="center" style="border:none;font-size:12px; font-weight:bold">Purchase Order</td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="2" style="border:none;  font-weight:bold">Website:www.esajee.com</td>
		    <td colspan="2" align="right" style="border:none;">&nbsp;</td>
		    <td width="13%" align="left" style="border:none;font-weight:bold;">Date As:&nbsp;&nbsp;</td>
		    <td width="11%" align="left" style="border:none;font-weight:bold; text-decoration:underline"><?php echo $addtime?></td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="2" style="border:none;  font-weight:bold">Email:&nbsp;&nbsp;<?php echo $email;?>&nbsp;</td>
		    <td colspan="2" align="right" style="border:none;">&nbsp;</td>
		    <td align="left" style="border:none;font-weight:bold">Purchase Order #:&nbsp;&nbsp;</td>
		    <td align="left" style="border:none;font-weight:bold; text-decoration:underline"><?php echo $pkpurchaseorderid?></td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="2" style="border:none;  font-weight:bold">Address:&nbsp;&nbsp;<?php echo $storenameadd ; ?></td>
		    <td colspan="2" align="right" style="border:none;">&nbsp;</td>
		    <td align="left" style="border:none;font-weight:bold">&nbsp;</td>
		    <td align="left" style="border:none;font-weight:bold;">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="6" style="border:none;  font-weight:bold"><hr/>&nbsp;</td>
	      </tr>
		 <!-- <tr style="border:none;">
		    <td width="12%" style="border:none;font-size:12px; font-weight:bold">About Vendor:</td>
		    <td colspan="5" style="border:none;font-size:12px; font-weight:bold">&nbsp;</td>
	      </tr>-->
		 <?php /*?> <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">Vendor Name:&nbsp;&nbsp;</td>
		    <td colspan="5" style="border:none;  font-weight:bold; text-decoration:underline"><?php echo $companyname;?></td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">Company Name:&nbsp;&nbsp;</td>
		    <td colspan="5" style="border:none;  font-weight:bold; text-decoration:underline"><?php echo $firstname;?></td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">Address:</td>
		    <td colspan="5" style="border:none;  font-weight:bold; text-decoration:underline"><?php echo  $add?>&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">&nbsp;</td>
		    <td width="31%" style="border:none;  font-weight:bold">&nbsp;</td>
		    <td width="26%" align="right" style="border:none;  font-weight:bold">Phone:</td>
		    <td colspan="3" style="border:none;  font-weight:bold; text-decoration:underline">&nbsp;<?php echo $phone?></td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">&nbsp;</td>
		    <td style="border:none;  font-weight:bold">&nbsp;</td>
		    <td align="right" style="border:none;  font-weight:bold">&nbsp;</td>
		    <td colspan="3" style="border:none;  font-weight:bold; ">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold"><span style="border:none;font-size:12px; font-weight:bold">About Ship To:</span></td>
		    <td style="border:none;  font-weight:bold;font-size:12px;">&nbsp;&nbsp;<?php echo  $ship_to; ?></td>
		    <td align="right" style="border:none;  font-weight:bold">&nbsp;</td>
		    <td colspan="3" style="border:none;  font-weight:bold; ">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">Vendor Name:&nbsp;</td>
		    <td style="border:none;  font-weight:bold"><span style="border:none;  font-weight:bold; text-decoration:underline"><?php echo $companyname;?></span></td>
		    <td align="right" style="border:none;  font-weight:bold">&nbsp;</td>
		    <td colspan="3" style="border:none;  font-weight:bold; ">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">Company Name:&nbsp;&nbsp;</td>
		    <td style="border:none;  font-weight:bold"><?php echo $firstname;?></td>
		    <td align="right" style="border:none;  font-weight:bold">&nbsp;</td>
		    <td colspan="3" style="border:none;  font-weight:bold; ">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">Address:</td>
		    <td style="border:none;  font-weight:bold"><span style="border:none;  font-weight:bold; text-decoration:underline"><?php echo  $add?></span></td>
		    <td align="right" style="border:none;  font-weight:bold">&nbsp;</td>
		    <td colspan="3" style="border:none;  font-weight:bold;">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td style="border:none;  font-weight:bold">&nbsp;</td>
		    <td style="border:none;  font-weight:bold">&nbsp;</td>
		    <td align="right" style="border:none;  font-weight:bold">Phone:</td>
		    <td colspan="3" style="border:none;  font-weight:bold; text-decoration:underline"><?php echo $phone?></td>
	      </tr><?php */?>
		  <tr style="border:none;">
		    <td colspan="6" style="border:none;  font-weight:bold">&nbsp;</td>
	      </tr>
		  <tr style="border:none;">
		    <td colspan="6" style="border:none;  font-weight:bold"><table width="100%" class="simple">
		      <tr>
		        <td width="41%" align="center">Details</td>
		        <td width="22%" align="center">Quantity </td>
		        <td width="20%" align="center">Unit Price</td>
		        <td width="17%" align="center">Total</td>
	          </tr>
              <?php 
			  $sub_total = 0;
			  for($i=0;$i<count($reportresult);$i++)
		{
			$sub_total +=$reportresult[$i]['value'];
			  ?>
		      <tr>
		        <td><?php echo $reportresult[$i]['itemdescription'];?>&nbsp;</td>
		        <td align="right"><?php echo $reportresult[$i]['quantity'];?>&nbsp;</td>
		        <td align="right"><?php echo $reportresult[$i]['price'];?>&nbsp;</td>
		        <td align="right"><?php echo $reportresult[$i]['value'];?>&nbsp;</td>
	          </tr>
              <?php } ?>
		      <tr>
		        <td colspan="2">&nbsp;</td>
		        <td align="right">Total:</td>
		        <td align="right"><?php echo $sub_total;?>&nbsp;</td>
	          </tr>
		      <tr>
		        <td colspan="4" valign="top" style="font-size:12px;">Additional Notes:</td>
		       
	          </tr>
		      <tr>
		        <td colspan="3" rowspan="2" valign="top" style="text-align:justify;"><?php echo $remarks;?>&nbsp;</td>
		       
	          </tr>
		      <tr>
		        <td colspan="2" align="center"><p>&nbsp;</p>
	            <p>&nbsp;</p>
	            <p>&nbsp;</p>
	            <p>&nbsp;</p>
	            <p>&nbsp;</p>
	            <p>&nbsp;</p>
	            <p>&nbsp;</p>
	           
	            <p>&nbsp;</p></td>
	          </tr>
		      </table>
            
            </td>
	      </tr>
          
	    </table>
        </td>
  </tr>
   
   
   

	
</table>
   
	</form> 
	
</body>
</html>
