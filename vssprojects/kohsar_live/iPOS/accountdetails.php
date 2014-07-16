<?php 
include_once("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
$sessionclosing	=	$_SESSION['sessionclosing'];
$id	=	$_GET['id'];
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 		= 	'accountdetails.php';
$div		=	'cccc';
$form 		=	"frmaccdetails";	
$css 		= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 		= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************
// replaced from_unixtime(chequedate,'%d-%m-%y %h:%i:%s') with IF(chequedate='0','-',from_unixtime(chequedate,'%d-%m-%y %h:%i:%s')) as chequedate by Yasir - 07-07-11
// Removed  AND	fkclosingid = '$closingsession' by Yasir - 21-07-11 
//changed $dbname_main to $dbname_detail on line 33, 34, 35 by ahsan 22/02/2012
$query	=	"SELECT 
					pkaccountpaymentid, 
					title as accounttitle,
					description,					
					from_unixtime(paymentdate,'%d-%m-%y %h:%i:%s') as paytime, 
					round(amount,2) as amount,
					IF(paymentmethod ='c','Cash','Cheque') as paymentmethod,				
					chequeno, 	
					IF(chequedate='0','-',from_unixtime(chequedate,'%d-%m-%y %h:%i:%s')) as chequedate
					,(select bankname from bank where bankid=pkbankid) as bankname
			FROM 
				$dbname_detail.accountpayment,
				$dbname_detail.account
			WHERE 
				fkstoreid 		= '$storeid'  AND  
				fkaccountid		= id AND
				id				=	'$id'	
				";
/************* DUMMY SET ***************/
$labels = array("ID","Account Title","Description","Pay Time","Amount","Method","Bank","Cheque#","Cheque Date");
$fields = array("pkaccountpaymentid","accounttitle","description","paytime","amount","paymentmethod","bankname","chequeno","chequedate");
$sortorder	=	" paymentdate DESC ";
$navbtn	=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'processpayout.php','printdiv','cccc','duplicatepayout') title=\"Print a Duplicate Copy\"><span class=\"\">Print</span></a>&nbsp;";
?>
<div id="printdiv"></div>
<div id="cccc" style="clear:both;float:left;width:100%;padding-top:15px;">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?>
</div>