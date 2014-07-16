<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(29);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
$packingid	=	$_GET['id'];
$shipmentid	=	$_GET['param'];
if($packingid=='')
{
	$retain		=	explode("_",$_GET['retain']);
	$packingid	=	$retain[0];
	$shipmentid	=	$retain[1];
}
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
				$delcondition =" pkpackingid  = '$value' ";
				$AdminDAO->deleterows('packing',$delcondition,'1');
			}
		}
	if(sizeof($ids)>0)
	{
		print"<script>adminnotice('Selected Box has been deleted.',0,5000);</script>";
	}
	$packingid	=	$_GET['param'];	
}
/************* DUMMY SET ***************/
//$labels = array("ID","Picture","Product Name","Description");
//$fields = array("pkproductid","defaultimage","productname","description");
$dest 	= 	'managebox.php';
$div	=	'attdiv';
$form 	= 	"frm1boxes";	
define(IMGPATH,'../images/');
if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition
	 $query 	= 	"SELECT 
				pkpackingid,
				packingname,
				(SELECT packingname FROM packing p2 WHERE p1.fkpackingid=p2.pkpackingid) as pname
			FROM
				packing p1
			WHERE
				fkpackingid ='$packingid'
				
			";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
 $query 	= 	"SELECT 
				pkpackingid,
				packingname,
				(SELECT packingname FROM packing p2 WHERE p1.pkpackingid=p2.pkpackingid) as pname
			FROM
				packing p1
			WHERE
				pkpackingid ='$packingid'
			";
}//end edit
$navbtn	=	"";
if(in_array('63',$actions))
{
	$navbtn .= "<a class='button2' id='addpacking' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addbox.php','itemgrid','attdiv','$shipmentid-$packingid')\" title='Add Box'>
				<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
}
if(in_array('64',$actions))
{
	$navbtn .="	<a class=\"button2\" id=\"editpacking\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addbox.php','itemgrid','attdiv','$shipmentid-$packingid') title=\"Edit Box\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('65',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletepackings onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$packingid') title=\"Delete Boxes\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
// if
/********** END DUMMY SET ***************/
?>
<div id="itemgrid"></div>
<div id="sugrid"></div>
<div id="attdiv"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Boxes</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />

</div>