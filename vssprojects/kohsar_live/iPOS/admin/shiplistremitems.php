<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_GET['id'];
$remaining	=	$AdminDAO->getrows("shiplist","IF(purchasequantity-(SELECT SUM(reserved) FROM packinglist WHERE fkshiplistid='$id')IS NULL,purchasequantity,purchasequantity-(SELECT SUM(reserved) FROM packinglist WHERE fkshiplistid='$id')) as quantity","pkshiplistid='$id'");
$quantity	=	$remaining[0]['quantity'];
echo "<input type=\"text\" name=\"remquantity\" id=\"remquantity\" value=\"$quantity\" class=\"text\" onblur=\"checkmax(this.value)\" onfocus=\"this.select()\" />";
?>
<script language="javascript" type="text/javascript">
document.getElementById('maxquantity').value=<?php echo $quantity;?>;
</script>