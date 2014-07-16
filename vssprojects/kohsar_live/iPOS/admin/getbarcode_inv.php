<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
$inid=$_REQUEST['id'];
	$sql="SELECT * FROM $dbname_detail.stock WHERE  fksupplierinvoiceid='$inid'";
	$bararr	=	$AdminDAO->queryresult($sql);
	echo json_encode($bararr);
?>