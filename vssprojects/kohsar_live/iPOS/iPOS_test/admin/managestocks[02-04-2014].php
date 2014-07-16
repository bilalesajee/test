<?php
include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(5);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$totals		=	array('quantity','unitsremaining');//the fields in this array will be summed up at end of grid
//echo "$groupid!=$ownergroup";
if($groupid!=$ownergroup)
{
	//$where=" AND fkstoreid='$storeid' ";
}
if($barcode!='')
{
	$where.=" AND b.barcode='$barcode'";
}
//echo $_GET['param'];
if($_GET['param']=='brand')
{
	$brnadid	=	$_GET['id'];
	$where=" AND
		br.pkbrandid='$brnadid'   AND
		bb.fkbarcodeid=b.pkbarcodeid AND
		bb.fkbrandid=br.pkbrandid ";
	$from= " ,
		barcodebrand bb,
		brand br ";
}
/************* DUMMY SET ***************/
$dest 	= 	'managestocks.php';
$div	=	'maindiv';
$form 	= 	"frmstock";	
$tablename='stock';
define(IMGPATH,'../images/');
/*$query=" SELECT  DISTINCT (
					fkbarcodeid
					) AS barcodeid, (
					
					SELECT SUM( quantity )
					FROM stock
					WHERE fkbarcodeid = barcodeid $where
					) AS quantity, (
					
					SELECT SUM( unitsremaining )
					FROM stock
					WHERE fkbarcodeid = barcodeid  $where
					) AS unitsremaining,
					(
						SELECT  IF (min(expiry) = '0', '--------',min(FROM_UNIXTIME(expiry,'%d-%m-%y')))
						FROM stock
						WHERE fkbarcodeid = barcodeid $where
					) AS minexpiry
					,
					
					(
					
					SELECT round(MAX(retailprice),2)
					FROM stock
					WHERE fkbarcodeid = barcodeid $where
					ORDER BY pkstockid DESC
					LIMIT 0 , 1
					) AS currprice, 
		 			(select round(MAX(priceinrs),2)  from stock where fkbarcodeid=barcodeid  $where order by pkstockid DESC	LIMIT 0,1) as tradeprice,
					barcode.barcode
					
					FROM stock, barcode, product
					WHERE fkbarcodeid = barcode.pkbarcodeid
					AND barcode.fkproductid = product.pkproductid 
					$where
		";
*/
/*
,
		(SELECT 
CONCAT( productname, ' (', 

(SELECT GROUP_CONCAT(attributeoptionname) FROM 	
	attribute a,
	attributeoption ao,
	productinstance pi,
	barcode bc
WHERE 
 a.pkattributeid 		=	ao.fkattributeid AND
 pkattributeoptionid 	=	pi.fkattributeoptionid AND 
pi.fkbarcodeid			=	bc.pkbarcodeid AND
barc.pkbarcodeid 		=	bc.pkbarcodeid 
 ORDER BY attributeposition) ,') ',brn.brandname) PRODUCTNAME

*/
/*
$query	= "SELECT
		DISTINCT (fkbarcodeid) AS barcodeid,
		SUM( quantity )	AS quantity,
		SUM( unitsremaining ) AS unitsremaining,
		(IF (min(expiry) = '0', '--------',min(FROM_UNIXTIME(expiry,'%d-%m-%y')))) AS minexpiry	,
		round(MAX(retailprice),2) as currprice, 
		round(MAX(priceinrs),2)  as tradeprice,
		b.barcode,
		productname as productattributeoptionname
	FROM 
		stock ,
		barcode b,
		product
		
	WHERE
		fkbarcodeid = pkbarcodeid AND
		pkproductid = fkproductid 
		
		$where
	GROUP By
		barcodeid
		";
*/
/*
,
		SUM( quantity )	AS quantity,
		SUM( unitsremaining ) AS unitsremaining,
		(IF (min(expiry) = '0', '--------',min(FROM_UNIXTIME(expiry,'%d-%m-%y')))) AS minexpiry	,
		round(MAX(retailprice),2) as currprice, 
		round(MAX(priceinrs),2)  as tradeprice,
*/

 $query	= "SELECT
		DISTINCT (s.fkbarcodeid) AS barcodeid,
		 SUM( quantity )	AS quantity,
		SUM( unitsremaining ) AS unitsremaining,
		(IF (min(expiry) = '0', '--------',min(FROM_UNIXTIME(expiry,'%d-%m-%y')))) AS minexpiry	,
		round(MAX(retailprice),2) as currprice, 
		round(MAX(priceinrs),2)  as tradeprice,
		b.barcode,
		b.itemdescription as productattributeoptionname
	FROM 
		$dbname_detail.stock s,
		barcode b
		
		$from
		
	WHERE
		s.fkbarcodeid = b.pkbarcodeid 
		
		
		$where
	GROUP By
		barcodeid
		";
