<?php

/* $action = $_GET[áction];
  $action();

  $deleteRecords->{$action};

  function a(){

  }
  function b(){

  } */
require_once('conn.php');
extract($_POST);
//$id = $_GET['id'];
/* if ($a == '') {
    $select = "DELETE FROM person WHERE ID=$id";
} else { */
    $select = "DELETE FROM person WHERE  ID in ($id)";
//}

if ($run = mysqli_query($link,$select)) {
   // $msg = "One Record deleted successfully";
    $result=TRUE;
} else {
    $msg = "Failed to delete..";
    $result=FALSE;
}
//$deltedId = explode(',', $a);
$rs = array('status'=>$result);
echo json_encode($rs);
