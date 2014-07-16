<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	filter($_REQUEST['q']);
$bcid			=	filter($_REQUEST['bc']);

$item			=	filter($_REQUEST['item']);
/**********************************check for BOXED item********************************************/
//get from barcode (boxbarcode WHERE barcode = $bcid)
//if size is 0 for boxbarcode: don't change barcode otherwise bcid = newly got boxbarcode
/*********************************************************************************/
$boxbarcode	=	$AdminDAO->getrows("barcode","boxbarcode"," barcode = '$bcid'");
$boxbarcode	=	$boxbarcode[0]['boxbarcode'];
//$and		=	" AND b.barcode = '$bcid' ";
if($boxbarcode!="")
{
	$box		= 	$boxbarcode;
	$boxbarcode	=	$AdminDAO->getrows("barcode","barcode"," pkbarcodeid = '$boxbarcode'");
	$boxbarcode	=	$boxbarcode[0]['barcode'];
	$bcid 		= 	$boxbarcode;
	
	//$and	=	" AND b.pkbarcodeid = '$bcid' ";
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
	$brands_array	=	$AdminDAO->getrows('brand, barcodebrand, countries '," pkbrandid, CONCAT(brandname,' ',countryname) AS brandname "," branddeleted<>'1' AND fkbrandid=pkbrandid AND fkbarcodeid='$pkbarcodeid' AND fkcountryid=pkcountryid ");
//$brands				=	$Component->makeComponent('d','brands',$brands_array,"pkbrandid","brandname",1,$selected_brands,'onchange="getbrandsupplier(this.value)"');
}
//echo $pkbarcodeid."is the barcode";
if ($pkbarcodeid=='')
{
	echo "<script language=\"javascript\">jQuery('#productdiv').load('proinstancefrm.php?bc=$bcid');
	hidediv('stockitem');</script>";
}
$q1	=	"<select name=brand onchange=getbrandsupplier(this.value) class=eselect>";
for($i=0;$i<sizeof($brands_array); $i++)
{
	$brandid	=	$brands_array[$i]['pkbrandid'];
	$brandname	=	$brands_array[$i]['brandname'];
	$q2.=	"<option value=$brandid>$brandname</option>";
}
$q	=	$q1.$q2."</select>";
?>
<script language="javascript">
if(<?php echo $item;?>==0)
{
	document.getElementById('productname').value="<?php echo $productname; ?>";
}
jQuery('#itembrands').html('<?php echo $q;?>');
if(document.getElementById('locksupplier').checked==false)
{
	document.getElementById('brandsupplier2').innerHTML='';
	document.getElementById('brandsupplierdiv').style.display='none';
	jQuery('#brandsupplier').load('loadshipmentgroup.php?id=<?php echo $brandid;?>'+'&type=brandsupplier');
}
document.getElementById('attributes').innerHTML='';
jQuery('#attributes').load('get_codeinstance.php?cd=<?php echo $bcid;?>');
<?php 
	if($box !="")
	{
		for($i=1; $i<11;$i++)
		{
		?>
			document.getElementById('boxprice'+<?php print"$i";?>).value="";
			document.getElementById('boxprice'+<?php print"$i";?>).disabled=false;
			
	<?php
		}//for
		//print"HELLO";
	?>
		document.getElementById('barcode1').value	=	'<?php echo $bcid;?>';
	<?php
	}//if
if($siteconfig==1){//from main, edit by ahsan 15/02/2012
	$itempricedata	=	$AdminDAO->getrows("$dbname_detail.stock","costprice,retailprice","fkbarcodeid='$pkbarcodeid' ORDER BY pkstockid DESC LIMIT 0,1");
	$itemprice		=	$itempricedata[0]['retailprice'];
	$costprice		=	$itempricedata[0]['costprice'];
	$itemcounterprice	=	$AdminDAO->getrows("$dbname_detail.pricechange","price","fkbarcodeid='$pkbarcodeid'");
	$counterprice		=	$itemcounterprice[0]['price'];
?>
	document.getElementById('itemcostprice').innerHTML	=	'<?php echo $costprice;?>';
	document.getElementById('itemsaleprice').innerHTML	=	'<?php echo $itemprice;?>';
	document.getElementById('counterprice').innerHTML	=	'<?php echo $counterprice;?>';
<?php }//end edit
?>
</script>
<?php
if($box !="")
{
	$_SESSION['boxid']	=	$box;
}
else
{
	$_SESSION['boxid'] = '';
}
?>