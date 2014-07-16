<?php 
include("includes/security/adminsecurity.php");
global $AdminDAO;
$groupid	=	$_SESSION['groupid'];
  $sql="select 
				ua.* 
			from 
				useralerts ua  LEFT JOIN $dbname_detail.useralertstatus
				ON ua.pkuseralertid=fkuseralertid AND fkaddressbookid='$empid' 
				
			WHERE 
				ua.moduleid='2' and 
				ua.groupid='$groupid' AND 
				
				fkuseralertid IS NULL AND
				fkaddressbookid IS NULL
				
				order by  ua.pkuseralertid 	DESC";

	$alertsarr	=	$AdminDAO->queryresult($sql);

$totalalerts	=	sizeof($alertsarr);

if($totalalerts>0)
{
?>
 <h3 style="padding:3px;"><?php echo $totalalerts; ?>: Alerts</h3>
    <?php 
	//useralertstatus
	
	for($a=0;$a<count($alertsarr);$a++)
	{
		$pkuseralertid	=	$alertsarr[$a]['pkuseralertid'];
		$title			=	$alertsarr[$a]['title'];
		$datetime		=	$alertsarr[$a]['datetime'];
	?>
    	<li id="useraltersli"><a href="javascript:void(0)" onclick="readalert('<?php echo $pkuseralertid;?>');" ><?php echo $a+1;?>. <?php echo $title;?></a></li>
    <?php
	}
}//end of if
?>

<script language="javascript">
	function readalert(alertid)
	{
		//alert();
		//jQuery('#useraltersli'+alertid).hide();
		jQuery('#main-content').load('alertdetail.php?alertid='+alertid);
	}
</script>