<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$productid	=	$_REQUEST['id'];
if($productid!='-1')
{
?>
	<script language="javascript" type="text/javascript">
		loadsection('sugrid','addinstance.php?param=<?php echo $productid;?>');
    </script>
<?php		
}
else
{
$selected_product	=	array($productid);
$product_array		=	$AdminDAO->getrows("product","pkproductid,productname", " productdeleted<>1 ORDER BY productname ASC ");
$products			=	$Component->makeComponent('d','id',$product_array,'pkproductid','productname',1,$selected_product,"onchange=javascript:loadsection('subsection','addinstance.php?param='+this.value)");
?>
<form name="abc">
	<?php
    	echo $products;
	?>
</form>
<?php
}//else
?>