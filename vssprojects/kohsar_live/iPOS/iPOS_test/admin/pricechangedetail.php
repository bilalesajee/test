<?php
include("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;
$id	=	$_REQUEST['id1'];
if($id==''){
$id	=	$_REQUEST['id2'];	
$result_barcode		=	$AdminDAO->getrows("barcode","pkbarcodeid","barcode='$id'");
 $id	=	$result_barcode[0]['pkbarcodeid'];
}

$result		=	$AdminDAO->getrows("barcode,addressbook,$dbname_detail.pricechange,$dbname_detail.pricechangehistory","*","fkbarcodeid=pkbarcodeid AND fkpricechangeid=pkpricechangeid AND pkbarcodeid='$id' AND fkaddressbookid=pkaddressbookid ORDER BY pkpricechangehistoryid DESC");
$stockdata	=	$AdminDAO->getrows("$dbname_detail.stock","retailprice","fkbarcodeid='$id' ORDER BY pkstockid DESC LIMIT 0,1");
		$price		=	$stockdata[0]['retailprice'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<title>Price Change History</title>
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<div style="width:6.0in;padding:0px;font-size:17px;" align="center"> <b>Price Change History</b><br />
  <br />
  <b><?php echo $result[0]['itemdescription'];?></b><br />
  Current Price : <b><?php echo number_format($result[0]['price'],2);?></b> <br />
  Stock Price   : <b><?php echo $price;?></b> <br />
  <br />
  <table class="simple" style="min-width:800px;">
    <tr>
      <th>Old Price</th>
      <th>New Price</th>
      <th>Changed by</th>
      <th>Change Date & Time</th>
    </tr>
    <?php
for($i=0;$i<sizeof($result);$i++)
{
	//if($result[$i]['price']!=$result[$i]['oldprice']){
	if($i==0)
	{
		$newprice	=	"<span style=\"color:red;font-weight:bold;\">".number_format($result[$i]['price'],2)."</span>";
	}
	else
	{
		$newprice	=	number_format($result[$i-1]['oldprice'],2);
	}
if($newprice!=number_format($result[$i]['oldprice'],2)){
?>
    <tr>
      <td align="center"><?php echo number_format($result[$i]['oldprice'],2);?></td>
      <td align="center"><?php echo $newprice;?></td>
      <td><?php echo $result[$i]['firstname']." ".$result[$i]['lastname'];?></td>
      <td><?php echo date("H:i:s a d-m-y",$result[$i]['updatetime']);?></td>
    </tr>
    <?php
	}
}
?>
  </table>
  </form> <!--end form-->
</div>
</body>
</html>
<?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>