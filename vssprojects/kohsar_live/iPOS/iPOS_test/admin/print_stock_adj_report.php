<?php 
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	include_once("dbgrid.php");
}//end edit
global $AdminDAO;
$id	=	$_REQUEST['ids'];

	$id	=	trim($id,',');

$idarr	=	explode(',',$id);
 $newid	=	$idarr[(sizeof($idarr)-1)];

	if($newid=='')

	{

		print"<b>No Row  Selected</b>";
     exit;

	}
	  
  $query = "SELECT b.itemdescription,b.barcode,quantity,orgquantity,if(type=0,'Addition','Subtraction') type from $dbname_detail.stock_adjustment_detail  left join $dbname_main.barcode b on (fkbarcodeid=pkbarcodeid)
	 where  fkstockadjustmentid = '$newid'     ";
	
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
<body><br />
<br />
<table width="70%" align="center" style="none" border="0" >
  <tr><td align="left"  bordercolor="#FFFFFF";><h3>Stock Adjustment Report</h3></td>
    </tr></table>

<table width="70%" align="center" class="simple">
  <tr>
    <th width="77">SNo</th>
    <th width="62">Barcode</th>
    <th width="218">Item</th>
     <th width="99">Orignal Quantity</th>
      <th width="99">Adjusted Quantity</th>
         <th width="99">Operation</th>
    </tr>
  <?php
	$totalamount = 0;
		for($i=0;$i<count($reportresult);$i++)
		{
	
			?>
  <tr>
    <td><?php echo $i+1;?>&nbsp;</td>
    
    <td><?php echo $reportresult[$i]['barcode'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['itemdescription'];?></td>
    <td align="right"><?php echo $reportresult[$i]['orgquantity'];?>&nbsp;</td>
    <td align="right"><?php echo $reportresult[$i]['quantity'];?>&nbsp;</td>
    <td align="right"><?php echo $reportresult[$i]['type'];?></td>
  
  </tr><?php 
		
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

