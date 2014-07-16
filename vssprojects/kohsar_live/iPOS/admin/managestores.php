<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
/**************RIGHTS***************************/
$rights	 	=	$userSecurity->getRights(4);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
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
			$delcondition =" pkstoreid = '$value' ";
			$AdminDAO->deleterows('store',$delcondition);
		}
	}
}
/************* DUMMY SET ***************
$labels = array("ID","Store Name ","Phone","Address","City");
$fields = array("pkstoreid","storename","storephonenumber","storeaddress","cityname");
*/
$dest 	= 	'managestores.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
						s.pkstoreid,
						s.storename,
						s.storephonenumber,
						s.storeaddress,
						c.cityname
					FROM
						store s LEFT JOIN city c ON (s.fkcityid	=	c.pkcityid)
					WHERE
						s.storedeleted <> 1  
					";
//$AdminDAO->getrows('brand',$delcondition);
//$navbtn = array("Add","Edit","Delete","custom");
$navbtn	=	"";
if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	if(in_array('7',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addstores' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addstore.php','subsection','maindiv','','$formtype')\" title='Add Store'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;
				";
	}
	if(in_array('8',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editstore\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addstore.php','subsection','maindiv','','$formtype') title=\"Edit Store\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('9',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=deletestores onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Stores\"><span class=\"deleterecord\">&nbsp;</span></a>";
	}
/*}else{

	if(in_array('7',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addstores' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addstore.php','subsection','maindiv')\" title='Add Store'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;
				";
	}
	if(in_array('8',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editstore\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addstore.php','subsection','maindiv') title=\"Edit Store\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('9',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=deletestores onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Stores\"><span class=\"deleterecord\">&nbsp;</span></a>";
	}*/
}//Edit by Ahsan on 08/02/2012

/*
$navbtn = "<a class='button2' id='addstores' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addstore.php','subsection')\" title='Add Store'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editstore\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addstore.php','subsection') title=\"Edit Store\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletestores onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Stores\"><span class=\"deleterecord\">&nbsp;</span></a>";
//&nbsp;<a href=\"javascript: getgrid('managesuppliers.php',document.$form.checks,'Suppliers_tab')\" title=\"View Suppliers\">SUPPLIERS</a>
			
/********** END DUMMY SET ***************/
?><head>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
</head>

<div id='maindiv'>
	<div class="breadcrumbs" id="breadcrumbs">Stores</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<br />
<br />
<div id="sugrid"></div>