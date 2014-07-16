<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	filter($_REQUEST['q']);
$bcid			=	filter($_REQUEST['bc']);
$item			=	filter($_REQUEST['item']);
/**********************************check for BOXED item***************************/
//get from barcode (boxbarcode WHERE barcode = $bcid)
//if size is 0 for boxbarcode: don't change barcode otherwise bcid = newly got boxbarcode
/*********************************************************************************/
$boxbarcodes		=	$AdminDAO->getrows("barcode","boxbarcode,pkbarcodeid"," barcode = '$bcid'");
$boxbarcode			=	$boxbarcodes[0]['boxbarcode'];
//$and		=	" AND b.barcode = '$bcid' ";
if($boxbarcode!="")
{
	$box		= 	$boxbarcode;
	$boxbarcode	=	$AdminDAO->getrows("barcode","barcode"," pkbarcodeid = '$boxbarcode'");
	$boxbarcode	=	$boxbarcode[0]['barcode'];
	$bcid 		= 	$boxbarcode;
}
$sql	= "SELECT itemdescription as PRODUCTNAME, pkbarcodeid as bc 
			FROM 
				barcode
			WHERE 
				barcode = '$bcid'
		";
if($bcid!='')
{
	$barcode_array	=	$AdminDAO->queryresult($sql);
	$pkbarcodeid	=	$barcode_array[0]['bc'];
	$productname	=	$barcode_array[0]['PRODUCTNAME'];
}
// checking stocks to fetch last purchase price
$fkbarcodeid		=	$boxbarcodes[0]['pkbarcodeid'];
$spricedata			=	$AdminDAO->getrows("stock,shipment,currency","pkstockid,pkcurrencyid,MAX(purchaseprice) as pprice,currencysymbol","fkbarcodeid='$fkbarcodeid' AND pkcurrencyid=shipmentcurrency AND fkshipmentid=pkshipmentid GROUP BY fkbarcodeid");
/*echo "<pre>";
print_r($spricedata);
echo "</pre>";
exit;*/
$spricecurrency		=	$spricedata[0]['currencysymbol'];
$lastpurchaseprice	=	$spricedata[0]['pprice'];
$quantity			=	$spricedata[0]['unitsremaining'];
$currencyid			=	$spricedata[0]['pkcurrencyid'];
// expiries
$expiries			=	$AdminDAO->getrows("stock","pkstockid,FROM_UNIXTIME(expiry,'%d-%m-%y') as expiry","fkbarcodeid='$fkbarcodeid'");
$expirysel		=	"<select name=\"expiry2\" id=\"expiry2\" style=\"width:100px;\" onchange=\"getexpitem(this.value);\"><option value=\"\">Select Expiry</option>";
for($i=0;$i<sizeof($expiries);$i++)
{
	$expiry		=	$expiries[$i]['expiry'];
	$expiryid	=	$expiries[$i]['pkstockid'];
	$select		=	"";
	if($storeid == $selected_store)
	{
		$select = "selected=\"selected\"";
	}
	$expirysel2	.=	"<option value=\"$expiryid\" $select>$expiry</option>";
}
$expirybox		=	$expirysel.$expirysel2."</select>";
// end stores
?>
<script language="javascript">
	document.getElementById('itemdescription').value="<?php echo $productname; ?>";
	document.getElementById('lastpurchaseprice').innerHTML="<?php echo $lastpurchaseprice; ?>";
	document.getElementById('currency').innerHTML="<?php echo $spricecurrency; ?>";
	document.getElementById('lastpprice').value="<?php echo $lastpurchaseprice; ?>";
	document.getElementById('currencyid').value="<?php echo $currencyid;?>";
	document.getElementById('quantity').value=	"<?php echo $quantity;?>";
	document.getElementById('expdate').innerHTML	=	'<?php echo $expirybox;?>';
	document.getElementById('quantity').focus();
</script>