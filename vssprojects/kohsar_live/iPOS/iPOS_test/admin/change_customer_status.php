<?php session_start();
$_REQUEST['addressbookid']=$_SESSION['addressbookid'];
$mData=json_encode($_REQUEST,true);
 $mData=urlencode($mData);
echo  file_get_contents("https://main.esajee.com/admin/changecustomerstatus.php?get=".$mData);

?>