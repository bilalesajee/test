<?php

require 'dbManager.php';
extract($_POST);
$obj = new dbManager();

if ($tablename == 'person') {
    
    return $obj->saveEmployee($_POST);
}

if ($tablename == 'location') {
    return $obj->saveLocation($_POST);
}

if ($tablename == 'department') {
    return $obj->saveDepartment($_POST);
}

 
