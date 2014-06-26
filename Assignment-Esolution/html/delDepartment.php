<?php
require_once('conn.php');
extract($_POST);
$id=$_GET['id'];
if($a==''){
 $select="DELETE FROM department WHERE ID=$id";}
 else{
     $select="DELETE FROM department WHERE  ID in ($a)";
     }
if($run=mysql_query($select)){
$msg="One Record deleted successfully";
	}
	else {
	    $msg="Failed to delete..";
}
$rs=array();
$rs['msg']=$msg;
echo json_encode($rs);