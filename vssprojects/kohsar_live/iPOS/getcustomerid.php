<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
$saleid	=	$_GET['saleid'];
$customerids	=	$AdminDAO->getrows("$dbname_detail.sale","fkaccountid","pksaleid='$saleid'");
$customerid		=	$customerids[0]['fkaccountid'];
?>
<script language="javascript" type="text/javascript">
customerbillprint('<?php echo $saleid;?>','<?php echo $customerid;?>');
function customerbillprint(sid,cid)
{
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=600,left=100,top=25';
	window.open('generatecreditorreport.php?tempsaleid='+sid+'&customerid='+cid,'Invoice',display); 
}
</script>