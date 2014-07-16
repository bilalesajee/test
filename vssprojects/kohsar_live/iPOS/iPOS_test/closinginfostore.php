<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(25);
//$closingid	=	$_GET['id'];
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
//***********************sql for record set**************************//changed $dbname_main to $dbname_detail on line 32, 46, 47 by ahsan 22/02/2012
$query	=	"SELECT 
						ci.countername,
						from_unixtime(closingdate,'%Y-%m-%d  %h:%i:%s') as closingdate,
						round(SUM(amount),2) as payouts,
						(SELECT CONCAT(firstname,' ',lastname) from $dbname_detail.addressbook where pkaddressbookid=fkaddressbookid) as username,
						round(openingbalance,2) as openingbalance,
						round(sum(cashsale),2) as cashsale,
						round(sum(creditsale),2) as creditsale,
						round(sum(creditcardsale),2) as creditcardsale,
						round(sum(chequesale),2) as chequesale,
						round(sum(foreigncurrencysale),2) as foreigncurrencysale,
						round(sum(netcash),2) as netcash,
						round(sum(declaredamount),2) as declaredamount,
						IF( cashdiffirence > 0,CONCAT(round(sum(cashdiffirence),2),' Extra'),CONCAT(round(cashdiffirence,2),' Short') ) as cashdiffirence  ,
						sum(totalbills) as totalbills,
						round(sum(totalsale),2) as totalsale,
						sum(totalitems) as totalitems
				FROM 
					$dbname_detail.closinginfo ci,
					$dbname_detail.accountpayment ap
				WHERE 
					closingstatus='a' AND
					ap.countername	=	ci.countername
				GROUP BY
					ci.countername
				";
/************* DUMMY SET ***************/
$labels = array("ID","Counter","Date & Time","User","Opening Bal","Cash","Credit","CC","Cheque","F.C","Net Cash","Declared","Diff","Bills","Total Sale","Total Items","Payouts");
$fields = array("pkclosingid","countername","closingdate","username","openingbalance","cashsale","creditsale","creditcardsale","chequesale","foreigncurrencysale","netcash","declaredamount","cashdiffirence","totalbills","totalsale","totalitems",'payouts');

$navbtn	=	"";
//$navbtn="";
?>
<div id="mainpanel">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionarray," ORDER BY closingdate DESC ");
?>
</div>