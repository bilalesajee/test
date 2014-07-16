<?php ob_start();
	  session_start();
      error_reporting(-1);
     $_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);

   set_time_limit(0);
   include("../../includes/security/adminsecurity.php");
   
   global $AdminDAO;
   $date_formate = date('m/d/Y');
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
AND b.pkbarcodeid = s.fkbarcodeid and sd.quantity < 10000
GROUP BY s.fkbarcodeid
ORDER BY SUM( sd.quantity ) DESC LIMIT 0,100 ";
 $result = $AdminDAO->queryresult($q);
$row_cnt = count($result);


///////////////////////////////////////////////////////////////////////////////////
$query_lowest = "SELECT b.barcode, sd.fkstockid,b.itemdescription as desc2,FROM_UNIXTIME(sd.`timestamp`,'%d-%m-%Y') as dattime,sum(sd.quantity) as qty1 ,sd.saleprice as saleprice ,s.purchaseprice as purchaseprice ,s.costprice as costprice,s.priceinrs as priceinrs FROM $dbname_detail.`saledetail` sd , $dbname_detail.stock s ,
$dbname_main.barcode b
WHERE sd.timestamp
BETWEEN  '$newdate'
AND  '$newcuu333'  and pkstockid=fkstockid and b.pkbarcodeid=s.fkbarcodeid  group by s.fkbarcodeid
ORDER BY sum(sd.quantity) ASC limit 0,100";


 $result_lowest = $AdminDAO->queryresult($query_lowest);
$row_cnt_lowest = count($result_lowest);
////////////////////////////////////////////////////////////////////////////////////

$query_expiry = "SELECT su.companyname,sh.shipmentname ,FROM_UNIXTIME(st.expiry,'%d-%m-%Y') expiry,b.itemdescription ,st.quantity ,st.priceinrs,st.purchaseprice as purchaseprice ,st.costprice as costprice,st.retailprice
from $dbname_detail.stock st
left join $dbname_main.barcode b on b.pkbarcodeid=st.fkbarcodeid
left join $dbname_main.shipment sh on sh.pkshipmentid = st.fkshipmentid
LEFT JOIN $dbname_main.supplier su on st.fksupplierid= su.pksupplierid
where st.expiry BETWEEN  ' $newcuu444'
AND  '$newdate2' 
 ORDER BY expiry asc limit 0,100 ";
 


 $result_expiry = $AdminDAO->queryresult($query_expiry);
$row_cnt_expiry = count($result_expiry);


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
color:#FFFFFF;
}

.table
{
border-collapse:collapse;
}
.table, td, th
{
border:1px solid #000000;
}
.topheadinggreen {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size: 28px;
font-weight: normal;
color:#7e8901;
}


.headerfont{ color: #FFFFFF;
font-family: Verdana,Arial,Helvetica,sans-serif;
font-size: 24px;
font-weight: bold;

}



.border {
border-top-width: 1px;
border-right-width: 1px;
border-bottom-width: 1px;
border-left-width: 1px;
border-top-style: double;
border-right-style: double;
border-bottom-style: double;
border-left-style: double;
border-top-color:#000000;
border-right-color:#000000;
border-bottom-color: #000000;
border-left-color:#000000;

}

.s1
{
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
color:#FFFFFF;
text-decoration:none;

}

.img {
padding:1px;
border:3px solid #021a40;
background-color:#ff0;
}







</style>

</head>
<body>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
<tr>
<th colspan="6" height="100px" align="center" style=" color: #FFFFFF;
font-family: Verdana,Arial,Helvetica,sans-serif;
font-size: 24px;
font-weight: bold;" bgcolor="#000000" >Weekly Report</th>
<tr>
<th colspan="6" height="50px" align="left" bgcolor="#000000" style="color:#FFFFFF"  >Heighest Selling Item Of Last Week From Date  '.$new_newdate.' To Date '.$newcuu.'</th>
</tr>
<tr>
<th width="286" bgcolor="#000000" style="color:#FFFFFF">Item</th>
<th width="139" bgcolor="#000000" style="color:#FFFFFF">Quantity</th>
<th width="100" bgcolor="#000000" style="color:#FFFFFF">Trade Price</th>
<th width="134" bgcolor="#000000" style="color:#FFFFFF">Retail Price</th>

