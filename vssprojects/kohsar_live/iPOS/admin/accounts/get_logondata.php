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

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////Invoice query///////////////////////////////////////////////////////////////////////////////////
$query_getdata = "SELECT  * from $dbname_detail.loginhistory where  FROM_UNIXTIME(logintime,'%d-%m-%Y') = '$date' ";
$result = $AdminDAO->queryresult($query_getdata);
$row_run_log=count($result);
for($i=0;$i<$row_run_log;$i++)
        {
	     /*$arr_loghistory[$i]['logintime']=$result[$i]['logintime'];
	     $arr_loghistory[$i]['ipaddress']=urlencode($result[$i]['ipaddress']);
	     $arr_loghistory[$i]['referrallink']=urlencode($result[$i]['referrallink']);
	     $arr_loghistory[$i]['browserinfo']=urlencode($result[$i]['browserinfo']);
	     $arr_loghistory[$i]['logtype']=$result[$i]['logtype'];
	     $arr_loghistory[$i]['username']=$result[$i]['username'];
	     $arr_loghistory[$i]['loc']=$result[$i]['loc'];
		 $arr_loghistory[$i]['loc_counter']=$result[$i]['loc_counter'];
	     $arr_loghistory[$i]['sess_id']=urlencode($result[$i]['sess_id']);
		 $arr_loghistory[$i]['logouttime']=$result[$i]['logouttime'];
		 $arr_loghistory[$i]['fkaddressbookid']=$result[$i]['fkaddressbookid'];
*/
 file_get_contents("https://main.esajee.com/admin/accounts/insertloginhistory.php?fkaddressbookid=".$result[$i]['fkaddressbookid']."&logintime=".$result[$i]['logintime']."&username=".urlencode($result[$i]['username'])."&loc=".$result[$i]['loc']."&logouttime=".$result[$i]['logouttime']);
         }
		 file_get_contents("https://dha.esajee.com/admin/accounts/get_logondata.php");	 
         file_get_contents("https://gulberg.esajee.com/admin/accounts/get_logondata.php");	 
        file_get_contents("https://pharmadha.esajee.com/admin/accounts/get_logondata.php");
        file_get_contents("https://warehouse.esajee.com/admin/accounts/get_logondata.php");	

		 //echo "<pre>";
		 //print_r($arr_loghistory);
//		 echo file_get_contents("https://main.esajee.com/admin/accounts/insertloginhistory.php?logindata=$logdata");

?>