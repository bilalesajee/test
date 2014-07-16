<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(28);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
//*************delete************************
$shipmentid		=	$_REQUEST['id'];
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
			print"<script>adminnotice('Selected Packing has been deleted.',0,5000);</script>";
		}
	$shipmentid	=	$_REQUEST['param'];		
}
/************* DUMMY SET ***************/
//$labels = array("ID","Picture","Product Name","Description");
//$fields = array("pkproductid","defaultimage","productname","description");
$dest 	= 	'managepacking.php';
$div	=	'subsection';
$form 	= 	"frm1packing";	
define(IMGPATH,'../images/');
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	$query 	= 	"SELECT 
				pkpackingid,
				packingname
			FROM
				packing
			WHERE
				fkpackingid = '' AND
				fkshipmentid='$shipmentid'
			";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$query 	= 	"SELECT 
				pkpackinglistid, fkshipmentid, packnumber, packtime, packedby, quantity,Concat(firstname, ' ', lastname) as name,

				IF(packtime='0','--------',FROM_UNIXTIME(packtime, '%d-%m-%y')) as packtime
			FROM
				packinglist left join addressbook on pkaddressbookid=packedby
			WHERE
				
				fkshipmentid='$shipmentid'
			";
}//end edit
$navbtn	=	"";
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	if(in_array('60',$actions))
	{
		$navbtn .= "<a class='button2' id='addpacking' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addpacking.php','sugrid','subsection','$shipmentid')\" title='Add Package'>
					<span class='addrecord'>&nbsp;</span>
					</a>&nbsp;";
	}
	if(in_array('61',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editpacking\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addpacking.php','sugrid','subsection','$shipmentid') title=\"Edit Package\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('62',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=deletepackings onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$shipmentid') title=\"Delete Packages\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('67',$actions))
	{
		$navbtn .="<a href=\"javascript:showpage(1,document.$form.checks,'managebox.php','attdiv','subsection','$shipmentid') \" title=\"Boxes\"><b> Boxes</b></a>";
	}
	// if
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$navbtn	="<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addpacking.php','eidtpurchase','subsection','$param') \" title=\"Edit Packing\"><span class=\"editrecord\">&nbsp;</span></a>";
	$labels	=	array('ID','Pack Number','QTY','Pack Time','Added By');
	$fields	=	array('pkpackinglistid','packnumber','quantity','packtime','name');
}//end edit
/********** END DUMMY SET ***************/
?>
<div id="sugrid"></div>
<div id="attdiv"></div>
<div id="itemgrid"></div>
<div id="eidtpurchase"></div><?php //line added by ahsan from main, 16/02/2012?>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Packing</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />

</div>