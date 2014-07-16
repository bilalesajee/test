<?php


$dbname_server = $server_db = 'main_kohsar';
////////////////////////////////////////////////////////////////		  
$server_local = 'localhost';
$server_user_local = 'root';
$server_pwd_local = '';
////////////////////////////////////////////////////////////////


$dbh_local = new mysqli($server_local, $server_user_local, $server_pwd_local, $dbname_server);
$query = " insert into syncheck (caltime) values ('".time()."') ";
if(!$result_ = $dbh_local->query($query)){

    $log->error('Error Inserting File log ',$dbh_local->error);
			 
}?>