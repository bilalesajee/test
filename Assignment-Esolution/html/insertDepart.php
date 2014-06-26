<?php

require_once('conn.php');
extract($_POST);
$query = "INSERT INTO department(DEPT_NAME,DEPT_CODE,DEPT_HEAD) VALUES('$deptName','$deptCode','$deptH')";
if (mysql_query($query)) {
    $success = true;
} else {
    $success = false;
}
$id = mysql_insert_id();
$data = compact($id, $deptName, $deptCode, $deptH, array('id', 'deptName', 'deptCode', 'deptH'));
$rs = array('result' => $data, 'success' => $success);
echo json_encode($rs);
?>