<?php
require_once('conn.php');
extract($_POST);
$id=$_GET['id'];
if($a==''){
 $select="DELETE FROM location WHERE ID=$id";}
 else{
     $select="DELETE FROM location WHERE  ID in ($a)";
     }
if($run=mysql_query($select)){
$msg="One Record deleted successfully";
	}
	else {
	    $msg="Failed to delete..";
}
$deltedId= explode(',', $a);

$rs=array($deltedId);

echo json_encode($rs);