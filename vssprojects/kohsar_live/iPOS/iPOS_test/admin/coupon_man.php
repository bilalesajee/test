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
				$delcondition =" pkcouponid  = '$value' and status!='2'";
				$AdminDAO->deleterows("$dbname_detail.coupon_management",$delcondition,1);
			}
		
			
		}
}
//************ DUMMY SET ***************
$labels = array("ID", "Coupon ID","Amount ","Payment Method","Reason","Status");
$fields = array("id","coupon_id","amount","paymentmethod0","reason","status0");

$dest 	= 	'coupon_man.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
/*/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
define(IMGPATH,'../images/');



 $query 	= 	"SELECT
		c.pkcouponid id,c.pkcouponid  as coupon_id,c.amount,case c.paymentmethod when 'c' then 'Cash' when 'cc' then 'Credit Card' when 'fc' then 'Foreign Currency' else 'Cheque' end paymentmethod0,c.reason,
		case c.status when '1' then 'Active' when '2' then 'Used' else 'In Active' end status0
      FROM
	$dbname_detail.coupon_management c
	
	where 1=1 
	 ";
//$AdminDAO->getrows('brand',$delcondition);
//$navbtn = array("Add","Edit","Delete","custom");
$sortorder="pkcouponid desc";
$navbtn	=	"";



		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addcoupon_man.php','subsection','maindiv','','$formtype')\" title='Add Reason'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";

		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addcoupon_man.php','subsection','maindiv','','$formtype') title=\"Edit Reason\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";

	$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete \"><span class=\"deleterecord\">&nbsp;</span></a>";


?><head>
</head>

<div id='maindiv'>

	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
</div>
<br />
<br />
<div id="sugrid"></div>