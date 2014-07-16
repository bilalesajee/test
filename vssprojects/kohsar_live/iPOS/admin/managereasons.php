<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(19);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$deltype	=	"deldiscountreason";
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
				$delcondition =" pkreasonid = '$value' ";
				$AdminDAO->deleterows('discountreason',$delcondition);
			}
		}
}*/
$query		=	"SELECT 
						*
				FROM
						discountreason
				WHERE 	
						discountreasondeleted <> 1
						";

$dest 	= 	"managereasons.php";
$div	=	'maindiv';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');

$navbtn	=	"";
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	if(in_array('52',$actions))
	{
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addreason.php','subsection','maindiv')\" title='Add Reason'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('53',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addreason.php','subsection','maindiv') title=\"Edit Reason\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	if(in_array('52',$actions))
	{
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addreason.php','subsection','maindiv','','$formtype')\" title='Add Reason'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('53',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addreason.php','subsection','maindiv','','$formtype') title=\"Edit Reason\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
}//end edit
if(in_array('54',$actions))
{
	$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Reasons\"><span class=\"deleterecord\">&nbsp;</span></a>";
}
/********** END DUMMY SET ***************/
?><head>
</head>

<div id='maindiv'>
	<div class="breadcrumbs" id="breadcrumbs">Discount Reasons</div>
	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<br />
<br />
<div id="sugrid"></div>