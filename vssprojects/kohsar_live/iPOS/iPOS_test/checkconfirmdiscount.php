<?php

include_once("includes/security/adminsecurity.php");

global $AdminDAO;

//$addressbookid	=	$_SESSION['addressbookid'];

if($_GET['uname']=='' || $_GET['pass']=='')

{

?>

	<script language="javascript" type="text/javascript">

        errordiscount();

    </script>

<?php		

	exit;

}

$res1		=	$AdminDAO->getrows("employee","fkaddressbookid","fkgroupid='15'");

$var		=	0;

for($i=0; $i<=sizeof($res1); $i++)

{

	$addressbookid	=	$res1[$i]['fkaddressbookid'];	

	$res2			=	$AdminDAO->getrows("addressbook","password,username","pkaddressbookid='$addressbookid'");



	$username		=	$res2[0]['username'];

	$username2		=	$_GET['uname'];

	if($username2==$username)

	{

		$password		=	$res2[0]['password'];

		$password2		=	md5($_GET['pass']);	

		if($password2==$password)

		{

			$var	=	1;

			?>

			<script language="javascript" type="text/javascript">

            //jQuery('#cancelsalediv').load('givediscount.php?confirm=1');	

			processdiscount(1);

            </script>

            <?php

		}

	}

}

if($var==0)

{

?>

		<script language="javascript" type="text/javascript">

            errordiscount();

        </script>

<?php

}

?>