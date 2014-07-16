<?php
set_time_limit(0);
$date=time();
$date	=	date('d-m-Y', $date);
include("serverinfo_.php");


$dbh_server = new mysqli($kohsar_server,$kohsar_server_user, $kohsar_server_pwd , $kohsar_server_db);
	if($dbh_server->connect_errno > 0){
		
	
	echo $dbh_server->connect_error;
	
	}else{
		
$ips = array(1 => 32 , 2 => 132, 3 => 130);
////////////////////////////////////////////////////////////////		  
//$server = 'localhost';
/*$server_user = 'esajeeagent';
$server_pwd = 'esajeeagent';
$server_db = 'main_kohsar';*/
////////////////////////////////////////////////////////////////


foreach($ips as $counter => $ippart)
{
	 $server = "192.168.10.{$ippart}";
	
	//$dbh = new mysqli($server, 'esajeeagent', 'esajeeagent', 'main_kohsar');
        $dbh = new mysqli($server, $server_user, $server_pwd , $server_db);
	
	//$dbh = new mysqli('localhost', 'root', '', 'kohsar');
	if($dbh->connect_errno > 0){

	echo $dbh->connect_error;

}else{
/////////////////////////////////////////////////////Checking row syn or not//////////////////////////////////////	  
	  $q1 = "SELECT * FROM itemdemands WHERE  issent=0";
	  $result=$dbh->query($q1);
///////////////////////////////////////////////////////////////////////////////////////////////////////
	    while($row=$result->fetch_assoc()){
			
			  $q23 = "select * from itemdemands  where pkid='{$row['itemdemandsid']}' and counter_='{$row['counter_']}'";
	          $result=$dbh_server->query($q23);
              $row_cnt = $result->num_rows;
	   
	         if($row_cnt==0){
			 $q2 = "INSERT INTO itemdemands (fkaccountid,fkaddressbookid,datetime,status,customer,mobile,addtime,remarks,counter_,pkid,counter_ip) VALUES ('{$row['fkaccountid']}','{$row['fkaddressbookid']}','{$row['datetime']}','{$row['status']}','{$row['customer']}','{$row['mobile']}','{$row['addtime']}','{$row['remarks']}','{$row['counter_']}','{$row['itemdemandsid']}','{$server}') ";
	          $dbh_server->query($q2);
	/////////////////////////////////////////////////////////////////////////////////////////////		 
			 $q1 = "update itemdemands set issent=1  WHERE itemdemandsid='{$row['itemdemandsid']}' ";
	         $result=$dbh->query($q1);
	///////////////////////////////////////////////////	//////////////////////////////////////	 
			 }
			}
		
		
		  $dbh->close();
		
	
	
	}
	}
	$dbh_server->close();
		}
	

?>