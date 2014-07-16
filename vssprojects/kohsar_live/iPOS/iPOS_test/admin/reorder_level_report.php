<?php
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	include_once("dbgrid.php");
}//end edit
global $AdminDAO;
/*************************DATE CHECKS**************************/
 $sdate				=	strtotime($_GET['sdate']); 
 $edate				=	strtotime($_GET['edate']);
 $barcode           =  $_GET['barcode'];
 $reorderlevel          =  $_GET['reorderlevel'];


 if($barcode !='')
	{
	$cond = " and r.barcode ='$barcode' ";
    }
	
	 if($reorderlevel !='')
	{
	$cond .= " and r.reorderlevel ='$reorderlevel'  ";
    }

 $query = "select r.*,b.itemdescription,SUM( unitsremaining ) AS unitsremaining
from $dbname_detail.re_order_level r 
left join barcode b on b.pkbarcodeid = r.fkbarcodeid
left join $dbname_detail.stock s on s.fkbarcodeid = r.fkbarcodeid
 where 1=1  $cond";
$reportresult = $AdminDAO->queryresult($query);




/**************************************************************/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Reorder Level Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<link rel="stylesheet" type="text/css" href="../includes/css/style.css">
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
Reorder Level Report
</div>
<br />
<table class="simple">
<tr>
		<th>Barcode</th>
		<th>Item Description</th>
		<th>Reorder Level</th>
		<th>Remaining Quantity</th>
	  </tr>
   
	<?php

		for($i=0;$i<count($reportresult);$i++)
		{
			
			?>
			<tr>
				<td><?php echo $reportresult[$i]['barcode'];?>&nbsp;</td>
				<td><?php echo $reportresult[$i]['itemdescription'];?>&nbsp;</td>
				<td align="right"><?php echo $reportresult[$i]['reorderlevel'];?>&nbsp;</td>
				<td align="center"><?php echo $reportresult[$i]['unitsremaining'];?>&nbsp;</td>
		    </tr>
            <?php 
		
		}
	
	?>
			   
   
   

	
	</table>
      </form> <!--end form-->
</body>
</html>
    <?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>
