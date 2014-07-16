<?php

ini_set('display_errors', 0);
session_start();
 //$_SESSION['SERVER_ONLINE'] = 2;
  // echo 2;
//exit;
date_default_timezone_set('Asia/karachi');
include 'serverinfo.php';

$host = '192.168.10.110';
$port = 80;

$host_acc = '192.168.10.100';
$port_acc = 80;

$curr_tame=time();
//$port = 3306;
$waitTimeoutInSeconds = 5;

$fp = fsockopen($host, $port, $errCode, $errStr, $waitTimeoutInSeconds);
$dbh_cons = new mysqli($server_local, $server_user_local, $server_pwd_local);
if ($fp)
{
    if($_SESSION['SERVER_ONLINE']==2){
	
	$cklink="http://192.168.10.110/admin/accounts/slink.php?counter=1&uptime={$curr_tame}";
	$queryinsertserverlog="insert into main_kohsar.accounts_links (acc_link,kaltime) values ('$cklink','$curr_tame')";
    $dbh_cons->query($queryinsertserverlog);	
		}
	
	$_SESSION['SERVER_ONLINE'] = 1;
	/////////////////////////////////////////////////////////////////////////////////////
	$quer2run=$curr_tame-$_SESSION['reptame'];
	$quer2run=round(abs($quer2run) /60,2);
	if($quer2run > 15){
	$querygettamelog="select max(reptime) reptime from main.rep_check where 1";
    $result_utame=$dbh_cons->query($querygettamelog);	
	$row_utame = $result_utame->fetch_assoc();
	 
    $_SESSION['reptame']=$row_utame['reptime'];
	}
	$uptame=date('d-m-y H:i:s',$_SESSION['reptame']);
    $serverval=1;
	echo ($serverval.'+'.$uptame);
	//////////////////////////////////////////////////////////////////////////////////
	
    fclose($fp);
}
else
{
     
    $cklink="http://192.168.10.110/admin/accounts/slink.php?counter=1&downtime={$curr_tame}";
	$queryinsertserverlog="insert into main_kohsar.accounts_links (acc_link,kaltime) values ('$cklink','$curr_tame')";
    $dbh_cons->query($queryinsertserverlog);
    $_SESSION['SERVER_ONLINE'] = 2;
	
	//////////////////////////////////////////////////////////////////////
	$quer2run=$curr_tame-$_SESSION['reptame'];
	$quer2run=round(abs($quer2run) /60,2);
	if($quer2run > 15){
	$querygettamelog="select max(reptime) reptime from main.rep_check where 1";
    $result_utame=$dbh_cons->query($querygettamelog);	
	$row_utame = $result_utame->fetch_assoc();
	 
    $_SESSION['reptame']=$row_utame['reptime'];
	}
	$uptame=date('d-m-y H:i:s',$_SESSION['reptame']);
    $serverval=2;
	echo ($serverval.'+'.$uptame);
	///////////////////////////////////////////////////////////////////////
    
}


?>
