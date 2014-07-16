<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$rights	 		=	$userSecurity->getRights(1);
	$actions 		=	$rights['actions'];
}//end edit
$productid		=	$_REQUEST['id'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$markitems		=	$_REQUEST['mark'];
	$status 		=	$_REQUEST['status'];
}//end edit
$strpos			=	strpos($productid,",");
if($strpos!==false)
{
	$productid	=	$_REQUEST['param'];
}
$oper				=	$_REQUEST['oper'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	if($oper=='del')
	{
		//item should be deleted after proper checking	
	}
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	if($markitems=='1')
	{
		$items	=	$_REQUEST['id'];
		$markids	=	trim($items,',');
		$sql="UPDATE barcode set barcodestatus='$status' where pkbarcodeid IN($markids)";
		//$AdminDAO->queryresult($sql);
	
			$tblj	= 	'barcode';
			$field	=	array('barcodestatus');
			$value	=	array($status);
			
			$AdminDAO->updaterow($tblj,$field,$value,"pkbarcodeid IN($markids)");	
	}
}//end edit
$barcodeid			=	$_REQUEST['barcode'];
$product_array		=	$AdminDAO->getrows("product","productname", " pkproductid='$productid'");
$attributes_array	=	$AdminDAO->getrows("attribute a, productattribute pa","a.attributename, a.pkattributeid, pa.pkproductattributeid", " a.attributedeleted != 1 AND a.pkattributeid=pa.fkattributeid AND pa.fkproductid='$productid' AND pa.attributetype<>'n' GROUP BY a.attributename");
for($i=0; $i<sizeof($attributes_array); $i++)
{
	$attributename[] 	=	$attributes_array[$i]['attributename'];
	$attributeids[]		=	$attributes_array[$i]['pkproductattributeid'];
}//for
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$deltype	=	"deleinstances";
	include_once("delete.php");
	$labels = array("ID","Code","Description");
	$fields = array("pkbarcodeid","barcode","itemdescription");
	$dest 	= 	'viewinstances.php';
	$div	=	'items';
	$form 	= 	"itemsfrm";	
	$navbtn = "<a class=\"button2\" id=\"additems\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'addinstance.php','susection','$div','$productid') title=\"Add Item\"><span class=\"addrecord\">&nbsp;</span></a>&nbsp;
	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addinstance.php','susection','$div','$id') title=\"Edit Item\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
				<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$productid') title=\"Delete Items\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
				";
	$navbtn .="	
				|&nbsp;<a href=\"javascript: changeproduct('changeproduct.php','$div','susection') \" title=\"Move item to an other Product\"><b>Change Product</b></a>";
	
	$query	=	"SELECT 
						* 
					FROM 
						barcode 
					WHERE 
						fkproductid='$productid' 
						AND barcodedeleted <> 1";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
$deltype	=	"deleteinstances";
include_once("delete.php");
$labels = array("ID","Code","Description","Short Description","Barcode Status");
$fields = array("pkbarcodeid","barcode","itemdescription","shortdescription","bstatus");
$dest 	= 	'viewinstances.php';
$div	=	'items';
$form 	= 	"itemsfrm";	
if($_GET['param']=='brand')
{
	$query	=	"SELECT 
					* 
				FROM 
					barcode,brand,barcodebrand 
				WHERE 
					fkbarcodeid=pkbarcodeid AND
					fkbrandid=pkbrandid AND
					fkbrandid='$productid' AND
					barcodedeleted <> 1";
	// fetching product id 
	$proid	=	$AdminDAO->getrows("barcode,barcodebrand","fkproductid","fkbrandid='$productid' AND fkbarcodeid=pkbarcodeid");
	$prodid	=	$proid[0]['fkproductid'];
	// setting flag to retain brands page
	$flag	=	1;
}
else
{
	$query	=	"SELECT 
					pkbarcodeid,
					barcode,
					itemdescription,
					shortdescription,
					IF(barcodestatus = 1,'Wrong','Fixed') as bstatus

				FROM 
					barcode 
				WHERE 
					fkproductid='$productid' AND
					barcodedeleted <> 1";
}
if(in_array('122',$actions))
{
$navbtn = "<a class=\"button2\" id=\"additems\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'addinstance.php','susection','$div','$productid&brand=$prodid&flag=$flag&brandid=$productid') title=\"Add Item\"><span class=\"addrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('123',$actions))
{
$navbtn .= "<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addinstance.php','susection','$div','&flag=$flag&brandid=$productid') title=\"Edit Item\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
";
}
if(in_array('124',$actions))
{
$navbtn .= "<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$productid') title=\"Delete Items\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
			";
}
if($flag!=1)
{
	if(in_array('125',$actions))
	{
	$navbtn .="	|&nbsp;<a href=\"javascript: changeproduct('changeproduct.php','$div','susection') \" title=\"Move item to an other Product\"><b>Change Product</b></a>";
	}
}
if(in_array('126',$actions))
{
	$navbtn.="|&nbsp;<a href=\"javascript: changeproduct('changeitembrand.php','$div','susection',".$_REQUEST['id'].") \" title=\"Move item to an other Brand\"><b>Change Brand</b></a>";
//}
}

}//end edit
?>
<div id="susection"></div>
<div id='items'>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray);
?>
</div>
<input type="hidden" name="productid" value="<?php echo $productid;?>" />
</form>
<script language="javascript" type="text/javascript">
	var selectedviewinstances	= '';
	loading('Loading Form...');
</script>