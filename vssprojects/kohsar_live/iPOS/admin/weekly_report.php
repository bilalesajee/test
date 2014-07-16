<?php ob_start();
   error_reporting(0);
   set_time_limit(0);
  include '../Mail/email.php'; 
  include("config_autoget.php");
  $dbname_main = 'main'; 
  $dbname_detail = 'main_kohsar';
  $dbh = new mysqli($server, $server_user, $server_pwd , $dbname_detail);

 
  
  // require_once('PHPMailer/class.phpmailer.php');
   $currentdate =date("d-m-Y");
   $newcuu= date("d-m-Y",strtotime(date("d-m-Y", strtotime($currentdate)) . " -1 day"));
   $new_newdate = date("d-m-Y",strtotime(date("d-m-Y", strtotime($newcuu)) . " -6 day"));


   $currentdate2 =date("d-m-Y");
   $newcuu2= date("d-m-Y",strtotime(date("d-m-Y", strtotime($currentdate2)) . " -1 day"));
   $new_newdate2 = date("d-m-Y",strtotime(date("d-m-Y", strtotime($newcuu2)) . " +6 day"));

//////////////////////////////////////////////////////////////////////////////////////////////////
 $date =date("d-m-Y");
 $newcuu333= strtotime(date("d-m-Y", strtotime($date) . " -1 day"));
 $newdate = strtotime(date("d-m-Y",strtotime(date("d-m-Y", $newcuu333) . " -6 day")));


 $date2 =date("d-m-Y");
 $newcuu444= strtotime(date("d-m-Y", strtotime($date2) . " -1 day"));
 $newdate2 = strtotime(date("d-m-Y",strtotime(date("d-m-Y", $newcuu444) . " +6 day")));
//////////////////////////////////////////////////////////////////////////////////////////////////

 $q= "SELECT b.barcode,sd.fkstockid,b.itemdescription, FROM_UNIXTIME( sd.`timestamp` ,  '%d-%m-%Y' ) AS dattime, SUM( sd.quantity ) AS quantity, sd.saleprice AS saleprice, s.purchaseprice AS purchaseprice, s.costprice AS costprice, s.priceinrs AS priceinrs
FROM $dbname_detail.`saledetail` sd, $dbname_detail.stock s, $dbname_main.barcode b
WHERE sd.timestamp
BETWEEN  '$newdate'
AND  '$newcuu333' 
AND pkstockid = fkstockid  
AND b.pkbarcodeid = s.fkbarcodeid
GROUP BY s.fkbarcodeid
ORDER BY SUM( sd.quantity ) DESC LIMIT 0,10 ";
 $result = $dbh->query($q);
$row_cnt = $result->num_rows;
///////////////////////////////////////////////////////////////////////////////////
$query_lowest = "SELECT b.barcode, sd.fkstockid,b.itemdescription as desc2,FROM_UNIXTIME(sd.`timestamp`,'%d-%m-%Y') as dattime,sum(sd.quantity) as qty1 ,sd.saleprice as saleprice ,s.purchaseprice as purchaseprice ,s.costprice as costprice,s.priceinrs as priceinrs FROM $dbname_detail.`saledetail` sd , $dbname_detail.stock s ,
$dbname_main.barcode b
WHERE sd.timestamp
BETWEEN  '$newdate'
AND  '$newcuu333'  and pkstockid=fkstockid and b.pkbarcodeid=s.fkbarcodeid  group by s.fkbarcodeid
ORDER BY sum(sd.quantity) ASC limit 0,10
";
$result_lowest = $dbh->query($query_lowest);
$row_cnt_lowest = $result_lowest->num_rows;
////////////////////////////////////////////////////////////////////////////////////

