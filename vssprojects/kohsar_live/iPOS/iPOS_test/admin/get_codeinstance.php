<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$barcode	=	filter($_REQUEST['cd']);
$bc			=	filter($_REQUEST['bc']);
if($bc!='')
{
	$barcode=$bc;
}
$storeid	=	$_SESSION['storeid'];
$empid		=	$_SESSION['employeeid'];	
/****************************PRODUCT DATA*****************************/
$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `barcode`='$barcode'");
$productname 		=	$barcode_array[0]['productname'];
$productid	 		=	$barcode_array[0]['pkproductid'];
$pkbarcodeid 		=	$barcode_array[0]['pkbarcodeid'];
$productdescription =	$barcode_array[0]['productdescription'];
/***********************************Attributes DATA*************************/
if($productid!='')
{
	$attributes_array	=	$AdminDAO->getrows('productattribute,attribute,productinstance ','*',"`pkattributeid`=`fkattributeid` AND `fkproductid`='$productid' AND fkproductattributeid=pkproductattributeid AND fkbarcodeid='$pkbarcodeid' GROUP BY fkattributeid",'attributeposition');
	$proinstance_array	=	$AdminDAO->getrows('productinstance','*',"`fkbarcodeid`='$pkbarcodeid' ");
}

if ($barcode=='')
{
/*if (sizeof($proinstance_array)==0 && $bc=='')
{*/
/*echo "<script>alert('here i am $bc')</script>";*///comment added by ahsan 15/02/2012
	
	?>
<script language="javascript" type="text/javascript">
  			jQuery('#productdiv').load('proinstancefrm.php?bc=<?php echo $barcode;?>');
</script>
  <div id="productdiv">
  </div>
	<?php
	exit;
}

$shipment_array			=	$AdminDAO->getrows('shipment','pkshipmentid, shipmentname '," fkstoreid='$storeid'");
$shipment				=	$Component->makeComponent("d","shipment",$shipment_array,"pkshipmentid","shipmentname",1,$selected_shipment,'onchange=getshipmentgroup(this.value)');

$brands_array			=	$AdminDAO->getrows('brand, barcodebrand ',' pkbrandid, brandname '," branddeleted<>'1' AND fkbrandid=pkbrandid AND fkbarcodeid='$pkbarcodeid' ");
$brands				=	$Component->makeComponent("d","brands",$brands_array,"pkbrandid","brandname",1,$selected_brands,'onchange=getbrandsupplier(this.value)');

?>
<div id="error" class="notice" style="display:none"></div>
<table>
<tr>
<th>Attribute Name</th>
<th>Options</th>
</tr>
 <?php
for($i=0; $i<sizeof($attributes_array); $i++)
{
	//var_dump($attributes_array[$i]);
	$attributename[] 	=	$attributes_array[$i]['attributename'];
	$attributetype[]	=	$attributes_array[$i]['attributetype'];
	$attributeids[]		=	$attributes_array[$i]['pkproductattributeid'];
	$options_array	=	$AdminDAO->getrows('productinstance,attributeoption,barcode ','*',"`fkproductattributeid`='$attributeids[$i]' AND `fkattributeoptionid`=`pkattributeoptionid` AND fkbarcodeid=pkbarcodeid AND barcode='$barcode'");
	?>
<tr>
<td><input type="hidden" name="productattributeid[]" value="<?php echo $attributes_array[$i]['pkproductattributeid'];?>" />
				 	<?php echo $attributes_array[$i]['attributename'];?></td>

<td>
 <select name="<?php  echo "attribute".'_'."$attributeids[$i]"; ?>" class="eselect">
        <?php
			
			for ($j=0; $j<count($options_array); $j++)
			{
				$attributeoptionname	=	$options_array[$j]['attributeoptionname'];	
				?>
        <option  value="<?php echo $options_array[$j]['pkattributeoptionid'];?>"><?php print"$attributeoptionname";?></option>
       		<?php
			}
		?>
        </select>
</td>
</tr>
<?php
}
?>
</table>