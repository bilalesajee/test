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
$query_show_stock1 = "SELECT s.expiry,s.fksupplierinvoiceid,s.fkbarcodeid ,st.pkstoreid as loc_code,st.storeshortname as loc,
s.quantity as stock ,ROUND(s.costprice,2) as tp, case when ifnull( (select p.price from $dbname_detail.pricechange p where p.fkbarcodeid = s.fkbarcodeid) ,0) = '0' then s.retailprice else ifnull( (select p.price from $dbname_detail.pricechange p where p.fkbarcodeid = s.fkbarcodeid) ,0) end rp
FROM
$dbname_detail.stock s
left join store st on st.pkstoreid = s.fkstoreid
WHERE
s.fkbarcodeid ='$barcode' order by s.pkstockid desc";
 /*$query_show_stock1 = "SELECT s.expiry,s.fksupplierinvoiceid,s.fkbarcodeid ,st.pkstoreid as loc_code,st.storeshortname as loc,
				s.quantity as stock ,ROUND(s.priceinrs,2) as tp,ROUND(s.retailprice,2) as rp
				FROM 
				$dbname_detail.stock s
				left join store st on st.pkstoreid = s.fkstoreid
				
			WHERE
			
				 s.fkbarcodeid ='$barcode' order by s.pkstockid desc  ";*/
				 
				


$reportresult = $AdminDAO->queryresult($query_show_stock1);
//$a[]=$reportresult[0];
echo json_encode($reportresult[0]);

?>
