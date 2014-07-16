<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
/*$rights	 	=	$userSecurity->getRights(28);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];*/
//*************delete************************
$shipmentid		=	$_REQUEST['id'];
/************* DUMMY SET ***************/
$labels = array("ID","Barcode","Item","Quantity","Box Number","Packing Time","Added By");
$fields = array("pkorderpackid","barcode","itemdescription","quantity","packnumber","packtime","name");
$dest 	= 	'orderpacks.php';
$div	=	'subsection';
$form 	= 	"frm1orderpacking";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkorderpackid,barcode,itemdescription,packnumber,op.quantity,CONCAT(firstname, ' ', lastname) name, DATE_FORMAT(op.datetime,'%d-%m-%Y %H:%i:%s') packtime
			FROM
				`order`,orderpack op LEFT JOIN addressbook on pkaddressbookid=fkaddressbookid
			WHERE
				pkorderid	=	fkorderid AND
				op.fkshipmentid='$shipmentid'
			";
$navbtn	=	"";
$navbtn	="<a href=\"javascript: javascript:showpage(1,document.$form.checks,'orderaddpacking.php','editpack','subsection','$param','$formtype') \" title=\"Edit Packing\"><span class=\"editrecord\">&nbsp;</span></a>";
/********** END DUMMY SET ***************/
?>
<div id="editpack"></div>
<div id='viewpacked'>
<div class="breadcrumbs" id="breadcrumbs">Packing Records</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form, '', '','pkorderpackid DESC');
?>
<br />
<br />
</div>