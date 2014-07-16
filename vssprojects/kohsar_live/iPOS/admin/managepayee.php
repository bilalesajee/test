<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(64);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];

$dest 	= 	'managepayee.php';
$div	=	'maindiv';
$form 	= 	"frm1cutomers";	
$css 	= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 	= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************
  $query 	= 	"SELECT 
					id,
					CONCAT(firstname,' ',lastname) as name,
					title accounttitle,
					accountlimit,
					IF(status=1,'Active','Inactive') status
        		FROM
					$dbname_detail.account ac LEFT JOIN addressbook  ON (ac.fkaddressbookid = pkaddressbookid)  where ac.ctype=0	
					";
//*******************************************************************
if(in_array('141',$actions))
{
$navbtn	.=	"<a class='button2' href=\"javascript:showpage(0,'','addnewpayee.php','subsection','maindiv')\" title='Add New Payee'>
				<span class='addrecord'>&nbsp;</span>
			</a>";
}
if(in_array('142',$actions))
{
$navbtn	.=	"<a class=\"button2\" href=javascript:showpage(1,document.$form.checks,'addnewpayee.php','subsection','maindiv','') title=\"Edit Payee\"><span class=\"editrecord\">&nbsp;</span></a>";
}
if(in_array('140',$actions))
{
$navbtn	.=	"
			<a href=\"javascript:selectallpayeerecords('changestatuspayee.php','maindiv') \" title=\"Change Status\"><b>Change Status</b></a>
			";
}
?>
<div id="notice"></div>
<div id="maindiv">
<div id="subsection"></div>
	<div class="breadcrumbs" id="breadcrumbs">Payee Accounts</div>
  <?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>