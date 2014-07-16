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
				$delcondition =" pkactionid  = '$value' ";
				$AdminDAO->deleterows('action',$delcondition,1);
			}
		}
		$delid	=	$_GET['param'];
}
/************* DUMMY SET ***************/
$labels = array("ID","Label","Action Code");
$fields = array("pkactionid","actionlabel","actioncode");
$dest 	= 	'manageactions.php';
$div	=	'sugrid';
$form 	= 	"actionsfrm";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkactionid,
				actionlabel,
				actioncode
			FROM
				action
			WHERE
				fkscreenid='$delid'
			";
$navbtn	=	"";
$navbtn .= "<a class='button2' id='addactions' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addaction.php','subgrid','sugrid','$delid')\" title='Add Action'>
				<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
$navbtn .="	<a class=\"button2\" id=\"editactions\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addaction.php','subgrid','sugrid','$delid') title=\"Edit Action\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
$navbtn .="	<a class=\"button2\" id=deleteactions onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$delid') title=\"Delete Actions\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
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