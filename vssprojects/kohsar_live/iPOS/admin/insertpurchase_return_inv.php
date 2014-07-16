<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;
$id = $_REQUEST['id'];
 $suppliers	=	$_POST['supplier'];
$addtime	=	strtotime($_POST['addtime']);
 $remarks	=	$_POST['remarks'];
  $inv	=	$_POST['inv'];
$pkpurchasereturnid	=	$_POST['pkpurchasereturnid'];
$purchasereturnid		=	$_POST['id'];	
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
	echo $suppliers.'  '.$addtime.' '.$remarks;
*/


if(sizeof($_POST)>0)
{
	
 $field		=	array('fksupplierid','addtime','remarks','addby','invcid');
$value		=	array($suppliers,$addtime,$remarks,$_SESSION['addressbookid'],$inv);
 $fieldu		=	array('fksupplierid','edittime','remarks','editby','invcid','addtime');
$valueu		=	array($suppliers,time(),$remarks,$_SESSION['addressbookid'],$inv,$addtime);

if($id=="-1")
	{
	//	echo $suppliers.'  '.$addtime.' '.$remarks;
		 $pkpurchasereturnid= $AdminDAO->insertrow("$dbname_detail.purchase_return",$field,$value);
	 //exit;
	}else{

			
		///////////////////////////////////////////////update log////////////////////////////////////////////////////////////////////////

  $query_update_log="select * from $dbname_detail.purchase_return where pkpurchasereturnid='$id' ";
  $result = $AdminDAO->queryresult($query_update_log);
	 $Fksupplierid=$result[0]["fksupplierid"];	
	 $Addtime=$result[0]["addtime"];	
	 $Remarks=$result[0]["remarks"];	
	 $Addby=$result[0]["addby"];	
	 $Invcid=$result[0]["invcid"];	
	 $operation='Update'; 
	  $edit_time=time();
     $edit_by		=	$_SESSION['addressbookid'];
	 $old_id=$result[0]["pkpurchasereturnid"];
	 
	 $field_log		=	array('fksupplierid','addtime','remarks','addby','invcid','operation','edittime','editby','old_id');
     $value_log		=	array($Fksupplierid,$Addtime,$Remarks,$Addby,$Invcid,$operation,$edit_time,$edit_by,$old_id);
	 	
	$AdminDAO->insertrow("$dbname_detail.purchase_return_log",$field_log,$value_log);	
	/////////////////////////////////////////////////////////////////////////////////////////	

		$AdminDAO->updaterow("$dbname_detail.purchase_return",$fieldu,$valueu,"`pkpurchasereturnid`='$id'");
		
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
        if($barcode1 > 0){
        if(trim($pkpurchasereturndetailid))
	   {
	
		/////////////////////////////////////////////// detail log////////////////////////////////////////////////////////////////////////

 $query_deatail_log="select * from $dbname_detail.purchase_return_detail where pkpurchasereturndetailid='$pkpurchasereturndetailid' ";
$results = $AdminDAO->queryresult($query_deatail_log);


	 $Fkbarcodeid=$results[0]["fkbarcodeid"];
	 $Fkpurchasereturnid=$results[0]["fkpurchasereturnid"];
	 $Quantity=$results[0]["quantity"];
	 $Price=$results[0]["price"];
	 $Value=$results[0]["value"];
	 $operation='Update'; 
	 $edit_time=time();
     $edit_by		=	$_SESSION['addressbookid'];
	 $old_id=$results[0]["pkpurchasereturndetailid"]; 
		
		$field_detail_log		=	array('fkbarcodeid','fkpurchasereturnid','quantity','price','value','operation','edit_time','edit_by','old_id');
		$value_detail_log		=	array($Fkbarcodeid,$Fkpurchasereturnid,$Quantity,$Price,$Value,$operation,$edit_time,$edit_by,$old_id);
		$AdminDAO->insertrow("$dbname_detail.purchase_return_detail_log",$field_detail_log,$value_detail_log);	
	

		/////////////////////////////////////////////////////////////////////////////////////////

	
		$AdminDAO->updaterow("$dbname_detail.purchase_return_detail",$field_detail,$value_detail,"`pkpurchasereturndetailid`='$pkpurchasereturndetailid'");

	   }else{

	        $AdminDAO->insertrow("$dbname_detail.purchase_return_detail",$field_detail,$value_detail);	
		
		
	}


}



}
}

?>