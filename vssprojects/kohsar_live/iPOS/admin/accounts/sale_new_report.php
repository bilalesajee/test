<?php

session_start();
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
	
ob_start();
error_reporting(0);
include("../../includes/security/adminsecurity.php");
global $AdminDAO, $Component;
$id = $_REQUEST['id'];
//$dbname_detail="live_kohsar";
//$sdate = strtotime($_GET['sdate'].'00:00:00'); 
//$edate = strtotime($_GET['edate']."23:59:59");
//////////////////////////////////////////////////////////////////////////////////////

 $month1 =$_GET['month1'];
 $year1 = $_GET['year1'];
$month2 =$_GET['month2'];
 $year2 = $_GET['year2'];

$q0="select SUM(sd.quantity*sd.saleprice) as tsale ,SUM(s.amountdiscount+s.globaldiscount) as dis   ,  SUM(s.cheque) as chq  ,  SUM(sd.taxamount) as txa
FROM $dbname_detail.sale s left join $dbname_detail.saledetail sd on (s.pksaleid=sd.fksaleid) WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) ) between $month1 and $month2 and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' )) between $year1 and $year2  and s.status = '1'      ";

$query10 = $AdminDAO->queryresult($q0);


 $q="select SUM(p.amount) as cash     
FROM $dbname_detail.sale s left join $dbname_detail.payments p on (pksaleid=fksaleid) WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) ) between $month1 and $month2 and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' )) between $year1 and $year2  and s.status = '1' and p.paymentmethod='c'    ";

$query1 = $AdminDAO->queryresult($q);

$q="select SUM(p.amount) as creditcard     
FROM $dbname_detail.sale s left join $dbname_detail.payments p on (pksaleid=fksaleid) WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) ) between $month1 and $month2 and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' )) between $year1 and $year2  and s.status = '1' and p.paymentmethod='cc'  ";


$query2 = $AdminDAO->queryresult($q);
$q4="select SUM(p.amount) as fc     
FROM $dbname_detail.sale s left join $dbname_detail.payments p on (pksaleid=fksaleid) WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) ) between $month1 and $month2 and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' )) between $year1 and $year2  and s.status = '1' and p.paymentmethod='fc' ";


$query4 = $AdminDAO->queryresult($q4);


 $q1="SELECT    SUM(s.totalamount) as totalamount , FROM_UNIXTIME(s.updatetime,'%Y-%m-%d') as updatetime
FROM $dbname_detail.sale s WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) ) between $month1 and $month2 and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' )) between $year1 and $year2  and s.status = '1' and s.fkaccountid > 0    ";


$query3 = $AdminDAO->queryresult($q1);
 
	
 $count = count($query3);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<style type="text/css">
       
 .pg-normal {
                color: black;
                font-weight: normal;
                text-decoration: none;    
                cursor: pointer;    
            }
            .pg-selected {
                color: black;
                font-weight: bold;        
                text-decoration: underline;
                cursor: pointer;
            }
</style> 
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="500" align="center">
  <tr>
    <td colspan="2" align="center" class="topheadinggreen" style="border:none">Sale Report of <?php echo $month1.' - '.$year1;?> to <?php echo $month2.' - '.$year2;?> </td>
  </tr>
</table>
<table width="71%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" id="result" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
     <th width="131" bgcolor="#C0943B" colspan="4"><b>Sale</b></th>
     <th width="131" bgcolor="#C0943B" colspan="7"><b>Received</b></th>
  </tr>
  <tr>
    <th width="112" bgcolor="#C0943B" >Month</th>
     <th width="131" bgcolor="#C0943B">Sale</th>
     <th width="131" bgcolor="#C0943B">Discount</th>
  
     <th width="131" bgcolor="#C0943B">Total Sale</th>
     <th width="131" bgcolor="#C0943B"><b>|</b></th>
    <th width="131" bgcolor="#C0943B">Cash</th>
    <th width="143" bgcolor="#C0943B">Credit Card</th>
     <th width="143" bgcolor="#C0943B">FC</th>
     <th width="143" bgcolor="#C0943B">Cheque</th>
    <th width="136" bgcolor="#C0943B">Credit Sale</th>
    <th width="174" bgcolor="#C0943B">Total Amount </th>
  </tr>
  <?php
 $i=0;
while ($i < $count)
{
$cash =	$query1[$i]['cash'];
$cc =		$query2[$i]['creditcard'];
$critsale =	$query3[$i]['totalamount'];
  $Tsale =	$query10[$i]['tsale'];
   $Tcq =	$query10[$i]['chq'];
   $Tx =	$query10[$i]['txa'];
   $Tdis =	$query10[$i]['dis'];
   $fc =		$query4[$i]['fc'];
$total_amount = $cash + $cc + $critsale+$fc + $Tcq;
$total_sale = $Tsale - $Tdis;
		?>
  <tr>
    <td width="112" align="left"><?php echo date('F Y',strtotime($query3[0]['updatetime']));?>&nbsp;&nbsp;</td>
    <td width="131" align="center"><?php echo number_format($Tsale,2);?></td>
     <td width="131" align="center"><?php echo number_format($Tdis,2);?></td>
     
     <td width="131" align="center"><?php echo number_format($total_sale,2);?></td>
      <td width="131" align="center">&nbsp;</td>
    <td width="131" align="center"><?php echo number_format($cash,2);?></td>
    <td align="center"><?php echo number_format($cc,2);?>&nbsp;&nbsp;&nbsp;</td>
    <td width="136" align="center"><?php echo number_format($fc,2);?>&nbsp;&nbsp;</td>
     <td width="136" align="center"><?php echo number_format($Tcq,2);?>&nbsp;&nbsp;</td>
     <td width="136" align="center"><?php echo number_format($critsale,2);?>&nbsp;&nbsp;</td>
        
       <td width="174" align="center"><?php echo number_format( $total_amount ,2);?>&nbsp;&nbsp;</td>
  </tr>
  <?php 
	$i++;
 }?>
</table>
</body>
</html>
<script language="javascript">
	jQuery('#showsalel').hide();
</script>

