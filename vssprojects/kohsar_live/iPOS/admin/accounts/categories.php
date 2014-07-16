<?php session_start();
include_once("../../includes/security/adminsecurity.php");
include_once("../dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(58);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 	= 	'accounts/categories.php';
$div	=	'maindiv';
$form 	= 	"categoryfrm";	
define(IMGPATH,'../images/');
$query	= "SELECT 
				id,
				id as categoryid,
				name , 	
				description
			FROM 
				accountcategory	
			WHERE 1
			";
$i=0;
/********** END DUMMY SET ***************/
?>
<div id="menudiv"></div>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Manage Categories</div>
<!--<div class="breadcrumbs" id="breadcrumbs">New Arrivals</div>-->
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray,$orderby);
?>
</div>