$navbtn	=	"";


//$sortorder		=	"productattributeoptionname ASC"; // takes field name and field order e.g. brandname DESC
if(in_array('10',$actions))
{
	/*$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(2,document.$form.checks,'addstock.php','subsection','maindiv')\" title='Add Stock'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";*/
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(2,document.$form.checks,'newstock.php','subsection','maindiv')\" title='Add Stock'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";		
}
if(in_array('14',$actions))
{
	/*$navbtn .="	
			<a href=\"javascript: javascript:showpage(1,document.$form.checks,'viewinstancestock.php','subsection','maindiv') \" title=\"Stock Details\"><b>Stock Detail</b></a>";*/
			$navbtn .="	
			<a href=\"javascript: javascript:showpage(1,document.$form.checks,'stockdetail.php','stockdetailsdiv','maindiv') \" title=\"Stock Details\"><b>Stock Detail</b></a>";
}
if(in_array('15',$actions))
{
	$navbtn .="	
			|&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'barcodestock.php','sugrid','maindiv','stock') \" title=\"Manage Shipments\"><b>Shipments</b></a>";
			
}//if
if(in_array('84',$actions))
{
	$navbtn .="	
			|&nbsp;<a href=\"javascript: showpage(0,document.$form.checks,'movestockitem.php','sugrid','maindiv','stock') \" title=\"Move Stock\"><b>Move Stock</b></a>";
			
}//if

//THIS IS Process Return LINK		
$navbtn .="	
			|&nbsp;<a href=\"javascript: javascript:showpage(1,document.$form.checks,'stockprocessreturns.php','stockdetailsdiv','maindiv') \" title=\"Process Return\"><b>Process Return</b></a>";			

//THIS IS VIEW DAMAGES LINK			
$navbtn .="	
		|&nbsp;<a href=\"javascript: javascript:showpage(1,document.$form.checks,'veiwreturns.php','stockdetailsdiv','maindiv') \" title=\"View Return\"><b>View Return</b></a>";	

//********Print Tag************************/
/*$navbtn .="	
			|&nbsp;<a href=\" javascript:showpageinnewwindow(1,document.$form.checks,'printtag.php','stockdetailsdiv','maindiv') \" title=\"Print Tag\"><b>Print Tag</b></a>";*/
			
$navbtn .="	
			|&nbsp;<a href=\" javascript:selectallrecords('printtag.php','maindiv') \" title=\"Print Tag\"><b>Print Tag</b></a>";							
					
//********ITEMS HISTORY*************************/
$navbtn .="	
			|&nbsp;<a href=\"javascript: javascript:showpage(1,document.$form.checks,'stockprocessdamages.php','stockdetailsdiv','maindiv') \" title=\"Add Damages \"><b>Add Damages</b></a>";			
//********ITEMS HISTORY*************************/
/****** END DUMMY SET ********/
?>
</head>
<div id="sugrid"></div>
<div id="stockdetailsdiv"></div>
<div id='<?php echo $div;?>'>
<div class="breadcrumbs" id="breadcrumbs">Stocks</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,'',$sortorder,$tablename,$totals);
?>
</div>
<br />
<br />