<?php
set_time_limit(0);
$ips = array(1 => 32 , 2 => 132, 3 => 130);
////////////////////////////////////////////////////////////////		  
$server_user = 'esajeeagent';
$server_pwd = 'esajeeagent';
$server_db = 'main_kohsar';
////////////////////////////////////////////////////////////////


foreach($ips as $counter => $ippart)
{
	   $server = "192.168.10.{$ippart}";
        $dbh = new mysqli($server, $server_user, $server_pwd , $server_db);
if($dbh->connect_errno > 0){

	echo $dbh->connect_error;

}else{

      $fkpricechangeid=$_REQUEST['fkpricechangeid'];
	  $fkaddressbookid=$_REQUEST['fkaddressbookid'];
	  $changeprice=$_REQUEST['oldprice'];
	  $pkbarcodeid=$_REQUEST['fkbarcodeid'];
	  $newprice=$_REQUEST['price'];
	  $countername=$_REQUEST['countername'];
	  $changetime=time();
      if($pkbarcodeid!=''){
	
	 $query2 = "select pkpricechangeid,countername,price from pricechange where fkbarcodeid='$pkbarcodeid'";
     $result2 = $dbh->query($query2);
     $getorw = $result2->fetch_assoc();
     $pricechangeid	=	$getorw['pkpricechangeid'];

     if($pricechangeid > 0){
	 	 $q2 = "UPDATE pricechange SET fkbarcodeid = '$pkbarcodeid' , price = '$newprice', countername = '$countername' ,pupdatetime='$changetime' WHERE pkpricechangeid='$fkpricechangeid' ";
          $dbh->query($q2); 
		
		 
		 }else{
	
		  $q3="INSERT INTO pricechange (fkbarcodeid, price, countername , inserttime) VALUES ('$pkbarcodeid', '$newprice', '$countername' , '$changetime') ";
          $dbh->query($q3);
		  $fkpricechangeid = $dbh->insert_id;
			 
		}
   	
	$q4="INSERT INTO pricechangehistory (fkpricechangeid, fkaddressbookid, updatetime, oldprice) VALUES ('$fkpricechangeid', '$fkaddressbookid', '$changetime', '$changeprice') ";
		$dbh->query($q4);

	}
	      $dbh->close();
		
	
	
	}
	}

?>