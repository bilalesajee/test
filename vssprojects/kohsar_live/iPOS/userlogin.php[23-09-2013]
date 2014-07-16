<?php 
ob_start();
error_reporting(7);
session_start();
include_once("includes/conf/conf.php");//line added by ahsan 22/02/2012
//include_once("includes/classes/AdminDAO.php");//add comment by ahsan 22/02/2012
//include_once("includes/conf/userconfig.php");//add comment by ahsan 22/02/2012
$AdminDAO		=	new AdminDAO();
$countername	=	$_GET['pos'];
$counters	=	$AdminDAO->getrows("$dbname_detail.counter","1","countername='$countername'");
if(sizeof($counters)<1)
{
	echo "POS doesn't exist.";
	exit;
}
$_SESSION['countername']	=	$countername;
if(sizeof($_POST)>0)
{
include_once("includes/classes/filter.php");	
//include_once("../includes/security/adminsecurity.php");
include_once("includes/classes/login.class.php");
include_once("includes/classes/error.php");
$Error		=	new Error();
$Login	=	new Login($AdminDAO);
	$user	=	trim(filter($_POST['user'])," ");
	$pass	=	md5(trim(filter($_POST['pass'])," "));
	$module	=	$_POST['module'];
	$type	=	'employee';
	$result	=	$Login->loginprocess($user,$pass,$type,'pos');
	if($result	== 1)
	{
		if($module == 1)
		{
			header("Location:index.php");
			exit;
		}
		else
		{
			$storeid			=	$_SESSION['storeid'];
			//$countername	=	gethostbyaddr($_SERVER['REMOTE_ADDR']);
			$empid				=	$_SESSION['addressbookid'];
			if($empid!='' || $empid!=0)//checking if this counter have Pending closing Then Restrict new user to login 
			{
				$pendingclosingquery	=	"SELECT pkclosingid,firstname,lastname from $dbname_detail.closinginfo,addressbook where closingstatus='i' AND countername='$countername' AND fkstoreid = '$storeid' AND 	fkaddressbookid <> '$empid' AND pkaddressbookid=fkaddressbookid order by closingdate DESC LIMIT 0,1";
				$pendingclosingarray	=	$AdminDAO->queryresult($pendingclosingquery);
				$pendingid				=	$pendingclosingarray[0]['pkclosingid'];
				$pendingemployeename	=	$pendingclosingarray[0]['firstname'].' '.$pendingclosingarray[0]['lastname'];
				if($pendingid!='' && $empid!=40)// pending closing found on this counter for any other user
				{
					$e="pc";
					$_SESSION['pc']=$e;
					$_SESSION['pcusername']=$pendingemployeename;
					header("Location:userlogin.php?pos=$countername&e=$e&name=$pendingemployeename");
					exit;	
				}
				else
				{
					$_SESSION['pc']='';
					$_SESSION['pcusername']='';
				}
				
			}
			$getsalequery="SELECT pksaleid,fkclosingid from $dbname_detail.sale s,$dbname_detail.closinginfo where fkclosingid=pkclosingid AND closingstatus='i' AND s.status='0' AND s.countername='$countername' AND s.fkstoreid = '$storeid' AND s.fkuserid = '$empid' order by datetime DESC";
			$salearray	=	$AdminDAO->queryresult($getsalequery);
			$_SESSION['tempsaleid']=$salearray[0][pksaleid];
			$_SESSION['closingsession']=$salearray[0][fkclosingid];
			if($salearray[0][fkclosingid]=='')
			{
				$closingquery="SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus='i' AND countername='$countername' AND fkstoreid = '$storeid' AND 	fkaddressbookid = '$empid' order by closingdate DESC LIMIT 0,1";
				$closingarray	=	$AdminDAO->queryresult($closingquery);
				 $_SESSION['closingsession']=$closingarray[0][pkclosingid];
			}
			header("Location:index.php");
			exit;
		}
	}
	else
	{
		$error	=	$Error->display($result);
	}
}
if($error=='')
{
	$error		=	$_GET['e'];
	$username	=	$_GET['name'];
	if($error=='pc')
	{
		$error="Closing NOT finalized by <u>$username</u>.<br>Please get it closed and then try to login";
	}
}
$msg	=	$_GET['msg'];	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login</title>
<link rel="stylesheet" type="text/css" href="includes/css/all.css"/>
<script language="javascript" src="includes/js/jquery.js"></script>
</head>
<body style="background-color:#FFF;">
<div id="main_pos" align="center">
<div id="loginlock"><!-- --></div>
<div id="loginerror" class="notice" style="display:none"><?php echo $error; ?></div>
<br />
<br />
<br />
<br />
<br />
<br />
<form id="loginfrm" name="loginfrm" method="post" action="" class="form" style="width:350px;">
<fieldset>
	<legend>POS: <?php echo $_SESSION['countername'];?> Login</legend><br />
	<label> User Name </label>
	<input type="text" name="user" id="user" size="25" value="<?php echo $user; ?>" /> <br />
	<label> Password </label>
	<input type="password" name="pass" id="pass" size="25" /> <br />
   <!-- <label> Select Module </label>
	<select name="module">
		<option value="1">Stocks</option>
		<option value="2">Point of Sale</option>
	</select>-->
	<input type="hidden" name="module" value="2" id="module" />
<!--    <input type="submit" name="login" id="login" value="    Login     " class="button" />-->
	<div style="float:left; width:100px; margin:8px 0 0 123px;">
	<span class="buttons">
		<button type="submit" class="positive">
			<img src="images/tick.png" alt=""/> <?php //removed includes/ by ahsan 03/02/2012?>
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
<?php 
if($error!='' || $msg!='')
{
?>
document.getElementById('loginerror').style.display='block';
	<?php
		if($msg!='')
		{
	?>
		document.getElementById('loginerror').innerHTML='<?php echo $msg?>';
	<?php
		
		}
	?>

jQuery('#loginerror').fadeOut(10000);
<?php
}
?>
</script>
<?php ob_flush();?>