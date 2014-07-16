<?php 

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(6);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//*************delete************************
$deltype	=	"delbrand";
include_once("delete.php");
$dest 	= 	'managebrands.php';
$div	=	'maindiv';
$form 	= 	"frm1brands";	
define(IMGPATH,'../images/');
$param	=	$_REQUEST['param'];
$id				=	$_REQUEST['id'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
	$labels = array("ID","Brand Name","Country","Parent");
	$fields = array("pkbrandid","brandname","code3","parentbrand");
}//end edit
if($param == 'suppliers' && $id != '')
{
	$from	=	", supplier s,brandsupplier bs";
	$where	=	" AND s.pksupplierid = bs.fksupplierid AND bs.fkbrandid= b.pkbrandid AND s.pksupplierid = '$id'";
	$div	=	'subsection';
}
if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition
	 $query 	= 	"SELECT 
					pkbrandid, brandname, countryname
				FROM
					brand b LEFT JOIN countries c ON (b.fkcountryid	=	c.pkcountryid) $from
				WHERE
					b.branddeleted <> 1 
					
				$where
			GROUP BY pkbrandid
					";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/20/2012
 $query 	= 	"SELECT 
					pkbrandid, brandname ,fkparentbrandid pid,code3,
					(SELECT brandname FROM brand WHERE pkbrandid = pid) as parentbrand
					FROM
					brand b LEFT JOIN countries ON (b.fkcountryid=pkcountryid) $from
					WHERE
					b.branddeleted <> 1 
				$where
				GROUP BY pkbrandid
					";
}//end edit
$navbtn	=	"";
$sortorder	=	"brandname ASC"; // takes field name and field order e.g. brandname DESC
if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	if(in_array('16',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addbrand.php','sugrid','$div','$param','$formtype')\" title='Add Brand'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('17',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addbrand.php','sugrid','$div','$param','$formtype') title=\"Edit Brand\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('18',$actions))
	{
		$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Brands\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}
	$navbtn .="|&nbsp;
			<a href=\"javascript:showpage(1,document.$form.checks,'viewinstances.php','subsection','$div','brand') \" title=\"Manage Items\"><b>Items</b></a>";
			
	if(in_array('107',$actions))
	{
		$navbtn .="	|
				<a href=\"javascript:showpage(1,document.$form.checks,'addorderproduct.php','sugrid','$div','brand') \" title=\"Manage Orders\"><b>Add Order</b></a>
				";
	}
/*}else{
	if(in_array('16',$actions))
	{
	//	print"Hello";
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addbrand.php','sugrid','$div')\" title='Add Brand'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('17',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addbrand.php','sugrid','$div') title=\"Edit Brand\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('18',$actions))
	{
		$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Brands\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
	}*/
}//edit by Ahsan on 08/02/2012
if($param != 'suppliers')
{
	if(in_array('19',$actions))
	{
		$navbtn .="	<a href=\"javascript: 
		showpage(1,document.$form.checks,'managesuppliers.php','sugrid','$div','brand')
		\" title=\"Manage Suppliers\"><b>Suppliers</b></a>&nbsp;";
	}
}
//edit by Ahsan on 09/02/2012
/*$navbtn .="	
			|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'managestocks.php','center-column','maindiv','brand') \" title=\"Manage Shipments\"><b>Stocks</b></a>";*/
/*
if(in_array('20',$actions))

{

	$navbtn .="	

			| <a href=\"javascript: 

			showpage(1,document.$form.checks,'barcodestock.php','sugrid','$div','brand')\" title=\"Manage Stocks\"><b>View Stocks</b></a>";

}
*/
?><head>
</head>
<div id="sugrid"></div>
<div id="<?php echo $div;?>">
<div class="breadcrumbs" id="breadcrumbs">Brands</div>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?></div>