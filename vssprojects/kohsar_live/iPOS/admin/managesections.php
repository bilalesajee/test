<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$userSecurity;
$rights	 	=	$userSecurity->getRights(44);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//*************delete************************
$dest 		= 	'managesections.php';
$div		=	'maindiv';
$form 		= 	"frm1sections";	
define(IMGPATH,'../images/');
$param		=	$_REQUEST['param'];
$id			=	$_REQUEST['id'];
$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
	$ids	=	explode(",",$delid);
	foreach($ids as $value)
	{
		if($value!='')
		{
			$delcondition =" pksectionid  = '$value' ";
			$AdminDAO->deleterows('section',$delcondition,1);
			//updating screens
			$AdminDAO->updaterow("screen",array('fksectionid'),array(0),"fksectionid='$value'");
		}
	}
}
$query 		= 	"SELECT 
					pksectionid,
					sectionname,
					IF(status=1,'Active','Inactive') status
				FROM
					section
				WHERE
					1
				";
$navbtn	=	"";
$sortorder	=	"pksectionid DESC"; // takes field name and field order e.g. brandname DESC
if(in_array('102',$actions))
{
//	print"Hello";
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addsection.php','sugrid','$div','','$formtype')\" title='Add Section'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(in_array('103',$actions))
{
	$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addsection.php','sugrid','$div','','$formtype') title=\"Edit Section\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('104',$actions))
{
	$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Sections\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}//Edit by Ahsan start 03/02/2012
if(in_array('159',$actions))
{
	$navbtn .=" | <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'managesectionscreens.php','sugrid','$div','','$formtype') title=\"Screens\"><b>Screens</b></a>&nbsp;";
}//Edit by Ahsan end
if(in_array('105',$actions))
{
	$navbtn .=" | <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'attachscreen.php','sugrid','$div','','$formtype') title=\"Attach Screens\"><b>Attach Screens</b></a>&nbsp;";
}
?><head>
</head>

<div id="sugrid"></div>
<div id="<?php echo $div;?>">
<div class="breadcrumbs" id="breadcrumbs">Sections</div>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?></div>