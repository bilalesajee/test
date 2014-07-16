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
//////////////////////////////////////////////////////////////////////////////////////
 $year1 = $_GET['year1'];
 $year2 = $_GET['year2'];

 $q="SELECT  round(SUM(s.`quantity`*s.`purchaseprice`),2) as cash,FROM_UNIXTIME(updatetime, '%Y' ) as yer,round(SUM(rs.`quantity`*s.`purchaseprice`),2) as cash2
FROM $dbname_detail.stock s left join $dbname_detail.returns rs on (pkstockid=fkstockid) WHERE year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' )) between $year1 and $year2  group by  FROM_UNIXTIME( s.updatetime, '%Y' )  order by s.updatetime  desc";

$query = $AdminDAO->queryresult($q);
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
    <td colspan="2" align="center" class="topheadinggreen" style="border:none">Stock Report of <?php echo $year1.' - '.$year2;?></td>
  </tr>
</table>
<table width="71%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" id="result" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
  <th width="112" bgcolor="#C0943B">Year</th>
    <th width="131" bgcolor="#C0943B">Purchased</th>
    <th width="143" bgcolor="#C0943B">Returned</th>
    <th width="136" bgcolor="#C0943B">Stock Balance</th>
  </tr>
  <?php
 
foreach ($query as $row_run)
{
$cash =	$row_run['cash'];
$cash2 =	$row_run['cash2'];
$cash3=$cash-$cash2;
$year=$row_run['yer'];
		?>
  <tr>
    <td width="112" align="left"><?php echo $year;?>&nbsp;&nbsp;</td>
    <td width="131" align="center"><?php echo number_format($cash,2);?></td>
    <td align="center"><?php echo number_format($cash2,2);?>&nbsp;&nbsp;&nbsp;</td>
     <td align="center"><?php echo number_format($cash3,2);?>&nbsp;&nbsp;&nbsp;</td>
  </tr>
  <?php 
	//}
 }?>
</table>
</body>
</html>
<script language="javascript">
	jQuery('#showstockl').hide();
</script>

