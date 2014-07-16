<?php
////////////////////////////////////////////////////////////////		  
$server = 'localhost';
$server_user = 'myagent';
$server_pwd = 'Ye031@DB';
////////////////////////////////////////////////////////////////
$dbname_detail=$dbname_server='main_kohsar';
$get_option= date('d-m-y', strtotime('-1 day'));
$dbh_server = new mysqli($server, $server_user, $server_pwd, $dbname_server);
if($dbh_server->connect_errno > 0){
	echo "Error";
	echo $dbh_server->connect_error;
}else{
//////////////////////////////////////////////////////////STOCK MONITOR/////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////checking Sale///////////////////////////////////////////////////////////////////////////////////// 
$querygm1 = "INSERT INTO $dbname_detail.stockmonitor ( trans_id,quantity,price,fkbarcodeid ,fksupplierid ,fkstoreid , fkemployeeid ,brand_id ,updatetime ,
  product_id ,  fksupplierinvid,addtime,type,svalue ) SELECT  fksaleid , sd.quantity ,  priceinrs ,fkbarcodeid  ,fksupplierid ,fkstoreid  ,  fkemployeeid ,
  fkbrandid , addtime , fkproduct_id , fksupplierinvoiceid , '".time()."','dsi',sd.quantity*priceinrs  FROM    $dbname_detail.stock st ,$dbname_detail.saledetail sd where  pkstockid=fkstockid and from_unixtime(timestamp,'%d-%m-%y')='$get_option' and sd.quantity >0  ";
 $dbh_server->query($querygm1);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////checking sale return//////////////////////////////////////////////////////////////////////////////
 $querygm2 = "INSERT INTO $dbname_detail.stockmonitor ( trans_id,quantity,price,fkbarcodeid ,fksupplierid ,fkstoreid , fkemployeeid ,brand_id ,updatetime ,
  product_id ,  fksupplierinvid,addtime,type,svalue ) SELECT  fksaleid , (sd.quantity)*-1 ,  priceinrs ,fkbarcodeid  ,fksupplierid ,fkstoreid  ,  fkemployeeid ,
  fkbrandid , addtime , fkproduct_id , fksupplierinvoiceid , '".time()."','sr',(sd.quantity*priceinrs)*-1  FROM    $dbname_detail.stock st ,$dbname_detail.saledetail sd where  pkstockid=fkstockid and from_unixtime(timestamp,'%d-%m-%y')='$get_option' and sd.quantity<0  ";
 $dbh_server->query($querygm2);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////checking movement///////////////////////////////////////////////////////////////////////////////
 $querygm3 = "INSERT INTO $dbname_detail.stockmonitor ( trans_id,quantity,price,fkbarcodeid ,fksupplierid ,fkstoreid , fkemployeeid ,brand_id ,updatetime ,
  product_id ,  fksupplierinvid,addtime,type,svalue ) SELECT  pkstockid , quantity ,  priceinrs ,fkbarcodeid  ,fksupplierid ,fkstoreid  ,  fkemployeeid ,
  fkbrandid , addtime , fkproduct_id , fksupplierinvoiceid , '".time()."','ti',quantity*priceinrs  FROM    $dbname_detail.stock st where from_unixtime(addtime,'%d-%m-%y')='$get_option'  and fkconsignmentdetailid>0  ";
 $dbh_server->query($querygm3);
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////Updating Quantity in stock balance table/////////////////////////////////////////////////////////////////////////////////////////////////*/
            $stockQuantity=0;
            $querygmj = "SELECT fkbarcodeid,quantity,svalue,type FROM $dbname_detail.stockmonitor where from_unixtime(addtime,'%d-%m-%y')='$get_option' order by updatetime asc ";
            $resultj = $dbh_server->query($querygmj);
               while($uniquj=$resultj->fetch_assoc()){
              
			  $stockQuantity=$uniquj['quantity'];
			  $stockValue=$uniquj['svalue'];
			  $av_Value=($stockValue/$stockQuantity);
			  
			  $query_bal_update = "SELECT fkbarcodeid FROM $dbname_detail.stockbalance where fkbarcodeid='".$uniquj['fkbarcodeid']."' ";
              $res_bal = $dbh_server->query($query_bal_update);
              $check_row=$res_bal->fetch_assoc();
	          if($check_row['fkbarcodeid']==''){
		     $query_bal_update1 = "insert into $dbname_detail.stockbalance (cl_stock,fkbarcodeid,avg_value,tvalue) values ('$stockQuantity','".$uniquj['fkbarcodeid']."','".$av_Value."','".$stockValue."') ";
             $dbh_server->query($query_bal_update1);   
			   
			   }else{
			
				
			if($type=='gnr' or $type=='ti' or $type=='sr'){
			
$query_bal_update1 = "update  $dbname_detail.stockbalance set cl_stock=(cl_stock+'$stockQuantity') , tvalue=round((tvalue+'$stockValue'),2) , avg_value=round((tvalue+'$stockValue'),2)/round((cl_stock+'$stockQuantity'),2)  where fkbarcodeid='".$uniquj['fkbarcodeid']."' ";
            $dbh_server->query($query_bal_update1);	      	  
				  
				  
				  }else{
					  
		$query_bal_update1 = "update  $dbname_detail.stockbalance set cl_stock=round((cl_stock-'$stockQuantity'),2) , tvalue=round((tvalue-round(('$stockValue'*avg_value),2)),2)   where fkbarcodeid='".$uniquj['fkbarcodeid']."' ";
            $dbh_server->query($query_bal_update1);	       
					  
					  
					  }
					   
				   }
      	  }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////Updating Brand///////////////////////////////////////////////////////////////////////////////////
/*$Updatebrand = "update $dbname_detail.stockmonitor,main.barcodebrand set brand_id=fkbrandid  where $dbname_detail.stockmonitor.fkbarcodeid=main.barcodebrand.fkbarcodeid and brand_id=0 ";
  $dbh_server->query($Updatebrand);
////////////////////////////////////////////////////////Updating Product//////////////////////////////////////////////////////////////////////////////////  
 $Updateproduct = "update $dbname_detail.stockmonitor,main.barcode set product_id=fkproductid  where $dbname_detail.stockmonitor.fkbarcodeid=main.barcode.pkbarcodeid and product_id=0 ";
  $dbh_server->query($Updateproduct);*/
  
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>