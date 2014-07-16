<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(3);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$deltype	=	"delcategory";
include_once("delete.php");

$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}

/************* DUMMY SET ***************/
//$labels = array("ID","Picture","Product Name","Description");
//$fields = array("pkproductid","defaultimage","productname","description");
$dest 	= 	'managecategories.php';
$div	=	'maindiv';
$form 	= 	"catfrm1";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkcategoryid,
				name,
				description,
				categoryimage
			FROM
				category
			WHERE
				1
			";
$navbtn	=	"";
$sortorder	=	"name ASC"; // takes field name and field order e.g. brandname DESC
if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	if(in_array('38',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addproducts' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addcategory.php','sugrid','maindiv','','$formtype')\" title='Add Category'>
					<span class='addrecord'>&nbsp;</span>
					</a>&nbsp;";
	}
	if(in_array('39',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editproducts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addcategory.php','sugrid','maindiv','','$formtype') title=\"Edit Category\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}//if
/*}else{
	if(in_array('38',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addproducts' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addcategory.php','sugrid','maindiv')\" title='Add Category'>
					<span class='addrecord'>&nbsp;</span>
					</a>&nbsp;";
	}
	if(in_array('39',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editproducts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addcategory.php','sugrid','maindiv') title=\"Edit Category\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}//if*/
	if(in_array('55',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Categories\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}//if
	if(in_array('44',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editproducts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(2,document.$form.checks,'categorymap.php','itemgrid','maindiv') title=\"Show Category Map\"><b>Category Map</b></a>&nbsp;";
	}//if
	if(in_array('45',$actions))
	{
		$navbtn .="| <a class=\"button2\" id=\"editproducts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" 
		href=javascript:showpage(1,document.$form.checks,'manageproducts.php','itemgrid','maindiv','category')
		
	
		
		title=\"Show Category Map\"><b>Products</b></a>&nbsp;";
	}
//if
}//edit by Ahsan on 08/02/2012
//		getgrid('manageproducts.php',document.$form.checks,'1_tab','maindiv','category')	
//showpage(2,document.$form.checks,'manageproducts.php','sugrid','maindiv','category')
/********** END DUMMY SET ***************/
?>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>
<script src='../includes/js/jquery.simplemodal.js' type='text/javascript'></script>
<script src='../includes/js/basic.js' type='text/javascript'></script>-->




<?php /*?><div id="attdiv"></div>
<div id="itemgrid"></div>
<div id='maindiv'>
<div id="sugrid">
<?php 
	//require_once("../OpenCrypt/ajax_tree.php");
	//print ajax_tree(0,0, 1);
?>

</div>

<div class="breadcrumbs" id="breadcrumbs"></div>
</div>
<?php
//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />

</div><?php */?>

<div id="attdiv"></div>
<div id="itemgrid"></div>
<div id='maindiv'>
<div id="sugrid"></div>
<div class="breadcrumbs" id="breadcrumbs">Categories</div>
<?php 
//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?>
<br />
<br />

</div>
