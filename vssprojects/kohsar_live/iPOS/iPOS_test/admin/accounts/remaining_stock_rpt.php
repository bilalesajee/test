<?php

session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
include("../../includes/security/adminsecurity.php");
global $AdminDAO, $Component;

 $barcode=$_GET['barcode'];
  

 $query_show_stock1 = "SELECT st.pkstoreid as loc_code,st.storeshortname as loc,
				s.quantity as stock ,ROUND(s.priceinrs,2) as tp,ROUND(s.retailprice,2) as rp
				FROM 
				$dbname_detail.stock s
				left join store st on st.pkstoreid = s.fkstoreid
				
			WHERE
			
				 s.fkbarcodeid ='$barcode'  order by s.pkstockid desc limit 1 ";
				 


$reportresult		=	$AdminDAO->queryresult($query_show_stock1);
//$reportresult_sizeof = sizeof($reportresult[0]);

//echo "<pre>";
//print_r($reportresult);




?>

<style>

.table
{
border-collapse:collapse;
}
.table, td, th
{
border:1px solid #000000;
}


.border {
border-top-width: 1px;
border-right-width: 1px;
border-bottom-width: 1px;
border-left-width: 1px;
border-top-style: double;
border-right-style: double;
border-bottom-style: double;
border-left-style: double;
border-top-coloe:#000000;
border-right-color:#000000;
border-bottom-color: #000000;
border-left-color:#000000;

}




</style>

  <?php
  

  
$res=sizeof($reportresult);
 if($res>0){ 
for($i=0;$i<sizeof($reportresult);$i++)
{
	
	$loc_code	=	$reportresult[$i]['loc_code'];	
	$barcode	=	$reportresult[$i]['barcode'];	
	$loc	=	$reportresult[$i]['loc'];
	$stock	=	$reportresult[$i]['stock'];
	$tp	=	$reportresult[$i]['tp'];
	$rp	=	$reportresult[$i]['rp'];
	

  $htm = '<tr>
  
     <th  width="79" align="left">'.$loc.'</th>
	 <th width="104" align="left">'.$stock.' </th>
	  <th width="97" align="right">'.$tp.'</th>
    <th width="146" align="right">'.$rp.'</th>
  </tr>';
 
 }
 }else{
$htm = '<tr>
  
     <th  width="79" align="left">'.$loc.'</th>
	 <th width="104" align="left">0</th>
	  <th width="97" align="right">0</th>
    <th width="146" align="right">0</th>
  </tr>';
	 
	 
	 }
 
echo  $htm;
 
 ?>




