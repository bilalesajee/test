<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$countryname	=	trim(filter($_GET['q'])," ");
$id				=	$_GET['id'];
$ccid			=	$_GET['cid'];
/****************************PRODUCT DATA*****************************/
$sql=" SELECT pkcountryid,code3
		FROM countries
			WHERE
				code3 LIKE '%$countryname%'
			";			
if($countryname!='')
{
	$country_array	=	$AdminDAO->queryresult($sql);
	for($a=0;$a<count($country_array);$a++)
	{
		$cname		=	html_entity_decode($country_array[$a]['code3']);
		$cid		=	$country_array[$a]['pkcountryid'];
		echo "$cname|typecountry|$cid|$ccid\n";
	}
}
?>