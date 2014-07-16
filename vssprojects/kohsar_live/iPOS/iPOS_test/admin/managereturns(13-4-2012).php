<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
$invid	=	$_REQUEST['id'];
/****************** the start ********************/
$labels = array("ID","Barcode","Item Description","Total Units","Returned","Remaining","Reason","Status");
$fields = array("pkreturnid","barcode","itemdescription","quantity","qty","unitsremaining","returntype","status");
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
				IF(returnstatus='p','Pending','Confirmed') status
			FROM 
				$dbname_detail.returns r,$dbname_detail.stock s,returntype,barcode
			WHERE
				r.fkstockid				=	pkstockid AND
				r.fkreturntypeid		=	pkreturntypeid AND
				fkbarcodeid				=	pkbarcodeid AND
				s.fksupplierinvoiceid	=	'$invid'
			";

					
$navbtn="<a class=\"button2\" id=\"editstock\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'editreturns.php','editreturns','returnsdiv') title=\"Edit Returns\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
/********** END DUMMY SET ***************/
?>
<div id="editreturns"></div>
<div id="<?php echo $div;?>">
<div class="breadcrumbs" id="breadcrumbs">Stock Details for <div id="desc"><?php echo $barcode;?> <?php echo $itemdescription;?></div> </div>
<?php
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>