$query_expiry = "SELECT su.companyname,sh.shipmentname ,FROM_UNIXTIME(st.expiry,'%d-%m-%Y') expiry,b.itemdescription ,st.quantity ,st.priceinrs,st.purchaseprice as purchaseprice ,st.costprice as costprice
from $dbname_detail.stock st
left join $dbname_main.barcode b on b.pkbarcodeid=st.fkbarcodeid
left join $dbname_main.shipment sh on sh.pkshipmentid = st.fkshipmentid
LEFT JOIN $dbname_main.supplier su on st.fksupplierid= su.pksupplierid
where st.expiry BETWEEN  ' $newcuu444'
AND  '$newdate2' 
 ORDER BY expiry asc limit 0,55 ";
 
 $result_expiry = $dbh->query($query_expiry);
$row_cnt_expiry = $result_expiry->num_rows;


$table ='';
$table ='
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<style>
.rightheading
{
width:180px;
float: left;
margin:0px;
padding:0px;
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
font-weight:bold;
color:#7e8901;
}

.table
{
border-collapse:collapse;
}
.table, td, th
{
border:1px solid #C0943B;
}
.topheadinggreen {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size: 28px;
font-weight: normal;
color:#7e8901;
}
.s1{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#000000; font-weight:bold;}
.s2{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#790000; font-weight:bold;}
.s3{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#FFFFFF; background-repeat:no-repeat;}
.s4{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#858484;}
.s5{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#000000; font-weight:bold;}
.s6{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:#ffffff; font-weight:bold;}
.s7{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#5E709C; font-weight:bold;}
.banner{ font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#790000; background-repeat:no-repeat;}

.headerfont{ color: #000000;
font-family: Verdana,Arial,Helvetica,sans-serif;
font-size: 26px;
font-weight: bold;
padding-left: 135px;.
}

.menu a:link {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#B64D13; text-decoration:none;}
.menu a:visited {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#B64D13; text-decoration:none;}
.menu a:hover {font-family:Verdana, Arial, Helvetica, sans-serif font-size:11px; color:#000000; text-decoration:underline;}

.topmenu a:link {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#790000; text-decoration:none; font-weight:bold;}
.topmenu a:visited {font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; color:#790000; text-decoration:none; font-weight:bold;}
.topmenu a:hover {font-family:Verdana, Arial, Helvetica, sans-serif font-size:11px; color:#000000; font-weight:bold;}

.border {
border-top-width: 1px;
border-right-width: 1px;
border-bottom-width: 1px;
border-left-width: 1px;
border-top-style: double;
border-right-style: double;
border-bottom-style: double;
border-left-style: double;
border-top-color:#C0943B;
border-right-color:#C0943B;
border-bottom-color: #C0943B;
border-left-color:#C0943B;

}

.s1
{
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
color:#000000;
text-decoration:none;

}

.img {
padding:1px;
border:3px solid #021a40;
background-color:#ff0;
}


.accounts_combo{ border-style:solid; border-color:#C0943B; background-color:#FFFFCC; border-width:thin; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333333; height:18px; width:200px;}
.form_field_heading{ font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333333;}
.accounts_smalltxtfld {border:solid; border-color:#CCCC66; background-color:#FFFFCC; border-width:thin; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333333; height:18px; width:100px;}
.accounts_txtField{ border:solid; border-color:#C0943B; background-color:#F4E7BB; border-width:thin; font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#333333; height:auto; width:200px;}
.butt{border-color:#B64D13; background-color:#C0943B; font-weight:bold; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; border-width:1px; border-style:solid;}
.sersch_table_head{font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#000000; font-weight:bold;}




</style>

</head>
<body>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
<tr>
<th colspan="6" align="left" bgcolor="#FFFFFF">Heighest Selling Item Of Last Week From Date  '.$new_newdate.' To Date '.$newcuu.'</th>
</tr>
<tr>
<th width="286" bgcolor="#C0943B">Item</th>
<th width="139" bgcolor="#C0943B">Quantity</th>
<th width="134" bgcolor="#C0943B">Sale Price</th>
<th width="148" bgcolor="#C0943B">Purchase Price</th>
<th width="100" bgcolor="#C0943B">Cost Price</th>
<th width="120" bgcolor="#C0943B">Price In RS</th>

</tr>';


while($row = $result->fetch_assoc())
{


$table.='<tr>
<td align="left">'. $row ['itemdescription'].'</td>
<td width="139" align="center">'. $row ['quantity'].'</td>
<td width="134" align="right">'. $row ['saleprice'].'</td>
<td width="148" align="right">'. $row ['purchaseprice'].'</td>
<td width="100" align="right">'.$row ['costprice'].'</td>
<td width="120" align="right">'.$row ['priceinrs'].'</td>

</tr>';

}

$table .='</table><p>&nbsp;</p>';






$table .='<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
<tr>
<th colspan="6" align="left" bgcolor="#FFFFFF">Lowest Selling Items Of Last Week From Date  '.$new_newdate.' To Date '.$newcuu.'</th>
</tr>
<tr>
<th width="285" bgcolor="#C0943B">Item</th>
<th width="146" bgcolor="#C0943B">Quantity</th>
<th width="130" bgcolor="#C0943B">Sale Price</th>
<th width="145" bgcolor="#C0943B">Purchase Price</th>
<th width="101" bgcolor="#C0943B">Cost Price</th>
<th width="120" bgcolor="#C0943B">Price In RS</th>
</tr>';


while($row4 = $result_lowest->fetch_assoc())
{


$table.='<tr>
<td align="left"> '. $row4 ['desc2'].'</td>
<td width="146" align="center">'. $row4 ['qty1'].'</td>
<td width="130" align="right">'. $row4 ['saleprice'].'</td>
<td width="145" align="right">'. $row4 ['purchaseprice'].'</td>
<td width="101" align="right">'. $row4 ['costprice'].'</td>
<td width="120" align="right">'.$row4 ['priceinrs'].'</td>
</tr>';


}
$table.='</table><p>&nbsp;</p>';

$table.='<table width="80%" align="center" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
<tr>
<th colspan="8" align="left" bgcolor="#FFFFFF">Items Expired In Coming Week From Date  '.$newcuu2.' To Date '.$new_newdate2.'</th>
</tr>
<tr>
<th width="200" bgcolor="#C0943B">Item</th>
<th width="123" bgcolor="#C0943B">Shipment Name</th>
<th width="98" bgcolor="#C0943B">Supplier Name</th>
<th width="81" bgcolor="#C0943B">Quantity</th>
<th width="92" bgcolor="#C0943B">Price In RS</th>
<th width="110" bgcolor="#C0943B">Purchase Price</th>
<th width="110" bgcolor="#C0943B">Cost Price</th>
<th width="113" bgcolor="#C0943B">Expiry Date</th>
</tr>';


while($row3 = $result_expiry->fetch_assoc())
{
$table.='<tr>
<td align="left">'. $row3 ['itemdescription'].'&nbsp;</td>
<td align="left">'. $row3 ['shipmentname'].'&nbsp;</td>
<td align="left">'. $row3 ['companyname'].'&nbsp;</td>
<td align="center">'. $row3 ['quantity'].'&nbsp;</td>

<td align="right">'. $row3 ['priceinrs'].' &nbsp;</td>
<td align="right">'. $row3 ['purchaseprice'].'&nbsp;</td>
<td align="right">'. $row3 ['costprice'].'&nbsp;</td>
<td align="center">'. $row3 ['expiry'].'&nbsp;</td>

</tr>';

}

$table.='</table>';
$emailBody = $table;
//$To[] = 'saqibzahir39@gmail.com';
$To[] = 'fahadbuttqau@gmail.com';
$Subject='Esajee Kohsar Items Sale Report Last Week From Date $new_newdate To Date $newcuu';
email($To, $Subject, $emailBody);
echo $body;

?>

<p>&nbsp;</p>
<p></p>
</body>
</html>


