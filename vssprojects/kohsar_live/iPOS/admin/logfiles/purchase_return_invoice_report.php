<?php 
include_once("../includes/security/adminsecurity.php");
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	include_once("dbgrid.php");
}//end edit
global $AdminDAO;
//include("../../includes/security/adminsecurity.php");
//global $AdminDAO, $Component;
$id = $_REQUEST['id'];
/*$option = strtotime('01-01-2013');

date_default_timezone_set('Asia/karachi');
$date2 = time();
$date = $option;*/
 $sdate = strtotime($_GET['sdate']); 
 $edate = strtotime($_GET['edate']);

   $supplier_name=$_GET['supplier_name'];


 if($sdate != '' && $edate!='')
  {
   $cond = "  p.addtime  between '$sdate' and '$edate'"; 
  }
  
 if($supplier_name !='')
	{
	$cond .= " and sl.pksupplierid ='$supplier_name'  ";
    }


	  
	
$query = "SELECT p.pkpurchasereturnid, p.fksupplierid,FROM_UNIXTIME(p.addtime,'%d-%m-%Y') adddate ,SUM(d.value) as value ,sl.pksupplierid, sl.companyname from $dbname_detail.purchase_return p
	 left join $dbname_detail.purchase_return_detail d on d.fkpurchasereturnid = p.pkpurchasereturnid 
	 left join $dbname_main.supplier sl on p.fksupplierid=sl.pksupplierid
	 where  $cond  group by sl.pksupplierid,d.fkpurchasereturnid    ";
	
$reportresult = $AdminDAO->queryresult($query);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Purchase Return Without Invoice Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b>
</div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div><br />
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br />
<br />
Purchase Return Without Invoice Report</div>
<br />
<table style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
  <tr>
    <td colspan="2">Date: <?php echo date('d-m-Y',time());?></td>
  </tr>
  <tr>
    <td>From: <?php echo $_GET['sdate'];?></td>
    <td>To: <?php echo $_GET['edate'];;?></td>
  </tr>
</table>
<table class="simple">
  <tr>
    <th>SupplierID</th>
    <th>Company Name</th>
    <th>Date</th>
    <th>Amount</th>
  </tr>
  <?php
	
		for($i=0;$i<count($reportresult);$i++)
		{
		
			?>
  <tr>
    <td><?php echo $reportresult[$i]['fksupplierid'];?>&nbsp;</td>
    
    <td><?php echo $reportresult[$i]['companyname'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['adddate'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['value'];?>&nbsp;</td>
  </tr>
  <?php 
		
		}
	
	?>
</table>
<p>&nbsp;</p>
