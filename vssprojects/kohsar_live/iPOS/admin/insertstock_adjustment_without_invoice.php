<?php
include_once("../includes/security/adminsecurity.php");

global $AdminDAO,$V;
$id = $_REQUEST['id'];
$addtime	=	strtotime($_POST['addtime']);
 $remarks	=	$_POST['remarks'];
$pkstockadjustmentid	=	$_POST['pkstockadjustmentid'];
$stockadjustmentid		=	$_POST['id'];
$tam=time();
$add_by		=	$_SESSION['addressbookid'];
$edit_time=time();
$edit_by		=	$_SESSION['addressbookid'];
if(sizeof($_POST)>0)
{
if($id=="-1")
	{
		
		 $field		=	array('addtime','remarks','add_time','add_by');
         $value		=	array($addtime,$remarks,$tam,$add_by);
		 $pkstockadjustmentid= $AdminDAO->insertrow("$dbname_detail.stock_adjustment",$field,$value);
		
	}
	else
	{
		 $field1		=	array('addtime','remarks','modif_datetime','modfi_by');
         $value1		=	array($addtime,$remarks,$edit_time,$edit_by);
		
		$AdminDAO->updaterow("$dbname_detail.stock_adjustment",$field1,$value1,"`pkstockadjustmentid`='$id'");
		
	}
$d_vale=array();
foreach ($_POST['detail'] as $key => $detail)
{

foreach ($detail as $key1 => $val) {
$d_vale[$key1][$key] = $val;
}
}

/*echo "<pre>";
print_r($d_vale);
echo "</pre>";

exit;*/

foreach($d_vale as $row)
	{
	
		//print_r($row);
        $barcode = $row['barcode'];
		$itemdescription = $row['itemdescription'];
		$quantity = $row['quantity'];
		 $type = $row['type'];
		
		
		$pkadjustmentdetailid = $row['pkadjustmentdetailid'];
	


       
		 
		 $query			=	"SELECT pkbarcodeid from barcode where barcode = '$barcode'  ";
         $result		=	$AdminDAO->queryresult($query);

       $barcode1			=	$result[0]['pkbarcodeid']; 
		 
		
		$field_detail		=	array('fkbarcodeid','fkstockadjustmentid','quantity','type','orgquantity','fkstockid','datetime');
		
	$sql_ut			=	"SELECT sum(unitsremaining) um   from $dbname_detail.stock where fkbarcodeid='$barcode1'";
$result2_ut			=	$AdminDAO->queryresult($sql_ut);	
$UM=$result2_ut[0]['um'];
	
		
	  $sql			=	"SELECT unitsremaining,fkbarcodeid,pkstockid   from $dbname_detail.stock where fkbarcodeid='$barcode1' order by pkstockid desc limit 1  ";
      $result2			=	$AdminDAO->queryresult($sql);
   foreach($result2 as $row1)
	{
        $unitsremaining	+=	 $row1['unitsremaining'];
		
		 $fkbarocdeidd	=	 $row1['fkbarcodeid'];
		  $stkid	=	 $row1['pkstockid'];
		  
		 
		
	}
if($type == '1')
{
	$quantitymins=$unitsremaining-$quantity;
		
		//echo $quantitymins;
		//echo "ali";
		//print_r($quantitymins);exit;
			
			if($quantitymins <= 0)
			{
				$qty=0;
			}
			else
			{
				 $qty=$quantitymins;
				
			}

	

}
else if($type == '0')
{
	 $qty=$unitsremaining+$quantity;
	//echo  $quantityplus;
	//echo "alieeeeeeeeeeeeeee";
	
	
}
$fields_r		=	array('unitsremaining');
$values_r		=	array($qty);
	
$table		=	"$dbname_detail.stock";
	
if($qty==0){
	$qty=0;
$values_r		=	array($qty);
$AdminDAO->updaterow($table,$fields_r,$values_r,"fkbarcodeid='$barcode1'");	
}else{
$AdminDAO->updaterow($table,$fields_r,$values_r,"pkstockid='$stkid'");	
}
	//echo $barcode1;
	
$value_detail		=	array($barcode1,$pkstockadjustmentid,$quantity,$type,$UM,$stkid,$tam);

if($barcode1 > 0)
{

	//echo $pkadjustmentdetailid;
if(trim($pkadjustmentdetailid))
	{
		
			$AdminDAO->updaterow("$dbname_detail.stock_adjustment_detail",$field_detail,$value_detail,"`pkadjustmentdetailid`='$pkadjustmentdetailid'");
		
		
	}
	else
	{
		//print_r($value_detail);
	$AdminDAO->insertrow("$dbname_detail.stock_adjustment_detail",$field_detail,$value_detail);	
		
		
	}


}



}
}

?>