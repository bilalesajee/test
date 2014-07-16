<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$brandname		=	trim(filter($_GET['q'])," ");
$id				=	$_GET['id'];
$ccid			=	$_GET['cid'];
/****************************PRODUCT DATA*****************************/
$sql=" SELECT pkbrandid,brandname,countryname 
		FROM `brand` ,`countries`
			WHERE
				fkcountryid=pkcountryid AND
				brandname LIKE '%$brandname%'
			";			
if($brandname!='')
{
	$brand_array	=	$AdminDAO->queryresult($sql);
	for($a=0;$a<count($brand_array);$a++)
	{
		$bname		=	html_entity_decode($brand_array[$a]['brandname']);
		$country	=	" ".html_entity_decode($brand_array[$a]['countryname']);
		$bid		=	$brand_array[$a]['pkbrandid'];
		echo "$bname,$country|typebrand|$bid|$ccid\n";
	}
}
?>