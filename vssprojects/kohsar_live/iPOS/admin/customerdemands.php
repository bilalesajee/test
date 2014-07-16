<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
$page	=	$_GET['page'];
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
$param	=	$_REQUEST['param'];
$id		=	$_GET['id'];	
if($param=='fullfill')
{	
	$fields	=	array("demandstatus");
	$values	=	array('f');
	$table	=	"customerdemands";
	$AdminDAO->updaterow($table,$fields,$values," pkcustomerdemandsid='$id'");
	
	$del->jsmsg("Demand has been fullfilled successfully.",0);
}
$deltype	=	"delcsdemand";
include_once("delete.php");

/************* DUMMY SET ***************/
$labels = array("ID","Product Name","Advance Deposit","Date","Dead Line","Customer Info","Description","Location");
$fields = array("pkcustomerdemandsid","productname","advance","demanddate","deadline","customerinfo","description","storename");
$dest 	= 	'customerdemands.php';
$div	=	'sugrid';
$form 	= 	"csdemandsfrm1";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
					*,s.storename
			FROM
				customerdemands LEFT JOIN store s ON (pkstoreid = fkstoreid) 
			WHERE
				customerdemandsdeleted<>1 AND
				 demandstatus='p'
				 
			";

$navbtn = "<a class='button2' id='addproducts' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addcustomerdemand.php','subcsdemands','$div')\" title='Add Customer Demands'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editproducts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addcustomerdemand.php','subcsdemands','$div') title=\"Edit Customer Demands\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Customer Demands\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
			";
$navbtn .="	|
			<a href=\"javascript:showpage(1,document.$form.checks,'customerdemands.php','sugrid','$div','fullfill') \" title=\"Full fill\"><b>Fullfill</b></a>
			";

			
/********** END DUMMY SET ***************/
?><head>


</head>
<div id="subcsdemands">
</div>
<div id='<?php echo $div;?>'>

<div class="breadcrumbs" id="breadcrumbs">Products</div>
<?php 
//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />
