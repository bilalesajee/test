<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;

/**************RIGHTS***************************/
$rights	 	=	$userSecurity->getRights(55);
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
$dest 	= 	'dbsynch.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				s.pkdbsynchid,
				CONCAT(a.firstname,' ',a.lastname) as employeename,
				st.storename as storedb,
				FROM_UNIXTIME(s.updatetime,'%d-%m-%Y') updatetime,
				FROM_UNIXTIME(s.updatetime,'%Y-%m-%d') sortingdate,
				totalupdates
				
			FROM
				store st,
				dbsynch s, 
				addressbook a
			WHERE
				a.pkaddressbookid=s.fkaddressbookid AND 
				s.fkstoreid=st.pkstoreid
			";
/*			ORDER BY
				pkdbsynchid DESC*/
//$AdminDAO->getrows('brand',$delcondition);
//$navbtn = array("Add","Edit","Delete","custom");
$navbtn	=	"";

if(in_array('109',$actions))
{
	$navbtn .= "<a class='button2' id='addstores' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','dbsynchfrm.php','subsection','maindiv')\" title='Synchronize Database'>
				<span class=''>&nbsp;<img src='../images/iSync-icon.png' width=12px height=14px></span>
			</a>&nbsp;
			";
}
/********** END DUMMY SET ***************/
?>
</head>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Data Synchronization</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<br />
<br />
<div id="sugrid"></div>