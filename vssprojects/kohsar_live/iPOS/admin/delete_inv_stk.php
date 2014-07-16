<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
		

 $stockid = $_POST['pkstockid'];

		
$delete= mysql_query("update  $dbname_detail.stock set quantity=0,unitsremaining=0 where pkstockid='$stockid' ");

echo 'yes';

?>