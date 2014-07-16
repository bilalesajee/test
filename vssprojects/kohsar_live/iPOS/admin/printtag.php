<?php
session_start();
error_reporting(7);
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
include_once("../includes/bc/barcode.php");
global $AdminDAO;

$pkbarcodeid	=	$_REQUEST['id'];
$pkbarcodeidarr	= 	explode(",", $pkbarcodeid);
$i		=	1;		
//echo '<pre>';
//print_r($pkbarcodeidarr);
//exit;
foreach($pkbarcodeidarr as $key=>$value)
{
	if($value!='')
	{
		///echo $pkbarcodeidarr[$i];
		
		//getting the barcode and item description
		$barcodearray			=	$AdminDAO->getrows("barcode","barcode,itemdescription","pkbarcodeid='$value'");
		$barcode 				=	$barcodearray[0]['barcode'];
		$fullitemdescription	=	$barcodearray[0]['itemdescription'];
		
		//stripping the item description
		$startbracketpos		=	strpos($fullitemdescription,'(');
		$itemdescription		=	substr($fullitemdescription,0,$startbracketpos);
		$itemattribute			=	substr($fullitemdescription,$startbracketpos,15);
		//retrieving price from pricechange
		$pricearr			=	$AdminDAO->getrows("$dbname_detail.pricechange","price","fkbarcodeid='$value'");
		$retailprice		=	$pricearr[0]['price'];	
		if($retailprice=="")
		{
			//getting the retail price
			$latestrecordarr	=	$AdminDAO->getrows("$dbname_detail.stock","max(pkstockid) as pkstockid","fkbarcodeid='$value'");
			$latestrecord		= 	$latestrecordarr[0]['pkstockid'];
			
			$retailpricearr		=	$AdminDAO->getrows("$dbname_detail.stock","retailprice","fkbarcodeid='$value' AND pkstockid = '$latestrecord'");
			$retailprice		=	$retailpricearr[0]['retailprice'];
		}
		//getting default currency
		$currency = $AdminDAO->getrows('currency','currencysymbol',"`defaultcurrency`  = 1");
		$defaultcurrency = $currency[0]['currencysymbol'];
		
		//Final display for print
		?>
		<div align="center" style="font-family:Arial, Helvetica, sans-serif;font-size:11px;text-align:center;">
		<?php
		echo $itemdescription."<br />";
		echo $itemattribute." ";
		echo "<br />";
		//echo $defaultcurrency.$retailprice."<br />";
		//echo "bc_$i";
		@unlink("bc_$i.png");
		genBarCode($barcode,"bc_$i.png");	
		
		?>
		<img width="135" height="45" src="bc_<?php echo $i;?>.png" /><br />
		<?php
		//echo '<div style="text-align:right; width:150px;">'.$defaultcurrency." ".$retailprice."</div><br />";
		?>
		<?php
		if(!isset($barcodearray[$key+1]))
		{
			echo '<div style="page-break-after:always"></div>';
		}
		?>
		</div>
		<?php
        $i++;
	}
}// end for
?>
<script language="javascript">
	//window.resizeTo(140,100);
	window.print();
	window.close();
</script>