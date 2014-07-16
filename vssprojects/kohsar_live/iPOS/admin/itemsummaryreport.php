
<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$userSecurity;
$id=$_REQUEST['id'];
$empid		=	$_SESSION['addressbookid'];
$storeid	=	$_SESSION['storeid'];
//************************UNITS BOUGHT+Damages+Returns+Expired**************************************************
?>
<script type="text/javascript" language="javascript" src="../includes/js/jquery-1.4.2.js"></script>
<script language="javascript" >
$(document).ready(function() {
$("table.tablecolors tr:even").addClass("even");
$("table.tablecolors tr:odd").addClass("odd"); //This is not required - you can avoid this if you have a table background
$("table.tablecolors tr").hover(function(){
$(this).addClass("hovcolor");
}, function(){
$(this).removeClass("hovcolor");
});
$("table.tablecolors tr").click(function(){
//$("table.tablecolors tr").removeClass("highlightcolor"); // Remove this line if you dont want to de-highlight the previously highlighted row
$(this).toggleClass("highlightcolor");
});
});
</script>
<style>
body{
font-family:Arial, Helvetica, sans-serif;
}
table.tablecolors{
border-collapse:collapse;
width: 728px;
border: 1px solid #999;
}
table.tablecolors td{
padding: 8px 6px;
font-size: 12px;
border: 1px solid #FFF;
}
table.tablecolors .even{
background-color: #EFF4FB;  /*#efefef*/;
}
table.tablecolors .odd{
background-color: #FFF;
}
table.tablecolors .hovcolor{

cursor:pointer;
}
table.tablecolors .highlightcolor{
background-color: #BBD9EE/*<!--#8c2800-->*/;
color:#FFF;
}
</style>

<?php
			$query_for_employeename=$AdminDAO->getrows("addressbook","username","pkaddressbookid='$empid'");
			$query_for_storename=$AdminDAO->getrows("store","storename","pkstoreid='$storeid'");
			//print_r($query_for_employeename);
			//print_r($query_for_storename);
			$row	=	$AdminDAO->getrows("
			main.barcode mb,
			main_kohsar.stock mks,
			main_kohsar.damages mkd,
			main_kohsar.returns mkr"," 
			mb.barcode as barcode,
			mb.itemdescription as itemdescription,
			mks.pkstockid,
			sum( mks.quantity ) AS units_bought,
			sum(mkd.quantity) AS units_damages,
			sum( mkr.quantity ) AS units_returned,
			sum( mks.unitsremaining ) AS units_expired,
			FROM_UNIXTIME( mks.expiry, '%d-%m-%Y' ) AS expiredate",
			"mkd.fkstockid = mks.pkstockid
			AND mkr.fkstockid = mks.pkstockid
			AND mkd.damagestatus <> 'p'
			AND mkr.returnstatus <> 'p'
			AND FROM_UNIXTIME( mks.expiry, '%d-%m-%Y' ) < NOW( )
			AND mb.pkbarcodeid = '18'
			GROUP BY mb.barcode
			Limit 0,30");
//print_r($row);
//echo "<br>";
$unit_sold_query=$AdminDAO->getrows("
									main_kohsar.saledetail mksd,
									main_kohsar.stock mks, 
									main.barcode mb",
									"mks.pkstockid,
									mb.barcode,
									mb.itemdescription, 
									sum( mksd.quantity ) AS quantity_sold",
									"mb.pkbarcodeid = mks.fkbarcodeid
									AND mks.pkstockid = mksd.fkstockid
									AND fkbarcodeid = '18'
									GROUP BY fkbarcodeid
									LIMIT 0 , 30");
//echo "<br>";
//print_r($unit_sold_query);
$units_moved_query=$AdminDAO->getrows("
									  main_kohsar.stock mk, 
									  main.barcode mb, 
									  main.consignmentdetail mc",
									  "mk.pkstockid,mb.barcode,sum(mc.quantity) as units_moved",
									  "mb.pkbarcodeid = mk.fkbarcodeid
									  AND mk.pkstockid = mc.fkstockid
									  AND mk.fkbarcodeid = '18'
									  GROUP BY mk.fkbarcodeid
									  LIMIT 0 , 30");
$total_used=$unit_sold_query[0]['quantity_sold']+$row[0]['units_returned']+$row[0]['units_damages']+$row[0]['units_expired']+$units_moved_query[0]['units_moved'];
$remaining_items=$row[0]['units_bought']-$total_used;
?>
<div style="width:6.0in;padding:0px;font-size:17px; " align="center">
	<img src="../images/esajeelogo.jpg" width="150" height="50">
<br />
<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
	<b>Think globally shop locally</b>
<table width="198" class="tablecolors">
<tr>
    <td width="82" height="22" nowrap="nowrap"><b>Store Name:</b></td>
    
    <td width="100" nowrap="nowrap"><b><?php echo $query_for_storename[0]['storename'];?></b></td>
  </tr>
   <tr>
    <td width="82" height="22" nowrap="nowrap"><b>Report By:</b></td>
    
    <td width="100" nowrap="nowrap"><b><?php echo $query_for_employeename[0]['username'];?></b></td>
  </tr>
  <tr>
    <td width="82" height="22" nowrap="nowrap"><b>Dated:</b></td>
    
    <td width="100" nowrap="nowrap"><b><?php echo date("d-m-Y h:j:s",time())?></b></td>
  </tr>
  <tr>
    <td width="82" height="22" nowrap="nowrap"><b>Item Name:</b></td>
    
    <td width="100" nowrap="nowrap"><b><?php echo $row[0]['itemdescription'];?></b></td>
  </tr>
  
  <tr>
    <td width="82" height="22" nowrap="nowrap"><b>Barcode:</b></td>
    
    <td width="100" nowrap="nowrap"><b><?php echo $row[0]['barcode'];?></b></td>
  </tr>
 <tr>
   <td height="22" nowrap="nowrap"><b>Units Bought:</td>
   <td nowrap="nowrap"><b><?php echo $row[0]['units_bought'];?></b></td>
 </tr>
 <tr>
   <td height="22" nowrap="nowrap"><b>Units Sold:</b></td>
   <td nowrap="nowrap"><b><?php echo $unit_sold_query[0]['quantity_sold'];?></b></td>
 </tr>
 <tr>
   <td height="22" nowrap="nowrap"><b>Returned</b></td>
   <td nowrap="nowrap"><b><?php echo $row[0]['units_returned'];?></b></td>
 </tr>
 <tr>
   <td height="22" nowrap="nowrap"><b>Damages</b></td>
   <td nowrap="nowrap"><b><?php echo $row[0]['units_damages'];?></b></td>
 </tr>
 <tr>
   <td height="22" nowrap="nowrap"><b>Expired:</b></td>
   <td nowrap="nowrap"><b><?php echo $row[0]['units_expired'];?></b></td>
 </tr>
 <tr>
   <td height="22" nowrap="nowrap"><b>Moved:</b></td>
   <td nowrap="nowrap"><b><?php echo $units_moved_query[0]['units_moved'];?></b></td>
 </tr>
  <tr>
   <td height="22" nowrap="nowrap"><b>Remaining Items:</b></td>
   <td nowrap="nowrap" style="color:#710000"><b><?php echo $remaining_items;?></b></td>
 </tr>
</table>

