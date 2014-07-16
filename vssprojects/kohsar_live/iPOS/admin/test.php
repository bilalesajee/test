<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
/**************RIGHTS***************************/
$rights	 	=	$userSecurity->getRights();
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//*************delete************************
$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" fkbarcodeid  = '$value' ";
				$AdminDAO->deleterows("$dbname_detail.re_order_level",$delcondition,1);
			}
		
			
		}
}
//************ DUMMY SET ***************
$labels = array("ID","Barcodeid", "Barcode","Item Name","Re Order Level ");
$fields = array("id","barcode_id","barcode","itemdescription","reorderlevel");

$dest 	= 	'manage_reorder_level.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');



 $query 	= 	"SELECT
		pkbarcodeid id,r.fkbarcodeid as barcode_id,r.reorderlevel,r.barcode,b.itemdescription
		
      FROM
	$dbname_detail.re_order_level r
	left join barcode b on b.pkbarcodeid = r.fkbarcodeid
	
	where 1=1 
	 ";
//$AdminDAO->getrows('brand',$delcondition);
//$navbtn = array("Add","Edit","Delete","custom");
//$sortorder="pkbarcodeid desc";
$navbtn	=	"";



		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','add_reorder_level.php','subsection','maindiv','','$formtype')\" title='Add Reorder'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";

		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'add_reorder_level.php','subsection','maindiv','','$formtype') title=\"Edit Re order\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";

	$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete \"><span class=\"deleterecord\">&nbsp;</span></a>";


?><head>
</head>

<div id='maindiv'>

	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<br />
<br />
<div id="sugrid"></div>