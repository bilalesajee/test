<?php
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
// working on trade price
$pass		=	$AdminDAO->getrows("store","tppassword","pkstoreid='$storeid'");
$password	=	$pass[0]['tppassword'];
?>
<script language="javascript" type="text/javascript">
activatetpmode('<?php echo $password;?>');
</script>