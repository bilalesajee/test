<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_REQUEST['id'];
$err	=	0;
if($id)
{
	$userid	=	$_SESSION['addressbookid'];	
	//checking consignmentitem owner
	$rows	=	$AdminDAO->getrows("consignmentdetail","1","fkaddressbookid='$userid' AND fkconsignmentid='$id'");
	if(sizeof($rows)>0)
	{
		//checking current status
		$constatus		=	$AdminDAO->getrows("consignment","fkstatusid","pkconsignmentid='$id'");
		$currentstatus	=	$constatus[0]['fkstatusid'];
		if($currentstatus!=1)
		{
			$err	=	1;
		}
		else
		{
			$f	=	array('fkstatusid');
			$v	=	array(9);
			$AdminDAO->updaterow("consignment",$f,$v,"pkconsignmentid='$id'");
		}
	}
	else
	{
		$err	=	1;
	}
}
if($err==1)
{
?>
<script language="javascript" type="text/javascript">
alert('Either this movement is not pending or you do not have permission to update it.');
</script>
<?php
}
else
{
?>
<script language="javascript" type="text/javascript">
$('#maindiv').load('manageconsignments.php');
</script>
<?php
}
?>