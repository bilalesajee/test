<?php ob_start();
error_reporting(-1); 
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
  global $AdminDAO;
  $Pho=$_REQUEST['mobino'];
  $msg=urldecode($_REQUEST['message']); 
  $start_date = $_REQUEST['date'];
  if($start_date==''){
  $date=time();
  }else{
  $date=	strtotime($start_date.'00:00:00'); 
  }
////////////////////////////////////////////////////////Connecting SMS server///////////////////////////////
      $query = "insert into esajeesms.messages_queue (phoneno,msg,datetime) values ('$Pho','$msg','$date')  ";
      $AdminDAO->queryresult($query);
 
?>