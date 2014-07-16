<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$productname	=	filter($_REQUEST['q']);
$bcid			=	filter($_REQUEST['bc']);
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$edit			=	filter($_REQUEST['edit']);
}//end edit
$item			=	filter($_REQUEST['item']);
/**********************************check for BOXED item***************************/
//get from barcode (boxbarcode WHERE barcode = $bcid)
//if size is 0 for boxbarcode: don't change barcode otherwise bcid = newly got boxbarcode
/*********************************************************************************/
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
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
	$fkbarcodeid		=	$boxbarcodes[0]['pkbarcodeid'];
	// checking stocks to fetch last purchase price
	// searching for different locations
	// Step 1 
	// Connecting to store db to retrieve access rights
	$stores				=	$AdminDAO->getrows("store","*","1");
	for($i=0;$i<sizeof($stores);$i++)
	{
		// retrieving store details
		$storename		=	$stores[$i]['storename'];
		$storedb		=	$stores[$i]['storedb'];
		$storeip		=	$stores[$i]['storeip'];
		$storeuser		=	$stores[$i]['username'];
		$storepwd		=	$stores[$i]['password'];
		mysql_connect($storeip,$storeuser,$storepwd) or die("Could not connect to store ".$storename);
		mysql_select_db($storedb) or die("Could not select store database ".$storename);
		$storestock		=	"SELECT pkcurrencyid,MAX(purchaseprice) as pprice,currencysymbol FROM $storedb.stock,main.shipment,main.currency WHERE fkbarcodeid='$fkbarcodeid' AND pkcurrencyid=shipmentcurrency AND fkshipmentid=pkshipmentid GROUP BY fkbarcodeid";
		$results		=	mysql_query($storestock);
		while($spricedata	=	mysql_fetch_assoc($results))
		{
			$spricecurrency		=	$spricedata[0]['currencysymbol'];
			$lastpurchaseprice	=	$spricedata[0]['pprice'];
			$currencyid			=	$spricedata[0]['pkcurrencyid'];
			?>
			<script language="javascript">
				document.getElementById('currency'+<?php echo $i;?>).innerHTML="<?php echo $spricecurrency; ?>";
				document.getElementById('lastpprice'+<?php echo $i;?>).value="<?php echo $lastpurchaseprice; ?>";
				document.getElementById('currencyid'+<?php echo $i;?>).value="<?php echo $currencyid;?>";
			</script>
			<?php
		}
	}
	//$fkbarcodeid		=	$boxbarcodes[0]['pkbarcodeid'];
	//$spricedata			=	$AdminDAO->getrows("$dbname_detail.stock,shipment,currency","pkcurrencyid,MAX(purchaseprice) as pprice,currencysymbol","fkbarcodeid='$fkbarcodeid' AND pkcurrencyid=shipmentcurrency AND fkshipmentid=pkshipmentid GROUP BY fkbarcodeid");
	/*echo "<pre>";
	print_r($spricedata);
	echo "</pre>";
	exit;*/
	$spricecurrency		=	$spricedata[0]['currencysymbol'];
	$lastpurchaseprice	=	$spricedata[0]['pprice'];
	$currencyid			=	$spricedata[0]['pkcurrencyid'];
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$sql	= "SELECT pkbrandid,brandname,itemdescription as PRODUCTNAME, pkbarcodeid as bc
				FROM 
					barcode,barcodebrand bb,brand
				WHERE 
					barcode = '$bcid' AND pkbarcodeid = fkbarcodeid AND bb.fkbrandid = pkbrandid
			";
	$bcflag	=	0;
	if($bcid!='')
	{
		$barcode_array	=	$AdminDAO->queryresult($sql);
		if(sizeof($barcode_array)<1)
		{
			?>
			<script language="javascript" type="text/javascript">
				adminnotice('This Item does not exist',0,5000);
			</script>
			<?php
		}
		else
		{
			$pkbarcodeid	=	$barcode_array[0]['bc'];
			//$itembarcode	=	$barcode_array[0]['itembarcode'];
			$productname	=	$barcode_array[0]['PRODUCTNAME'];
						
			$brandname		=	$barcode_array[0]['brandname'];
			$brandid		=	$barcode_array[0]['pkbrandid'];
								
			/*if($productname=='') //case when productname is not available in barcode table
			{
				$itemnamedata	=	$AdminDAO->getrows("`order`","itemdescription,description,defaultimage,clientinfo","barcode='$bcid' LIMIT 0,1");
				$productname	=	$itemnamedata[0]['itemdescription'];
				$description	=	$itemnamedata[0]['description'];
				$defaultimage	=	$itemnamedata[0]['defaultimage'];
				$clientinfo		=	$itemnamedata[0]['clientinfo'];
				
			}*/
		}
	}
}//end edit
?>
<script language="javascript">
	document.getElementById('itemdescription').value="<?php echo $productname; ?>";
<?php	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition?>
	document.getElementById('lastpurchaseprice').innerHTML="<?php echo $lastpurchaseprice; ?>";
<?php	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012?>
	document.getElementById('barcodeid').value="<?php echo $pkbarcodeid; ?>";
	document.getElementById('brand').value="<?php echo $brandname;?>";
	document.getElementById('brandid').value="<?php echo $brandid;?>";
<?php }//end edit?>
	document.getElementById('quantity').focus();
</script>