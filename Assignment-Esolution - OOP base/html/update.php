<?php

require 'dbManager.php';
extract($_POST);
$obj = new dbManager();
$tablename=$_GET['tablename'];
if ($tablename == 'person') {
    $id=$_REQUEST['id'];
   $obj->updateEmoloyee($id);
}


 
