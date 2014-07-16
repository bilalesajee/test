<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	filter($_REQUEST['q']);
$bcid			=	filter($_REQUEST['bc']);
$item			=	filter($_REQUEST['item']);
$storeid		=	filter($_REQUEST['store']);
$quoteprice		=	$_REQUEST['qprice'];
$detid			=	$_REQUEST['detailid'];
$stockid		=	$_REQUEST['stockid'];
if($detid=='')
{
	$detid=-1;
}
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
if($stockid!='undefined')
{
	$where	=	"AND pkstockid=$stockid";
}
else
{
	$where	=	"AND unitsremaining>0";
}
$spricequery		=	"SELECT 
							*
						FROM 
							$dbname_detail.stock
						WHERE
							fkbarcodeid='$fkbarcodeid' $where
							order by pkstockid desc
							limit 0,15
							";
$spricedata			=	$AdminDAO->queryresult($spricequery);
$str	=	"<table><tr><th>Expiry</th><th>Trade Price</th><th>Sale Price</th><th>Quote Price</th><th>&nbsp;</th></tr>";
if(sizeof($spricedata)>0)
{
	for($s=0;$s<sizeof($spricedata);$s++)
	{
		if($quoteprice=='undefined')
		{
			$quoteprice	=	'';
		}
		$pkstockid			=	$spricedata[$s]['pkstockid'];
		$quantity			=	$spricedata[$s]['unitsremaining'];
		$costprice			=	$spricedata[$s]['costprice'];
		$retailprice		=	$spricedata[$s]['retailprice'];
		$expiry				=	date("d-m-y",$spricedata[$s]['expiry']);
		$exp2				=	$spricedata[$s]['expiry'];
		$batch				=	$spricedata[$s]['batch'];
		$purchaseprice		=	$spricedata[$s]['purchaseprice'];
		$priceinrs			=	$spricedata[$s]['priceinrs'];
		$shipmentcharges	=	$spricedata[$s]['shipmentcharges'];
		$fkshipmentid		=	$spricedata[$s]['fkshipmentid'];
		$fkbarcodeid		=	$spricedata[$s]['fkbarcodeid'];
		$fksupplierid		=	$spricedata[$s]['fksupplierid'];
		$fkagentid			=	$spricedata[$s]['fkagentid'];
		$fkcountryid		=	$spricedata[$s]['fkcountryid'];
		$fkbrandid			=	$spricedata[$s]['fkbrandid'];
		$shipmentpercentage	=	$spricedata[$s]['shipmentpercentage'];
		$boxprice			=	$spricedata[$s]['boxprice'];
		$rprice				=	number_format($retailprice,2);
		$iteration			=	$s+1;
		$str				.=	"<tr><td width=80 align=center>$expiry<input type=\"hidden\" name=\"stock[]\" value=\"$pkstockid\" id=\"stock\" /><input type=\"hidden\" name=\"batch[]\" value=\"$batch\" id=\"batch\" /><input type=\"hidden\" name=\"purchaseprice[]\" value=\"$purchaseprice\" id=\"purchaseprice\" /><input type=\"hidden\" name=\"shipmentcharges[]\" value=\"$shipmentcharges\" id=\"shipmentcharges\" /><input type=\"hidden\" name=\"fkshipmentid[]\" value=\"$fkshipmentid\" id=\"fkshipmentid\" /><input type=\"hidden\" name=\"fkbarcodeid[]\" value=\"$fkbarcodeid\" id=\"fkbarcodeid\" /><input type=\"hidden\" name=\"fksupplierid[]\" value=\"$fksupplierid\" id=\"fksupplierid\" /><input type=\"hidden\" name=\"fkagentid[]\" value=\"$fkagentid\" id=\"fkagentid\" /><input type=\"hidden\" name=\"fkcountryid[]\" value=\"$fkcountryid\" id=\"fkcountryid\" /><input type=\"hidden\" name=\"fkbrandid[]\" value=\"$fkbrandid\" id=\"fkbrandid\" /><input type=\"hidden\" name=\"shipmentpercentage[]\" value=\"$shipmentpercentage\" id=\"shipmentpercentage\" /><input type=\"hidden\" name=\"boxprice[]\" value=\"$boxprice\" id=\"boxprice\" /><input type=\"hidden\" name=\"costprice[]\" value=\"$costprice\" id=\"costprice\" /><input type=\"hidden\" name=\"priceinrs[]\" value=\"$priceinrs\" id=\"priceinrs\" /><input type=\"hidden\" name=\"retailprice[]\" value=\"$retailprice\" id=\"retailprice\" /><input type=\"hidden\" name=\"expiry[]\" value=\"$exp2\" id=\"expiry\" /></td><td width=80 align=right>$costprice</td><td align=center>$rprice</td><td><input type=\"text\" name=\"quoteprice[]\" id=\"quoteprice\" value=\"$quoteprice\" onkeydown=\"javascript:if(event.keyCode==13) {insertquoteitem($detid); return false;}\"></td><td><input type=\"button\" value=\"Add Item\" onclick=\"javascript:insertquoteitem($detid); return false;\"/></td></tr>";
	}
}
else
{
	$str	.=	"<tr><td width=80 align=center>$expiry<input type=\"hidden\" name=\"stock[]\" value=\"$pkstockid\" id=\"stock\" /><input type=\"hidden\" name=\"batch[]\" value=\"$batch\" id=\"batch\" /><input type=\"hidden\" name=\"purchaseprice[]\" value=\"$purchaseprice\" id=\"purchaseprice\" /><input type=\"hidden\" name=\"shipmentcharges[]\" value=\"$shipmentcharges\" id=\"shipmentcharges\" /><input type=\"hidden\" name=\"fkshipmentid[]\" value=\"$fkshipmentid\" id=\"fkshipmentid\" /><input type=\"hidden\" name=\"fkbarcodeid[]\" value=\"$fkbarcodeid\" id=\"fkbarcodeid\" /><input type=\"hidden\" name=\"fksupplierid[]\" value=\"$fksupplierid\" id=\"fksupplierid\" /><input type=\"hidden\" name=\"fkagentid[]\" value=\"$fkagentid\" id=\"fkagentid\" /><input type=\"hidden\" name=\"fkcountryid[]\" value=\"$fkcountryid\" id=\"fkcountryid\" /><input type=\"hidden\" name=\"fkbrandid[]\" value=\"$fkbrandid\" id=\"fkbrandid\" /><input type=\"hidden\" name=\"shipmentpercentage[]\" value=\"$shipmentpercentage\" id=\"shipmentpercentage\" /><input type=\"hidden\" name=\"boxprice[]\" value=\"$boxprice\" id=\"boxprice\" /><input type=\"hidden\" name=\"costprice[]\" value=\"$costprice\" id=\"costprice\" /><input type=\"hidden\" name=\"priceinrs[]\" value=\"$priceinrs\" id=\"priceinrs\" /><input type=\"hidden\" name=\"retailprice[]\" value=\"$retailprice\" id=\"retailprice\" /><input type=\"hidden\" name=\"expiry[]\" value=\"$exp2\" id=\"expiry\" /></td><td width=80 align=right>$costprice</td><td align=center>$rprice</td><td><input type=\"text\" name=\"quoteprice[]\" id=\"quoteprice\" value=\"$quoteprice\" onkeydown=\"javascript:if(event.keyCode==13) {insertquoteitem($detid); return false;}\"></td><td><input type=\"button\" value=\"Add Item\" onclick=\"javascript:insertquoteitem($detid); return false;\"/></td></tr>";
}
$str	.="</table>";
?>
<script language="javascript">
	document.getElementById('itemdescription').value='<?php echo "$productname"; ?>';
	document.getElementById('stockdetails').innerHTML='<?php echo $str; ?>';
	<?php
	if($iteration>0)
	{
	?>
	document.getElementById('quoteprice').focus();
	<?php
	}
	?>
</script>