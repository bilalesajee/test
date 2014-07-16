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

$q="select SUM(p.amount) as cash     
FROM $dbname_detail.sale s left join $dbname_detail.payments p on (pksaleid=fksaleid) WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) )='$month' and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' ))='$year'  and s.status = '1' and p.paymentmethod='c' group by  FROM_UNIXTIME( s.updatetime, '%m-%Y' )   ";

$query1 = $AdminDAO->queryresult($q);

$q="select SUM(p.amount) as creditcard     
FROM $dbname_detail.sale s left join $dbname_detail.payments p on (pksaleid=fksaleid) WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) )='$month' and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' ))='$year'  and s.status = '1' and p.paymentmethod='cc' group by  FROM_UNIXTIME( s.updatetime, '%m-%Y' ) ";


$query2 = $AdminDAO->queryresult($q);


 $q1="SELECT    SUM(s.totalamount) as totalamount , FROM_UNIXTIME(s.updatetime,'%Y-%m-%d') as updatetime
FROM $dbname_detail.sale s WHERE month(FROM_UNIXTIME( s.updatetime, '%Y-%m-%d' ) )='$month' and year( FROM_UNIXTIME(s.updatetime, '%Y-%m-%d' ))='$year'  and s.status = '1' and s.fkaccountid > 0 group by  FROM_UNIXTIME( s.updatetime, '%m-%Y' )   ";


$query3 = $AdminDAO->queryresult($q1);

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
<title>Monthly Sale Report</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="500" align="center">
  <tr>
    <td colspan="2" align="center" class="topheadinggreen" style="border:none">Monthly Sale</td>
  </tr>
  <tr>
    <td colspan="2" class="s1" style="border:none">&nbsp;</td>
  </tr>
  <tr>
    <td width="14" class="s1" style="border:none">To :</td>
    <td  style="border:none; width:150px;" align="left"><select name="month1" class="accounts_combo" id="month1" style="width:67px" >
 

<option value = "01">January</option>
<option value = "02">February</option>
<option value = "03">March</option>
<option value = "04">April</option>
<option value = "05" selected>May</option>
<option value = "06">June</option>
<option value = "07">July</option>
<option value = "08">August</option>
<option value = "09">September</option>
<option value = "10">October</option>
<option value = "11">November</option>
<option value = "12">December</option>
</select> &nbsp;&nbsp;&nbsp;&nbsp;  <select name="year1" class="accounts_combo" id="year1" style="width:67px" >
     
      <option value = "2010">2010</option>
      <option value = "2011">2011</option>
      <option value = "2012">2012</option>
      <option value = "2013" selected>2013</option>
      
    </select></td>
  </tr>


<tr>
    <td width="100" class="s1" style="border:none">From :</td>
    <td  style="border:none; width:170px;" align="left"><select name="month2" class="accounts_combo" id="month2" style="width:67px" >
 

<option value = "01">January</option>
<option value = "02">February</option>
<option value = "03">March</option>
<option value = "04">April</option>
<option value = "05" selected>May</option>
<option value = "06">June</option>
<option value = "07">July</option>
<option value = "08">August</option>
<option value = "09">September</option>
<option value = "10">October</option>
<option value = "11">November</option>
<option value = "12">December</option>
</select> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;<select name="year2" class="accounts_combo" id="year2" style="width:67px" >
     
      <option value = "2010">2010</option>
      <option value = "2011">2011</option>
      <option value = "2012">2012</option>
      <option value = "2013" selected>2013</option>
      
    </select></td>
 
  <td align="left" style="border:none; padding-left:0px;"><button type="button" class="butt" onClick="return show_report();">Find</button> </td>
  </tr>
</table>
<p>&nbsp;</p>
<div id="showsale"></div>
<div id="showsalel" style="display:none; padding-left:0px;" align="center">Please Wait .........</div>
<div id="showsalemain"> 
<table width="71%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" id="result" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
    <th width="112" bgcolor="#C0943B">Month</th>
    <th width="131" bgcolor="#C0943B">Cash</th>
    <th width="143" bgcolor="#C0943B">Credit Card</th>
    <th width="136" bgcolor="#C0943B">Credit Sale</th>
    <th width="174" bgcolor="#C0943B">Total Amount </th>
  </tr>
  <?php
 $cash =	$query1[0]['cash'];
$cc =		$query2[0]['creditcard'];
$critsale =	$query3[0]['totalamount'];
$total_amount = $cash + $cc + $critsale;

		?>
  <tr>
    <td width="112" align="left"><?php echo date('F Y',strtotime($query3[0]['updatetime']));?>&nbsp;&nbsp;</td>
    <td width="131" align="center"><?php echo number_format($cash,2);?></td>
    <td align="center"><?php echo number_format($cc,2);?>&nbsp;&nbsp;&nbsp;</td>
     <td width="136" align="center"><?php echo number_format($critsale,2);?>&nbsp;&nbsp;</td>
       <td width="174" align="center"><?php echo number_format( $total_amount ,2);?>&nbsp;&nbsp;</td>
  </tr>
  <?php 
	//}
 ?>
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
	var month1		=	document.getElementById('month1').value;
	var year1		=	document.getElementById('year1').value;

	var month2		=	document.getElementById('month2').value;
	var year2		=	document.getElementById('year2').value;

    jQuery('#showsale').load('sale_new_report.php?month1='+month1+'&year1='+year1+'&month2='+month2+'&year2='+year2);
	jQuery('#showsalemain').hide();
	jQuery('#showsalel').show();
	jQuery('#showsale').bind('load', function() {
	jQuery('#showsalemain').hide();
	jQuery('#showsalel').hide();
});
	
	}
</script>