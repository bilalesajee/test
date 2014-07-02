<?php
require_once('conn.php');
extract($_POST);
if($hiddenID !=''){
 $insert="UPDATE person SET NAME='$name',AGE='$age',ADDRESS='$address',EMAIL='$email',LOC='$loc',DEPT='$dept',STATUS='$status' WHERE ID=$hiddenID";	
    }
 else{
$insert="INSERT INTO person(NAME,AGE,ADDRESS,EMAIL,LOC,DEPT,STATUS) VALUES('$name', '$age', '$address', '$email','$loc','$dept','$status')";
   }
   
if(mysqli_query($link,$insert))
{
    $msg="data enter successfully";
     $success = true;
	}
	else{
		$msg="Failed..";
                $success = false;
}
if($hiddenID !=''){
$id=$hiddenID;
}
else
{
	$id=mysqli_insert_id($link);//Always return last id from reocord
}
if($status==1)
{
    $status = 'Active';
}
 else {
   $status = 'In Active';  
}
$data=compact($id, $name, $age, $address, $email, $status,array('id', 'name', 'age', 'address', 'email','status'));
//$data=array('id'=>$id, 'name'=>$name, 'age'=>$age, 'address'=>$address, 'email'=>$email,'status'=>$status);
$rs=array('msg'=>$msg,'success'=>$success, 'data'=>$data);
echo json_encode($rs);
?>