<?php

include("../includes/security/adminsecurity.php");
$shipid		=	$_REQUEST['shipid'];
global $AdminDAO,$Component;
$shipmentvalue		=	$AdminDAO->getrows("shipment,shipmentcharges sc","amountinrs,SUM(sc.chargesinrs) as chargesinrs","pkshipmentid = '$shipid' AND pkshipmentid = fkshipmentid GROUP BY fkshipmentid");
$shipmentstock		=	$AdminDAO->getrows("$dbname_detail.stock,shipment","exchangerate,SUM(shipmentcharges) as shipmentcharges,SUM(purchaseprice) as purchaseprice","fkshipmentid = '$shipid' AND pkshipmentid = fkshipmentid GROUP BY fkshipmentid");
// 1. The Shipment Cost Section
$totalshipcharges	=	$shipmentvalue[0]['chargesinrs'];
$chargesincurred	=	$shipmentstock[0]['shipmentcharges'];

// Exchange Rate
$exchangerate		=	$shipmentstock[0]['exchangerate'];
// Purchase Price
//$totalpurchaseprice	=	$shipmentstock[0]['purchaseprice'];
//now calculating losses
$damagesarr	=	$AdminDAO->getrows("$dbname_detail.stock s,$dbname_detail.damages d","d.quantity as dquantity, s.quantity as quantity,s.purchaseprice as pprice","pkstockid=fkstockid AND s.fkshipmentid = '$shipid'");
for($i=0;$i<sizeof($damagesarr);$i++)
{
	$purchaseprice	=	$damagesarr[$i]['pprice'];
	$damages		=	$damagesarr[$i]['dquantity'];
	$totalunits		=	$damagesarr[$i]['quantity'];
	$remaining		=	$totalunits-$damages;
	$remainingprice	+=	$exchangerate*$remaining*$purchaseprice;
	$damagedprice	+=	$damages*$purchaseprice;
}
// 2. The Shipment Price Section
$chargesincurred	=	$shipmentstock[0]['shipmentcharges']*$remaining;
// Remaining Charges in RS STEP 1 DONE
$shipcharges		=	$totalshipcharges-($chargesincurred+$damagedprice);
/************************************************************/
//$totalshipvalue		=	$shipmentvalue[0]['amountinrs'];
include_once("calcshipvalue.php");
$totalshipval		=	calcshipval($shipid);
$newshipvalue		=	$totalshipval-$remainingprice;

if($shipcharges!=0)
{
	$percentage			=	round(($shipcharges/$newshipvalue)*100,2);
}
else
{
	$percentage			=	0;
}
?>
<script language="javascript" type="text/javascript">
document.getElementById('percentagediv').innerHTML='<?php echo $percentage;?>';
document.getElementById('baseprice').innerHTML='<?php echo $newshipvalue;?>';
document.getElementById('baseexpense').innerHTML='<?php echo $shipcharges;?>';
</script>
