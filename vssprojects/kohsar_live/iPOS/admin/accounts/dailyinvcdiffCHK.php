<?php
ob_start();
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

include("../../includes/security/adminsecurity.php");
global $AdminDAO;
$start_date = $_REQUEST['start_date'];
if($start_date==''){
$date=time();
 $start_date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
	}


$query_supplier = "SELECT  pksupplierinvoiceid from  $dbname_detail.supplierinvoice  where   invoice_status=1 and FROM_UNIXTIME(datetime,'%d-%m-%Y')='$start_date'";
$reportresult_supplier = $AdminDAO->queryresult($query_supplier);	
foreach($reportresult_supplier as $getdata){
	$arr[]=$getdata['pksupplierinvoiceid'];
}

$acc_array=file_get_contents("http://210.2.171.14/accounts/PJV_no_list.php?startDate=$start_date&endDate=$start_date&location=0");
$acc_array=json_decode($acc_array,true);
if (count(array_unique($acc_array)) < count($acc_array)){
	
	
	echo "<pre>";
	$acc_duplicates=array_unique(array_diff_assoc($acc_array, array_unique($acc_array)));
	}
    echo "<pre>";
print_r($acc_array);

echo "<br>";
$accrows=count($acc_array);
echo "Total Account array Results=".$accrows;
echo "<br>";
echo "<pre>";
print_r($arr);

$posrows=count($arr);
echo "<br>";
echo "Total POS array Results=".count($arr);
$result=array_diff($arr,$acc_array);
$pos2accDiff=count($result);
echo "<pre>";
print_r($result);

$result1=array_diff($acc_array,$arr);
$acc2posDiff=count($result1);
echo "<pre>";
print_r($result1);

if($posrows!=$accrows){
	
	echo "Difference Found In Main Array";
	if($pos2accDiff!=$acc2posDiff){
	echo "Difference Found In Compare Array";
	if($pos2accDiff>0){
		echo "These invoices are not Present in Accounts =".implode(',',$result);	
		}	
    	if($pos2accDiff>0){
		echo "These invoices are Voided or not present in Kohsar =".implode(',',$result1);	
		}	


		
	}else{
		echo "<br>";
		echo "Duplicate Invoices in Accounts =".implode(',',$acc_duplicates);
		}
	
	}


?>