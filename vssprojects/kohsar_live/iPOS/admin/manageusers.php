<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(11);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//require_once("brandsmenu.php"); 
//*************delete************************
$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
$empid		=	$_REQUEST['id'];
$groupid	=	$_REQUEST['groupid'];	
$groupid	=	$_REQUEST['groupid'];	
if($groupid!='')
{
	$group_and	=	" AND g.pkgroupid='$groupid' ";	
}
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$select	=	"SELECT 
							ad.username,
							ad.password,
							emp.pkemployeeid,
							ad.email,
							emp.cnic,
							CONCAT(ad.firstname ,' ', ad.lastname) as empname,
							CONCAT(ad.phone ,', ', ad.mobile) as phoneno,
							CONCAT(ad.address1 ,', ', ad.address2,' ',ct.cityname,' ',st.statename,' ',ad.zip,' ',c.countryname) as address,
							c.countryname,
							g.groupname
							";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$select	=	"SELECT 
							ad.username,
							ad.password,
							emp.pkemployeeid,
							IF(emp.loginallowed>0,'Blocked','Active') loginallowed,
							ad.email,
							emp.cnic,
							CONCAT(ad.firstname ,' ', ad.lastname) as empname,
							CONCAT(ad.phone ,', ', ad.mobile) as phoneno,
							CONCAT(ad.address1 ,', ', ad.address2,' ',ct.cityname,' ',st.statename,' ',ad.zip,' ',c.countryname) as address,
							c.countryname,
							g.groupname
							";
}//end edit

$from	=	" FROM
						addressbook ad
						LEFT JOIN countries c ON (c.pkcountryid = ad.fkcountryid) 
						LEFT JOIN city ct ON (ct.pkcityid = ad.fkcityid)
						LEFT JOIN state st ON (st.pkstateid = ad.fkstateid),
						employee emp
						LEFT JOIN groups g ON (g.pkgroupid	=	emp.fkgroupid)";
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$where	=	" WHERE
						emp.fkaddressbookid =ad.pkaddressbookid AND
						emp.employeedeleted <> 1  $group_and
					group by pkemployeeid
						";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$where	=	" WHERE
						emp.fkaddressbookid =ad.pkaddressbookid AND
						emp.employeedeleted <> 1
					group by pkemployeeid
						";
}//end edit
if($delid!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" pkemployeeid = '$value' ";
				$AdminDAO->deleterows('employee',$delcondition);
			}
		}
}
/************* DUMMY SET **************
$labels = array("ID","Name","Email","Phone","NIC","Designation","Address");
$fields = array("pkemployeeid","empname","email","phoneno","cnic",'groupname',"address");
*/
$dest 	= 	"manageusers.php";
$div	=	'maindiv';
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$form 	= 	"manageusersfrm1";	
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$form 	= 	"frm1";	
}//end edit
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');

$query 	= $select.$from.$where;
//echo $query;
//$AdminDAO->getrows('brand',$delcondition);
//$navbtn = array("Add","Edit","Delete","custom");

if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	if(in_array('35',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addusers' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adduser.php','subsection','maindiv','','$formtype')\" title='Add User'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('36',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editusers\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adduser.php','subsection','maindiv','','$formtype') title=\"Edit User\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('37',$actions))
	{
		$navbtn .="<a class=\"button2\" id=\"editusers\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('manageusers.php','maindiv','') title=\"Delete Users\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}
/*}else{
/*	if(in_array('35',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addusers' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adduser.php','subsection','maindiv')\" title='Add User'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('36',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editusers\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adduser.php','subsection','maindiv') title=\"Edit User\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('37',$actions))
	{
		$navbtn .="<a class=\"button2\" id=\"editusers\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('manageusers.php','maindiv','') title=\"Delete Users\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}*/
}
if($_SESSION['siteconfig'] != 1){//edit by ahsan on 09/02/2012
	$navbtn .="	<a class=\"button2\" id=\"editusers\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'userclosings.php','subsection','maindiv') title=\"User Closings\"><span ><b>User Closings</b></span></a>&nbsp;";
}//edit by Ahsan on 09/02/2012


$groups			=	"<select name=\"group\" id=\"group\" style=\"width:100px;\" onchange=\"listusers(this.value)\"><option value=\"\">All</option>";
$listusergroups	=	$AdminDAO->getrows("groups","*");
// pkgroupid 	groupname
for($i=0;$i<sizeof($listusergroups);$i++)
{
	$pkgroupid	=	$listusergroups[$i]['pkgroupid'];
	$groupname		=	$listusergroups[$i]['groupname'];
	$select		=	"";
	if($pkgroupid == $groupid)
	{
		$select = "selected=\"selected\"";
	}
	$groups	.=	"<option value=\"$pkgroupid\" $select>$groupname</option>";
}
$groups			.=	"</select>";
/********** END DUMMY SET ***************/
?><head>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
</head>


<div id='maindiv'>
User Group <?php echo $groups;?>
	<div class="breadcrumbs" id="breadcrumbs">Employees</div>
	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<br />
<br />
<div id="sugrid"></div>
<script language="javascript">
	function listusers(groupid)
	{
		jQuery('#maindiv').load('manageusers.php?groupid='+groupid);	
	}
</script>