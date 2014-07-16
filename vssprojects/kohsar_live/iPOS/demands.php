<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");

global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
 $customerid	=	$_GET['id'];
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 		= 	'demands.php';
$div		=	'childdiv';
$form 		=	"frm1billing";	

$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" itemdemandsid  = '$value' ";
				$AdminDAO->deleterows("$dbname_detail.itemdemands",$delcondition,1);
			}
		}
}
$css 		= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 		= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************
//changed $dbname_main to $dbname_detail on line 28, 29, 34 by ahsan 22/02/2012
 
 $query			=	"SELECT i.itemdemandsid,IF(i.status = 1, 'Pending', IF(i.status = 2, 'Complete', 'Not Available')) AS status, i.quantity, remarks, from_unixtime( i.datetime, '%d-%m-%Y %h:%m:%s' ) AS addtime, i.mobile, CONCAT( a.firstname, ' ', a.lastname ) insertedby,CONCAT(a.firstname,' ', a.lastname) insertedby ,
						IF(i.customer='',(select CONCAT(cs.firstname,' ', cs.lastname) customername from main.customer cs where pkcustomerid=i.fkaccountid),i.customer)  as customername,reason
FROM $dbname_detail.itemdemands i
LEFT JOIN main.addressbook a ON a.pkaddressbookid = i.fkaddressbookid

					WHERE 1=1 
						 ";
	$orderby='i.itemdemandsid desc';
/************* DUMMY SET ***************/
$labels = array("ID","Customer Name","Mobile","Required By Date","Demands","Status","Reasons");
$fields = array("itemdemandsid","customername","mobile","addtime","remarks","status","reason");

/* Changed By yasir -- 06-07-11
   main-content div by collections for Bill Detail
   main-content div by collections for Bill Collections
   
*/ 

/*$navbtn	=	"<a class=\"button2\" id=\"editdemands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','gencreditdiv','childdiv','$customerid') title=\"Edit Demand\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";*/
 $navbtn ="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Reasons\">Delete Demand</a>";
				
?>
<div id="gencreditdiv"></div>
<?php /*?><!--Added By Yasir -- 06-07-11--><?php */?>
<div id="collections"></div>
<div id="childdiv"></div>
<div id='maindiv'>
<?php /*?><!----><?php */?>
<div id="mainpanel" style="clear:both;float:left;width:100%;">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$orderby);
?>
</div></div>

<?php /*?><!--Added by Yasir - 08-07-11--><?php */?>
<script language="javascript" type="text/javascript">
	document.getElementById('searchFieldchilddiv').focus();	
</script>