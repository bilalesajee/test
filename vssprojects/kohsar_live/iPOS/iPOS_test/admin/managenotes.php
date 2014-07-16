<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
//$qs	=	$_SESSION['qstring'];
//echo $_SERVER['QUERY_STRING'];
$rights	 	=	$userSecurity->getRights(13);
/*echo "<pre>";
print_r($rights);
echo "</pre>";*/
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
//print_r($labels);
$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
//$page	=	$_GET['page'];
//$page	=	$_GET['page'];
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
				$delcondition =" pknoteid  = '$value' ";
				$AdminDAO->deleterows('note',$delcondition);
			}
		}
}
/************* DUMMY SET ***************/
//$labels = array("ID","Picture","Product Name","Description");
//$fields = array("pkproductid","defaultimage","productname","description");
$dest 	= 	'managenotes.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pknoteid,
				title,
				description,
				status
			FROM
				note
			WHERE
				notedeleted<>1
			";
$navbtn	=	"";

if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	if(in_array('40',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addnotes' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addnote.php','subsection','maindiv','','$formtype')\" title='Add Note'>
					<span class='addrecord'>&nbsp;</span>
					</a>&nbsp;";
	}
	if(in_array('41',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editnotes\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addnote.php','subsection','maindiv','','$formtype') title=\"Edit Note\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('42',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=deletenotes onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Notes\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('43',$actions))
	{
		$navbtn .="	|
				<a href=\"javascript:showpage(1,document.$form.checks,'itemnotes.php','subsection','maindiv') \" title=\"Manage Notes\"><b>Manage Notes</b></a>";
	}
/*}else{
		if(in_array('40',$actions))
		{
		//	print"Hello";
			$navbtn .= "<a class='button2' id='addnotes' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addnote.php','subsection','maindiv')\" title='Add Note'>
						<span class='addrecord'>&nbsp;</span>
						</a>&nbsp;";
		}
		if(in_array('41',$actions))
		{
			$navbtn .="	<a class=\"button2\" id=\"editnotes\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addnote.php','subsection','maindiv') title=\"Edit Note\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
		}
		if(in_array('42',$actions))
		{
			$navbtn .="	<a class=\"button2\" id=deletenotes onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Notes\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
		}
		if(in_array('43',$actions))
		{
			$navbtn .="	|
					<a href=\"javascript:showpage(1,document.$form.checks,'itemnotes.php','subsection','maindiv') \" title=\"Manage Notes\"><b>Manage Notes</b></a>";
		}*/
}//edit by ahsan on 08/02/2012
// if
/********** END DUMMY SET ***************/
?><head>

</head>

<div id="sugrid"></div>
<div id="attdiv"></div>
<div id="itemgrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Notes</div>
<?php 
//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />

</div>
