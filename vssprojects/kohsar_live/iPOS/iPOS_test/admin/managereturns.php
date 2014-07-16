<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
$invid	=	$_REQUEST['id'];
/****************** the start ********************/
$labels = array("ID","Barcode","Item Description","Total Units","Returned","Remaining","Reason","Status","Acc Status");
$fields = array("pkreturnid","barcode","itemdescription","quantity","qty","unitsremaining","returntype","status","accdatasent");
$dest 	= 	'managereturns.php';
$div	=	"sugrid";
$form 	= 	"returns";
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkreturnid,
				barcode,
				itemdescription,
				r.quantity qty,
				s.quantity quantity,
				s.unitsremaining,
				returntype,
				IF(returnstatus='p','Pending','Confirmed') status,
				IF(accdatasent=0,'Pending','Confirmed') accdatasent
			FROM 
				$dbname_detail.returns r,$dbname_detail.stock s,returntype,barcode
			WHERE
				r.fkstockid				=	pkstockid AND
				r.fkreturntypeid		=	pkreturntypeid AND
				fkbarcodeid				=	pkbarcodeid AND
				s.fksupplierinvoiceid	=	'$invid' and r.issclose=1
			";

$navbtn="";					
//$navbtn="<a class=\"button2\" id=\"editstock\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'editreturns.php','editreturns','returnsdiv') title=\"Edit Returns\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
$navbtn .="<a href=\"javascript:void(0)\" onClick=\"printreturns(".$invid.")\" title='Print Consignment'><span class=\"printrecord\">&nbsp;</span></a>&nbsp;";
$navbtn .="|&nbsp;<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: invoicestatus_acc()\" title=\"Resend\"><b>Resend To Accounts</b></a>&nbsp;";


/********** END DUMMY SET ***************/
?>
<script language="javascript">
function printreturns(id)
{
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=500,height=600,left=100,top=25';
 	window.open('printreturns.php?id='+id,display); 
}
function invoicestatus_acc()
{
	
	var ids	=	selectedstring;
	invoice_status = $("#invoice_status").val(); 
	$.ajax({
type: "GET",
url: 'resend2acc_pr.php',
success: response_acc,
data: 'ids='+ids,


});
loadsection('maindiv','managesupplierinvoices.php');
}

</script>
<div id="editreturns"></div>
<div id="<?php echo $div;?>">
<div class="breadcrumbs" id="breadcrumbs">Stock Details for <div id="desc"><?php echo $barcode;?> <?php echo $itemdescription;?></div> </div>
<?php
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>