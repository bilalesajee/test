<?php session_start();
include_once("../../includes/security/adminsecurity.php");
include_once("../dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(59);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 	= 	'accounts/types.php';
$div	=	'maindiv';
$form 	= 	"typefrm";	
define(IMGPATH,'../images/');
$query	= "SELECT 
				id, 	
				name,
				id as typeid,
				description
			FROM 
				type
			WHERE 1
			";
if($id)
{
	$query	.=" AND category_id = '$id'";
}
$i=0;
if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	if(in_array('132',$actions))
	{
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','accounts/type.php','sugrid','$div')\" title='Add Type'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('133',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editmenus\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'accounts/type.php','sugrid','$div') title=\"Edit Type\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('134',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=deletemenus onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Type\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}
}//edit by Ahsan on 09/02/2012
/********** END DUMMY SET ***************/
?>
<div id="menudiv"></div>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Manage Types</div>
<!--<div class="breadcrumbs" id="breadcrumbs">New Arrivals</div>-->
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray,$orderby);
?>
</div>