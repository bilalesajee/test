<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;
 $id = $_REQUEST['id'];

      $invoiceid		=	$_POST['id'];
		$storeid			=	$_SESSION['storeid'];
	$boxid				=	$_SESSION['boxid'];
	$addressbookid		=	$_SESSION['addressbookid'];
/*echo "<pre>";
print_r($_POST['detail']);
echo "</pre>";
exit;*/

if(sizeof($_POST)>0)
{
	

$d_vale=array();
foreach ($_POST['detail'] as $key => $detail)
{

foreach ($detail as $key1 => $val) {
$d_vale[$key1][$key] = $val;
}
}

//print_r($d_vale);exit;
foreach($d_vale as $row)
	{
	
	 $pkstockid	=	$row['pkstockid'];
   $pkdamageid	=	$row['pkdamageid'];
		
	$barcode			=	$row['barcode1'];
	$units				=	$row['units'];
	$damaged			=	$row['damaged'];		
	$damagetype			= 	$row['damagetype'];
	$purchaseprice		=	$row['purchaseprice'];
	$priceinrs			=	$row['priceinrs'];
	$shipmentpercent	=	$row['shipmentpercentage'];	
	$shipmentcharges		= 	$row['shipmentcharges'];	
	$costprice			=	$row['costprice'];	
	$saleprice			=	$row['saleprice'];
	$boxprice			=	$row['boxprice'];
	$batch				=	$row['batch'];	
	$expirydate			=	strtotime($row['expiry']);	

$remainingunits 	= 	$units-$damaged;
       
		 
	   $query			=	"SELECT pkbarcodeid from barcode where barcode = '$barcode'  ";
        $result		=	$AdminDAO->queryresult($query);

       $barcodeid			=	$result[0]['pkbarcodeid'];  
		 
		 
	
		
			$fields				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice","fksupplierinvoiceid","addtime");
			$values				= 	array($batch,$units,$remainingunits,$expirydate,$purchaseprice,$costprice,$saleprice,$priceinrs,$shipmentcharges,0,0,$barcodeid,0,$storeid,$addressbookid,0,time(),$shipmentpercent,$boxprice,$invoiceid,time());




///////////////////////////////////////////////update log////////////////////////////////////////////////////////////////////////

 $query_update_log="select * from $dbname_detail.stock where pkstockid='$pkstockid' ";
$results = $AdminDAO->queryresult($query_update_log);

	foreach ($results as $result)
{
	 $Batch=$result["batch"];
	 $quantity=$result["quantity"];
	 $Unitsremaining=$result["unitsremaining"];
	 $Expiry=$result["expiry"];
	 $Purchaseprice=$result["purchaseprice"];
	 $Costprice=$result["costprice"];
	 $Retailprice=$result["retailprice"];
	 $Priceinrs=$result["priceinrs"];
	 $Priceinrs=$result["priceinrs"];
	 $Shipmentcharges=$result["shipmentcharges"];
	 $Fkshipmentgroupid=$result["fkshipmentgroupid"];
	 $Fkshipmentid=$result["fkshipmentid"]; 
	 $Fkbarcodeid=$result["fkbarcodeid"]; 
	 $Fksupplierid=$result["fksupplierid"]; 
	 $Fkstoreid=$result["fkstoreid"]; 
	 $Fkemployeeid=$result["fkemployeeid"];
	 $Fkbrandid=$result["fkbrandid"]; 
	 $Updatetime=$result["updatetime"]; 
	 $Shipmentpercentage=$result["shipmentpercentage"]; 
	 $Boxprice=$result["boxprice"]; 
	 $Fksupplierinvoiceid=$result["fksupplierinvoiceid"]; 
	 $Addtime=$result["addtime"]; 
	 $operation='Update'; 
	 $edit_time=time();
     $edit_by		=	$_SESSION['addressbookid'];
	 $old_id=$result["pkstockid"]; 
	$fields_log				=	array("batch","quantity","unitsremaining","expiry","purchaseprice","costprice","retailprice","priceinrs","shipmentcharges","fkshipmentgroupid","fkshipmentid","fkbarcodeid","fksupplierid","fkstoreid","fkemployeeid","fkbrandid","updatetime","shipmentpercentage","boxprice","fksupplierinvoiceid","addtime","operation","edit_time","edit_by","old_id");

	$values_log				= 	array($Batch,$quantity,$Unitsremaining,$Expiry,$Purchaseprice,$Costprice,$Retailprice,$Priceinrs,$Shipmentcharges,$Fkshipmentgroupid,$Fkshipmentid,$Fkbarcodeid,$Fksupplierid,$Fkstoreid,$Fkemployeeid,$Fkbrandid,$Updatetime,$Shipmentpercentage,$Boxprice,$Fksupplierinvoiceid,$Addtime,$operation,$edit_time,$edit_by,$old_id);
	
	$AdminDAO->insertrow("$dbname_detail.stock_log",$fields_log,$values_log);		   
			   
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
			$AdminDAO->updaterow("$dbname_detail.stock",$fields,$values,"`pkstockid`='$pkstockid'");
		
		
		 if($damaged >0)
			{
				$fields1		=	array("quantity","fkstoreid","fkemployeeid","damagedate","fkdamagetypeid");
				$data		=	array($damaged,$storeid,$addressbookid,time(),$damagetype);
					$AdminDAO->updaterow("$dbname_detail.damages",$fields1,$data,"`pkdamageid`='$pkdamageid'");
				
			}




}
}

?>