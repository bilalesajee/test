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
$option = $_REQUEST['option'];
if ($option == '') {
    $get_option = date("d-m-Y", time());
} else {
    $get_option = $option;
}


echo $query = "SELECT acci,AccountCode,SupplierCode from live_kohsar.accid";


$result = $AdminDAO->queryresult($query);

foreach ($result as $p) {

 echo $q = "update account set AccountCode = '210100001',fksupplierid = '{$p['SupplierCode']}' where id = '{$p['AccountCode']}'";
     mysql_query($q);
}

exit;



$query = "SELECT p.fkaccountid,p.pkpurchaseorderid,p.quotetitle,p.ponum,FROM_UNIXTIME(p.deadline,'%d-%m-%Y') deadline,FROM_UNIXTIME(p.addtime,'%d-%m-%Y') addtime,p.status,p.expired,p.terms from $dbname_detail.purchaseorder p
where 1=1 ";//FROM_UNIXTIME(p.deadline,'%d-%m-%Y') = '$get_option'



$reportresult = $AdminDAO->queryresult($query);
//print_r($reportresult);
$trow = count($reportresult);

for ($p = 0; $p < $trow; $p++) {

	$pkpurchaseorderid = $reportresult[$p]['pkpurchaseorderid'];
        $quotetitle = $reportresult[$p]['quotetitle'];
        $ponum = $reportresult[$p]['ponum'];
        $deadline = $reportresult[$p]['deadline'];
        $addtime = $reportresult[$p]['addtime'];
        $status = $reportresult[$p]['status'];
        $expired = $reportresult[$p]['expired'];
        $terms = $reportresult[$p]['terms'];
        $fkaccountid = $reportresult[$p]['fkaccountid'];
        $fkaccountid = str_pad($fkaccountid, 6, 0, STR_PAD_LEFT);
        
	  $query2 = "SELECT * from  $dbname_detail.podetail where fkpurchaseorderid = '$pkpurchaseorderid' ";
            
          $reportresult2 = $AdminDAO->queryresult($query2);
$array[] =  array('id'=>$pkpurchaseorderid,'fkaccountid'=>$fkaccountid,'ponum'=>$ponum,'deadline'=>$deadline,'quotetitle'=>$quotetitle,'status'=>$status,'addtime'=>$addtime,'expired'=>$expired,'terms'=>$terms,'detail'=>$reportresult2);
   
}
echo json_encode($array);
/*echo "<pre>";
print_r($array);*/
?>
