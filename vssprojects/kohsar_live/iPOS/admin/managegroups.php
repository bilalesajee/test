<?php
include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component;
//*************delete************************
$deltype	=	"delgroup";
include_once("delete.php");
/*
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
				$delcondition =" pkgroupid = '$value' ";
				$AdminDAO->deleterows('stock',$delcondition);
			}
		}
}*/
/************* DUMMY SET ***************/
$labels = array("ID","Group Name");
$fields = array("pkgroupid","groupname");
$dest 	= 	'managegroups.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
					pkgroupid,
					groupname
				FROM
					groups
				WHERE 1
			";

if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	$navbtn = "<a class='button2' id='addgroup' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addgroup.php','subsection','maindiv')\" title='Add Group'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;
				<a class=\"button2\" id=\"editgroup\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addgroup.php','subsection','maindiv') title=\"Edit Group\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
				<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Groups\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
				<a href=\"javascript: 
				loadsubgrid('subsection',document.$form.checks,'loadgroupdetails.php','maindiv') 
				\" title=\"View Details\"><b>View Details</b></a>";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$navbtn = "<a class='button2' id='addgroup' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addgroup.php','subsection','maindiv','','$formtype')\" title='Add Group'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editgroup\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addgroup.php','subsection','maindiv','','$formtype') title=\"Edit Group\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Groups\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}//end edit
/********** END DUMMY SET ***************/

?><head>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
</head>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Groups</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<br />
<br />
<div id="sugrid"></div>