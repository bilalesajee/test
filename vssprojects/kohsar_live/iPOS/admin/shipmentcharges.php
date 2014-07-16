<?php 

//echo $_SERVER['QUERY_STRING'];
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
//require_once("brandsmenu.php"); 
//*************delete************************
$id				=	$_REQUEST['id'];
$deltype="delcharges";
include_once("delete.php");
/*$oper			=	$_REQUEST['oper'];
if($id!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$id);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" pkchargesid = '$value' ";
				$AdminDAO->deleterows('charges',$delcondition);
			}
		}
}*/
/************* DUMMY SET ***************/
$labels = array("ID","Charges Name");
$fields = array("pkchargesid","chargesname");
$dest 	= 	'shipmentcharges.php';
$div	=	'charges';
$form 	= 	"frm2";	
/*
$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';
*/
define(IMGPATH,'../images/');
//***********************sql for record set**************************

    $query=	"SELECT 
				pkchargesid,
				chargesname
			FROM 
				charges
			WHERE
				chargesdeleted!=1
			";

//***********************sql for record set**************************
$navbtn = "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addcharges.php','editdiv','charges')\" title='Add Shipment Charges'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addcharges.php','editdiv','charges') title=\"Edit Shipment Charges\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Shipment Charges\"><span class=\"deleterecord\">&nbsp;</span></a>";
			
/********** END DUMMY SET ***************/
?><head>
<!--<link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/js/jquery.js"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
</head>
<div id="editdiv"></div>
<div class="breadcrumbs" id="breadcrumbs">View Charges</div>
<div id="charges">
		<?php 
			grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
		?>
</div>