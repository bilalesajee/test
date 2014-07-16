<?php
include_once("includes/security/adminsecurity.php");
//include_once("../surl.php");
$id	=	$_REQUEST['ids'];
$idarr	= 	explode(",", $id);
$is_close	=	$_REQUEST['is_close'];
/*echo "<pre>";
print_r($_GET);
echo "</pre>";*/
 $q = "select is_close from $dbname_detail.sale where pksaleid = '$idarr[1]'";
$rres = mysql_query($q);
$r=mysql_fetch_array($rres);
$status			=	$r['is_close'];

if($status == 1 and $is_close=='close')
{
	
	echo $msq= 'Status Allready Closed';
	exit;
	
}
	
	

	

 if($status == 0)
	{
	$st= '1';
	 $update= mysql_query("update  $dbname_detail.sale set is_close = '$st'  where  pksaleid = '$idarr[1]' ");
	 $msq= 'Status Changed From Open To Closed';

	}
	
		
 
  echo $msq;


?>