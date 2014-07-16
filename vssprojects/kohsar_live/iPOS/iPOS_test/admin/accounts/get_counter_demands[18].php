<?php
set_time_limit(0);
$date=time();
 $date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));

$dbh_server = new mysqli('localhost', 'posapp', 'posapp' , 'main_kohsar');
	if($dbh_server->connect_errno > 0){
		
	
	echo $dbh_server->connect_error;
	
	}else{
		
$ips = array(1 => 32 , 2 => 132, 3 => 130);
////////////////////////////////////////////////////////////////		  
//$server = 'localhost';
$server_user = 'esajeeagent';
$server_pwd = 'esajeeagent';
$server_db = 'main_kohsar';
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
	  $q1 = "SELECT * FROM itemdemands WHERE  FROM_UNIXTIME(addtime,'%d-%m-%Y') = '$date'";
	 $result=$dbh->query($q1);

	    while($row=$result->fetch_assoc()){
			
	
			  $q2 = "INSERT INTO itemdemands (fkaccountid,fkaddressbookid,datetime,status,customer,mobile,addtime,remarks,counter_,pkid,counter_ip) VALUES ('{$row['fkaccountid']}','{$row['fkaddressbookid']}','{$row['datetime']}','{$row['status']}','{$row['customer']}','{$row['mobile']}','{$row['addtime']}','{$row['remarks']}','{$row['counter_']}','{$row['itemdemandsid']}','{$server}') ";
	
	     $dbh_server->query($q2);
			
			}
		
		
		  $dbh->close();
		
	
	
	}
	}
	$dbh_server->close();
		}
	

?>