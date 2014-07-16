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
  $date=time();
  $date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
    $Bar=$_REQUEST['barcode'];
	$Pho=$_REQUEST['mobile'];
	if($Bar!='' and $Pho!=''){
	 $que = "SELECT * from esajeesms.messages_queue where  fkbarcodeid='$Bar' and  phoneno='$Pho'";
     $repor = $AdminDAO->queryresult($que);
     $row_=count($repor);
	  if($row_==0){
	  $msg="Hello Dear Customer, We have just updated our stock with your favourite items.
       Please visit out store at your earliest to fetch your request while the stock lasts.
	   Thank you.
	   Esajee & Co. -- Esajee & Co Warehouse";
	   
      $query = "insert into esajeesms.messages_queue (phoneno,msg,fkbarcodeid,datetime) values ('$Pho','$msg','$Bar','".time()."')  ";
      $AdminDAO->queryresult($query);
	  }
	}
?>