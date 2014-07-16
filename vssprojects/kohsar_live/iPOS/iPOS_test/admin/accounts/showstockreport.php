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

 $month =date('m');
 $year = date('Y');

$q="SELECT  round(SUM(s.`quantity`*s.`purchaseprice`),2) as cash,FROM_UNIXTIME(updatetime, '%Y' ),round(SUM(rs.`quantity`*s.`purchaseprice`),2) as cash2
FROM $dbname_detail.stock s left join $dbname_detail.returns rs on (pkstockid=fkstockid) WHERE year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' ))= $year ";


$query = $AdminDAO->queryresult($q);

$q2="SELECT  round(SUM(s.`quantity`*s.`purchaseprice`),2) as totalvalue FROM $dbname_detail.stock s  WHERE 1=1 ";
$query2 = $AdminDAO->queryresult($q2);



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
<title>Yearly Stock Report</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<table width="500" align="center">
  <tr>
    <td colspan="2" align="center" class="topheadinggreen" style="border:none">Yearly Stock Report</td>
  </tr>
  <tr>
    <td colspan="2" class="s1" style="border:none">&nbsp;</td>
  </tr>
  <tr>
    <td width="14" class="s1" style="border:none">To :</td>
    <td  style="border:none; width:150px;" align="left"><select name="year1" class="accounts_combo" id="year1" style="width:67px" >
     
      <option value = "2010">2010</option>
      <option value = "2011">2011</option>
      <option value = "2012">2012</option>
      <option value = "2013" selected>2013</option>
      
    </select></td>
  </tr>


<tr>
    <td width="100" class="s1" style="border:none">From :</td>
    <td  style="border:none; width:150px;" align="left"><select name="year2" class="accounts_combo" id="year2" style="width:67px" >
     
      <option value = "2010">2010</option>
      <option value = "2011">2011</option>
      <option value = "2012">2012</option>
      <option value = "2013" selected>2013</option>
      
    </select></td>
   <td  align="left" style="border:none; padding-left:0px;"><button type="button" class="butt" onClick="return show_report();">Find</button> <div  id="maindiv"></div></td>
  </tr>
</table>
<p> </p>
<div id="showstock"></div>
<div id="showstockl" style="display:none; padding-left:0px;" align="center">Please Wait .........</div>
<div id="showstockmain"> 
<table width="71%" border="0" align="center" cellpadding="0" cellspacing="0"  style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">

<tr>
    <td  class="s1" style="border:none;width: 152px;">Total value of Stock :</td>
       <td  align="left" style="border:none; padding-left:0px;"><?php echo $query2[0]['totalvalue'];?></td>
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
//////////////discount////////////////////
////////////////////////////////////////
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
</table></div>
<br />
<br />
</body>
</html>
<script type="text/javascript" src="../../includes/js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.js"></script><script language="javascript">
jQuery().ready(function() 
{
	
});
function show_report()
{
	var year1		=	document.getElementById('year1').value;
	var year2		=	document.getElementById('year2').value;
    jQuery('#showstock').load('stock_report.php?year1='+year1+'&year2='+year2);
	jQuery('#showstockmain').hide();
	jQuery('#showstockl').show();
	jQuery('#showstock').bind('load', function() {
	jQuery('#showstockmain').hide();
	jQuery('#showstockl').hide();
});
	
	}


</script>


