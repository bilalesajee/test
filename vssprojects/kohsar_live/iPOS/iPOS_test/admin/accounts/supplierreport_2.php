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
//include_once("connection.php");
$exl =$_REQUEST['exl'];
include("../../includes/security/adminsecurity.php");
global $AdminDAO, $Component;
$id = $_REQUEST['id'];
/*$option = strtotime('01-01-2013');

date_default_timezone_set('Asia/karachi');
$date2 = time();
$date = $option;*/
 $sdate = strtotime($_GET['sdate']); 
 $edate = strtotime($_GET['edate']);
 
 
 
  $asc=$_GET['asc'];
  $desc=$_GET['desc'];
 
   $supplier_name=$_GET['supplier_name'];


 if($sdate == $edate)
  {
   $cond = "  s.addtime  >= '$sdate' and '$edate'"; 
  }
else
  {
   $cond .= "  s.addtime  between '$sdate' and '$edate'"; 
  }

  
    if($supplier_name !='')
	{
	$cond .= " and sl.companyname LIKE '$supplier_name%'  ";
    }

  if($asc != '' )
  {
   $cond2 = " sl.pksupplierid,s.addtime asc"; 
  }
  else if($desc!='')
  {
	  $cond2 .= " sl.pksupplierid,s.addtime desc";
	  }
	  
	
   $query = "SELECT sl.pksupplierid, su.billnumber,s.fksupplierinvoiceid, FROM_UNIXTIME(s.addtime,'%d-%m-%Y') adddate, s.fksupplierid ,SUM(s.quantity * s.priceinrs) as invoice_value , sl.companyname from $dbname_detail.supplierinvoice su
	 left join $dbname_detail.stock s on su.pksupplierinvoiceid = s.fksupplierinvoiceid left join main.supplier sl on su.fksupplierid=sl.pksupplierid
	 where  $cond  and  su.invoice_status = 1 group by sl.companyname,s.fksupplierinvoiceid,su.billnumber order by $cond2   ";
	
$reportresult = $AdminDAO->queryresult($query);
$date_formate = date('d-m-Y');
 $row_run=count($reportresult);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Supplier Report</title>
<?php if(!$exl) { ?>
<link href="style.css" rel="stylesheet" type="text/css" />
<?php } ?>
</head>
<body>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
    <td align="center" style="font-size:18px; " colspan="6"></td>
  </tr>
  <tr>
    <td align="center" style="font-size:18px; " colspan="6"> Supplier Report from <?php echo $sdate22 = $_GET['sdate']; ?> to <?php echo  $edate33 = $_GET['edate'];?></td>
  </tr>
  <tr>
    <td align="center" style="font-size:18px; " colspan="6"> </td>
  </tr>
  <tr>
    <th bgcolor="#C0944B" colspan="2" style="height:30px;">Supplier</th>
    <th bgcolor="#C0943B" colspan="4">Bill</th>
    
  </tr>
 
  <tr>
    <th width="30" >Id</th>
    <th width="235" >Name</th>
    <th width="111" >#</th>
    <th width="114" >Supplier Invoice ID</th>
    <th width="104" >Date</th>
    <th width="125">Amount</th>
   
  </tr>
  <?php
 
for($i=0;$i<$row_run;$i++)
{
	
?>
  
   <?php 
   if($i==1 && $i==0){ 
   $total_value='';
   $total_value=$reportresult[$i]['invoice_value'];
   ?>
   <tr>
	<td width="30" align="left">&nbsp;<?php echo $reportresult[$i]['fksupplierid'];?></td>
    <td width="235" align="left">&nbsp;<?php echo $reportresult[$i]['companyname'];?></td>
    <td align="left">&nbsp;<?php echo $reportresult[$i]['billnumber'];?></td>
    <td width="114" align="center"><?php echo $reportresult[$i]['fksupplierinvoiceid'];?></td>
    <td width="104" align="center">&nbsp;<?php echo $reportresult[$i]['adddate'];?></td>
    <td width="125" align="right">&nbsp;<?php echo round($reportresult[$i]['invoice_value'],2);?></td>
  </tr>
       
	   <?php }else{
    
	
	if($reportresult[$i]['fksupplierid']!=$reportresult[$i-1]['fksupplierid'] ){
		
		$total_value=$reportresult[$i]['invoice_value'];
		?>
    
    <tr>
    <td width="30" align="left">&nbsp;<?php echo $reportresult[$i]['fksupplierid'];?></td>
    <td width="235" align="left">&nbsp;<?php echo $reportresult[$i]['companyname'];?></td>
    <?php }else{ ?>
	<td width="50" align="left" ></td>
    <td width="50" align="center">&nbsp;</td>
	
	<?php
	
	$total_value=$total_value+$reportresult[$i]['invoice_value'];
		}	?>
    <td align="left">&nbsp;<?php echo $reportresult[$i]['billnumber'];?></td>
    <td width="125" align="center"><?php echo $reportresult[$i]['fksupplierinvoiceid'];?></td>
    <td width="124" align="center">&nbsp;<?php echo $reportresult[$i]['adddate'];?></td>
    <td width="100" align="right">&nbsp;<?php echo round($reportresult[$i]['invoice_value'],2)?></td>
  </tr>
		<?php

if($reportresult[$i]['fksupplierid']!=$reportresult[$i+1]['fksupplierid'] ){?>
<tr><td align="right" colspan="5">&nbsp;<b> Total Value </b></td><td width="125" align="right" >&nbsp;<b><?php echo round($total_value,2)?></b></td></tr>
<?php }

}


}
?>
</table>

<?php

if($exl)
{
	header("Content-type: application/octet-stream");
	# replace excelfile.xls with whatever you want the filename to default to
	header("Content-Disposition: attachment; filename=Report.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
}



?>





