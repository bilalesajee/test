<?php 
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
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

   $bcid=$_GET['bc'];
   $ckall=$_GET['ckb'];


 if($sdate != '' && $edate!='')
  {
   $cond = "  datetime  between '$sdate' and '$edate'"; 
  }
  
 if($bcid !='' and $ckall=='false')
	{
	$cond .= " and barcode ='$bcid'  ";
    }

	  
	
$query = 
"SELECT b.itemdescription,b.barcode,quantity,orgquantity,if(type=0,'Addition','Subtraction') type from $dbname_detail.stock_adjustment_detail  left join $dbname_main.barcode b on (fkbarcodeid=pkbarcodeid)
	 where  $cond     ";
	
$reportresult = $AdminDAO->queryresult($query);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Stock Adjustment Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b>
</div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div><br />
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br />
<br />
Stock Adjustment Report</div>
<br />
<table style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
  <tr>
    <td colspan="2">Date: <?php echo date('d-m-Y',time());?></td>
  </tr>
  <tr>
    <td>From: <?php echo $_GET['sdate'];?></td>
    <td>To: <?php echo $_GET['edate'];?></td>
  </tr>
</table>
<table class="simple">
  <tr>
    <th>Barcode</th>
    <th>Item Name</th>
    <th>Orignal Quantity</th>
    <th>Changed Quantity</th>
    <th>Operation</th>
  </tr>
  <?php
	
		for($i=0;$i<count($reportresult);$i++)
		{
		
			?>
  <tr>
    <td><?php echo $reportresult[$i]['barcode'];?>&nbsp;</td>
    
    <td><?php echo $reportresult[$i]['itemdescription'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['orgquantity'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['quantity'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['type'];?>&nbsp;</td>
  </tr>
  <?php 
		
		}
	
	?>
</table>
<p>&nbsp;</p>

  </form> <!--end form-->
</body>
</html>
  <?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>
