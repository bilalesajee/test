<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(28);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$page		=	$_GET['page'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
//*************delete************************
$shipmentid		=	$_REQUEST['id'];
$delid			=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];


if($delid!='' && $oper=='del')
{
	$shipmentid			=	$_REQUEST['shipmentid'];
	$query				=	"SELECT * FROM purchase where fkshiplistdetailsid='$delid' and fkshipmentid=$shipmentid";
	$reportresult		=	$AdminDAO->queryresult($query);
	
	if(count($reportresult)>0){
		echo "<script>adminnotice('Selected record can not be, it already exist in purchase.',0,5000);</script>";
		exit;	
	}else{
		$delcondition =" pkshiplistdetailsid  = '$delid' ";	
		$AdminDAO->deleterows('shiplistdetails',$delcondition,'1');
		echo "<script>jQuery(\"#subsection\").load('manageshipmentdetails.php?id=$shipmentid');		
				adminnotice('Selected record deleted.',0,5000)	</script>";
		exit;
	}
}

/************* DUMMY SET ***************/
//$labels = array("ID","Picture","Product Name","Description");
//$fields = array("pkproductid","defaultimage","productname","description");
$dest 	= 	'manageshimentdetails.php';
$div	=	'subsection';
$form 	= 	"frm1packing";	
define(IMGPATH,'../images/');
	$query		=	"SELECT
		pkshiplistdetailsid,
		barcode,
		itemdescription,
		sd.quantity,
		sl.lastpurchaseprice,
		weight,
		GROUP_CONCAT(companyname) companyname
	FROM 
		shiplistdetails sd,shiplist sl LEFT JOIN shiplistsupplier LEFT JOIN supplier ON (fksupplierid=pksupplierid) ON (fkshiplistid=pkshiplistid)
	WHERE
		pkshiplistid	=	sd.fkshiplistid	AND
	sd.fkshipmentid		=	'$shipmentid'
	GROUP BY pkshiplistdetailsid
	";
$navbtn	=	"";
/*if(in_array('60',$actions))
{
	$navbtn .= "<a class='button2' id='addpacking' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addpacking.php','sugrid','subsection','$shipmentid')\" title='Add Package'>
				<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
}
if(in_array('61',$actions))
{
	$navbtn .="	<a class=\"button2\" id=\"editpacking\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addpacking.php','sugrid','subsection','$shipmentid') title=\"Edit Package\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('62',$actions))
{
	$navbtn .="	<a class=\"button2\" id=deletepackings onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$shipmentid') title=\"Delete Packages\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('67',$actions))
{
	$navbtn .="<a href=\"javascript:showpage(1,document.$form.checks,'managebox.php','attdiv','subsection','$shipmentid') \" title=\"Boxes\"><b> Boxes</b></a>";
}
*/
//$navbtn .="	<a class=\"button2\" id=\"editpacking\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addpacking.php','sugrid','subsection','$shipmentid') title=\"Edit Package\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
//$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('updatepurchase.php','subsection','$_SESSION[qs]') title=\"Delete purchase\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";

//$navbtn	.="<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addpacking.php','eidtpurchase','subsection','$param') \" title=\"Edit Packing\"><span class=\"editrecord\">&nbsp;</span></a>";
$navbtn .="	<a class=\"button2\" id=deletepackings onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void(0);\" onclick=\"javascript: return delitem('$shipmentid');\" title=\"Delete Item\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";

// if
/********** END DUMMY SET ***************/
$labels	=	array('ID','Barcode','Product','Quantity','Price','Weight');
$fields	=	array('pkshiplistdetailsid','barcode','itemdescription','quantity','lastpurchaseprice','weight');

?>
<div id="sugrid"></div>
<div id="attdiv"></div>
<div id="itemgrid"></div>
<div id="eidtpurchase"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Shipment Details</div>
<input type="hidden" value="<?php echo $shipmentid;?>" name="shipmentid"  id="shipmentid"/>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />

<script>function delitem(shipmentid)
{	
	var ids	=	selectedstring.split(',');
	var	id	=	(ids[1]);	
	param="shipmentid="+shipmentid+"&oper="+id;	
	if(id==undefined || (ids.length>2)){
		alert('Please select one record to delete.');
		return false;		
	}else if(confirm("Are you sure you want to delete this record")){
		jQuery('#eidtpurchase').load('manageshipmentdetails.php?shipmentid='+shipmentid+'&oper=del&id='+id);
	}
	return false;
}
</script>

</div>