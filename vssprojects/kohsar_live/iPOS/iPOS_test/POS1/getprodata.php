<?php
@session_start();
include_once("includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$barcode		=	trim(filter($_REQUEST['code']));
$productid		=	filter($_REQUEST['productid']);
/****************************PRODUCT DATA*****************************/
if($barcode!='')
{
	$boxbarcode	=	$AdminDAO->getrows("barcode","boxbarcode"," barcode = '$barcode'");
	$boxbarcode	=	$boxbarcode[0]['boxbarcode'];
	if($boxbarcode!="")
	{
		$box		= 	$boxbarcode;
		$boxbarcode	=	$AdminDAO->getrows("barcode","barcode"," pkbarcodeid = '$boxbarcode'");
		$boxbarcode	=	$boxbarcode[0]['barcode'];
		$barcode	= 	$boxbarcode;
	}
	$and=" AND `barcode`='$barcode'	";

}
elseif($productid!='')
{
	$and=" AND `pkproductid`='$productid'	";
}
//echo $and;
if($barcode!='' || $productname!='')
{
	$barcode_array		=	$AdminDAO->getrows('barcode,product','productname,pkproductid,pkbarcodeid,barcode'," `fkproductid`=`pkproductid` $and ");
	//print_r($barcode_array);
	$productname 		=	$barcode_array[0]['productname'];
	$productid	 		=	$barcode_array[0]['pkproductid'];
	$pkbarcodeid 		=	$barcode_array[0]['pkbarcodeid'];	
	
	if($pkbarcodeid=='')
	{ // uncommented by Yasir -- 01-07-11
		echo "<script language=javascript>jQuery('#main-content').load('sale.php');notice('The barcode is not valid. Please enter new.','',5000);	
		document.getElementById('productname').focus();
		document.getElementById('productname').onkeyup='';
		</script>";
		exit;
	
	}
	$barcode	 		=	$barcode_array[0]['barcode'];
	$sql="SELECT pkstockid,
					IF (expiry>0,from_unixtime(expiry,'%D %b %Y'),'') as expiry, 
					expiry as exp,
					unitsremaining,
					from_unixtime(shipmentdate,'%D %b %Y') as shipmentdate
	 			FROM 
					$dbname_detail.stock LEFT JOIN shipment ON (fkshipmentid	=	pkshipmentid)
				WHERE 
					fkbarcodeid 	= '$pkbarcodeid'
				ORDER BY 
					pkstockid DESC
	";	
	/*echo $sql;
	
	$sql="SELECT pkstockid,
					IF (expiry>0,from_unixtime(expiry,'%D %b %Y'),'') as expiry, 
					expiry as exp,
					unitsremaining,
					from_unixtime(shipmentdate,'%D %b %Y') as shipmentdate
	 			FROM 
					$dbname_main.stock LEFT JOIN shipment ON (fkshipmentid	=	pkshipmentid)
				WHERE 
					fkbarcodeid 	= '$pkbarcodeid' AND
					unitsremaining  <> 0
				ORDER BY 
					exp ASC
	";	
	*/
	$stockdata			=	$AdminDAO->queryresult($sql);
	
	if(sizeof($stockdata)>0)
	{
	?>
    <script language="javascript">
    var qty=new Array();
	<?php
	$flag1	=	0;
	for($i=0;$i<count($stockdata);$i++)
	{
		$totalitem[$i]   =	 $stockdata[$i]['unitsremaining'];
		if($stockdata[$i]['unitsremaining']<=0)
		{
				continue;
		}
		else
		{
			$flag	=	1;
		
		?>
		qty['<?php echo $stockdata[$i]['expiry'].'_'.$stockdata[$i]['pkstockid'];?>']=<?php echo $stockdata[$i]['unitsremaining'];
		?>;
   		
				
	<?php
		}
	}
	if(!$flag1)
	{
		?>
		qty['<?php echo $stockdata[$i-1]['expiry'].'_'.$stockdata[$i-1]['pkstockid'];?>']=<?php echo $stockdata[$i-1]['unitsremaining'];?>;
	<?php
	}
	?>
     </script>
	 <input type="hidden" value="" name="remqty" id="remqty" readonly="readonly" class="text" />
     <div id="expdivshowhide"  style="display:none;">
    <select name="exp" id="exp" class="selectbox" onchange="getqty(this.value)">
	<?php
	$flag	=	0;
	for($s=0;$s<count($stockdata);$s++)
	{
		if($stockdata[$s]['unitsremaining']<=0)
		{
			continue;
		}
		else
		{
			$flag	=	1;
		}
		//THIS IS ARRAY FOR SUM OF TOTAL ITEM IN STOCK
		$totalitem[$i]   =	 $stockdata[$i]['unitsremaining'];
		?>
		<option value="<?php echo $stockdata[$s][expiry].'_'.$stockdata[$s]['pkstockid'];?>">
		<?php
		if($stockdata[$s][expiry]!='')
		{
			echo " Expiry- ".$stockdata[$s][expiry];
		}
		else
		{
			echo " Arr- ".$stockdata[$s][shipmentdate];
		}
		?>
		</option>
		<?php
	}
	if(!$flag)
	{
		?>
        
        <option value="<?php echo $stockdata[$s-1][expiry].'_'.$stockdata[$s-1]['pkstockid'];?>"><?php echo $s;?></option>
        <?php
	}
	?>
	</select>
 	
 	<script language="javascript">
		//getqty(document.getElementById('exp').value);
		var valx	=	document.getElementById('exp').value;
		var qty2	=	qty[valx];	
//SETTING THE VALUE OF REMAINING STOCK
		document.getElementById('remqty').value='<?php  echo array_sum($totalitem)?>';
    </script>
    </div>
 <?php
/*$a=array(0=>"5",1=>"-15",2=>"25");
echo "the sume is".array_sum($a); 
var_dump($totalitem);
echo "THE NUMBBER OF ITEMS IN THIS ARRAY ARE".array_sum($totalitem);*/
	}
	else
	{
		$sql=" SELECT itemdescription,shortdescription, b.barcode as bc
			FROM 
				barcode b
			WHERE
				b.pkbarcodeid	='$pkbarcodeid'
		";
		$itemdata	=	$AdminDAO->queryresult($sql);
		$product	=	$itemdata[0]['shortdescription'];
		if($product=='')
		{
			$product	=	$itemdata[0]['itemdescription'];
		}
	?>
    <input type="hidden" value="" name="remqty" id="remqty" readonly="readonly" class="text" />
    <div div id="expdivshowhide2"  style="display:none;">
    	<script language="javascript">
			document.getElementById('productname').value='<?php echo $product;?>';
			document.getElementById('price').value='0';
			if(document.getElementById('saleprice'))
			{
				document.getElementById('saleprice').innerHTML='';
			}
        </script>
       <input type="radio" name="newstock" value="exp" checked="checked"/>Expiry
       <!--<input type="radio" name="newstock" value="ship"/>Arrival-->
       <!--<input type="text" name="exp" id="exp" readonly="readonly" size="10"/>-->
	   <?php
	   	$expiry	=	date('y-m-d');
		$exp	=	explode('-',$expiry);
	   	$expy	=	$exp[0];
		$expm	=	$exp[1];
		$expd	=	$exp[2];
	   ?>
       <input type="text" name="expd" id="expd" onkeypress="return isNumberKey(event)" size="1" maxlength="2" value="<?php echo $expd;?>"/>
       <input type="text" name="expm" id="expm" onkeypress="return isNumberKey(event)" size="1" maxlength="2" value="<?php echo $expm;?>"/>
       <input type="text" name="expy" id="expy" onkeypress="return isNumberKey(event)" size="1" maxlength="2" onblur="checkdate()" value="<?php echo $expy;?>" />
    <?php	
		// this gets the price from saledetail requested by hasnain
			//added by Riz & Co
			//23-12-2009
			/*$pricequery	=	"SELECT 
								sd.saleprice 
							FROM 
								$dbname_main.saledetail sd,
								$dbname_main.stock st,
								barcode b 
							WHERE
								st.fkbarcodeid		=	b.pkbarcodeid AND
								sd.fkstockid		=	pkstockid AND
								st.priceinrs		<>	sd.saleprice AND
								b.pkbarcodeid		=	'$pkbarcodeid' 
							ORDER BY 
								timestamp desc 
							LIMIT 0,1";*/
			$pricequery		=	"SELECT	
									price
								FROM
									$dbname_detail.pricechange
								WHERE
									fkbarcodeid	=	'$pkbarcodeid'  ORDER BY pkpricechangeid DESC
								LIMIT 0,1
								";
			$priceresult	=	$AdminDAO->queryresult($pricequery);
			$priceinrs		=	$priceresult[0]['price'];
		// this gets the price of last stock if this product have any stock
		//added by Riz
		//14-12-2009
		if(!$priceinrs)
		{
			$sqlstk=" SELECT 
					MAX(priceinrs) as priceinrs 
					FROM 
								$dbname_detail.stock
					WHERE
							 fkbarcodeid	='$pkbarcodeid'
			";
			$stkpricearr	=	$AdminDAO->queryresult($sqlstk);
			$priceinrs	=	$stkpricearr[0]['priceinrs'];
		}
		if($priceinrs>0)
		{
			?>
				<script language="javascript">
					document.getElementById('price').value='<?php echo $priceinrs;?>';
				</script>
			<?php
		}
	}
}//end of if barcode
//$brands_array			=	$AdminDAO->getrows('brand, barcodebrand ',' pkbrandid, brandname '," branddeleted<>'1' AND fkbrandid=pkbrandid AND fkbarcodeid='$pkbarcodeid' ");
//echo $brands				=	$Component->makeComponent("d","brands",$brands_array,"pkbrandid","brandname",1,$selected_brands);
?>
</div>
<script language="javascript">
jQuery().ready(function() 
{
	//$("#exp").datepicker({dateFormat: 'yy-mm-dd'});	
	getstock();
	//submitformsale();
	//var stockidarr	=	document.getElementById('exp').value;
	//var stockid	=	stockidarr.split('_');
	//jQuery('#instancediv').load('codeitem.php?code=<?php //echo $_GET['code'];?>&stockid='+stockid[1]);	
});
function getstock()
{
	if(document.getElementById('exp'))
	{
		var stockidarr	=	document.getElementById('exp').value;
		var stockid		=	stockidarr.split('_');
		jQuery('#instancediv').load('codeitem.php?pkbarcodeid=<?php echo $pkbarcodeid;?>&code=<?php echo $_GET['code'];?>&stockid='+stockid[1]);
	}
	else if(document.getElementById('expy'))
	{
		jQuery('#instancediv').load('codeitem.php?pkbarcodeid=<?php echo $pkbarcodeid;?>&code=<?php echo $_GET['code'];?>');
	}
}
function checkdate()
{
	yy	=	document.getElementById('expy').value;
	year	=	parseInt(yy)+2000;
	mm	=	document.getElementById('expm').value;
	dd	=	document.getElementById('expd').value;
	valid	=	dd+'-'+mm+'-'+year;
	if(!isValidDate(valid))
	{
		alert("The date you entered is not valid. Correct format is: day-month-year (dd-mm-yy)");
	}
}
if(document.getElementById('exp'))
{
	if(<?php echo (sizeof($stockdata));?> > 1)
	{
		//document.getElementById('exp').focus();
		document.getElementById('quantity').focus();
	}
	else
	{
		document.getElementById('quantity').focus();
	}
}
else
{
	<?php 
	if($pkbarcodeid!='')
	{
		?>
		if(document.getElementById('expd'))
		{
			//document.getElementById('expd').focus();
			document.getElementById('quantity').focus();
		}
		
	<?php
	}
	?>
}
</script>