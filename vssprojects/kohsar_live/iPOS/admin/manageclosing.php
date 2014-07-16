<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(6);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$closingid	=	$_GET['id'];
//echo $_GET['param'];
//$H->dump($rights,1);

$dest 		= 	'manageclosing.php';
$div		=	'mainpanel';
$form 		=	"frmclosing";	
define(IMGPATH,'images/');
$employeeid				=	$_SESSION['addressbookid'];
//***********************sql for record set**************************
/*$query	=	"SELECT 
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
						round(netcash+(cashcollect+cccollect+fccollect+chequecollect),2) as netcash,
						round(declaredamount,2) as declaredamount,
						IF( cashdiffirence > 0,CONCAT(round(cashdiffirence-(cashcollect+cccollect+fccollect+chequecollect),2),' Extra'),CONCAT(round(cashdiffirence-(cashcollect+cccollect+fccollect+chequecollect),2),' Short') ) as cashdiffirence  ,
						totalbills,
						round(totalsale,2) as totalsale,
						totalitems
				FROM 
					$dbname_detail.closinginfo 
				WHERE 
					closingstatus='a' 
					
				
				";*/
				
				 // Removed -(cashcollect+fccollect) from cashdiffernce by Yasir 12-08-11
				 // Removed +(cashcollect+fccollect) from net cash by yasir 12-08-11
				 
 if($employeeid==1928)
{
	$andchk=" and closingdate > 1395646473";
	//$and='';
}

$query	=	"SELECT 
						pkclosingid,
						
						from_unixtime(openingdate,'%d-%m-%y  %h:%i %p') as 	openingdate,from_unixtime(closingdate,'%d-%m-%y  %h:%i %p') as 	closingdate,
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
						IF( (cashdiffirence-advance_bk) > 0,CONCAT(round((cashdiffirence-advance_bk),2),' Extra'),CONCAT(round((cashdiffirence-advance_bk),2),' Short') ) as cashdiffirence  ,
						totalbills,
						round(totalsale+advance_bk,2) as totalsale,
						totalitems,
						IF(accdatasent = 0, 'Pending','Confirm') AS account_status
				FROM 
					$dbname_detail.closinginfo 
				WHERE 
					closingstatus='a' $andchk 
					
				
				";
/************* DUMMY SET ***************/
if($employeeid==1888)
{
$labels = array("ID","CID","Opening Time","Closing Time","User","Counter","Opening Bal","Cash","Credit","CC","Cheque","Net Cash","Declared","Diff","Bills","Total Sale","Total Items","Payouts","AccStatus");
$fields = array("pkclosingid","pkclosingid","openingdate","closingdate","username","countername","openingbalance","cashsale","creditsale","creditcardsale","chequesale","netcash","declaredamount","cashdiffirence","totalbills","totalsale","totalitems","payouts","account_status");
}else{

$labels = array("ID","CID","Opening Time","Closing Time","User","Counter","Opening Bal","Cash","Credit","CC","Cheque","F.C","Net Cash","Declared","Diff","Bills","Total Sale","Total Items","Payouts");
$fields = array("pkclosingid","pkclosingid","openingdate","closingdate","username","countername","openingbalance","cashsale","creditsale","creditcardsale","chequesale","foreigncurrencysale","netcash","declaredamount","cashdiffirence","totalbills","totalsale","totalitems","payouts");
}
 $navbtn	=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:closingtype('') title=\"Print Closing Details\"><span class=\"printrecord\">&nbsp;</span></a>&nbsp;
 
|  <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'managecollections.php','subgrid','mainpanel','cloingbills') title=\"Show's selected closing bills\"><span class=\"addbrands\"><b>Bills</b></span></a>&nbsp;
|  <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void()\"; onclick= \"printclosing();return false;\" title=\"Last 10 Bills \"><span class=\"addbrands\"><b>Last 10 Bills</b></span></a>&nbsp;";
if($employeeid!=1928)
{

$navbtn.="|  <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'collectiveclosing.php','subgrid','mainpanel') title=\"Show's selected closing bills\"><span class=\"addbrands\"><b>Closing Report</b></span></a>&nbsp;|";

$navbtn .="	<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','payoutreport.php','subgrid','mainpanel')\" title='Payout Reports'>
				<span class='addbrands'><b>Payouts Report</b></span>
			</a>&nbsp;|";

$navbtn .="	<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','collectionreport.php','subgrid','mainpanel')\" title='Collection Reports'>
				<span class='addbrands'><b>Collections Report</b></span>
			</a>&nbsp;";
}
if($employeeid==1888)
{

$navbtn .="|&nbsp;<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: invoicestatus_acc()\" title=\"Resend\"><b>Send to Accounts</b></a>&nbsp;";

}
//$navbtn="";
$_SESSION['countername']=1;
$totals		=	array('openingbalance','cashsale','creditsale','creditcardsale','chequesale','foreigncurrencysale','netcash','declaredamount','cashdiffirence','totalbills','totalsale','totalitems');//the fields in this array will be summed up at end of grid
?>
<div id="dialogbox" style="display:none;position:absolute;background-color:#FFC;border:1px solid #F96;padding:15px;margin:150px 0 0 200px;">
<input type="button" value=" A4 Print " onclick="closing(2)" />
<input type="button" value=" POS Print" onclick="closing(1)" />
<input type="button" value=" Cancel" onclick="javascript:document.getElementById('dialogbox').style.display='none';" />
</div>
<script language="javascript">
function closingtype()
{
	if(document.getElementById('dialogbox').style.display=='none')
	{
		document.getElementById('dialogbox').style.display='block';
		return;
	}
}
function printclosing()
{
	var sel	=	getselected('mainpanel');
	var sb;
	if (sel.length > 1)
	{
		for (i=1; i < sel.length; i++)
		{
			 sb	=	sel[i];
		} 
		var sb1	=	sb.split('mainpanel');
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
 	window.open('lastbills.php?id='+sb1,display); 
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
		return false;
	}
}
function closing(val)
{
	var sel	=	getselected('mainpanel');
	var sb;
	if (sel.length > 1)
	{
		for (i=1; i < sel.length; i++)
		{
			 sb	=	sel[i];
		} 
		var sb1	=	sb.split('mainpanel');
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
 	window.open('../closingprint.php?param=admin&ids='+sb1+'&ptype='+val,display); 
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
		return;
	}
}
//this function shows the multiple closings

function invoicestatus_acc()
{
	//if(selectedstring==''){
var r = confirm("Are You Sure Send This Closing!");
if (r == true){
	
	var ids	=	selectedstring;
	invoice_status = $("#invoice_status").val(); 
	$.ajax({
type: "GET",
url: 'accounts/get_accclosing.php',
success: response_acc,
data: 'cid='+ids,


});
loadsection('mainpanel','manageclosing.php');
}else{
  loadsection('mainpanel','manageclosing.php');
  }

	//}else{
	//	alert("Please Select Closing");
	//	loadsection('maindiv','manageclosing.php');
	//	}
}

function response_acc(text)

{
alert(text);
loadsection('mainpanel','manageclosing.php');
}

</script>
<div id="subgrid"></div>
<div id="mainpanel">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionarray,"  pkclosingid DESC ");
?>
</div>