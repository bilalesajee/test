<?php

include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$del;
/************* DUMMY SET ***************/
$labels = array("ID","Attribute Option Name");
$fields = array("pkattributeoptionid","attributeoptionname");
$dest 	= 	'loadattributes.php';
$div	=	'subsection';
$form 	= 	"optionfrm2";
$deltype	=	"attributeoption";
include_once("delete.php");
$attributeid	=	$_GET['id'];
/*$oper			=	$_GET['oper'];
if($attributeid!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$attributeid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" pkattributeoptionid = '$value' ";
				$AdminDAO->deleterows('attributeoption',$delcondition);
			}
		}
	$attributeid="";
}*/

	if($_REQUEST['param']!='undefined' && $_REQUEST['param']!='')
	{
		$attributeid	=	$_REQUEST['param'];	
	}

/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
  $query 	= 	"SELECT 
							pkattributeoptionid,
							attributeoptionname
					FROM
							attributeoption
					WHERE
						fkattributeid = '$attributeid'
					AND 
						attributeoptiondeleted <> 1
						";

if($attributeid!='')
{
$btnadd="<a class=\"button2\" id=\"adbrand\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:loadsection('addive','addoptions.php?attid=$attributeid') title=\"Add Option\"><span class=\"addrecord\">&nbsp;</span></a>&nbsp;";	
}
$sortorder	=	"attributeoptionname ASC"; // takes field name and field order e.g. brandname DESC
$navbtn = "
$btnadd

<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addoptions.php','addive','$div') title=\"Edit Option\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$attributeid') title=\"Delete Options\"><span class=\"deleterecord\">&nbsp;</span></a>
			";
/********** END DUMMY SET ***************/
?><head>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
</head>

<div id='<?php print"$div";?>'>
<div id="addive"></div>
<div class="breadcrumbs" id="breadcrumbs">Attribute Options</div>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?>
</div>
<br />
<br />