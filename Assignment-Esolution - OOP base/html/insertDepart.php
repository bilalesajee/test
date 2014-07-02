<?php

require_once('conn.php');
extract($_POST);
$query = "INSERT INTO department(DEPT_NAME,DEPT_CODE,DEPT_HEAD) VALUES('$deptName','$deptCode','$deptH')";
if (mysqli_query($link,$query)) {
    $success = true;
} else {
    $success = false;
}
$id = mysqli_insert_id($link);
$data = compact($id, $deptName, $deptCode, $deptH, array('id', 'deptName', 'deptCode', 'deptH'));
$rs = array('result' => $data, 'success' => $success);
echo json_encode($rs);
?>