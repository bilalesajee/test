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


$query = "SELECT pksaleid,s.fkaccountid,sum(s.totalamount) sale,0 as cash , 0 as cc,0 as fc , 0 as cheque, FROM_UNIXTIME(s.datetime,'%d-%m-%Y') datetime , sum(s.globaldiscount) as discount from $dbname_detail.sale s 
where FROM_UNIXTIME(s.datetime,'%d-%m-%Y') = '$get_option' and s.status = 1 and fkaccountid = 0 group by fkaccountid ";
$reportresultNC = $AdminDAO->queryresult($query);

/*
$query_coll = " select sum(c.amount) amount,  case paymentmethod when 'c' then 'cash' when 'ch' then 'cheque' else paymentmethod end paymentmethod from  $dbname_detail.payments c inner join $dbname_detail.sale d on pksaleid = fksaleid where d.status = 1 and paymenttype = 'c' and FROM_UNIXTIME(c.paytime,'%d-%m-%Y') = '$get_option' and d.fkaccountid= 0 group by paymentmethod ";
				$collectionrows = $AdminDAO->queryresult($query_coll);
				if( count($collectionrows) > 0)
				{
					foreach($collectionrows as $colc)
					{
						$reportresultNC[$colc['paymentmethod']] += $colc['amount'];
					}
					
					//$bill['collection'] = $col;
				}
				$collectionrows = null;*/
$arr ['NC'] = $reportresultNC;
$query = "SELECT pksaleid,s.fkaccountid,(s.totalamount) sale, 0 cash , 0 as cc, 0 as fc , 0 as cheque, FROM_UNIXTIME(s.datetime,'%d-%m-%Y') updatetime, (s.globaldiscount) as discount from $dbname_detail.sale s 
where FROM_UNIXTIME(s.datetime,'%d-%m-%Y') = '$get_option' and s.status = 1 and fkaccountid != 0 order by fkaccountid, pksaleid ";


$reportresult = $AdminDAO->queryresult($query);

$trow = count($reportresult);

if (empty($reportresult)) {
    $arr = array();
} else {
    for ($p = 0; $p < $trow; $p++) {


        $customer = $reportresult[$p]['fkaccountid'];
       

        $customer = str_pad($customer, 6, 0, STR_PAD_LEFT);
		
		$bill = $reportresult[$p];
        //$bill = array( 'customer' => $customer, 'billno' => $reportresult[$p]['pksaleid'], 'totalsale' => (float)$Sale, 'cash' => (float)$cash, 'cc' => $cc, 'fc' => $fc, 'chq' => $chq, 'totalpaid' => $amount, 'discount' => $dis, 'updatetime' => $reportresult[$p]['updatetime']);
        if ($reportresult[$p]['fkaccountid'] != 0) {

            $query2 = "SELECT b.barcode,b.itemdescription,sd.quantity,sd.saleprice as saledetprice,sd.taxamount from $dbname_detail.saledetail sd 
  LEFT JOIN $dbname_detail.stock stk ON (stk.pkstockid = sd.fkstockid)
LEFT JOIN barcode b ON (b.pkbarcodeid = stk.fkbarcodeid)
where fksaleid = {$reportresult[$p]['pksaleid']} ";
            $reportresult2 = $AdminDAO->queryresult($query2);
			
			if($old_customer != $customer)
			{
				$query_coll = " select sum(c.amount) amount,  case paymentmethod when 'c' then 'cash' when 'ch' then 'cheque' else paymentmethod end paymentmethod from  $dbname_detail.payments c inner join $dbname_detail.sale d on pksaleid = fksaleid where d.status = 1 and paymenttype = 'c' and FROM_UNIXTIME(c.paytime,'%d-%m-%Y') = '$get_option' and d.fkaccountid= {$reportresult[$p]['fkaccountid']} group by paymentmethod ";
				$collectionrows = $AdminDAO->queryresult($query_coll);
				if( count($collectionrows) > 0)
				{
					foreach($collectionrows as $colc)
					{
						$bill[$colc['paymentmethod']] += $colc['amount'];
					}
					
					//$bill['collection'] = $col;
				}
			}
			
        }

        
        $bill['billdetail'] = $reportresult2;
        $arr['customer'][$customer][] = $bill;
		$old_customer  = $customer;

        //file_get_contents('http://192.168.5.71:100/accounts/create_voucher_sale.php?amount='.$amount.'&cash='.$cash.'&cc='.$cc.'&fc='.$fc.'&cheque='.$chq);
    }
}
echo json_encode($arr);
/*echo "<pre>";
print_r($arr);*/
?>
