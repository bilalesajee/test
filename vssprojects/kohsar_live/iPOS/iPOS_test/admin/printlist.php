<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$shipmentid		=	$_GET['shipmentid'];
$packingdata	=	$AdminDAO->getrows("packing p,packinglist pl,shiplist","*","pl.fkpackingid=pkpackingid AND p.fkshipmentid='$shipmentid' AND pkshiplistid=pl.fkshiplistid ORDER BY pl.fkpackingid");
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
/*echo "<pre>";
print_r($packingdata);
echo "</pre>";*/
?>
<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<body style="background-color:#FFF;">
<table style="margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;">
<tr>
<td colspan="4"><img src="../images/logo.gif" align="Esajee and Company" border="0"></td>
</tr>
</table>
<table style="margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;">
   <?php
	for($i=0;$i<sizeof($packingdata);$i++)
	{
		$boxname		=	$packingdata[$i]['packingname'];
		$packinglist	=	$packingdata[$i]['pkpackinglistid'];
		$received		=	$packingdata[$i]['received'];
		$reserveditems	=	$packingdata[$i]['reserved'];
		$remaining		=	$reserveditems-$received;
		$y2="";
		$y1	=	"<select name=\"packlistitem[]\">";	
		for($j=$remaining;$j>0;$j--)
		{
			$val	=	$j."_".$packinglist;
			$y2.=	"<option value=\"$val\" >$j</option>";
		}
		$receivedunits	=	$y1.$y2."</select>";
		if($i>0 && $packingdata[$i]['packingname']==$packingdata[$i-1]['packingname'])
		{
			?>
           	&nbsp;
            <?php
		}
        else
        {
        ?>
       	<tr>
		   	<th width="100%" colspan="10"><?php echo $boxname; ?></th>
        </tr>
        <tr>
            <th width="18%">Item</th>
            <th width="10%">Barcode</th>
			<th width="8%">Expiry</th>
            <th width="8%">Purchase Price</th>
            <th width="8%">Sales Tax</th>
            <th width="8%">Surcharge</th>
            <th width="8%">Charges</th>
            <th width="8%">Charges in <?php echo $defaultcurrency;?></th>
            <th width="8%">Quantity</th>
            <th width="8%">Received</th>
        </tr>
   		<?php
		}
		?>
        <tr>
        <td colspan="10">
            <table width="100%" style="margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;">
                <tr>
                	<td width="18%"><?php echo $packingdata[$i]['itemdescription'];?></td>
                	<td width="10%"><?php echo $packingdata[$i]['barcode'];?></td>
                	<td width="8%"><?php echo implode("-",array_reverse(explode("-",$packingdata[$i]['expiry'])));?></td>
                    <td width="8%"><?php echo $packingdata[$i]['purchaseprice'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['salestax'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['surcharge'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['charges'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['chargesinrs'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['reserved'];?></td>
                    <td width="8%"><?php echo $packingdata[$i]['received'];?></td>
                </tr>
            </table>
        </td>
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