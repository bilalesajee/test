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
	  
$query_sup = "SELECT p.pkpurchasereturnid, p.fksupplierid,FROM_UNIXTIME(p.addtime,'%d-%m-%Y') adddate  ,sl.pksupplierid, sl.companyname from $dbname_detail.purchase_return p
	
	 left join $dbname_main.supplier sl on p.fksupplierid=sl.pksupplierid
	 where p.pkpurchasereturnid = ' $newid'  ";
	
$reportresult_sup = $AdminDAO->queryresult($query_sup);	  
	  
	  $supplierid=	$reportresult_sup[0]['fksupplierid'];
		$companyname=	$reportresult_sup[0]['companyname'];
		$adddate=	$reportresult_sup[0]['adddate'];
	
 $query = "SELECT b.itemdescription,b.barcode,d.quantity,d.price,p.pkpurchasereturnid, p.fksupplierid,FROM_UNIXTIME(p.addtime,'%d-%m-%Y') adddate ,d.value as value ,sl.pksupplierid, sl.companyname from $dbname_detail.purchase_return p
	 left join $dbname_detail.purchase_return_detail d on d.fkpurchasereturnid = p.pkpurchasereturnid 
	 left join $dbname_main.supplier sl on p.fksupplierid=sl.pksupplierid
	  left join $dbname_main.barcode b on d.fkbarcodeid=b.pkbarcodeid
	 where  fkpurchasereturnid = '$newid'     ";
	
$reportresult = $AdminDAO->queryresult($query);
echo"<div align=left><b><h3> $storename</h3><br>Supplier Name: $companyname &nbsp;&nbsp;&nbsp;Date: ".$adddate."</b>

</div></div>

	";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Purchase Return Without Invoice Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body><br />
<br />
<table width="70%" align="center" style="none" border="0" >
  <tr><td align="left"  bordercolor="#FFFFFF";><h3>Purchase Return Without Invoice Report</h3></td>
    </tr></table>
<table width="70%" align="center" class="simple">
  <tr>
    <th width="77">SNo</th>
    <th width="62">Barcode</th>
    <th width="218">Item</th>
     <th width="99">Quantity</th>
    <th width="100">Price</th>
    <th width="140">Amount</th>
  </tr>
  <?php
	$totalamount = 0;
		for($i=0;$i<count($reportresult);$i++)
		{
		$totalamount += $reportresult[$i]['value'];
			?>
  <tr>
    <td><?php echo $i+1;?>&nbsp;</td>
    
    <td><?php echo $reportresult[$i]['barcode'];?>&nbsp;</td>
    <td><?php echo $reportresult[$i]['itemdescription'];?></td>
   
    <td align="right"><?php echo $reportresult[$i]['quantity'];?>&nbsp;</td>
    <td align="right"><?php echo $reportresult[$i]['price'];?></td>
    <td align="right"><?php echo $reportresult[$i]['value'];?>&nbsp;</td>
  </tr><?php 
		
		}
	
	?>
  <tr>
    <td colspan="4">&nbsp;</td>
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

