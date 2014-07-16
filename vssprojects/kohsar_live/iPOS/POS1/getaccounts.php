<?php 
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
$qstring	=	trim(filter($_REQUEST['q'])," ");
if($qstring!='')
{
	$accountsarr	=	$AdminDAO->getrows("$dbname_detail.account","*","1 HAVING title LIKE '%$qstring%' AND status='1'");
	for($i=0;$i<count($accountsarr);$i++)
	{
		$actitle	=	$accountsarr[$i]['title'];
		$id			=	$accountsarr[$i]['id'];
		$limit		=	$accountsarr[$i]['accountlimit'];
		echo "$actitle|$id|$limit\n";
	}
}
?>