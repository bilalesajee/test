<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$shipmentid	=	$_GET['shipmentid'];
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
$shipdetails	=	$AdminDAO->getrows("shipment,store,countries","storename,code3,FROM_UNIXTIME(shipmentdate,'%d-%m-%y') as shipmentdate","pkshipmentid='$shipmentid' AND fkstoreid=pkstoreid AND shipment.fkcountryid=pkcountryid");
$source			=	$shipdetails[0]['code3'];
$destination	=	$shipdetails[0]['storename'];
$shipmentdate	=	$shipdetails[0]['shipmentdate'];

// receiving the fields data
$itemdescription	=	$_GET['itemdescription'];
$barcode			=	$_GET['barcode'];
$purchasequantity	=	$_GET['purchasequantity'];
$expiry				=	$_GET['expiry'];
$lastpurchaseprice	=	$_GET['lastpurchaseprice'];
$purchaseprice		=	$_GET['purchaseprice'];
$salestax			=	$_GET['salestax'];
$surcharge			=	$_GET['surcharge'];
$weight				=	$_GET['weight'];
$chargesinrs		=	$_GET['chargesinrs'];

//receiving fields
//like itemdescription, barcode etc
$shipdata	=	$AdminDAO->getrows("shiplist,packinglist","*","fkshipmentid='$shipmentid' AND fkshiplistid=pkshiplistid ORDER BY itemdescription ASC");
?>
<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<body style="background-color:#FFF;">
<table style="margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;">
<tr>
<td colspan="4"><img src="../images/logo.gif" align="Esajee and Company" border="0"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>Date</td>
<td><?php echo $shipmentdate; ?></td>
</tr>
<tr>
<td>From</td>
<td><?php echo $source; ?></td>
<td>To</td>
<td><?php echo $destination; ?></td>
</tr>
</table>
<br>
<table style="margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;" align="left">
  <tr>
  	<th width="">Sr. #</th>
    <?php if($itemdescription=="itemdescription"){?><th>Item Name</th><?php }?>
    <?php if($barcode=="barcode"){?><th>Barcode</th><?php }?>
    <?php if($purchasequantity=="purchasequantity"){?><th>Quantity</th><?php }?>
    <?php if($expiry=="expiry"){?><th>Expiry</th><?php }?>
    <?php if($lastpurchaseprice=="lastpurchaseprice"){?><th>Last Purchase Price</th><?php }?>
    <?php if($purchaseprice=="purchaseprice"){?><th>Purchase Price</th><?php }?>
    <?php if($salestax=="salestax"){?><th>Sales Tax</th><?php }?>
    <?php if($surcharge=="surcharge"){?><th>Surcharge</th><?php }?>
    <?php if($weight=="weight"){?><th>Weight</th><?php }?>
    <?php if($chargesinrs=="chargesinrs"){?><th>Charges in <?php echo $defaultcurrency;?></th><?php }?>                  
  </tr>
  <?php 
  //print_r($shipdata);
  for($i=0;$i<sizeof($shipdata);$i++)
  {
  ?>
  <tr>
	<td><?php echo $i+1; ?></td>
    <?php if($itemdescription=="itemdescription"){?><td><?php echo $shipdata[$i]['itemdescription']; ?></td><?php }?>
    <?php if($barcode=="barcode"){?><td><?php echo $shipdata[$i]['barcode']; ?></td><?php }?>
    <?php if($purchasequantity=="purchasequantity"){?><td><?php echo $shipdata[$i]['purchasequantity']; ?></td><?php }?>
    <?php if($expiry=="expiry"){?><td><?php echo $shipdata[$i]['expiry']; ?></td><?php }?>
    <?php if($lastpurchaseprice=="lastpurchaseprice"){?><td><?php echo $shipdata[$i]['lastpurchaseprice']; ?></td><?php }?>
    <?php if($purchaseprice=="purchaseprice"){?><td><?php echo $shipdata[$i]['purchaseprice']; ?></td><?php }?>
    <?php if($salestax="salestax"){?><td><?php echo $shipdata[$i]['salestax']; ?></td><?php }?>
    <?php if($surcharge=="surcharge"){?><td><?php echo $shipdata[$i]['surcharge']; ?></td><?php }?>
    <?php if($weight=="weight"){?><td><?php echo $shipdata[$i]['weight']; ?></td><?php }?>
    <?php if($chargesinrs=="chargesinrs"){?><td><?php echo $shipdata[$i]['chargesinrs']; ?></td><?php }?>      
  </tr>
  <?php
  }
  ?>
</table>
<script language="javascript">
	window.print();
	//window.close();
</script>
</body>