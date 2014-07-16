<?php
// $_SESSION['closingsession'] optional

//$_SESSION['addressbookid'] = '';

if (!isset($_SESSION['countername']) || $_SESSION['countername'] == '' || $_SESSION['countername'] == 0  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' || $_SESSION['addressbookid'] == 0 ){
 /*$counter	=	$_SESSION['countername'];
 $msg = 'Please login';	 
 header("Location:userlogin.php?pos=$counter&msg=$msg");*/
 echo '<script>location.reload();</script>';
 exit;
}
?>