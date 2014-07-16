<?php
session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
	$_SESSION['addressbookid']=$_GET['empid'];
	
include("../../includes/security/adminsecurity.php");

global $AdminDAO,$Component,$qs;

$np		=	$_REQUEST['np'];

$bcid	=	$_REQUEST['bcid'];


 $changetimeu = time();
$fkaddressbookid	=	$_SESSION['addressbookid'];
if($np > 0){

	$barcodeid	=	$bcid;
	$newprice	=	$np;

	$fields	=	array('price','fkbarcodeid','inserttime','pupdatetime');
	$values	=	array($np,$bcid,$changetimeu,$changetimeu);

	

		$pricechanges	=	$AdminDAO->getrows("$dbname_detail.pricechange","pkpricechangeid,countername,price","fkbarcodeid='$bcid'");
		$pricechangeid	=	$pricechanges[0]['pkpricechangeid'];
		$changeprice	=	$pricechanges[0]['price'];

	
$fieldsu	=	array('price','fkbarcodeid','pupdatetime');
$valuesu	=	array($np,$bcid,$changetimeu);	

if($pricechangeid > 0){
////////////////////////////////Update///////////////////////////////////////////////////////////////////////////////////////////////////// 
$AdminDAO->updaterow("$dbname_detail.pricechange",$fieldsu,$valuesu," pkpricechangeid='$pricechangeid'  ");
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
}else{
/////////////////////////////////////Insert/////////////////////////////////////////////////////////////////////////////////	
$pcid=$AdminDAO->insertrow("$dbname_detail.pricechange",$fields,$values);	
$pricechangeid=$pcid;
$changeprice=$newprice;
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					
}
	

    $fieldsh	=	array('fkpricechangeid','fkaddressbookid','updatetime','oldprice');
	$valuesh	=	array($pricechangeid,$_SESSION['addressbookid'],time(),$changeprice);
	$pcidc	=	$AdminDAO->insertrow("$dbname_detail.pricechangehistory",$fieldsh,$valuesh);

 $url_insert = ('https://kohsar.esajee.com/admin/accounts/local_mob_prc_change.php?fkpricechangeid='.$pricechangeid.'&fkaddressbookid='.$fkaddressbookid.'&oldprice='.$changeprice.'&fkbarcodeid='.$bcid.'&price='.$np.'&countername=1');
  $recDataE = file_get_contents($url_insert);						

}



?>
