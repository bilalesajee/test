<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
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
//////////////////////////////update log/////////////////////////////////////////////
$query_log="select * from $dbname_detail.saledetail where pksaledetailid='{$pkid}' ";
 $res = $AdminDAO->queryresult($query_log);
 	foreach ($res as $result)
{
	 $Saleprice=$result["saleprice"];
	
	 $edit_time=time();
     $edit_by		=	$_SESSION['addressbookid'];
	 $Orignalrate=$result["orignalrate"];
	 $operation='Update'; 
	 $old_id=$result["pksaledetailid"];
	 $field_detail_log		=	array('saleprice','editby','edittime','orignalrate','operation','old_id');
     $value_detail_log		=	array($Saleprice,$edit_by,$edit_time,$Orignalrate,$operation,$old_id);
	 $AdminDAO->insertrow("$dbname_detail.saledetail_log",$field_detail_log,$value_detail_log);
}

/////////////////////////////////////////////////////////////////////////////////////
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
//////////////////////////////update log/////////////////////////////////////////////
$query_log2="select * from $dbname_detail.sale where pksaleid='{$_POST['saleid']}' ";
 $res2 = $AdminDAO->queryresult($query_log2);
 	foreach ($res2 as $results)
{
	 $totalamount=$results["totalamount"];
	
	 $edit_time=time();
     $edit_by		=	$_SESSION['addressbookid'];
	 $fkaccountid=$results["fkaccountid"];
	 $operation='Update'; 
	 $updatetime=$results["updatetime"];
	 $old_id=$results["pksaleid"];
	 $field_log		=	array('totalamount','fkaccountid','updatetime','editby','edittime','operation','old_id');
     $value_log		=	array($totalamount,$fkaccountid,$updatetime,$edit_by,$edit_time,$operation,$old_id);
	 $AdminDAO->insertrow("$dbname_detail.sale_log",$field_log,$value_log);
}

/////////////////////////////////////////////////////////////////////////////////////
$AdminDAO->updaterow("$dbname_detail.sale",$field_detail,$value_detail,"pksaleid='{$_POST['saleid']}'");		
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($csid > 0){

//file_get_contents($Url_admin."sale_edit&amount=".$prc_s."&saleid=".$_POST['saleid']."&counter=".$cpunter."&date=".$bdate."&customerid=".$csid);

}else{

//file_get_contents($Url_admin."sale_edit&amount=".$prc_s."&saleid=".$_POST['saleid']."&counter=".$cpunter."&date=".$bdate);	

	}
}

?>