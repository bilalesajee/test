<?php ob_start();

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

$Inv_ID = $_REQUEST['invoiceid'];

if($Inv_ID=='' or $Inv_ID==0){
echo "Enter Supplier Invoice Id";
exit;
}else{
	
	 $queryh = "SELECT pksupplierinvoiceid from $dbname_detail.supplierinvoice su where   pksupplierinvoiceid='$Inv_ID' and invoice_status=0";
     $reportresulth = $AdminDAO->queryresult($queryh);
	 $foo=count($reportresulth);
 if($foo>0){
  $queryg = "INSERT INTO $dbname_detail.stock_woide ( pkstockid ,batch,quantity,unitsremaining ,expiry ,  purchaseprice ,costprice ,retailprice , priceinrs , shipmentcharges ,
  suggestedsaleprice ,fkshipmentgroupid ,fkshipmentid ,fkbarcodeid ,fkorderid ,fksupplierid , fkagentid , fkcountryid ,
  fkstoreid ,
  fksupplierinvoiceid ,
  fkemployeeid ,
  fkbrandid ,
  updatetime ,
  unitsreserved ,
  shipmentpercentage ,
  boxprice ,
  refstockid ,
  srcstoreid ,
  fkconsignmentdetailid ,
  fkpurchaseid ,
  fkqueryloggerid ,
  addtime ,
  fkproduct_id ,
  fksupplierinvid,shiftdate  ) SELECT  pkstockid ,batch,quantity,unitsremaining ,expiry ,  purchaseprice ,costprice ,retailprice , priceinrs , shipmentcharges ,
  suggestedsaleprice ,fkshipmentgroupid ,fkshipmentid ,fkbarcodeid ,fkorderid ,fksupplierid , fkagentid , fkcountryid ,
  fkstoreid ,
  fksupplierinvoiceid ,
  fkemployeeid ,
  fkbrandid ,
  updatetime ,
  unitsreserved ,
  shipmentpercentage ,
  boxprice ,
  refstockid ,
  srcstoreid ,
  fkconsignmentdetailid ,
  fkpurchaseid ,
  fkqueryloggerid ,
  addtime ,
  fkproduct_id ,
  fksupplierinvid,'".time()."'  FROM    $dbname_detail.stock where fksupplierinvoiceid='$Inv_ID'";
  $reportresultg = $AdminDAO->queryresult($queryg);
  $queryh1 = "delete from $dbname_detail.stock where fksupplierinvoiceid='$Inv_ID'";
  $AdminDAO->queryresult($queryh1);	
  $queryh2 = "update $dbname_detail.supplierinvoice set invoice_status = 2 where pksupplierinvoiceid='$Inv_ID'";
  $AdminDAO->queryresult($queryh2);
  
 }
	
}
?>