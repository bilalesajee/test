<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;
$id = $_REQUEST['id'];
 $suppliers	=	$_POST['supplier'];
$addtime	=	strtotime($_POST['addtime']);
 $remarks	=	$_POST['remarks'];
 $ship_to	=	$_POST['ship_to'];
$pkpurchaseorderid	=	$_POST['pkpurchaseorderid'];
$purchaseorderid		=	$_POST['id'];
	
/*echo "<pre>";
print_r($_POST['detail']);
echo "</pre>";
exit;*/

if(sizeof($_POST)>0)
{
	
 $field		=	array('fksupplierid','addtime','remarks','ship_to','addby');
$value		=	array($suppliers,$addtime,$remarks,$ship_to,$_SESSION['addressbookid']);
$fieldu		=	array('fksupplierid','edittime','remarks','ship_to','editby');
$valueu		=	array($suppliers,time(),$remarks,$ship_to,$_SESSION['addressbookid']);

if($id=="-1")
	{
		
		
		$pkpurchaseorderid= $AdminDAO->insertrow("$dbname_detail.purchase_order",$field,$value);
		
	}
	else
	{
		$AdminDAO->updaterow("$dbname_detail.purchase_order",$fieldu,$valueu,"`pkpurchaseorderid`='$id'");
		
	}
$d_vale=array();
foreach ($_POST['detail'] as $key => $detail)
{

foreach ($detail as $key1 => $val) {
$d_vale[$key1][$key] = $val;
}
}

//print_r($d_vale);
foreach($d_vale as $row)
	{
	
		
        $barcode = $row['barcode'];
		$itemdescription = $row['itemdescription'];
		$quantity = $row['quantity'];
		$price = $row['price'];
		$value = $row['value'];
		$pkpurchaseorderdetailid = $row['pkpurchaseorderdetailid'];
	


       
		 
		 $query			=	"SELECT pkbarcodeid from barcode where barcode = '$barcode'  ";
         $result		=	$AdminDAO->queryresult($query);

         $barcode1			=	$result[0]['pkbarcodeid'];  
		 
		
		$field_detail		=	array('fkbarcodeid','fkpurchaseorderid','quantity','price','value');
		$value_detail		=	array($barcode1,$pkpurchaseorderid,$quantity,$price,$value);
if($barcode1 > 0)
{
if(trim($pkpurchaseorderdetailid))
	{
		
			$AdminDAO->updaterow("$dbname_detail.purchase_order_detail",$field_detail,$value_detail,"`pkpurchaseorderdetailid`='$pkpurchaseorderdetailid'");
		
		
	}
	else
	{
	$AdminDAO->insertrow("$dbname_detail.purchase_order_detail",$field_detail,$value_detail);	
		
		
	}


}



}
}

?>