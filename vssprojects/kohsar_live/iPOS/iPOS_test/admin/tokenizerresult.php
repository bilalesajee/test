<?php
include_once("../includes/security/adminsecurity.php");
$qry	=	$_GET['qry'];
$field	=	$_GET['field'];
$q		=	$_GET['q'];
if($field!='' && $q!='' && $qry!='')
{
	$and		=	" where $field LIKE '%%$q%%' ";
	$qry		=	$qry.''.$and;
	$resarray	=	$AdminDAO->queryresult($qry);
}

for($i=0;$i<sizeof($resarray);$i++)
{
	$arr[]	=	array("id"=>$resarray[$i][$field],"name"=>$resarray[$i][$field]);
}
echo json_encode($arr);
?>