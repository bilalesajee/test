<?php
include("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$Bill;
$Bill			=	new Bill($AdminDAO);
$rights	 	=	$userSecurity->getRights(8);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
/************* DUMMY SET ***************/
$labels = array("ID","Currency Name","Currency Symbol","Currency Rate");
$fields = array("pkcurrencyid","currencyname","currencysymbol","rate");

$dest 	= 	'rates.php';
$div	=	'mainpanel';
$form 	= 	"frm1rates";	
$css 	= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 	= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************
  $query 	= 	"SELECT pkcurrencyid, currencyname,currencysymbol,rate FROM currency ";
?>
<div id="mainpanel">
<div id="childdiv"></div>
  <?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<!--Added by Yasir - 08-07-11-->
<script language="javascript" type="text/javascript">
	document.getElementById('searchFieldmainpanel').focus();	
</script>