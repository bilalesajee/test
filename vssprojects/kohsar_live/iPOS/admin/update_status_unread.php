<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;


     
/*echo "<pre>";
print_r($_POST['detail']);
echo "</pre>";
exit;*/
$message_id = $_POST['message_id'];
if(sizeof($_POST)>0)
{
	

/*$d_vale=array();
foreach ($_POST['detail'] as $key => $detail)
{

foreach ($detail as $key1 => $val) {
$d_vale[$key1][$key] = $val;
}
}*/


foreach($_POST['message_id'] as $row)
	{
	
	 $message_id	=	$row;
 
       
		 
	
		 
		$field1		=	array('status');
	     $value1		=	array(0);
		$AdminDAO->updaterow("$dbname_detail.messages",$field1,$value1,"`message_id`='$message_id'");


}
}

?>