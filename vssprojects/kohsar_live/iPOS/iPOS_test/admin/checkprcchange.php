<?php
 		    session_start();
            date_default_timezone_set('Asia/Karachi');
            //set_time_limit(0);     
			include("server_info.php");  
         
		   $now=time();
		   $now_before30min = (time() - 3600); //setting time before 30 mints
		   $dbh_server = new mysqli($server, $server_user, $server_pwd, $dbname_server);
	       $rowlimit=5;
	    //////checking new updation in pricechange/////////////////
         $queryserver = "SELECT * FROM $dbname_server.pricechange where inserttime between $now_before30min and $now";
	     // $queryserver = "select $dbname_server.pricechange.* from $dbname_server.supplierinvoice,$dbname_server.pricechange where fksupplierinvoiceid=pksupplierinvoiceid and FROM_UNIXTIME(invoice_close_date,'%d-%m-%Y')='19-12-2013' ";
	      $result=$dbh_server->query($queryserver);
          $row_cnt1 = $result->num_rows;
		  if($row_cnt1>0){
			 
			 
		  if($row_cnt1>$rowlimit){
		   $ofst=0;
		   while($ofst<$row_cnt1){
		      
		 $queryserver2 = "SELECT * FROM $dbname_server.pricechange where inserttime between $now_before30min and $now LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
while ($row = $result2->fetch_assoc())
    {
        $data[$dbname_server.'.pricechange'][] = $row;
    }
$locdata=urlencode(json_encode($data));
echo ("https://main.esajee.com/admin/receivepData.php?op=1&locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.pricechange'][] = $row;
    }
$locdata=urlencode(json_encode($data));
echo ("https://main.esajee.com/admin/receivepData.php?op=1&locdata=$locdata");
			 
			 
			 }
		 
		  }
	
 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  
$queryserver = "SELECT * FROM $dbname_server.pricechange where pupdatetime between $now_before30min and $now";
	     // $queryserver = "select $dbname_server.pricechange.* from $dbname_server.supplierinvoice,$dbname_server.pricechange where fksupplierinvoiceid=pksupplierinvoiceid and FROM_UNIXTIME(invoice_close_date,'%d-%m-%Y')='19-12-2013' ";
	      $result=$dbh_server->query($queryserver);
          $row_cnt2 = $result->num_rows;
		  if($row_cnt2>0){
			 
			 
		  if($row_cnt2>$rowlimit){
		   $ofst=0;
		   while($ofst<$row_cnt2){
		      
		 $queryserver2 = "SELECT * FROM $dbname_server.pricechange where pupdatetime between $now_before30min and $now LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
while ($row = $result2->fetch_assoc())
    {
        $data[$dbname_server.'.pricechange'][] = $row;
    }
$locdata=urlencode(json_encode($data));
echo ("https://main.esajee.com/admin/receivepData.php?op=2&locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.pricechange'][] = $row;
    }
$locdata=urlencode(json_encode($data));
echo ("https://main.esajee.com/admin/receivepData.php?op=2&locdata=$locdata");
			 
			 
			 }
		 
		  }
	
 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	  

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////Insert cron Time/////////////////////////////////////////////////////////////////
 $row_cnt=$row_cnt1+$row_cnt2;
 //$dbh_server->query("insert into $dbname_server.rep_check (reptime,totalrow) values ('$now','$row_cnt') ");
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

?>
