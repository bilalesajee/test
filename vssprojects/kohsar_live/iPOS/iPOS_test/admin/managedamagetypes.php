<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(18);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$deltype	=	"deldamagetypes";
include_once("delete.php");
//require_once("brandsmenu.php"); 
//*************delete************************
/*$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" pkdamagetypeid = '$value' ";
				$AdminDAO->deleterows('damagetype',$delcondition);
			}
		}
}*/
$query		=	"SELECT 
						*
				FROM
						damagetype
				WHERE 	
						damagetypedeleted <> 1
						";

$dest 	= 	"managedamagetypes.php";
$div	=	'maindiv';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');

$navbtn	=	"";

if($_SESSION['siteconfig'] != 3){//edit by ahsan on 08/03/2012
	if(in_array('49',$actions))
	{
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adddamagetype.php','subsection','maindiv','','$formtype')\" title='Add Damage Type'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('50',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddamagetype.php','subsection','maindiv','','$formtype') title=\"Edit Damage Type\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('51',$actions))
	{
		$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Damage Types\"><span class=\"deleterecord\">&nbsp;</span></a>";
	}
}//Edit by Ahsan on 08/03/2012
/********** END DUMMY SET ***************/
?><head>
</head>

<div id='maindiv'>
	<div class="breadcrumbs" id="breadcrumbs">Damage Types</div>
	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<br />
<br />
<div id="sugrid"></div>