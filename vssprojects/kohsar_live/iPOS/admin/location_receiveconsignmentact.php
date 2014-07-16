<?php ob_start();
error_reporting(-1);  
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

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
set_time_limit(0);
$addressbookid	=	$_SESSION['addressbookid'];
$dbname_detail='main_kohsar';
$dbname_main='main';
if(sizeof($_GET)>0)
{
	
	$id=$_GET['cid'];
	$sid=$_GET['stid'];
	$chkrep=$_GET['check_rep'];
	$finalchk=$_GET['confirmitem'];
	$teatime=time();	
	if($id>0){
	if($chkrep==1){
	$src_barcode	=	$AdminDAO->getrows("$dbname_main.consignmentdetail","COUNT(fkbarcodeid) as num","fkconsignmentid = '$id'");
    echo $src_barcode_count = $src_barcode[0]['num'];
	exit;
	}
	if($finalchk==1){
/***********************************************************************************************************/
$Returnstockquery="update $dbname_detail.stock stk left join $dbname_main.consignmentdetail cd on (stk.fkbarcodeid = cd.fkbarcodeid ) set  stk.unitsremaining=stk.unitsremaining+cd.damaged_qty , stk.updatetime=$teatime where stk.fksupplierid = cd.fksupplierid and cd.fkconsignmentid='{$id}' and cd.fkdamagetypeid=6 ";
 $AdminDAO->queryresult($Returnstockquery);
$Extrastockquery="update $dbname_detail.stock stk left join $dbname_main.consignmentdetail cd on (stk.fkbarcodeid = cd.fkbarcodeid ) set  stk.unitsremaining=stk.unitsremaining-cd.damaged_qty , stk.updatetime=$teatime where stk.fksupplierid = cd.fksupplierid and cd.fkconsignmentid='{$id}' and cd.fkdamagetypeid=9 ";
$AdminDAO->queryresult($Extrastockquery);
   exit;
	}
			/***********************************************************************************************************/
		 $stockquery	=	"INSERT INTO  $dbname_detail.stock (batch,quantity,unitsremaining,expiry,purchaseprice,costprice,retailprice,priceinrs,shipmentcharges,fkshipmentid,fkbarcodeid,fksupplierid,fkagentid,fkcountryid,fkstoreid,fkemployeeid,fkbrandid,updatetime,unitsreserved, shipmentpercentage,boxprice,fkconsignmentdetailid, addtime,srcstoreid,damaged_qty,damagetypeid ) (select batch,quantity,receivedquantity,expiry,purchaseprice,costprice,retailprice,priceinrs,shipmentcharges, fkshipmentid,fkbarcodeid,fksupplierid,fkagentid,fkcountryid,3,'$addressbookid',fkbrandid,'$teatime',0, shipmentpercentage,boxprice,pkconsignmentdetailid,'$teatime','$sid',damaged_qty,fkdamagetypeid from $dbname_main.consignmentdetail where fkconsignmentid='{$id}' )";
		  $AdminDAO->queryresult($stockquery);

		 	$damagesquery	=	"INSERT INTO $dbname_detail.damages (fkstockid,quantity,fkstoreid,fkemployeeid,damagedate,damagestatus,fkdamagetypeid) (select pkstockid,st.damaged_qty,3,'$addressbookid','$teatime','p',damagetypeid  from $dbname_detail.stock st left join $dbname_main.consignmentdetail sd on (pkconsignmentdetailid=fkconsignmentdetailid) where fkconsignmentid='{$id}' and  st.damaged_qty > 0 )";					
			$AdminDAO->queryresult($damagesquery);	
		
    $resultprc=$AdminDAO->queryresult("select fkbarcodeid,newretailprice from $dbname_main.consignmentdetail where fkconsignmentid='{$id}' and update_price=1");	
    for($j=0;$j<count($resultprc);$j++){
	$bid=$resultprc[$j]['fkbarcodeid'];
	$nrp=$resultprc[$j]['newretailprice'];
         $pricechangequery	=	"DELETE FROM $dbname_detail.pricechange WHERE fkbarcodeid = '$bid'";
		 $AdminDAO->queryresult($pricechangequery);
		  $pricechangequery2	=	"INSERT INTO $dbname_detail.pricechange  (fkbarcodeid,price,inserttime) values ('$bid','$nrp','$teatime') ";
		  $AdminDAO->queryresult($pricechangequery2);
		 $pricechangehistory	=	"INSERT INTO $dbname_detail.pricechangehistory  ( fkpricechangeid , fkaddressbookid , updatetime ) values ( '(SELECT MAX(pkpricechangeid) FROM $dbname_detail.pricechange)' , '$bid' , '$teatime' ) ";	
	    $AdminDAO->queryresult($pricechangehistory);
	
	}	
	
	echo 1;
	}
}else{

echo -1;

	}


?>