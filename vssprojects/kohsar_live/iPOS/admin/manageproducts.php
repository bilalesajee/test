<?php ob_start();
session_start();
//echo $_SESSION['siteconfig'];
include("../includes/security/adminsecurity.php");

include_once("dbgrid.php");

global $AdminDAO,$Component,$userSecurity;

$rights	 	=	$userSecurity->getRights(1);

$labels	 	=	$rights['labels'];

$fields		=	$rights['fields'];

$param		=	$_GET['param'];

$barcode	=	$_GET['barcode'];

$barcodeid	=	$_GET['id'];

$div	=	 'maindiv';

if($param=="category" )

{

	$categoryid =	$_REQUEST['id'];

	$div		= 'main_cat';

}

$actions 	=	$rights['actions'];

$page		=	$_GET['page'];

if($page)

{

	$_SESSION['qstring']='';

	$qstring	= $_SERVER['QUERY_STRING'];

	$_SESSION['qstring']=$qstring;

}

//$page	=	$_GET['page'];

//$page	=	$_GET['page'];

//*************delete************************

$deltype	=	"delproduct";

include_once("delete.php");



/************* DUMMY SET ***************/

//$labels = array("ID","Picture","Product Name","Description");

//$fields = array("pkproductid","defaultimage","productname","description");

$dest 	= 	'manageproducts.php';

$form 	= 	"productsfrm";	

define(IMGPATH,'../images/');

if($barcode!='')

{

	$barcodeqry="barcode,";

	$barcodefrom=" ,barcode b ";

	$barcodewhere=" b.fkproductid=pkproductid AND b.barcode='$barcode' AND";

}

else

{

	$groupby=" group by pkproductid ";

}

$query 	= 	"SELECT 

				pkproductid,

			

				defaultimage,

				productname,

				productdescription as description,

				productprice,

				productstatus as pstatus,

				isdownloadable

			";

/*********************************COMING FROM CATEGORIES PAGE*******************/

if($categoryid > 0)			

{

		$query	.= " FROM

						product LEFT JOIN productcategory pc ON (pkproductid = fkproductid) 

					WHERE

						productdeleted <> 1 AND

						pc.fkcategoryid = '$categoryid'

					";

}

/****************************************************/

else

{

	$query	.= " FROM

				product

				$barcodefrom

			WHERE

				$barcodewhere

				productdeleted <> 1 $groupby";

}

//echo $query;

$navbtn	=	"";



$sortorder	=	"productname ASC"; // takes field name and field order e.g. brandname DESC



if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012

	if(in_array('1',$actions))

	{

	//	print"Hello";

		$navbtn .= "<a class='button2' id='addproducts' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addproduct.php','subsection','$div','','$formtype')\" title='Add Product'>

					<span class='addrecord'>&nbsp;</span>

					</a>&nbsp;";

	}

	if(in_array('2',$actions))

	{

		$navbtn .="	<a class=\"button2\" id=\"editproducts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addproduct.php','subsection','$div','','$formtype') title=\"Edit Product\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";

	}

	if(in_array('3',$actions))

	{

		$navbtn .="	<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Products\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";

	}

	if(in_array('4',$actions))

	{

		$navbtn .="

				<a href=\"javascript:showpage(1,document.$form.checks,'viewinstances.php','subsection','$div') \" title=\"Manage Items\"><b>Items</b></a>";

	}

	if(in_array('5',$actions))

	{

		$navbtn .="	|

				<a href=\"javascript:showpage(2,document.$form.checks,'additem.php','subsection','$div') \" title=\"Add Item\"><b>Add Item</b></a>";

	}

	if(in_array('6',$actions))

	{

		$navbtn .="	|

				<a href=\"javascript:showpage(2,document.$form.checks,'manageattributes.php','sugrid','$div') \" title=\"Manage Attributes\"><b>Attributes</b></a>

				";

				

	}//if

	if(in_array('106',$actions))

	{

		$navbtn .="	|

				<a href=\"javascript:showpage(1,document.$form.checks,'addorderproduct.php','sugrid','$div','product') \" title=\"Manage Attributes\"><b>Add Order</b></a>

				";

				

	}

/*}else{

	if(in_array('1',$actions))

	{

	//	print"Hello";

		$navbtn .= "<a class='button2' id='addproducts' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addproduct.php','subsection','$div')\" title='Add Product'>

					<span class='addrecord'>&nbsp;</span>

					</a>&nbsp;";

	}

	if(in_array('2',$actions))

	{

		$navbtn .="	<a class=\"button2\" id=\"editproducts\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addproduct.php','subsection','$div') title=\"Edit Product\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";

	}

	if(in_array('3',$actions))

	{

		$navbtn .="	<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Products\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";

	}

	if(in_array('4',$actions))

	{

		$navbtn .="

				<a href=\"javascript:showpage(1,document.$form.checks,'viewinstances.php','subsection','$div') \" title=\"Manage Items\"><b>Items</b></a>";

	}

	if(in_array('5',$actions))

	{

		$navbtn .="	|

				<a href=\"javascript:showpage(2,document.$form.checks,'additem.php','subsection','$div') \" title=\"Add Item\"><b>Add Item</b></a>";

	}

	if(in_array('6',$actions))

	{

		$navbtn .="	|

				<a href=\"javascript:showpage(2,document.$form.checks,'manageattributes.php','sugrid','$div') \" title=\"Manage Attributes\"><b>Attributes</b></a>

				";

				

	}//if*/

}//edit by ahsan on 08/02/2012

/********** END DUMMY SET ***************/

?>



</head>

<div id="sugrid"></div>

<div id="attdiv"></div>

<div id="itemgrid"></div>

<div id=<?php print"$div";?>>

<div class="breadcrumbs" id="breadcrumbs">Products</div>

<?php 

//dump($_REQUEST);

//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");

grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);

?>

<br />

<br />



</div>

