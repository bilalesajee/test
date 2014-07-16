<?php
include("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$Bill;
$Bill		=	new Bill($AdminDAO);
$rights	 	=	$userSecurity->getRights(8);
//$countername		=	gethostbyaddr($_SERVER['REMOTE_ADDR']);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
/************* DUMMY SET ***************/
$labels = array("ID","Account Title","Amount","Last Payout");
$fields = array("accountheadid","accounttitle","amount","paymentdate");
$dest 	= 	'paydetails.php';
$div	=	'mainpanel2';
$form 	= 	"frmpayouts";	
$css 	= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 	= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************  
  $query 	= 	"SELECT id as accountheadid,title as accounttitle,round(sum(amount),2) as amount,from_unixtime(MAX(paymentdate),'%d-%m-%y  %h:%i:%s') as paymentdate   
  	FROM  
		$dbname_detail.account LEFT JOIN $dbname_detail.accountpayment ON (fkaccountid = id)				
				  WHERE fkclosingid = '$closingsession'
				  GROUP BY 
				  		id
  ";
//*******************************************************************

$navbtn	=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'accountdetails.php','childdiv','mainpanel2','') title=\"Account Details\"><span class=\"\">Account Details</span></a>&nbsp;";

//$navbtn="";
?>
<div id="childdiv"></div>
<div id="mainpanel2" style="clear:both;float:left;width:100%;padding-top:15px;">
  <?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>