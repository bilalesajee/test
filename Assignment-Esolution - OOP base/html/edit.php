<?php
require_once('conn.php');
extract($_POST);
$id=$_GET['id'];
$select="SELECT p.ID,p.NAME,p.AGE,p.ADDRESS,p.EMAIL,p.STATUS FROM person p WHERE ID=$id";
$run=mysql_query($select);
while($row=mysql_fetch_array($run)){
	$id=$row['ID'];
	$name=$row['NAME'];
	$age=$row['AGE'];
	$address=$row['ADDRESS'];
	$email=$row['EMAIL'];
	$status=$row['STATUS'];
	}

$rs=array('id'=>$id,'Name'=>$name,'Age'=>$age,'Address'=>$address,'Email'=>$email,'Status'=>$status);
echo json_encode($rs);
?>