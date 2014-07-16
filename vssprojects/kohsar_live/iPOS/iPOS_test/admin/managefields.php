<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
//*************delete************************
$delid			=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
	$condition="";
	$ids	=	explode(",",$delid);
	foreach($ids as $value)
	{
		if($value!='')
		{
			$delcondition =" pkfieldid  = '$value' ";
			$AdminDAO->deleterows('field',$delcondition,1);
		}
	}
	$delid	=	$_GET['param'];
}
/************* DUMMY SET ***************/
$labels = array("ID","Label","Field Name");
$fields = array("pkfieldid","fieldlabel","fieldname");
$dest 	= 	'managefields.php';
$div	=	'sugrid';
$form 	= 	"fieldsfrm";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkfieldid,
				fieldlabel,
				fieldname
			FROM
				field
			WHERE
				fkscreenid='$delid'
			";
$navbtn	=	"";
$navbtn .= "<a class='button2' id='addfields' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addfield.php','subgrid','sugrid','$delid')\" title='Add Field'>
				<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
$navbtn .="	<a class=\"button2\" id=\"editfields\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addfield.php','subgrid','sugrid','$delid') title=\"Edit Field\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
$navbtn .="	<a class=\"button2\" id=deletenotes onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$delid') title=\"Delete Fields\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
//$navbtn	=	"";
// if
/********** END DUMMY SET ***************/
?>
</head>
<div id="subgrid"></div>
<div id="<?php echo $div;?>">
<div class="breadcrumbs" id="breadcrumbs">Fields</div>
<?php 
//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />

</div>