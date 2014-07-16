<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
$customerid	=	$_GET['id'];
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 		= 	'customercollections.php';
$div		=	'childdiv';
$form 		=	"frm1customercollections";	
$css 		= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 		= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************
if($customerid!='')
{
	$and=" AND 
				fkcustomerid = '$customerid'	GROUP BY pksaleid ";
}//changed $dbname_main to $dbname_detail on line 32 by ahsan 22/02/2012
$query		=	"SELECT paymentmethod,
					CASE paymentmethod
					WHEN 'c'
					THEN 'Cash'
					WHEN 'cc'
					THEN 'Credit Card'
					WHEN 'fc'
					THEN 'Foreign Currency'
					ELSE 'Cheque'
					END AS type, pkcollectionid, amount,FROM_UNIXTIME(datetime,'%d-%m-%Y %h:%i:%s') trdatetime
				   FROM $dbname_detail.collection
				  where fkaccountid='$customerid'";

/************* DUMMY SET ***************/
$labels = array("ID","Collection #","Date","Type","Amount");
$fields = array("pkcollectionid","pkcollectionid","trdatetime","type","amount");

/* Changed By yasir -- 06-07-11
   main-content div by collections for Bill Detail
   main-content div by collections for Bill Collections
   
*/

$navbtn	=	"<a class='button2' id='printcollections' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(1,document.$form.checks,'processduplicatecollection.php','duplicatediv','childdiv','duplicatecollection')\" title='Print'>
				<span class='print'>Print</span>
			</a>";
				/*<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'sale.php','main-content','mainpanel','adjustment') title=\"Bill Adjustment\"><span class=\"\">Bill Adjustment</span></a>&nbsp;
				jQuery('#main-content').load('creditorbilling.php');
				*/
?>
<div id="duplicatediv"></div>
<div id="gencreditdiv"></div>
<!--Added By Yasir - 06-07-11-->
<div id="collections"></div>
<div id="childdiv"></div>
<!---->
<div id="mainpanel" style="clear:both;float:left;width:100%;">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<!--Added by Yasir - 08-07-11-->
<script language="javascript" type="text/javascript">
	document.getElementById('searchFieldcollectiondetails').focus();	
</script>