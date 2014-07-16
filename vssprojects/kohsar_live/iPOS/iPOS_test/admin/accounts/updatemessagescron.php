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
  $dbname_detail='main_kohsar';
  
////////////////////////////////////////////////////////Connecting SMS server///////////////////////////////
//$query = "SELECT cs.mobile from $dbname_detail.stock st,$dbname_detail.itemdemands it,main.customer cs where st.unitsremaining >= it.quantity and FROM_UNIXTIME(st.addtime,'%d-%m-%Y') = '$date' and st.fkbarcodeid=it.fkbarcodeid and it.fkaccountid=pkcustomerid ";

// $query = "SELECT cs.mobile,st.fkbarcodeid,sum(st.unitsremaining) as remu,it.quantity reqq from $dbname_detail.stock st,$dbname_detail.itemdemands it,main.customer cs where  st.fkbarcodeid=it.fkbarcodeid and it.fkaccountid=pkcustomerid and st.unitsremaining > 0 and it.status='Pending' group by it.fkbarcodeid  ";
 $query = "SELECT it.mobile,st.fkbarcodeid,sum(st.unitsremaining) as remu,it.quantity reqq from $dbname_detail.stock st,$dbname_detail.itemdemands it  where  st.fkbarcodeid=it.fkbarcodeid  and st.unitsremaining > 0 and it.status='Pending'  group by it.fkbarcodeid,it.fkaccountid  ";

$reportresult = $AdminDAO->queryresult($query);
$row_run=count($reportresult);
for($i=0;$i<$row_run;$i++)
{
      
	  if($reportresult[$i]["reqq"]<=$reportresult[$i]["remu"]){
	  
	  $Pho=$reportresult[$i]["mobile"];
	  $Bar=$reportresult[$i]["fkbarcodeid"];
	  if($Pho!=''){
     
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
	  }
}
 //file_get_contents("https://dha.esajee.com/admin/accounts/updatemessagescron.php");	 
 //file_get_contents("https://gulberg.esajee.com/admin/accounts/updatemessagescron.php");	 
 
 
?>