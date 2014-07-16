<?php
 		    session_start();
            date_default_timezone_set('Asia/Karachi');
            //set_time_limit(0);  
			include("server_info.php");     
		   $now=time();
		   $now_before30min = (time() - 900); //setting time before 30 mints
		   $dbh_server = new mysqli($server, $server_user, $server_pwd, $dbname_server);
	       $stock_updates=array();
	    //////checking new updation in stock/////////////////
          $queryserver = "select * from main.addressbook where modif_datetime between $now_before30min and $now ";
	   // echo $queryserver = "select * from pricechange where pupdatetime between 1370158568 and 1370239378 ";
	      $result=$dbh_server->query($queryserver);
          $data=count($result);
		 if($data>0){
		 while($row = $result->fetch_assoc()){
     	 $stock_updates[]=$row;
          }
		  }else{
			$stock_updates[]='';
			}
		 	/*echo "<pre>";
           print_r($stock_updates);*/
	   echo json_encode($stock_updates);
		  //////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
?>
