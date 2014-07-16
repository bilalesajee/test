

<?php

 ob_start();
error_reporting(0); 
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
	
include("../../includes/security/adminsecurity.php");

global $AdminDAO, $Component;
 $jsondata=json_decode($_GET['jsondata'],true);

   $username=$jsondata['username'];
 $password=$jsondata['password'];

//////////admin////////////////
//$username='admin';
//$password='our#main';
///////////////////////
////////////////////user////////////////////////
//$username='esajee';
//$password='0d67207937f77e0400512ec9d01885a3';
//////////////////////////////////////////
/////////////////////wrong u and p//////////////////////
//$username='admin3333';
//$password='c4887e400636bdb1ddedd2';
//////////////////////////////////////////
//echo $addressbookid = $_SESSION['addressbookid'];
 $query			=	"SELECT a.pkaddressbookid, a.mobile_app_rights from addressbook a

 where a.username = '$username' AND a.password = md5('$password') ";
$result		=	$AdminDAO->queryresult($query);

//$w=count($result);


 if (($result[0]['pkaddressbookid']) > 0 ) { // evaluate the count
// $groupid	=	$result[0]['fkgroupid'];
/* $a['Save'] = 2;
$a['Search'] = 2;*/
 $mobile_app_rights =$result[0]['mobile_app_rights'];
echo $mobile_app_rights;
 }
 else
 {
	$b['Save'] = -1;
   $b['Search'] = -1;
   echo json_encode($b);
	 }
 /*
if($groupid == '6')
{
	//////////////////if admin is login////////////////////////////
	   echo '2';
	//////////////////////////////////////////////////////////////   
}
	    
else{
	////////////////////////////if user is login///////////////////////
echo '1' ;
///////////////////////////////////////////////////////////////////
	}
 }

else {
	////////////////////if wrong u and p login///////////////////

	echo '0' ;
	exit();
	//////////////////////////////////////////////////////////
	}
*/




?>
