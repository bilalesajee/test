<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
$id	=	$_GET['id'];
global $AdminDAO,$Component,$userSecurity;
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	$fields	=	array("pkpackingid","packingname","reserved");
	$labels	=	array("ID","Box Name","Quantity");
	$dest 	= 	'managelistbox.php';
	$div	=	'maindiv';
	$form 	= 	"frm1";	
	define(IMGPATH,'../images/');
	$query	=	"SELECT 
					pkpackingid,packingname,reserved 
				FROM 
					shiplist,packing,packinglist pl 
				WHERE 
					fkshiplistid = '$id' AND 
					pkpackingid = pl.fkpackingid AND 
					pkshiplistid	= fkshiplistid";
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
					$delcondition =" pkpackinglistid  = '$value' ";
					$AdminDAO->deleterows('packinglist',$delcondition,1);
				}
			}
	}
	$fields	=	array("pkpackinglistid","packingname","reserved");
	$labels	=	array("ID","Box Name","Quantity");
	$dest 	= 	'managelistbox.php';
	$div	=	'subsection';
	$form 	= 	"listboxfrm";	
	define(IMGPATH,'../images/');
	$query	=	"SELECT 
					pkpackinglistid,packingname,reserved
				FROM 
					shiplist,packing,packinglist pl 
				WHERE 
					fkshiplistid 	= '$id' AND 
					pkpackingid 	= pl.fkpackingid AND 
					pkshiplistid	= fkshiplistid";
	$navbtn	=	"";
	$navbtn .=" <a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$id') title=\"Delete Wish Lists\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
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
	<div class="breadcrumbs" id="breadcrumbs">Box Details for &gt;&gt; <?php echo $itemname;?></div>
	<?php
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
	<br />
	<br />
	</div>
<?php }//end edit?>