<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$storeid		=	$_SESSION['storeid'];
$employeeid		=	$empid;
/***********************************GET DATA AND VALIDATE**************************************/
list($barcodeid,$demandname)=	explode("_",$_REQUEST['barcodeid']);
list($x,$y,$demandid)		=	explode("-",$demandname);
$units						=	$_REQUEST['units'];
$add						=	$_REQUEST['add2'];
$comments					=	$_REQUEST['comments'];
$deadline					=	$_REQUEST['deadline'];
$brandid					=	$_REQUEST['brandname'];
$demanddate					=	date('Y-m-d');
$v			=	array("Units,$units,e","Dead Line,$deadline,e");
if ($V->validate($v) ==1)
{
	echo $V->msg;
	exit;
}
/****************************Whether TO PUT IN DEMAND OR NOT********************/
if ($add ==-1)//demand has not been generated
{
	//insert new row in demand for this store
	$table		=	"$dbname_detail.demand";
	$field		=	array('demandname','fkstoreid','demanddate','	fkaddressbookid 	');
	$value		=	array($demandname,$storeid,strtotime($demanddate),$employeeid);
	$demandid	=	$AdminDAO->insertrow($table,$field,$value);
}//if
/*******************************************DEMAND DETAILS data***********************************************/
$table				=	"$dbname_detail.demanddetails";
$field				=	array('fkdemandid','fkbarcodeid','comments','deadline','fkbrandid','units');
$value				=	array($demandid,$barcodeid,$comments,strtotime($deadline),$brandid,$units);
$demanddetailsid 	=	$AdminDAO->insertrow($table,$field,$value);
/******************************************INSTANCE DEMAND DETAILS DATA***********************************				
foreach($_POST as $k=>$p)
{
	//print"$k  = $p<br>";
	/*if (strpos($k,"attribute_")!== false)
	{
		$table				=	"instancedemanddetails";
		$field				=	array('fkdemanddetailsid','fkproductattributeid','fkattributeoptionid');
		list($a,$fkproductattributeid)	=	explode("_",$k);
		if(is_array($_POST[$k]))
		{
			foreach ($_POST[$k] as $attributeoptionid)
			{
		
				$value	=	array($demanddetailsid,$fkproductattributeid,$attributeoptionid);
				$AdminDAO->insertrow($table,$field,$value);
			}//foreach
		}//if
		else
		{
			$value	=	array($demanddetailsid,$fkproductattributeid,$_POST[$k]);
			$AdminDAO->insertrow($table,$field,$value);
		}//else
	}//if
}//foreach
*/
$from	=	" product, barcode, $dbname_detail.demanddetails ";
$fields	=	" DISTINCT (productname)";
$condition	=	"fkdemandid = '$demandid' AND fkbarcodeid = pkbarcodeid AND fkproductid = pkproductid";
$products_array 	=	$AdminDAO->getrows($from,$fields,$condition);
for($i=0; $i<=sizeof($products_array); $i++)
{
	$products	.="  ".$products_array[$i]['productname'];
}
$fields	=	array('demandproduct');
$values	=	array($products);
$AdminDAO->updaterow("$dbname_detail.demand",$fields,$values, " pkdemandid = '$demandid'");
?>
