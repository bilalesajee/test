<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;
$id = $_REQUEST['id'];
 $suppliers	=	$_POST['supplier'];
$addtime	=	strtotime($_POST['addtime']);
 $remarks	=	$_POST['remarks'];
$pkpurchasereturnid	=	$_POST['pkpurchasereturnid'];
$purchasereturnid		=	$_POST['id'];
	
/*echo "<pre>";
print_r($_POST['detail']);
echo "</pre>";
exit;*/

if(sizeof($_POST)>0)
{
	
 $field		=	array('fksupplierid','addtime','remarks');
$value		=	array($suppliers,$addtime,$remarks);

if($id=="-1")
	{
		
		
		$pkpurchasereturnid= $AdminDAO->insertrow("$dbname_detail.purchase_return",$field,$value);
		
	}
	else
	{
		$AdminDAO->updaterow("$dbname_detail.purchase_return",$field,$value,"`pkpurchasereturnid`='$id'");
		
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
		$pkpurchasereturndetailid = $row['pkpurchasereturndetailid'];
	


       
		 
		 $query			=	"SELECT pkbarcodeid from barcode where barcode = '$barcode'  ";
         $result		=	$AdminDAO->queryresult($query);

         $barcode1			=	$result[0]['pkbarcodeid'];  
		 
		
		$field_detail		=	array('fkbarcodeid','fkpurchasereturnid','quantity','price','value');
		$value_detail		=	array($barcode1,$pkpurchasereturnid,$quantity,$price,$value);
if($barcode1 > 0)
{
if(trim($pkpurchasereturndetailid))
	{
		
			$AdminDAO->updaterow("$dbname_detail.purchase_return_detail",$field_detail,$value_detail,"`pkpurchasereturndetailid`='$pkpurchasereturndetailid'");
		
		
	}
	else
	{
	$AdminDAO->insertrow("$dbname_detail.purchase_return_detail",$field_detail,$value_detail);	
		
		
	}


}



}
}

?>