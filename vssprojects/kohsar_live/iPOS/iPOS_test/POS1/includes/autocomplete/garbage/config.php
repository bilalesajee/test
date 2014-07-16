<?php
// REQUIRED! Parameters to connecto to your DB
// CHANGE ONLY $db_host, $db_name, $username, $password
$db_host="localhost";
$db_name="catalog";
$username="root";
$password="";

// DON'T CHANGE THE FOLLOWING CODE!
$db_con=mysql_connect($db_host,$username,$password);
$connection_string=mysql_select_db($db_name);
mysql_connect($db_host,$username,$password);
mysql_select_db($db_name);
?>