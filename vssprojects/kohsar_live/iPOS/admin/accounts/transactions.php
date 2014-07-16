<?php session_start();
include_once("../../includes/security/adminsecurity.php");
include_once("../dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(62);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 	= 	'accounts/transactions.php';
$div	=	'maindiv';
$form 	= 	"transactionfrm";	
define(IMGPATH,'../images/');
$query	= "SELECT 
				id,
				id as transactionid,
				details,
				FROM_UNIXTIME(at,'%d %M %Y %h:%i:%s %p') as at
			FROM 
				$dbname_detail.transaction
			WHERE 1
			";
$i=0;
if(in_array('133',$actions))
{
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','accounts/transaction2.php','sugrid','$div')\" title='New Transaction'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}

/*if(in_array('15',$actions))
{
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','transaction.php','sugrid','$div')\" title='New Transaction'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
*/
/*if(in_array('142',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletemenus onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Transaction\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}*/
if(in_array('135',$actions))
{
	$navbtn .="	<a class=\"button2\" id=transactiondetails onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'accounts/reports/journal2.php','sugrid','$div','transactiondetails') title=\"Transaction Details\"><b >Transaction Details</b></a>&nbsp;";
}

if(in_array('139',$actions))
{
$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:printTransaction('$div','')\" title='Select a record and Click to Print'>
				<img src='../images/printer.png' border='0' />
			</a>&nbsp;";
}

/********** END DUMMY SET ***************/
?>
<div id="menudiv"></div>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Manage Transactions</div>
<!--<div class="breadcrumbs" id="breadcrumbs">New Arrivals</div>-->
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray,$orderby);
?>
</div>