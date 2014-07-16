<?php 
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(6);
//$labels	 	=	$rights['labels'];
//$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//require_once("brandsmenu.php"); 
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
				$delcondition 		=" pkdiscountid = '$value' ";
				$delconditiondetail =" fkdiscountid = '$value' ";
				$AdminDAO->deleterows('discount',$delcondition,"1");
				$AdminDAO->deleterows('discountdetail',$delconditiondetail,"1");
			}
		}
}
/************* DUMMY SET ***************
$labels = array("ID","Brand Name","Country");
$fields = array("pkbrandid","brandname","countryname");
*/
$labels = array("ID","Location","Discount Name","Start Date","End Date","Discount Type","Status");
$fields = array("pkdiscountid","storename","discountname","startdate","enddate","typename","discountstatus");

$dest 	= 	'managediscounts.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
						d.pkdiscountid,d.discountname,FROM_UNIXTIME(d.startdate,'%d %M %Y') as startdate,FROM_UNIXTIME(d.startdate,'%Y %m %d') as sortstartdate,FROM_UNIXTIME(d.enddate,'%d %M %Y') as enddate,FROM_UNIXTIME(d.enddate,'%Y %m %d') as sortenddate,IF(d.discountstatus= 'i','In Active','Active')as discountstatus,
						t.typename,storename
					FROM
						discount d,
						store,
						discounttype t
					WHERE
						fkstoreid	=	pkstoreid AND
						d.fkdiscounttypeid=	t.pkdiscounttypeid 
					
					";
//$AdminDAO->getrows('brand',$delcondition);
//$navbtn = array("Add","Edit","Delete","custom");
$navbtn	=	"";

if($_SESSION['siteconfig'] != 3){//edit by ahsan on 08/03/2012
	if(in_array('16',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='adddiscount' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adddiscount.php','subsection','maindiv','','$formtype')\" title='Add Discount'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('17',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editdiscount\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddiscount.php','subsection','maindiv','','$formtype') title=\"Edit Discount\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('18',$actions))
	{
		$navbtn .="<a class=\"button2\" id=\"deletediscounts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Discounts\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}
}//Edit by Ahsan on 08/03/2012
	//$navbtn .="	<a href=\"javascript: loadsubgrid('sugrid',document.$form.checks,'discountdetails.php','maindiv')\" title=\"View Discount Details\"><b>Discount Detail</b></a>&nbsp;";


	//$navbtn .="| <a href=\"javascript: getgrid('barcodestock.php',document.$form.checks,'Stocks_tab','maindiv','brand')\" title=\"View Stocks\"><b>View Stocks</b></a>";

			
/********** END DUMMY SET ***************/


?><head>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>
</head>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Discounts and Offers</div>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?></div>
<br />
<br />
