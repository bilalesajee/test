<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$barcode	=	$_GET['bc'];
$cntr = $_GET['cntr'];
$itemdesc	=	$AdminDAO->getrows("barcode","pkbarcodeid,itemdescription","barcode='$barcode'");
$itemdescrption	=	$itemdesc[0]['itemdescription'];
$pkbarcodeid	=	$itemdesc[0]['pkbarcodeid'];
?>
<script language="javascript" type="text/javascript">
document.getElementById('barcodeid').value	=	'<?php echo $pkbarcodeid;?>';
document.getElementById('itemdescription_'+<?php echo $cntr;?>).value	=	'<?php echo $itemdescrption;?>';
document.getElementById('btn2').focus();
</script>