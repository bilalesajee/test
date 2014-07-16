<?php

session_start();

include_once("../includes/security/adminsecurity.php");



$q	=	$_GET['q'];

/*$arr2[]=array("id"=>"1","name"=>"Waqas");

$arr2[]=array("id"=>"2","name"=>"Waqar Ahmad");

$arr2[]=array("id"=>"3","name"=>"Just Abc");

$arr2[]=array("id"=>"4","name"=>"Zia Khan");

$arr2[]=array("id"=>"5","name"=>"Asad Ali");

$arr2[]=array("id"=>"6","name"=>"Amjad Iqbal Khan");

$arr2[]=array("id"=>"7","name"=>"Umar Hayat Khan");

$arr2[]=array("id"=>"8","name"=>"Rizwan Abbas");*/

$sql="select * from main.brand  where brandname like '%$q%' order by brandname ASC limit 0,20";

$disce	=	$AdminDAO->queryresult($sql);

$p=0;

$rsixe=count($disce);

while($p<$rsixe)

{

	

$arr[]=array("id"=>$disce[$p][pkbrandid],"name"=>"(".$disce[$p][pkbrandid].")-".$disce[$p][brandname]);

$p++;

}

echo json_encode($arr);



?>