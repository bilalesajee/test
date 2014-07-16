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
	$query_sup = "SELECT FROM_UNIXTIME(p.datetime,'%d-%m-%Y') adddate  , sl.companyname from 
	 $dbname_detail.supplierinvoice p
	 left join $dbname_main.supplier sl on p.fksupplierid=sl.pksupplierid
	 where p.pksupplierinvoiceid = ' $newid'  ";
	
$reportresult_sup = $AdminDAO->queryresult($query_sup);	  
	  
	 
		$companyname=	$reportresult_sup[0]['companyname'];
		$adddate=	$reportresult_sup[0]['adddate'];
	  
  $query = "SELECT b.itemdescription,b.barcode,adj.quantity,adj.orignalquantity,adj.price,adj.orignalrate from $dbname_detail.adjustment adj
	  left join $dbname_detail.stock stk on (pkstockid=fkstockid) 
	  left join $dbname_main.barcode b on stk.fkbarcodeid=b.pkbarcodeid
	 where  adj.fksupplierinvoiceid = '$newid'     ";
	
$reportresult = $AdminDAO->queryresult($query);
echo"<div align=left><b><h3> $storename</h3><br>Supplier Name: $companyname &nbsp;&nbsp;&nbsp;Invoice Add Date: ".$adddate."</b>

</div></div>

	";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Invoice Adjustment Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body><br />
<br />
<table width="70%" align="center" style="none" border="0" >
  <tr><td align="left"  bordercolor="#FFFFFF";><h3>Invoice Adjustment Report</h3></td>
    </tr></table>

<table width="70%" align="center" class="simple">
  <tr>
    <th width="77">SNo</th>
    <th width="62">Barcode</th>
    <th width="218">Item</th>
     <th width="99">Orignal Quantity</th>
      <th width="99">Adjusted Quantity</th>
    <th width="100">Orignal Price</th>
    <th width="100">Adjusted Price</th>
    <th width="140">Amount</th>
  </tr>
  <?php
	$totalamount = 0;
		for($i=0;$i<count($reportresult);$i++)
		{
		$totalamount += ($reportresult[$i]['quantity']*$reportresult[$i]['price']);
			?>
  <tr>
    <td><?php echo $i+1;?>&nbsp;</td>
    
    <td><?php echo $reportresult[$i]['barcode'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['itemdescription'];?></td>
    <td align="right"><?php echo $reportresult[$i]['orignalquantity'];?>&nbsp;</td>
    <td align="right"><?php echo $reportresult[$i]['quantity'];?>&nbsp;</td>
     <td align="right"><?php echo $reportresult[$i]['orignalrate'];?></td>
    <td align="right"><?php echo $reportresult[$i]['price'];?></td>
    <td align="right"><?php echo $reportresult[$i]['quantity']*$reportresult[$i]['price'];?>&nbsp;</td>
  </tr><?php 
		
		}
	
	?>
  <tr>
    <td colspan="6">&nbsp;</td>
    <td align="right">Total:</td>
    <td align="right"><?php echo $totalamount; ?></td>
  </tr>
  
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

