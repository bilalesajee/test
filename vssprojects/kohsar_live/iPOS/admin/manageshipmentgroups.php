<?php 

//echo $_SERVER['QUERY_STRING'];
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
//require_once("brandsmenu.php"); 
//*************delete************************
$id				=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
/*$qs = explode('&oper=',$_SERVER['QUERY_STRING']);//$qs[1];
$_SESSION['qs'] = $qs[0] ;
*/
if($id!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$id);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" pkshipmentgroupid = '$value' ";
				$AdminDAO->deleterows('shipmentgroups',$delcondition);
			}
		}
}
if($id!="-1")
{
	//$from	=	" , shipmentgroupjunc ";
	//$where 	= " AND fkshipmentid = $id AND fkshipmentgroupid = pkshipmentgroupid";
}
/************* DUMMY SET ***************/
$labels = array("ID","Group Name","Percentage");
$fields = array("pkshipmentgroupid","shipmentgroupname","percentage");
$dest 	= 	'manageshipmentgroups.php';
$div	=	'subsection';
$form 	= 	"shipmentgroupfrm";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
//***********************sql for record set**************************
 $query 	= 	"SELECT 
						pkshipmentgroupid,
						shipmentgroupname,
						percentage
					FROM
						shipmentgroups 
						$from
					
					WHERE
						shipmentgroupsdeleted<>1
						$where
									
					";
//*******************************************************************
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage('0',document.$form.checks,'addshipmentgroup.php','sgroups','maindiv')\" 

title='Add Shipment Group'>


				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addshipmentgroup.php','sgroups','scharges') title=\"Edit Shipment Group\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Shipment Groups\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
		";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage('0',document.$form.checks,'addshipmentgroup.php','sgroups','subsection','','$formtype')\" 

title='Add Shipment Group'>


				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addshipmentgroup.php','sgroups','subsection','','$formtype') title=\"Edit Shipment Group\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Shipment Groups\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
		";
}//end edit
			
/*showpage(0,'','addshipmentgroup.php','sgroups','scharges')********* END DUMMY SET ***************/


?><head>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
</head>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Shipment Groups</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<br />
<br />
<div id="sugrid"></div>