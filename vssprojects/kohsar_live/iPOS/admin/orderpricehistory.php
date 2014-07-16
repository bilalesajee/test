<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$bc 		= 	$_GET['bc'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Price History for <?php echo $bc;?></title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>

<body><br />
<?php
// fetch stores
$stores	=	$AdminDAO->getrows("store","pkstoreid,storename,storedb","storestatus=1");
for($s=0;$s<sizeof($stores);$s++)
{
	$storename	=	$stores[$s]['storename'];
	$storedb		=	$stores[$s]['storedb'];
?>
<center>
<span style="font-weight:bold;font-size:14px;"><?php echo $storename;?></span>
<table class="simple">
	<tr>
    	<th>Price Change</th>
        <th>Stock Prices</th>
    </tr>
    <tr>
        <td valign="top">
        <table class="simple">
          <tr>
            <th>Serial</th>
            <th>Time</th>
            <th>Retail Price</th>
          </tr>
            <?php
            $priceres	=	$AdminDAO->getrows("$storedb.pricechange,$storedb.pricechangehistory,barcode","price,oldprice,FROM_UNIXTIME(updatetime,'%d-%m-%Y %h:%i:%s %p') updatetime","fkbarcodeid=pkbarcodeid AND pkpricechangeid=fkpricechangeid AND barcode='$bc' ORDER BY pkpricechangehistoryid DESC LIMIT 0,10");
            for($i=0;$i<sizeof($priceres);$i++)
            {
                //
                $price				=	$priceres[$i]['price'];
                $oldprice			=	$priceres[$i]['oldprice'];
                $updatetime			=	$priceres[$i]['updatetime'];
            ?>
          <tr>
            <td><?php echo $i+1;?></td>
            <td><?php echo $updatetime;?></td>
            <td><?php if($oldprice==0) {echo $price;} else {echo $oldprice;}?></td>
          </tr>
            <?php
            }
            ?>
        </table>
        </td>
        <td valign="top">
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;border-collapse:collapse;padding:3px;border-color:#000;">
          <tr>
            <th>Serial</th>
            <th>Time</th>
            <th>Trade Price</th>
            <th>Retail Price</th>
          </tr>
            <?php
            $stockres	=	$AdminDAO->getrows("$storedb.stock,barcode","priceinrs,retailprice,FROM_UNIXTIME(updatetime,'%d-%m-%Y %h:%i:%s %p') updatetime","fkbarcodeid=pkbarcodeid AND barcode='$bc' ORDER BY pkstockid DESC LIMIT 0,10");
            for($i=0;$i<sizeof($stockres);$i++)
            {
                //
                $tradeprice		=	$stockres[$i]['costprice'];
                $saleprice		=	$stockres[$i]['retailprice'];
                $updatetime		=	$stockres[$i]['updatetime'];
            ?>
          <tr>
            <td><?php echo $i+1;?></td>
            <td><?php echo $updatetime;?></td>
            <td><?php echo $tradeprice;?></td>
            <td><?php echo $saleprice;?></td>
          </tr>
            <?php
            }
            ?>
        </table>
        </td>
    </tr>
</table>
</center>
<?php
}
?>
</body>
</html>