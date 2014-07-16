<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;


$message_id = $_POST['message_id'];
if(sizeof($_POST)>0)
{

foreach($_POST['message_id'] as $row)
	{
	 $message_id	=	$row;
	 $field1		=	array('status');
	 $value1		=	array(1);
	 $AdminDAO->updaterow("$dbname_detail.messages",$field1,$value1,"`message_id`='$message_id'");
}
}

?>