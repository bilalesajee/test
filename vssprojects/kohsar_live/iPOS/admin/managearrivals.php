<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
//*************delete************************
$delid			=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
/************* DUMMY SET ***************/
$labels = array("ID","Code","Item Description","Short Description");
$fields = array("pkbarcodeid","barcode","itemdescription","shortdescription");
$dest 	= 	'managearrivals.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');
/*
$query	=	"SELECT 
					s.pkproductinstanceid,
					s.fkbarcodeid,
					b.productdes as productattributeoptionname,
					b.barcode,
					br.brandname
				FROM 
					productinstance s,
					barcode b, 
					product p, 
					brand br,
					barcodebrand bb
				WHERE 
					s.fkbarcodeid 	= b.pkbarcodeid AND
					b.fkproductid 	= p.pkproductid AND 
					bb.fkbrandid	= br.pkbrandid AND
					bb.fkbarcodeid	= b.pkbarcodeid
					
					 $where GROUP BY fkbarcodeid";

$query	=	"SELECT 
					b.itemdescription as productattributeoptionname,
					b.barcode,
					br.brandname
				FROM 
					barcode b, 
					brand br,
					barcodebrand bb
				WHERE 
					bb.fkbrandid	= br.pkbrandid AND
					bb.fkbarcodeid	= b.pkbarcodeid
					
					 GROUP BY fkbarcodeid ";*/
					 
				$query	=	"SELECT 
								pkbarcodeid,
								itemdescription,
								shortdescription,
								barcode
							FROM 
								barcode
						";

					 $limit='-1';//limit -1 hides the paging navigation
/*$res	=	$AdminDAO->queryresult($query);
$i=0;*/
$navbtn = "";
/********** END DUMMY SET ***************/
?><head>
</head>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">New Arrivals</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray);
?>
</div>
<br />
<br />
<div id="sugrid"></div>