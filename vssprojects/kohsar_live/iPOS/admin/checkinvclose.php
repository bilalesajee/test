<?php
 		    session_start();
            date_default_timezone_set('Asia/Karachi');
            //set_time_limit(0);     
			include("server_info.php");  
         
		   $now=time();
		   $now_before30min = (time() - 3600); //setting time before 30 mints
		   $dbh_server = new mysqli($server, $server_user, $server_pwd, $dbname_server);
	       $rowlimit=5;
	    //////checking new updation in stock/////////////////
         $queryserver = "select $dbname_server.stock.* from $dbname_server.supplierinvoice,$dbname_server.stock where fksupplierinvoiceid=pksupplierinvoiceid and (invoice_close_date between $now_before30min and $now) ";
	     // $queryserver = "select $dbname_server.stock.* from $dbname_server.supplierinvoice,$dbname_server.stock where fksupplierinvoiceid=pksupplierinvoiceid and FROM_UNIXTIME(invoice_close_date,'%d-%m-%Y')='19-12-2013' ";
	      $result=$dbh_server->query($queryserver);
          $row_cnt1 = $result->num_rows;
		  if($row_cnt1>0){
			 
			 
		  if($row_cnt1>$rowlimit){
		   $ofst=0;
		   while($ofst<$row_cnt1){
		      
		 $queryserver2 = "select $dbname_server.stock.* from $dbname_server.supplierinvoice,$dbname_server.stock where fksupplierinvoiceid=pksupplierinvoiceid and (invoice_close_date between $now_before30min and $now) LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
while ($row = $result2->fetch_assoc())
    {
        $data[$dbname_server.'.stock'][] = $row;
    }
$locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/receiveData.php?locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.stock'][] = $row;
    }
$locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/receiveData.php?locdata=$locdata");
			 
			 
			 }
		 
		  }
	
 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
////////////////////////////////////////////Movement Stock//////////////////////////////////////////////////////////////////////////
        $queryserver = "select * from $dbname_server.stock where fksupplierinvoiceid=0 and fkconsignmentdetailid > 0  and (addtime between $now_before30min and $now) ";
	   $result=$dbh_server->query($queryserver);
       $row_cnt2 = $result->num_rows;
	   if($row_cnt2>0){
	   if($row_cnt2>$rowlimit){
		   $ofst=0;
	   while($ofst<$row_cnt2){
		      
 $queryserver2 = "select * from $dbname_server.stock where fksupplierinvoiceid=0 and fkconsignmentdetailid > 0  and (addtime between $now_before30min and $now)  LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
      while ($row = $result2->fetch_assoc()){
      $data[$dbname_server.'.stock'][] = $row;
      }
$locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/receiveData.php?locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.stock'][] = $row;
    }
$locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/receiveData.php?locdata=$locdata");
			 }
		  }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////Insert cron Time/////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
////////////////////////////////////////////Movement Stock//////////////////////////////////////////////////////////////////////////
       $queryserver = "select * from $dbname_server.stock where fksupplierinvoiceid=0 and fkconsignmentdetailid=0 and (addtime between $now_before30min and $now) ";
	   $result=$dbh_server->query($queryserver);
       $row_cnt3 = $result->num_rows;
	   if($row_cnt3>0){
	   if($row_cnt3>$rowlimit){
		   $ofst=0;
	   while($ofst<$row_cnt3){
		      
$queryserver2 = "select * from $dbname_server.stock where fksupplierinvoiceid=0 and fkconsignmentdetailid=0 and (addtime between $now_before30min and $now) LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
      while ($row = $result2->fetch_assoc()){
      $data[$dbname_server.'.stock'][] = $row;
      }
$locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/receiveData.php?locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.stock'][] = $row;
    }
$locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/receiveData.php?locdata=$locdata");
			 }
		  }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////Insert cron Time/////////////////////////////////////////////////////////////////
 $row_cnt=$row_cnt1+$row_cnt2+$row_cnt3;
 $dbh_server->query("insert into $dbname_server.rep_check (reptime,totalrow) values ('$now','$row_cnt') ");
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

?>
