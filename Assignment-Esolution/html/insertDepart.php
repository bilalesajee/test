<?php
require_once('conn.php');
extract($_POST);
echo $query="INSERT INTO department(DEPT_NAME,DEPT_CODE,DEPT_HEAD) VALUES('$deptName','$deptCode','$deptH')";
mysql_query($query);


?>