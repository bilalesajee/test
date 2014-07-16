<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
//$rights	 	=	$userSecurity->getRights(30);
//$labels	 	=	$rights['labels'];
//$fields		=	$rights['fields'];
$labels			=	array("ID","Account Title","Name","Address","Email","Phone","NIC");
$fields			=	array("pkcustomerid","title","name","address","email","phone","nic");
//$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
$deltype	=	'delwishlist';
include_once("delete.php"); 
$dest 	= 	'managecreditors.php';
$div	=	'maindiv';
$form 	= 	"frmcreditors";
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkcustomerid,
				CONCAT(firstname,' ',lastname) as name,
				CONCAT(address1,' ',address2) as address,
				email,
				companyname title,
				CONCAT(phone ,' / ',mobile) as phone,
				nic    
			FROM
				customer
			WHERE
				isdeleted <> 1 AND ctype!=0 GROUP BY pkcustomerid";
$navbtn	=	"";
$navbtn .="	<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(1,'','creditorreport.php','sugrid','$div')\" title='Creditor Reports'>
				<span class='printrecord'>&nbsp;</span>
			</a>&nbsp;";
/********** END DUMMY SET ***************/
?>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Creditors</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionarray,"  name ASC ");
?>
<br />
<br />
</div>