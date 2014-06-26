<?php
require_once('conn.php');
extract($_POST);
if($hiddenID !=''){
 $insert="UPDATE person SET NAME='$name',AGE='$age',ADDRESS='$address',EMAIL='$email' WHERE ID=$hiddenID";	
    }
 else{
$insert="INSERT INTO person(NAME,AGE,ADDRESS,EMAIL,LOC,DEPT,STATUS) VALUES('$name', '$age', '$address', '$email','$loc','$dept','$status')";
   }
   
if(mysql_query($insert))
{
$msg="data enter successfully";
	}
	else{
		$msg="Failed..";
}
if($hiddenID !=''){
$id=$hiddenID;
}
else
{
	$id=mysql_insert_id();//Always return last id from reocord
}
$data=compact($id, $name, $age, $address, $email, $status,array('id', 'name', 'age', 'address', 'email','status'));
//$data=array('id'=>$id, 'name'=>$name, 'age'=>$age, 'address'=>$address, 'email'=>$email,'status'=>$status);
$rs=array('msg'=>$msg, 'data'=>$data);
echo json_encode($rs);
?>