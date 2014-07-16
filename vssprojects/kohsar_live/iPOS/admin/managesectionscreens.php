<?php
////////////******************************//////////////
//File Added by: Ahsan
//Date: 03/02/2012
////////////******************************//////////////

include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$sectionid = $_GET['id'];
/*$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
/*echo "<pre>";
print_r($rights);
echo "</pre>";*/
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
				$delcondition =" pkscreenid  = '$value' ";
				$AdminDAO->deleterows('screen',$delcondition,1);
			}
		}
}
/************* DUMMY SET ***************/
$labels = array("ID","Screen Name");
$fields = array("pkscreenid","screenname");
$dest 		= 	'managesectionscreens.php';
$div		=	'sugrid';
$form 		= 	"frmscreens";	
$tablename	=	'screen';
define(IMGPATH,'../images/');

 $query	=	"SELECT
				pkscreenid,screenname
			FROM
				screen
			WHERE
				fkmoduleid	=	1 && fksectionid='$sectionid'
			";
$navbtn	=	"";
//$sortorder		=	"productattributeoptionname ASC"; // takes field name and field order e.g. brandname DESC
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,document.$form.checks,'addscreen.php','subsection','$div')\" title='Add Screen'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";

	$navbtn .="	|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'viewfields.php','subgrid','$div','stock') \" title=\"View Fields\"><b>View Fields</b></a>";
	
		$navbtn .="	
			|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'viewactions.php','subgrid','$div','stock') \" title=\"View Actions\"><b>View Actions</b></a>";

/********** END DUMMY SET ***************/
$sortorder	=	"pkscreenid DESC";
?>
</head>
<div id="subgrid"></div>
<div id='<?php echo $div;?>'>
<div class="breadcrumbs" id="breadcrumbs">Screens</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,'',$sortorder,$tablename);
?>

<br />
<br />
</div>