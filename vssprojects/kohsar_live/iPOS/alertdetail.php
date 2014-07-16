<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
$alertid	=	$_GET['alertid'];
$groupid	=	$_SESSION['groupid'];
$sql="select * from useralerts WHERE moduleid='2' and groupid='$groupid' and pkuseralertid='$alertid' ";
$alertsarr	=	$AdminDAO->queryresult($sql);
$title			=	$alertsarr[0]['title'];
$datetime		=	$alertsarr[0]['datetime'];
$description	=	$alertsarr[0]['description'];
$sqlchkmsg="select pkuseralertstatusid from $dbname_detail.useralertstatus where fkaddressbookid='$empid' and fkuseralertid='$alertid'";//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
$msgstatus	=	$AdminDAO->queryresult($sqlchkmsg);
if(sizeof($msgstatus)<1)
{
	$sqlupdate="insert into $dbname_detail.useralertstatus set fkaddressbookid='$empid',fkuseralertid='$alertid',updatetime='".time()."'";//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	$AdminDAO->queryresult($sqlupdate);
	?>
    <script language="javascript">
		jQuery('#useralertsdiv').load('useralerts.php');
    </script>
	<?php
}
?>
<div id="alertdetail">
 <h3 style="padding:3px;"><?php echo $title;?> <span style="font-size:10px"> On:<?php echo $datetime;?></span></h3>
 <span style="padding:3px;">
<?php
	echo $description;
?>
</span>
</div>