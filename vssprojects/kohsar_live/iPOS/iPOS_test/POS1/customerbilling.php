<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
$customerid	=	$_GET['id'];
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 		= 	'customerbilling.php';
$div		=	'childdiv';
$form 		=	"frm1billing";	
$css 		= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 		= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************
if($customerid!='')
{
	$and=" AND 
				fkaccountid = '$customerid'	GROUP BY pksaleid ";
}//changed $dbname_main to $dbname_detail on line 36 by ahsan 22/02/2012
$query			=	"SELECT 
						pksaleid,
						round(globaldiscount,2) as discount,
						from_unixtime(updatetime,'%d-%m-%Y %h:%m:%s') as datetime, 
						countername, 
						CONCAT(firstname,' ', lastname) employeename,
						totalamount as total,
						cash,
						cc,
						fc,
						cheque,
						round((cash+cc+fc+cheque),2) as paid,
						(totalamount-cash-cc-fc-cheque-globaldiscount) as credit
					FROM
						$dbname_detail.sale s,addressbook LEFT JOIN employee ON (pkaddressbookid = fkaddressbookid)
					WHERE
						s.fkstoreid		=	'$storeid' AND
						fkuserid		=	fkaddressbookid AND
						fkaccountid	=	'$customerid'
					";
/************* DUMMY SET ***************/
$labels = array("ID","Bill #","Date","Counter","Cashier","Cash","CC","FC","Cheque","Total","Paid","Discount","Credit");
$fields = array("pksaleid","pksaleid","datetime","countername","employeename","cash","cc","fc","cheque","total","paid","discount","credit");

/* Changed By yasir -- 06-07-11
   main-content div by collections for Bill Detail
   main-content div by collections for Bill Collections
   
*/

$navbtn	=	"
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'codeitem.php','collections','childdiv','billing') title=\"Bill Details\"><span class=\"\">Bill Detail</span></a>&nbsp;
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'collections.php','collections','childdiv','bill') title=\"Collections\"><span class=\"\">Collections</span></a>&nbsp;
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(2,document.$form.checks,'gencreditorbilling.php','collections','childdiv','$customerid') title=\"Bill Collections\"><span class=\"\">Bill Collections</span></a>&nbsp;
			";
?>
<div id="gencreditdiv"></div>
<?php /*?><!--Added By Yasir -- 06-07-11--><?php */?>
<div id="collections"></div>
<div id="childdiv"></div>
<?php /*?><!----><?php */?>
<div id="mainpanel" style="clear:both;float:left;width:100%;">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>
<?php /*?><!--Added by Yasir - 08-07-11--><?php */?>
<script language="javascript" type="text/javascript">
	document.getElementById('searchFieldchilddiv').focus();	
</script>