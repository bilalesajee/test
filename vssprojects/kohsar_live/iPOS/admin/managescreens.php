<?php
include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(52);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
/*echo "<pre>";
print_r($rights);
echo "</pre>";*/
//*************delete************************
$delid		=	$_REQUEST['id'];
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
$dest 		= 	'managescreens.php';
$div		=	'maindiv';
$form 		= 	"frmscreens";	
$tablename	=	'screen';
define(IMGPATH,'../images/');

 $query	=	"SELECT
				pkscreenid,screenname,url,modulename,visibility,displayorder
			FROM
				screen,module
			WHERE
				pkmoduleid	=	fkmoduleid
			";
$navbtn	=	"";
//$sortorder		=	"productattributeoptionname ASC"; // takes field name and field order e.g. brandname DESC
if(in_array('127',$actions))
{
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,document.$form.checks,'addscreen.php','subsection','maindiv')\" title='Add Screen'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(in_array('128',$actions))
{
	/*$navbtn .="	
			<a href=\"javascript: javascript:showpage(1,document.$form.checks,'viewinstancestock.php','subsection','maindiv') \" title=\"Stock Details\"><b>Stock Detail</b></a>";*/
			$navbtn .="	
			<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addscreen.php','subsection','maindiv') \" title=\"Edit Screen\"><span class=\"editrecord\">&nbsp;</span></a>";
}
if(in_array('129',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletescreen onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Screen\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}//if
if(in_array('130',$actions))
{
	$navbtn .="	|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managefields.php','sugrid','maindiv','stock') \" title=\"View Fields\"><b>View Fields</b></a>";
}//if
if(in_array('131',$actions))
{
	$navbtn .="	
			|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'manageactions.php','sugrid','maindiv','stock') \" title=\"View Actions\"><b>View Actions</b></a>";
			
}//if
/********** END DUMMY SET ***************/
$sortorder	=	"pkscreenid DESC";
?>
</head>
<div id="sugrid"></div>
<div id='<?php echo $div;?>'>
<div class="breadcrumbs" id="breadcrumbs">Screens</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,'',$sortorder,$tablename);
?>

<br />
<br />
</div>