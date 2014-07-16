<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$lid	    =	$_REQUEST['loc'];
$ltyp	    =	$_REQUEST['ctap'];

if($lid!='')
{
if($ltyp!=''){
$loc_and	=	" AND location='$lid' and ctype='$ltyp'";	
	}else{
 $loc_and	=	" AND location='$lid' ";	
	}
}
/************* DUMMY SET ***************/
$labels = array("ID","Location","Type","Title","Email","Address","Phone","NIC","Status");
$fields = array("id","location","ctype","companyname","email","address","phone","nic","customer_status");

$dest 	= 	'managecustomers.php';
$div	=	'maindiv';
$form 	= 	"frm1cutomers";	
$css 	= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 	= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************
 $query 	= 	"SELECT 
					pkcustomerid id,
					IF(location=1,'DHA',IF(location=2,'Gulberg',IF(location=3,'Kohsar','Pharma')))    location,
					IF(ctype=1,'Hotel','Creditor') ctype,
					companyname,
					CONCAT(address1,' ',address2) as address,
					email,
					
					taxnumber,
					ntn,
					CONCAT(phone ,' ',mobile) as phone,
					nic,IF(isdeleted=1,'Deactive','Active') customer_status
        		FROM
					customer 
				WHERE
					1=1 $loc_and ";
					
$locations			=	"<select name=\"loc\" id=\"loc\" style=\"width:100px;\" onchange=\"listusers(this.value,1)\"><option value=\"\">All</option>";
$listlocs	=	$AdminDAO->getrows("store","*");
// pkgroupid 	groupname
for($i=0;$i<sizeof($listlocs);$i++)
{
	$locid	=	$listlocs[$i]['pkstoreid'];
	$groupname		=	$listlocs[$i]['storename'];
	$select		=	"";
	if($locid == $lid)
	{
		$select = "selected=\"selected\"";
	}
	$locations	.=	"<option value=\"$locid\" $select>$groupname</option>";
}
$locations			.=	"</select>";					
					
//*******************************************************************
$navbtn	=	"<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addnewcustomer.php','subsection','maindiv')\" title='Add New Customer'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addnewcustomer.php','subsection','maindiv','') title=\"Edit Customer\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			";
				$navbtn .="&nbsp;|	<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: custstatus('close')\" title=\"Change Status\"><b>Change Status</b></a>&nbsp;";
			
?>
<div id="notice"></div>
<div id="maindiv">
<div id="sloc"><b> Select Location </b> <?php echo $locations;?> &nbsp;&nbsp;&nbsp; 	
        	<select name="ctype"  id="ctype"><option value="">Select Type</option> <option <?php if($ltyp==2){ ?> selected="selected"<?php }?>value="2">Creditor</option><option value="1" <?php if($ltyp==1){ ?> selected="selected"<?php }?>>Hotel</option></select></div>
<div id="subsection"></div>
<?php		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);	?>
</div>
<script language="javascript">
	function listusers(groupid,chkf)
	{
		var chkf=document.getElementById('ctype').value;
		if(chkf!=''){
		jQuery('#maindiv').load('managecustomers.php?loc='+groupid+'&ctap='+chkf);	
		}else {
		jQuery('#maindiv').load('managecustomers.php?loc='+groupid);	
		} 
	}
	
	function custstatus(text)
{
	
	var ids	=	selectedstring;
	customer_status = $("#customer_status").val(); 
	$.ajax({
type: "GET",
url: 'change_customer_status.php',
success: response,
data: 'ids='+ids+'&customer_status='+text,


});
loadsection('maindiv','managecustomers.php');
}

function response(text)

{
alert(text);
loadsection('maindiv','managecustomers.php');
}

</script>