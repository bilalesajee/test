<?php
require 'dbManager.php';

$id = $_REQUEST['id'];
$tablename = $_REQUEST['tablename'];

$obj = new dbManager();
if ($id != '') {
    $obj->deleteRow($tablename, $id);
}


