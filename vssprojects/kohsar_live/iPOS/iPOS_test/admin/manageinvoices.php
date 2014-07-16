<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$del;
$rights	 	=	$userSecurity->getRights(9);
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
/*				$delcondition =" pkinvoiceid = '$value' ";
				$AdminDAO->deleterows('invoice',$delcondition);*/
				$del->delinvoices($value);
			}
		}
}
/************* DUMMY SET ***************/
$labels = array("ID","Invoice Name","Creation Date","Country","Employee");
$fields = array("pkinvoiceid","invoicename","datetime","countryname","empname");

$dest 	= 	'manageinvoices.php';
$div	=	'sugrid';
$form 	= 	"invoicefrm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');
//$AdminDAO->queryresult("CALL product( 1, @productname )");
   $query 	= 	"SELECT 
					pkinvoiceid,
					invoicename,
					(SELECT countryname from countries where pkcountryid=fkcountryid) as countryname
					,from_unixtime(datetime,'%d-%m-%y') as datetime,
					(SELECT concat(firstname,' ',lastname)  from addressbook,employee where pkaddressbookid=fkaddressbookid and pkemployeeid=fkemployeeid) as empname 
					
				FROM 
					invoice
				WHERE 
					invoicedeleted<>1
					
			";

if(in_array('30',$actions))
{
//	print"Hello";
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addinvoice.php','invoicegrid','sugrid')\" title='Add New Invoice'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(in_array('31',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Demands\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
/*if(in_array('32',$actions))
{
	$navbtn .="<a class=\"button2\" id=\"editdemands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','sugrid','maindiv') title=\"Add Items\"><b>Add Items</b></a>&nbsp;| ";
}
*/
/*if(in_array('33',$actions))
{
	$navbtn .="<a class=\"button2\" id=\"editdemands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','subsection','maindiv') title=\"Add Items\"><b>Add Items</b></a>&nbsp;|";
}*/
if(in_array('34',$actions))
{
$navbtn .="<a href=\"javascript: showpage(1,document.$form.checks,'manageorders.php','invoicegrid','sugrid') \" title=\"View Invoice and packaging Details\"><b>Invoice Details</b></a>";	
}
/*
$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','adddemand.php','subsection','maindiv')\" title='Add Demand'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Demands\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=\"editdemands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','subsection','maindiv') title=\"Add Items\"><b>Add Items</b></a>&nbsp;|
			<a href=\"javascript: loadsubgrid('sugrid',document.$form.checks,'demanddetails.php','maindiv') \" title=\"View Demand Details\"><b>Demand Details</b></a>";
			
/********** END DUMMY SET ***************/

?>
<div id="invoicegrid"></div>
<div class="breadcrumbs" id="breadcrumbs">Invoices</div>
	<?php 
        //$button->makebutton("Customer Demands","javascript: showpage(0,'','customerdemands.php','sugrid','maindiv')");
        grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
    ?>
</div>