<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$empid=$_SESSION['addressbookid'];
if(sizeof($_POST)>0)
{
$csid=$_POST['cust'];
$cpunter=$_POST['countr'];
$bdate=date('d-m-Y',$_POST['billdate']);
$d_vale=array();
foreach ($_POST as $key => $detail)
{

foreach ($detail as $key1 => $val) {
$d_vale[$key1][$key] = $val;
}
}
foreach($d_vale as $row)
	{
$price=$row['price'];
$pkid=$row['detailid'];

$sql_			=	"SELECT saleprice  from $dbname_detail.saledetail where pksaledetailid='{$pkid}' and saleprice='{$price}' ";
$rev_			=	$AdminDAO->queryresult($sql_);
$prc_	=	$rev_[0]['saleprice'];
if($prc_==''){
$sql_org			=	"SELECT saleprice  from $dbname_detail.saledetail where pksaledetailid='{$pkid}' ";
$rev_org			=	$AdminDAO->queryresult($sql_org);
$prc_org	=	$rev_org[0]['saleprice'];
$field_detail		=	array('saleprice','editby','edittime','orignalrate');
$value_detail		=	array($price,$empid,time(),$prc_org);
$AdminDAO->updaterow("$dbname_detail.saledetail",$field_detail,$value_detail,"pksaledetailid='$pkid'");	
}
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
$sql_s			=	"SELECT sum(saleprice*quantity) as tm  from $dbname_detail.saledetail where fksaleid='{$_POST['saleid']}'";
$rev_s			=	$AdminDAO->queryresult($sql_s);
$prc_s	=	$rev_s[0]['tm'];
if($csid>0){
$field_detail		=	array('totalamount','fkaccountid');
$value_detail		=	array($prc_s,$csid);
}else{
$field_detail		=	array('totalamount');
$value_detail		=	array($prc_s);
	}
$AdminDAO->updaterow("$dbname_detail.sale",$field_detail,$value_detail,"pksaleid='{$_POST['saleid']}'");		
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($csid > 0){
file_get_contents("http://210.2.171.14/accounts/pos_common_entry.php?type=sale_edit&amount=".$prc_s."&saleid=".$_POST['saleid']."&counter=".$cpunter."&date=".$bdate."&customerid=".$csid);
}else{
file_get_contents("http://210.2.171.14/accounts/pos_common_entry.php?type=sale_edit&amount=".$prc_s."&saleid=".$_POST['saleid']."&counter=".$cpunter."&date=".$bdate);	
	}
}

?>