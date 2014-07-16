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

include("../../includes/security/adminsecurity.php");
global $AdminDAO;
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$location = '0';
$start_datee = strtotime($start_date . '00:00:00');
$end_datee = strtotime($end_date . "23:59:59");


$query_supplier = "SELECT  pksupplierinvoiceid VoucherNumber, fksupplierid supplier, from_unixtime(datetime, '%d-%m-%Y') date, invamount Amount  from  $dbname_detail.supplierinvoice  where   invoice_status=1 and datetime between $start_datee and $end_datee  ";
$reportresult_supplier = $AdminDAO->queryresult($query_supplier);	


$acc_array=file_get_contents("http://210.2.171.14/accounts/PJV_no_list.php?startDate=$start_date&endDate=$end_date&location=$location");
$acc_array=json_decode($acc_array,true);
$index = array();
foreach ($acc_array as $item) {
    $index[] = $item['VoucherNumber'];
}
if (count(array_unique($index)) < count($index)){ //Checking Duplicates
	echo "<pre>";
	print_r(array_unique(array_diff_assoc($index, array_unique($index))));
	}
 
echo "<br>";
echo "Total Account array Results=".count($acc_array);
echo "<br>";
echo "Total POS array Results=".count($reportresult_supplier);

function filter_by_value ($array, $index, $value){
if(is_array($array) && count($array)>0)
{
foreach(array_keys($array) as $key){
$temp[$key] = $array[$key][$index];

if ($temp[$key] == $value){
$newarray[$key] = $array[$key];
}
}
}
return $newarray;
}

//////////////////////////////////////////////////////////////////////////////////////////
foreach ($reportresult_supplier as $ival) {
     $ival['supplier']=str_pad($ival['supplier'],6,'0',0);
$sresult=filter_by_value($acc_array,'VoucherNumber',$ival['VoucherNumber']);
$count=count($sresult);
if($count > 1){
	$arrdup['duplicate']=$ival['VoucherNumber'];
	}else if($count==0){
	$arrnotexsists['notExsists']=$ival;	
		}else{
	if("{$ival['VoucherNumber']}"=="{$sresult['VoucherNumber']}" and "{$ival['supplier']}"=="{$sresult['supplier']}" and "{$ival['date']}"=="{$sresult['date']}" and "{$ival['Amount']}"=="{$sresult['Amount']}" ){
		
		}else{
			
			$mismatch['mismatch']=array('pos'=>$ival,'accounts'=>$sresult);
			}		
		}
}
//////////////////////////////////////////////////////////////////////////////////////////
/*echo "<pre>";
print_r($arrdup);
echo "<pre>";
print_r($arrnotexsists);
echo "<pre>";
print_r($mismatch);*/


exit;
$result=array_diff($reportresult_supplier,$acc_array);
echo "<pre>";
print_r($result);

$result1=array_diff($acc_array,$reportresult_supplier);
echo "<pre>";
print_r($result1);
?>