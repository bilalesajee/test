<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$clientname		=	trim(filter($_GET['q'])," ");
$id				=	$_GET['id'];
$ccid			=	$_GET['cid'];
/****************************PRODUCT DATA*****************************/
$sql=" SELECT firstname,lastname,pkcustomerid FROM `customer` 
				inner join addressbook 
				on pkaddressbookid=fkaddressbookid
			WHERE
				firstname LIKE '%$clientname%' OR lastname LIKE '%$clientname%'
			";	
/*$sql=" SELECT name FROM `client` 
			WHERE
				firstname LIKE '%$clientname%' OR lastname LIKE '%$clientname%'
			";		*/	//attempted by jafer leaved because of short time						
if($clientname!='')
{
	$client_array	=	$AdminDAO->queryresult($sql);
	for($a=0;$a<count($client_array);$a++)
	{
		$cname		=	html_entity_decode($client_array[$a]['firstname']). " " . html_entity_decode($client_array[$a]['lastname']);
		$cid		=	$client_array[$a]['pkcustomerid'];
		echo "$cname|typeclient|$cid|$ccid\n";
	}
}
?>