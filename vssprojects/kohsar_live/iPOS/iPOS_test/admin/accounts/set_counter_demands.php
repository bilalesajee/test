<?php
set_time_limit(0);
		
////////////////////////////////////////////////////////////////		  
//$server = 'localhost';
$server_user = 'esajeeagent';
$server_pwd = 'esajeeagent';
$server_db = 'main_kohsar';
////////////////////////////////////////////////////////////////


$reas=urldecode($_GET['reason']);
	 $server =$_GET['cip'];
	
	//$dbh = new mysqli($server, 'esajeeagent', 'esajeeagent', 'main_kohsar');
        $dbh = new mysqli($server, $server_user, $server_pwd , $server_db);
	
	//$dbh = new mysqli('localhost', 'root', '', 'kohsar');
	if($dbh->connect_errno > 0){

	echo $dbh->connect_error;

}else{
	  $q1 = "update itemdemands set status='{$_GET['status']}',reason='$reas' WHERE  itemdemandsid = '{$_GET['pkid']}'";
	 $result=$dbh->query($q1);

	   
		
		  $dbh->close();
		
	
	
	}
	
	

?>