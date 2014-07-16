<?php
// $_SESSION['closingsession'] optional
//$_SESSION['addressbookid'] = '';
if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){//added if condition by ahsan 16/02/2012, 
//security.php code block for POS
	if (!isset($_SESSION['countername']) || $_SESSION['countername'] == '' || $_SESSION['countername'] == 0  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' || $_SESSION['addressbookid'] == 0 ){
	 /*$counter	=	$_SESSION['countername'];
	 $msg = 'Please login';	 
	 header("Location:userlogin.php?pos=$counter&msg=$msg");*/
	 echo '<script>location.reload();</script>';
	 exit;
	}
}else{
	//security.php code block for main/store
				//start code from store_secuity.php, add by ahsan 15/02/2012
	if (!isset($_SESSION['admin_section']) || !isset($_SESSION['storeid']) || $_SESSION['storeid'] == ''  || !isset($_SESSION['addressbookid']) || $_SESSION['addressbookid'] == '' ){
	 header("Location:../admin/userlogin.php");
	 exit;
	}//end add code
}
?>