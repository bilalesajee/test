<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(25);
$closingid	=	$_GET['id'];
if($_GET['param']=='printclosing')
{
	
	?>
    <script language="javascript">
		
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
		//jQuery('body').append('closingprint.php');
 		window.open('closingprint.php?id=<?php echo $closingid;?>','Closing',display); 
    </script>
    <?php
}
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//$H->dump($rights,1);
$dest 		= 	'closinginfo.php';
$div		=	'mainpanel';
$form 		=	"frm1";	
define(IMGPATH,'images/');
//***********************sql for record set**************************//changed $dbname_main to $dbname_detail on line 32, 48 by ahsan 22/02/2012
$query	=	"SELECT 
						pkclosingid,
						
						from_unixtime(closingdate,'%Y-%m-%d  %h:%i:%s') as closingdate,
						round((SELECT SUM(amount) FROM $dbname_detail.accountpayment WHERE fkclosingid = pkclosingid) ,2) as payouts,
						(SELECT CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=fkaddressbookid) as username,
						countername,
						round(openingbalance,2) as openingbalance,
						round(cashsale,2) as cashsale,
						round(creditsale,2) as creditsale,
						round(creditcardsale,2) as creditcardsale,
						round(chequesale,2) as chequesale,
						round(foreigncurrencysale,2) as foreigncurrencysale,
						round(netcash,2) as netcash,
						round(declaredamount,2) as declaredamount,
						IF( cashdiffirence > 0,CONCAT(round(cashdiffirence,2),' Extra'),CONCAT(round(cashdiffirence,2),' Short') ) as cashdiffirence  ,
						totalbills,
						round(totalsale,2) as totalsale,
						totalitems
				FROM 
					$dbname_detail.closinginfo 
				WHERE 
					closingstatus='a' AND
					countername='$countername'
				
				";
/************* DUMMY SET ***************/
$labels = array("ID","CID","Date & Time","User","Counter","Opening Bal","Cash","Credit","CC","Cheque","F.C","Net Cash","Declared","Diff","Bills","Total Sale","Total Items","Payouts");
$fields = array("pkclosingid","pkclosingid","closingdate","username","countername","openingbalance","cashsale","creditsale","creditcardsale","chequesale","foreigncurrencysale","netcash","declaredamount","cashdiffirence","totalbills","totalsale","totalitems",'payouts');

$navbtn	=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'closinginfo.php','main-content','mainpanel','printclosing') title=\"Print Closing Details\"><span class=\"\">Print</span></a>&nbsp;
			";
$navbtn	.=	"&nbsp;|&nbsp; <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:void(0) onclick=loadclosingfrm(); title=\"Proccess Closing (CTRL+Z)\"><span class=\"\">Proccess Closing</span></a>&nbsp;
			";
$navbtn	.=	"&nbsp;|&nbsp; <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'closinginfostore.php','main-content','mainpanel') title=\"Closing Report\"><span class=\"\">Closing Report</span></a>&nbsp;
			";						
//$navbtn="";
?>
<div id="mainpanel">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionarray," ORDER BY closingdate DESC ");
?>
</div>