</tr>';


for($i=0;$i<$row_cnt;$i++)

{


$table.='<tr>
<td align="left">'. $result[$i]['itemdescription'].'</td>
<td width="139" align="right">'. $result[$i]['quantity'].'</td>
<td width="100" align="right">'.round($result[$i]['costprice'],2).'</td>
<td width="134" align="right">'.round($result[$i]['saleprice'],2).'</td>

</tr>';

}

$table .='</table><p>&nbsp;</p>';

$table .='<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
<tr>
<th colspan="6" align="left" height="50px"  bgcolor="#000000" style="color:#FFFFFF">Lowest Selling Items Of Last Week From Date  '.$new_newdate.' To Date '.$newcuu.'</th>
</tr>
<tr>
<th width="285" bgcolor="#000000" style="color:#FFFFFF">Item</th>
<th width="146" bgcolor="#000000" style="color:#FFFFFF">Quantity</th>
<th width="145" bgcolor="#000000"style="color:#FFFFFF">Trade Price</th>
<th width="130" bgcolor="#000000" style="color:#FFFFFF">Retail Price</th>
</tr>';



for($i=0;$i<$row_cnt_lowest;$i++){

$table.='<tr>
<td align="left"> '. $result_lowest[$i]['desc2'].'</td>
<td width="146" align="right">'. $result_lowest[$i] ['qty1'].'</td>
<td width="101" align="right">'. round($result_lowest[$i]['costprice'],2).'</td>
<td width="130" align="right">'. round($result_lowest[$i]['saleprice'],2).'</td>
</tr>';


}
$table.='</table><p>&nbsp;</p>';

$table.='<table width="80%"  align="center" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
<tr>
<th colspan="8" align="left" height="50px"  bgcolor="#000000" style="color:#FFFFFF">Items Expired In Coming Week From Date  '.$newcuu2.' To Date '.$new_newdate2.'</th>
</tr>
<tr>
<th width="200" bgcolor="#000000" style="color:#FFFFFF">Item</th>
<th width="123" bgcolor="#000000" style="color:#FFFFFF">Shipment Name</th>
<th width="98" bgcolor="#000000" style="color:#FFFFFF">Supplier Name</th>
<th width="81" bgcolor="#000000" style="color:#FFFFFF">Quantity</th>
<th width="110" bgcolor="#000000" style="color:#FFFFFF">Trade Price</th>
<th width="92" bgcolor="#000000" style="color:#FFFFFF">Retail Price</th>
<th width="113" bgcolor="#000000" style="color:#FFFFFF">Expiry Date</th>
</tr>';


	for($i=0;$i<$row_cnt_expiry;$i++)

{
$table.='<tr>
<td align="left">'. $result_expiry[$i]['itemdescription'].'&nbsp;</td>
<td align="left">'.$result_expiry[$i]['shipmentname'].'&nbsp;</td>
<td align="left">'. $result_expiry[$i]['companyname'].'&nbsp;</td>
<td align="right">'. $result_expiry[$i]['quantity'].'&nbsp;</td>
<td align="right">'. round($result_expiry[$i]['costprice'],2).'&nbsp;</td>
<td align="right">'. round($result_expiry[$i] ['retailprice'],2).'&nbsp;</td>
<td align="center">'.$result_expiry[$i] ['expiry'].'&nbsp;</td>

</tr>';

}

$table.='</table>';
$body = $table;
$body	.=" <div align='center'>".date('Y-m-d h:i:s')."</div>";

//empty closing session variable for new session


$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

$to = "accounts@esajee.com,abdul.rahim@esajee.com";
$subject = "Esajee Kohsar Items Sale Report Last Week From Date $new_newdate To Date $newcuu";
$from = "Kohsar SHOP System <kohsar@esajee.com>";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
if($msent){
echo 1;
}else{
echo 0;	
	}
file_get_contents("https://pharmadha.esajee.com/admin/accounts/weekly_report.php");
file_get_contents("https://gulberg.esajee.com/admin/accounts/weekly_report.php");
file_get_contents("https://dha.esajee.com/admin/accounts/weekly_report.php");
file_get_contents("https://main.esajee.com/admin/cron_ship_report.php");
?>
