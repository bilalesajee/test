<?php
require_once('conn.php');
extract($_POST);
$id=$_GET['id'];
if($a==''){
 $select="DELETE FROM department WHERE ID=$id";}
 else{
     $select="DELETE FROM department WHERE  ID in ($a)";
     }
if($run=mysqli_query($link,$select)){
	mysql_affected_rows();
$msg="One Record deleted successfully";
	}
	else {
	    $msg="Failed to delete..";
}
$rs=array('msg'=>$msg);

echo json_encode($rs);