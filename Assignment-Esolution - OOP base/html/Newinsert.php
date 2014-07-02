<?php
require 'dbManager.php';
class insertdata extends dbmanager{
    public function __construct() {
        parent::__construct();
        }
        public function save($data) {
            extract($_POST);
            print_r($_POST);
            //$insertQuery="insert into";
            //query();
            
        }        
}
$obj=new insertdata();
