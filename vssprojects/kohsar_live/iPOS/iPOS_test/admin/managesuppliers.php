<?php
session_start();
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(7);
$labels	 	=	$rights['labels'];
$labels[] = "Code";
$fields		=	$rights['fields'];
$fields[] = "pksupplierid";
$actions 	=	$rights['actions'];
//print_r($labels);
//*************delete************************
$deltype	=	"delsupplier";
include_once("delete.php");
$param		=	$_GET['param'];

if($param=='supplier' )
{
	if($_REQUEST['brandid']!='' )
	{
		$brandid	=	$_REQUEST['brandid'];
	}
	else
	{
		$brandid	=	$_REQUEST['id'];
	}

}
elseif($param=='brand')
{
	$div	=	'sugrid';
	$brandid	=	$_REQUEST['id'];
}
else
{
	$div	=	'maindiv';	
}
if($brandid !='')
{
	$from	.=	" , brandsupplier ";
	$where	.=	"AND fkbrandid	=	'$brandid'
					AND	fksupplierid	=	pksupplierid
				";
}

  $query		=	"SELECT 
						pksupplierid,
						email,
						CONCAT(phone ,', ', mobile) as phoneno,
						CONCAT(contactperson1 ,', ', contactperson2) as contactpersons,
						CONCAT(address1 ,' ', address2,' ',IFNULL(cityname,''),' ',IFNULL(statename,''),' ',zip,' ',IFNULL(countryname,'')) as address, companyname,suppliercode
						
				FROM
						supplier $from,addressbook 
						LEFT JOIN countries ON (fkcountryid = pkcountryid)
						LEFT JOIN city  ON (fkcityid = pkcityid)
						LEFT JOIN state ON (fkstateid = pkstateid)
				WHERE 	fkaddressbookid = pkaddressbookid AND
						supplierdeleted <>1
						 $where
					group by pksupplierid
						";

$dest 	= 	"managesuppliers.php";

$form 	= 	"frm1";	
define(IMGPATH,'../images/');

$navbtn	=	"";
if($_SESSION['siteconfig'] != 3){//edit by ahsan on 08/03/2012
	if(in_array('21',$actions))
	{
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addsupplier.php','subsection','$div','','$formtype')\" title='Add Supplier'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('22',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addsupplier.php','subsection','$div','','$formtype') title=\"Edit Supplier\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('23',$actions))
	{
		$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$param&brandid=$brandid') title=\"Delete Suppliers\"><span class=\"deleterecord\">&nbsp;</span></a>";
	}
	
	/*if(in_array('56',$actions) && $param != "brand")
	{
		$navbtn .="<a class=\"button2\" id=viewbrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'managebrands.php','subsection','$div','suppliers') title=\"View Brands\">
		<b>View Brands</b>
		</a>";
		
	}*/
	if(in_array('108',$actions))
	{
		$navbtn .="	|
				<a href=\"javascript:showpage(1,document.$form.checks,'addorderproduct.php','subsection','$div','supplier') \" title=\"Manage Orders\"><b>Add Order</b></a>
				";
	}
}//Edit by Ahsan on 08/03/2012

?><head>
</head>
<!--<div id="sugrid"></div>-->
<div id='<?php echo $div;?>'>
	<div class="breadcrumbs" id="breadcrumbs">Suppliers</div>
	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<br />
<br />