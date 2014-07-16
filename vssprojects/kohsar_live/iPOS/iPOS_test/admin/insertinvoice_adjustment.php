<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;
$id = $_REQUEST['id'];
 $suppliers	=	$_POST['supplier'];
$addtime	=	strtotime($_POST['addtime']);
 $remarks	=	$_POST['remarks'];
$pkadjustmentid	=	$_POST['pkadjustmentid'];
$purchasereturnid		=	$_POST['id'];	
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
	echo $suppliers.'  '.$addtime.' '.$remarks;
*/


if(sizeof($_POST)>0)
{
	
 $field		=	array('fksupplierid','addtime','remarks','addby');
$value		=	array($suppliers,$addtime,$remarks,$_SESSION['addressbookid']);
 $fieldu		=	array('fksupplierid','edittime','remarks','editby');
$valueu		=	array($suppliers,time(),$remarks,$_SESSION['addressbookid']);

if($id=="-1")
	{
	//	echo $suppliers.'  '.$addtime.' '.$remarks;
		 $pkadjustmentid= $AdminDAO->insertrow("$dbname_detail.adjustment",$field,$value);
	 //exit;
	}else{
		
		$AdminDAO->updaterow("$dbname_detail.adjustment",$fieldu,$valueu,"`pkadjustmentid`='$id'");
		
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
		$field_detail		=	array('fkbarcodeid','fkadjustmentid','quantity','price','value');
		$value_detail		=	array($barcode1,$pkadjustmentid,$quantity,$price,$value);
        if($barcode1 > 0){
        if(trim($pkpurchasereturndetailid))
	   {
		
			$AdminDAO->updaterow("$dbname_detail.adjustment_detail",$field_detail,$value_detail,"`pkpurchasereturndetailid`='$pkpurchasereturndetailid'");
	   }else{
	        $AdminDAO->insertrow("$dbname_detail.adjustment_detail",$field_detail,$value_detail);	
		
		
	}


}



}
}

?>