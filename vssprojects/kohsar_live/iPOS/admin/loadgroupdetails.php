<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");

/************* DUMMY SET ***************/
$labels = array("ID","Permission","Permission Level");
$fields = array("pkpermissionid","permission","permissionlevel");
$dest 	= 	'loadgroupdetails.php';
$div	=	'sugrid';
$form 	= 	"frm2";	
$groupid	=	$_GET['id'];
$id		=	$_GET['id'];
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
 $query 	= 	"SELECT 
							p.pkpermissionid,
							p.permission,
							pl.permissionlevel
					FROM
							grouppermission gp,
							permission p,
							permissionlevel pl
					WHERE
							gp.fkgroupid = '$groupid' AND
							gp.fkpermissionid = p.pkpermissionid AND
							p.fklevelid = pl.pkpermissionlevelid
							";

$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adddemand.php','subsection')\" title='Add Permission'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddetails.php','subsection') title=\"Edit Permission\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Permissions\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
			";
			
/********** END DUMMY SET ***************/
?><head>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
</head>
<div class="breadcrumbs" id="breadcrumbs">Demand Details</div>
<div id='maindiv'>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?></div>
<br />
<br />
<div id="sugrid_child"></div>