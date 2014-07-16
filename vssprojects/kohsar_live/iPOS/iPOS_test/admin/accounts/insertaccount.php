<?php
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$id 			=	$_GET['id'];
$title 			=	$_POST['title'];
$code 			=  	$_POST['code'];
$category_id	=	$_POST['categories'];
$status			=	$_POST['status'];		
$type_id		= 	$_POST['types'];
$creation_date	=	"";
if($title=='')
{
	print"Account Title can not be left blank";
	exit;
}
if($code == '')
{
	print"Account Code can not be left blank";
	exit;
}
if($category_id ==0)
{
	print"Account Category can not be left blank";
	exit;
}
if($title)
{
	//$AdminDAO->displayquery=1;
	$unique = $AdminDAO->isunique("$dbname_detail.account", 'id', $id, 'title', $title);
	if($unique=='1')
	{
		echo "Account Title <b><u>$title</u></b> already exists. Please provide any other title.";	
		exit;
	}
}
if($code)
{
	$unique = $AdminDAO->isunique("$dbname_detail.account", 'id', $id, 'code', $code)   ;
	if($unique=='1')
	{
		echo "Account Code <b><u>$code</u></b> already exists. Please provide any other code.";	
		exit;
	}
}
$field	=	array('title','code','category_id','status','type_id');
$value	=	array("$title","$code",$category_id,$status,$type_id);
if($id > 0)
{
	$AdminDAO->updaterow("$dbname_detail.account",$field,$value," id='$id' ");
  
  ?><script>
  $('#maindiv').load('accounts/accounts.php?nu=1');
  hideform_main('account','');
  </script>
  <?php
  //  header('location:http://localkohsar.esajee.com/admin/accounts/accounts.php');

}
else
{
	$field[]	=	"creationdate";
	$value[]	=	time();
	$AdminDAO->insertrow("$dbname_detail.account",$field,$value);
	
	  ?><script>
  $('#maindiv').load('accounts/accounts.php?na=1');
  hideform_main('account','');
  </script>
  <?php



}
exit;
?>