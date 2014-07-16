<?php
 		    session_start();
            date_default_timezone_set('Asia/Karachi');
            //set_time_limit(0);     
			include("../server_info.php");  

           $now=time();
		   $now_before30min = (time() - 3600); //setting time before 30 mints
		   $dbh_server = new mysqli($server, $server_user, $server_pwd, $dbname_server);
	       $rowlimit=5;
		   
	
 
		    $mdata=file_get_contents("https://main.esajee.com/admin/accounts/gettabid.php?locdata=$dbname_server");
      
	        $dataarr=json_decode($mdata,true);
	        if($dataarr['dbstatus']==1){
	
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			 $returnid=$dataarr['returns'];
			
			    $queryserver = "select * from $dbname_server.returns where pkreturnid > $returnid ";
	      $result=$dbh_server->query($queryserver);
          $row_cnt1 = $result->num_rows;
		  if($row_cnt1>0){
			 
			 
		  if($row_cnt1>$rowlimit){
		   $ofst=0;
		   while($ofst<$row_cnt1){
		      
		 $queryserver2 = "select * from $dbname_server.returns where pkreturnid > $returnid LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
while ($row = $result2->fetch_assoc())
    {
        $data[$dbname_server.'.returns'][] = $row;
    }
 $locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/accounts/receivessrData.php?locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.returns'][] = $row;
    }
 $locdata=urlencode(json_encode($data));
echo file_get_contents("https://main.esajee.com/admin/accounts/receivessrData.php?locdata=$locdata");
			 
			 
			 }
		 
		  }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			$sid=$dataarr['sale'];

			
			    $queryserver = "select * from $dbname_server.sale where pksaleid > $sid ";
	      $result=$dbh_server->query($queryserver);
          $row_cnt1 = $result->num_rows;
		  if($row_cnt1>0){
			 
			 
		  if($row_cnt1>$rowlimit){
		   $ofst=0;
		   while($ofst<$row_cnt1){
		      
		 $queryserver2 = "select * from $dbname_server.sale where pksaleid > $sid LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
while ($row = $result2->fetch_assoc())
    {
        $data[$dbname_server.'.sale'][] = $row;
    }
 $locdata=urlencode(json_encode($data));
echo file_get_contents("https://main.esajee.com/admin/accounts/receivessrData.php?locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.sale'][] = $row;
    }
 $locdata=urlencode(json_encode($data));
echo file_get_contents("https://main.esajee.com/admin/accounts/receivessrData.php?locdata=$locdata");
			 
			 
			 }
		 
		  }
		
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			$sdid=$dataarr['saledetail'];

			
			    $queryserver = "select * from $dbname_server.saledetail where pksaledetailid > $sdid ";
	      $result=$dbh_server->query($queryserver);
          $row_cnt1 = $result->num_rows;
		  if($row_cnt1>0){
			 
			 
		  if($row_cnt1>$rowlimit){
		   $ofst=0;
		   while($ofst<$row_cnt1){
		      
		 $queryserver2 = "select * from $dbname_server.saledetail where pksaledetailid > $sdid LIMIT $ofst , $rowlimit";
$result2=$dbh_server->query($queryserver2);
while ($row = $result2->fetch_assoc())
    {
        $data[$dbname_server.'.saledetail'][] = $row;
    }
 $locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/accounts/receivessrData.php?locdata=$locdata");
$ofst=$ofst+$rowlimit;
$data='';		   
		   }
		 }else{
			 
while ($row = $result->fetch_assoc())
    {
        $data[$dbname_server.'.saledetail'][] = $row;
    }
 $locdata=urlencode(json_encode($data));
file_get_contents("https://main.esajee.com/admin/accounts/receivessrData.php?locdata=$locdata");
			 
			 
			 }
		 
		  }
			
			
			 }

		   

?>