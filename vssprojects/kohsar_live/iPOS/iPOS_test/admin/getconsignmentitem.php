<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	filter($_REQUEST['q']);
$bcid			=	filter($_REQUEST['bc']);
$item			=	filter($_REQUEST['item']);
$storeid		=	filter($_REQUEST['store']);
$qty			=	$_REQUEST['qty'];
$detid			=	$_REQUEST['detailid'];
$stockid		=	$_REQUEST['stockid'];
if($detid=='')
{
	$detid=-1;
}
//getting storedb
//$storedatabase	=	$AdminDAO->getrows("store","storedb","pkstoreid='$storeid'");
//$storedb		=	$storedatabase[0]['storedb'];

//retrieving store info for local updates
$stores		=	$AdminDAO->getrows("store","*","pkstoreid='$storeid'");
$host		=	$stores[0]['storeip'];
$username	=	$stores[0]['username'];
$password	=	$stores[0]['password'];
$storedb	=	$stores[0]['storedb'];
//end database connection section
/**********************************check for BOXED item***************************/
//get from barcode (boxbarcode WHERE barcode = $bcid)
//if size is 0 for boxbarcode: don't change barcode otherwise bcid = newly got boxbarcode
/*********************************************************************************/
$boxbarcodes		=	$AdminDAO->getrows("barcode","boxbarcode,pkbarcodeid"," barcode = '$bcid'");
$boxbarcode			=	$boxbarcodes[0]['boxbarcode'];
//$and		=	" AND b.barcode = '$bcid' ";
/*if($boxbarcode!="")
{
	$box		= 	$boxbarcode;
	$boxbarcode	=	$AdminDAO->getrows("barcode","barcode"," pkbarcodeid = '$boxbarcode'");
	$boxbarcode	=	$boxbarcode[0]['barcode'];
	$bcid 		= 	$boxbarcode;
}*/
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
//$spricedata			=	$AdminDAO->getrows("$storedb.stock","pkstockid as stockid,unitsremaining as quantity,priceinrs as pprice,from_unixtime(expiry,'%d-%m-%y') as expiry","fkbarcodeid='$fkbarcodeid' AND unitsremaining>0");
$spricequery		=	"SELECT 
							pkstockid,
							unitsremaining,
							costprice,
							retailprice,
							FROM_UNIXTIME(expiry,'%d-%m-%y') expiry,
							expiry exp2,
							batch,
							purchaseprice,
							priceinrs,
							shipmentcharges,
							fkshipmentid,
							fkbarcodeid,
							fksupplierid,
							fkagentid,
							fkcountryid,
							fkbrandid,
							shipmentpercentage,
							boxprice
						FROM 
							stock
						WHERE
							fkbarcodeid='$fkbarcodeid' $where
							";
//connecting to the local db
mysql_connect($host,$username,$password) or die("Failed connecting to local database".mysql_error());
mysql_select_db($storedb) or die("Failed selecting local database".mysql_error());
$spricequerydata			=	mysql_query($spricequery) or die("Failed selecting units".mysql_error());
$spricedata					=	array();
while ($spricequeryarray	=	mysql_fetch_array($spricequerydata))
{
	  array_push($spricedata,$spricequeryarray);
}
$str	=	"<table><tr><th>Expiry</th><th>Price</th><th>Quantity</th><th>Move</th></tr>";
for($s=0;$s<sizeof($spricedata);$s++)
{
	if($qty=='undefined')
	{
		$qty	=	'';
	}
	$pkstockid			=	$spricedata[$s]['pkstockid'];
	$quantity			=	$spricedata[$s]['unitsremaining'];
	$costprice			=	$spricedata[$s]['costprice'];
	$retailprice		=	$spricedata[$s]['retailprice'];
	$expiry				=	$spricedata[$s]['expiry'];
	$exp2				=	$spricedata[$s]['exp2'];
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
	$str				.=	"<tr><td width=80 align=center>$expiry<input type=\"hidden\" name=\"stock[]\" value=\"$pkstockid\" id=\"stock\" /><input type=\"hidden\" name=\"batch[]\" value=\"$batch\" id=\"batch\" /><input type=\"hidden\" name=\"purchaseprice[]\" value=\"$purchaseprice\" id=\"purchaseprice\" /><input type=\"hidden\" name=\"shipmentcharges[]\" value=\"$shipmentcharges\" id=\"shipmentcharges\" /><input type=\"hidden\" name=\"fkshipmentid[]\" value=\"$fkshipmentid\" id=\"fkshipmentid\" /><input type=\"hidden\" name=\"fkbarcodeid[]\" value=\"$fkbarcodeid\" id=\"fkbarcodeid\" /><input type=\"hidden\" name=\"fksupplierid[]\" value=\"$fksupplierid\" id=\"fksupplierid\" /><input type=\"hidden\" name=\"fkagentid[]\" value=\"$fkagentid\" id=\"fkagentid\" /><input type=\"hidden\" name=\"fkcountryid[]\" value=\"$fkcountryid\" id=\"fkcountryid\" /><input type=\"hidden\" name=\"fkbrandid[]\" value=\"$fkbrandid\" id=\"fkbrandid\" /><input type=\"hidden\" name=\"shipmentpercentage[]\" value=\"$shipmentpercentage\" id=\"shipmentpercentage\" /><input type=\"hidden\" name=\"boxprice[]\" value=\"$boxprice\" id=\"boxprice\" /><input type=\"hidden\" name=\"costprice[]\" value=\"$costprice\" id=\"costprice\" /><input type=\"hidden\" name=\"priceinrs[]\" value=\"$priceinrs\" id=\"priceinrs\" /><input type=\"hidden\" name=\"retailprice[]\" value=\"$retailprice\" id=\"retailprice\" /><input type=\"hidden\" name=\"expiry[]\" value=\"$exp2\" id=\"expiry\" /></td><td width=80 align=right>$rprice</td><td align=center>$quantity</td><td><input type=\"text\" name=\"quantity[]\" value=\"$qty\" id=\"quantity$iteration\" onkeydown=\"javascript:if(event.keyCode==13) {insertconsignment($detid); return false;}\"/></td></tr>";
	//$str				.=	"<tr><td width=80><input type=\"hidden\" name=\"stock[]\" value=\"$pkstockid\" id=\"stock\" />$expiry</td><td width=80 align=right>$lastpurchaseprice</td><td><input type=\"text\" name=\"quantity[]\" value=\"$quantity\" /></td></tr>";
	//	echo $str;
}
$str	.="</table>";
$productname	=	json_encode($productname);
?>
<script language="javascript">
	document.getElementById('itemdescription').value=<?php echo $productname; ?>;
	document.getElementById('stockdetails').innerHTML='<?php echo $str; ?>';
	<?php
	if($iteration>0)
	{
	?>
	document.getElementById('quantity1').focus();
	<?php
	}
	?>
</script>