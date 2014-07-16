<?php
session_start();
require_once("../includes/conf/conf.php");
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	ob_start();
	error_reporting(7);
	if(sizeof($_POST)>0)
	{
	//include_once("../includes/classes/AdminDAO.php");	
	include_once("../includes/classes/filter.php");
	//include_once("../includes/security/adminsecurity.php");
	include_once("../includes/classes/login.class.php");
	include_once("../includes/classes/error.php");
	$AdminDAO	=	new AdminDAO();
	$Error		=	new Error();
	$Login	=	new Login($AdminDAO);
		$user	=	trim(filter($_POST['user'])," ");
		$pass	=	trim(filter($_POST['pass'])," ");
		$module	=	$_POST['module'];
		$type	=	'employee';
		$result	=	$Login->loginprocess($user,$pass,$type);
		if($result	== 1)
		{
			if($module == 1)
			{
				header("Location:index.php");
				exit;
			}
			else
			{
				$getsalequery="SELECT pksaleid from sale where status='0' order by datetime DESC";
				$salearray	=	$AdminDAO->queryresult($getsalequery);
				$_SESSION['tempsaleid']=$salearray[0][pksaleid];
				header("Location:../pos/index.php");
				exit;
			}
		}
		else
		{
			$error	=	$Error->display($result);
		}
	}
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="../includes/css/all.css" />
	<script language="javascript" src="../includes/js/jquery.js"></script>
	</head>
	<body style="background-color:#FFF;">
	<div id="main" align="center">
	<div id="loginlock"><!-- --></div>
	<div id="loginerror"><?php echo $error; ?></div>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<form id="loginfrm" name="loginfrm" method="post" action="" class="form" style="width:350px;">
	<fieldset>
		<legend>Login</legend><br />
		<label> User Name </label>
		<input type="text" name="user" id="user" size="25" value="" /> <br />
		<label> Password </label>
		<input type="password" name="pass" id="pass" size="25" /> <br />
		<label> Select Module </label>
		<select name="module">
			<option value="1">Stocks</option>
			<option value="2">Point of Sale</option>
		</select>
	<!--    <input type="submit" name="login" id="login" value="    Login     " class="button" />-->
		<div style="float:left; width:100px; margin:8px 0 0 123px;">
		<span class="buttons">
			<button type="submit" class="positive">
				<img src="../images/tick.png" alt=""/> 
				Login
			</button>
		</span>
		</div>
	</fieldset>
	</form>
	</div>
	</body>
	</html>
	<script language="javascript" type="text/javascript">
	document.loginfrm.user.focus();
	jQuery('#loginerror').fadeOut(3000);
	</script>
	<?php ob_flush();?>
<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	session_start();
	error_reporting(1);
	include_once("../includes/classes/AdminDAO.php");	
	include_once("../includes/classes/filter.php");	
	//include_once("../includes/security/adminsecurity.php");
	include_once("../includes/classes/login.class.php");
	include_once("../includes/classes/error.php");
	$AdminDAO	=	new AdminDAO();
	$Error		=	new Error();
	$Login	=	new Login($AdminDAO);
	if(sizeof($_POST)>0)
	{
		$user		=	trim(filter($_POST['user'])," ");
		$pass		=	trim(filter($_POST['pass'])," ");
		$storeid	=	$_POST['store'];
		$type		=	'employee';
		$result		=	$Login->loginprocess($user,$pass,$type);
		if($result	== 1)
		{
			if($storeid=='dc')
			{
				
				header("Location:../../datacleanup/view_cleaned_data.php");
				exit;
			}
			$_SESSION['store']	=	$storeid;
			header("Location:index.php");
			exit;
			/*if($module == 1)
			{
				header("Location:index.php");
				exit;
			}
			else
			{
				$getsalequery="SELECT pksaleid from sale where status='0' order by datetime DESC";
				$salearray	=	$AdminDAO->queryresult($getsalequery);
				$_SESSION['tempsaleid']=$salearray[0][pksaleid];
				header("Location:../pos/index.php");
				exit;
			}*/
		}
		else
		{
			$error	=	$Error->display($result);
		}
	}
	// selecting stores
	$storesarray		= 	$AdminDAO->getrows("store","*", "storedeleted<>1 AND storestatus=1");
	$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:180px;\" ><option value=\"0\">Select Store</option>";
	for($i=0;$i<sizeof($storesarray);$i++)
	{
		$storename		=	$storesarray[$i]['storename'];
		$storeid		=	$storesarray[$i]['pkstoreid'];
		$storesel2	.=	"<option value=\"$storeid\">$storename</option>";
	}
	$storesel2.=" <option value='dc'>Data Clean Up</option>";
	$stores				=	$storesel.$storesel2."</select>";
	// end stores
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="../includes/css/all.css" />
	<script language="javascript" src="../includes/js/jquery.js"></script>
	</head>
	<body style="background-color:#FFF;">
	<div id="main" align="center">
	<div id="loginlock"><!-- --></div>
	<div id="loginerror"><?php echo $error; ?></div>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<form id="loginfrm" name="loginfrm" method="post" action="" class="form" style="width:350px;">
	<fieldset>
		<legend>Login</legend><br />
		<label> User Name </label>
		<input type="text" name="user" id="user" size="25" value="<?php echo $user; ?>" /> <br />
		<label> Password </label>
		<input type="password" name="pass" id="pass" size="25" /> <br />
		<label> Select Store </label>
		<?php echo $stores;?>
		<div style="float:left; width:100px; margin:8px 0 0 123px;">
		<span class="buttons">
			<button type="submit" class="positive">
				<img src="../images/tick.png" alt=""/> 
				Login
			</button>
		</span>
		</div>
	</fieldset>
	</form>
	</div>
	</body>
	</html>
	<script language="javascript" type="text/javascript">
	document.loginfrm.user.focus();
	jQuery('#loginerror').fadeOut(3000);
	</script>
<?php }//end edit?>	