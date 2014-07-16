<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	$id	=	$_GET['id'];
	$fields	=	array("pkshiplistdetailsid","weight","price","quantity","expiry");
	$labels	=	array("ID","Weight","Price","Quantity","Expiry");
	$dest 	= 	'managelistorder.php';
	$div	=	'maindiv';
	$form 	= 	"frm1";	
	define(IMGPATH,'../images/');
	$query 	= 	"SELECT
					pkshiplistid
					pkshiplistdetailsid,
					shiplistdetails.quantity,
					price,
					shiplistdetails.weight,
					DATE_FORMAT(expiry,'%d-%m-%y') as expiry
				FROM
					shiplist,shiplistdetails
				WHERE
					fkshiplistid 	= '$id' AND
					pkshiplistid	=	fkshiplistid
				";
	$navbtn	=	"";
	$navbtn .=" <a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Wish Lists\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	?>
	<script language="javascript" type="text/javascript">
	function getcountrylist(id)
	{
		//jQuery('#maindiv').load('managecountrylist.php?countryid='+id);
	}
	</script>
	<div id="sugrid"></div>
	<div id='maindiv'>
	<div class="breadcrumbs" id="breadcrumbs">Country List Details</div>
	<?php
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
	<br />
	<br />
	</div>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	$id	=	$_GET['param'];
	if($id=='undefined')
	{
		$id	=	$_GET['id'];
	}
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
					$delcondition =" pkshiplistdetailsid  = '$value' ";
					$AdminDAO->deleterows('shiplistdetails',$delcondition,1);
				}
			}
	}
	$fields	=	array("pkshiplistdetailsid","weight","price","newpurchaseprice","newsaleprice","quantity","expiry");
	$labels	=	array("ID","Weight","Trade Price","New Purhcase Price","New Sale Price","Quantity","Expiry");
	$dest 	= 	'managelistorder.php';
	$div	=	'subsection';
	$form 	= 	"listorderfrm";	
	define(IMGPATH,'../images/');
	$query 	= 	"SELECT
					quantity,
					purchaseprice,
					weight,
					DATE_FORMAT(expiry,'%d-%m-%y') as expiry
				FROM
					purchase
				WHERE
					fkshiplistdetailsid 	= '$id'
				";
	$navbtn	=	"";
	$navbtn .=" <a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$id') title=\"Delete Orders\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	//fetching item description
	$sitems		=	$AdminDAO->getrows("shiplist","itemdescription","pkshiplistid='$id'");
	$itemname	=	$sitems[0]['itemdescription'];
	?>
	<script language="javascript" type="text/javascript">
	function getcountrylist(id)
	{
		//jQuery('#maindiv').load('managecountrylist.php?countryid='+id);
	}
	</script>
	<div id="sugrid"></div>
	<div id='maindiv'>
	<div class="breadcrumbs" id="breadcrumbs">Purchase Details for &gt;&gt; <?php echo $itemname;?></div>
	<?php
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
	<br />
	<br />
	</div>
<?php }//end edit?>