<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$barcode		=	trim(filter($_REQUEST['code'])," ");
$productid		=	trim(filter($_REQUEST['productid'])," ");
$id				=	$_REQUEST['id'];
/****************************PRODUCT DATA*****************************/
if($barcode!='')
{
	$and=" AND `barcode`='$barcode'	";
}
elseif($productid!='')
{
	$and=" AND `pkproductid`='$productid'	";
}
if($barcode!='' || $productname!='')
{
	$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` $and ");
	$productname 		=	$barcode_array[0]['productname'];
	$productid	 		=	$barcode_array[0]['pkproductid'];
	$pkbarcodeid 		=	$barcode_array[0]['pkbarcodeid'];
	$barcode	 		=	$barcode_array[0]['barcode'];
	 $sql="SELECT pkstockid,date_format(expiry,'%D %b %Y') as expiry,unitsremaining,date_format(shipmentdate,'%D %b %Y') as shipmentdate
	 					FROM 
							stock, shipment
						WHERE 
							fkbarcodeid 	= '$pkbarcodeid' AND
							fkshipmentid	=	pkshipmentid
						ORDER BY expiry DESC
	";	
	
	$stockdata			=	$AdminDAO->queryresult($sql);
	if(sizeof($stockdata)>0)
	{
		?>
		
		<select <?php if($id!="") echo "name=\"expiry2[]\" id=\"expiry2\""; else echo "name=\"expiry[]\" id=\"expiry\""; ?> multiple="multiple" size="5">
		<?php
		for($s=0;$s<count($stockdata);$s++)
		{
			?>
			<option value="<?php echo $stockdata[$s]['pkstockid'];?>" >
			<?php
			echo "(".$stockdata[$s][shipmentdate].")  Expiry - ".$stockdata[$s][expiry];
			?>
			</option>
			<?php
		}
		?>
		</select>
 <?php
	}
 else
 {
	 ?>
     <input type="text" name="expiry" id="expiry" readonly="readonly" />
     <?php
 }
}//end of if barcode
?>
