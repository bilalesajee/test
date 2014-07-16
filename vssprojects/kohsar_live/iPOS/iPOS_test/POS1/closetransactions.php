<?php
session_start();
$_SESSION['closingsession']='';

// session destroy and logout by Yasir 22-06-11
unset($_SESSION['closingsession']);
$counter	=	$_SESSION['countername'];
session_destroy();
sleep(3);
echo "<script>$(location).attr('href','userlogin.php?pos=$counter');</script>";
exit;
//
?>