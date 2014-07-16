<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$bcid	=	filter($_REQUEST['bc']);
$it		=	filter($_REQUEST['it']);
$pro	=	filter($_REQUEST['pro']);
$bcx	=	filter($_REQUEST['bcid']);
$sql	= "SELECT itemdescription as PRODUCTNAME, pkbarcodeid as bc, barcode as itembarcode
			FROM 
				barcode
			WHERE 
				barcode = '$bcid'
		";
if($bcid!='')
{
	$barcode_array	=	$AdminDAO->queryresult($sql);
	if(sizeof($barcode_array)<1)
	{
		?>
        <script language="javascript" type="text/javascript">
			adminnotice('<li>This Item does not exist</li>',0,5000);
		</script>
        <?php
	}
	else
	{
		$pkbarcodeid		=	$barcode_array[0]['bc'];
		$itembarcode		=	$barcode_array[0]['itembarcode'];
		$productname		=	$barcode_array[0]['PRODUCTNAME'];
	}
}
// checking stocks to fetch last purchase price
$fkbarcodeid		=	$boxbarcodes[0]['pkbarcodeid'];
?>
<script language="javascript">
	document.getElementById('<?php echo $pro;?>').value="<?php echo $productname; ?>";
	document.getElementById('<?php echo $bcx;?>').value="<?php echo $pkbarcodeid;?>";
	document.getElementById('<?php echo $it;?>').focus();
</script>