<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$qs			=	$_SESSION['qstring'];
$rights	 	=	$userSecurity->getRights(13);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
$invoiceid	=	$_REQUEST['id'];
$param		=	$_REQUEST['param'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
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
				$delcondition =" pkinvoicepackagingid  = '$value' ";
				$AdminDAO->deleterows('invoicespackaging',$delcondition);
			}
		}
	if($param!='' && $param!='edit' && $_REQUEST['oper']=='del')
	{
		 $invoiceid =$param;	
	}

?>
	<script language="javascript">
		jQuery('#invoicegrid').load('manageorders.php?id=<?php echo $invoiceid;?>');
	</script>
<?php
	exit;
}
/************* DUMMY SET ***************/
$labels = array("ID","Barcode","Product Name","Units","Unit Price","Total Price","Expiry","Origin","Box#");
$fields = array("pkinvoicepackagingid","barcode","productname","units","unitprice","totalprice","expiry","origin","boxno");
$dest 	= 	'invoicespackaging.php';
$div	=	'invoicegrid';
$form 	= 	"inovoicefrm1";	
define(IMGPATH,'../images/');
 
 $query 	= 	"SELECT 
				pkinvoicepackagingid,
				barcode,
				origin as o,
					(
					 SELECT 
				CONCAT( productname, 
					   ' (', GROUP_CONCAT( IFNULL(attributeoptionname,'') 
												  ORDER BY attributeposition) ,')',
					   brandname
					   ) PRODUCTNAME 
			FROM 
				productattribute pa RIGHT JOIN (product p, attribute a) ON ( pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid ) , 
			attributeoption ao LEFT JOIN productinstance pi ON (pkattributeoptionid = pi.fkattributeoptionid), barcode b,brand br,barcodebrand bb 
			WHERE 
				pkproductid = pa.fkproductid 
				AND pkattributeid = pa.fkattributeid 
				AND pkproductattributeid = fkproductattributeid 
				AND pkattributeid = ao.fkattributeid 
				AND b.fkproductid = pkproductid 
				AND pi.fkbarcodeid = b.pkbarcodeid 
				AND br.pkbrandid=bb.fkbrandid
				AND bb.fkbarcodeid=b.pkbarcodeid
				AND b.pkbarcodeid = i.fkbarcodeid
					group by  PRODUCTNAME) as productname,
				status,
				units,
				unitprice,
				totalprice,
				(select DISTINCT(countryname) from countries where pkcountryid=o) as origin,
				from_unixtime(expiry,'%d-%m-%y') as expiry,
				boxno
				
			FROM
				invoicespackaging i
			WHERE 
				status='p'
			AND fkinvoiceid	=	'$invoiceid'
			AND invoicespackagingdeleted<>1
			";
$navbtn	=	"";

if(in_array('40',$actions))
{

	$navbtn .= "<a class='button2' id='addnotes' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addnote.php','subsection','maindiv')\" title='Add Note'>
				<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
}
if(in_array('41',$actions))
{
	$navbtn .="	<a class=\"button2\" id=\"editnotes\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addnote.php','sugrid','maindiv') title=\"Edit Note\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('42',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletenotes onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Notes\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('43',$actions))
{
	$navbtn .="	|
			<a href=\"javascript:showpage(1,document.$form.checks,'itemnotes.php','sugrid','maindiv') \" title=\"Manage Notes\"><b>Manage Notes</b></a>";
}
$navbtn .="	<a class=\"button2\" id=\"editnotes\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'manageorders.php','orderdiv','invoicegrid','edit') title=\"Edit Selected record\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;| ";
$navbtn .="	&nbsp;<a class=\"button2\" id=deletenotes onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('invoicespackaging.php','$div','$_SESSION[qs]','$invoiceid') title=\"Delete invoice records\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;|";
$navbtn .="&nbsp;<a href=\"javascript: openpopup(920,'auto','printinvoice.php?invoiceid=$invoiceid&type=print') \" title=\"Print Invoice & packaging Details\"><b>Print invoice</b></a>&nbsp;|";
$navbtn .="&nbsp;<a href=\"printinvoice.php?invoiceid=$invoiceid&type=excel\" title=\"Get Excel Sheet of Invoice & packaging Details\"><b>Get Excel Sheet</b></a>";	
$navbtn .="|&nbsp;<a class=\"button2\"  onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,'','printinvoice.php','emailinvoice','invoicegrid','$invoiceid') title=\"Email Invoice & Packaging Details\"><span ><b>Email This</b></span></a>&nbsp;";
// if
/********** END DUMMY SET ***************/
?>
<div id="attdiv"></div>
<div id="sugrid"></div>
<div id="itemgrid"></div>
<div id='invoicesdiv'>
<div class="breadcrumbs" id="breadcrumbs">Invoices & Packaging</div>
<?php 
//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />

</div>
