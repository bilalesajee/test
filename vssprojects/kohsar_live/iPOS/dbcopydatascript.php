<?php
//by ahsan 
	set_time_limit(0);
	$uname = 'root';
	$passwrd = '051Dev`*_db';
	$dbhost = 'localhost';
	$dbname = 'ipos';

	$conn = mysql_connect($dbhost,$uname,$passwrd) or die(mysql_error());
	$db = mysql_select_db($dbname)or die(mysql_error());

	$msg = mysql_query("ALTER TABLE accountpayment ADD `updates` BOOLEAN NOT NULL DEFAULT '0'") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE collection ADD `updates` BOOLEAN NOT NULL DEFAULT '0'") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE creditinvoices ADD `updates` BOOLEAN NOT NULL DEFAULT '0'") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE itemdemands ADD `updates` BOOLEAN NOT NULL DEFAULT '0'") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE purchaseorder ADD `updates` BOOLEAN NOT NULL DEFAULT '0'") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE sale ADD `updates` BOOLEAN NOT NULL DEFAULT '0'") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE saledetail ADD `updates` BOOLEAN NOT NULL DEFAULT '0'") or die(mysql_error());

	$msg = selection("cashpayment","payments", "c");
	$msg = selection("ccpayment","payments", "cc");
	$msg = selection("chequepayment","payments", "ch");
	$msg = selection("fcpayment","payments", "fc");
	$msg = selection("accounthead","account");
	$msg = selection("customer","account");
	
	$msg = mysql_query("ALTER TABLE accountpayment DROP `updates`") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE collection DROP `updates`") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE creditinvoices DROP `updates`") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE itemdemands DROP `updates`") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE purchaseorder DROP `updates`") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE sale DROP `updates`") or die(mysql_error());
	$msg = mysql_query("ALTER TABLE saledetail DROP `updates`") or die(mysql_error());

	function selection($table, $inserttable, $paymethod=''){
		$query = "SELECT * FROM $table";
		$result = mysql_query($query)or die(mysql_error());
		while($row = mysql_fetch_assoc($result)){
			$msg = insertion($row, $inserttable, $paymethod);
		}
		return $msg;
	}

	function insertion($data, $table, $paymentmethod=''){
		$values = "'";

		$old_id = array_shift($data);
		
		if($paymentmethod == 'fc'){
			$data['fcamount'] = $data['amount'];
			$data['fctendered'] = $data['tendered'];
			unset($data['amount']);
			unset($data['tendered']);
		}
		
		if($table=="account"){
			if(isset($data['accounttitle'])){
				$data['title'] = $data['accounttitle'];
				unset($data['accounttitle']);
				$accounthead = 1;
			}	
			if(isset($data['companyname'])){
				$data['title'] = $data['companyname'];
				unset($data['companyname']);
				$customer = 1;
			}	
		}
		
		$fields = implode(', ',array_keys($data));
		$values .= implode("', '",array_values($data));
		
		if($paymentmethod!=''){
			$fields .= ', paymentmethod';
			$values .= "', '{$paymentmethod}'";
		}else{
			$values .= "'";
		}

		$query = "INSERT INTO $table($fields) VALUES ($values)";
		$msg = mysql_query($query) or die(mysql_error());
		
		if($table=="account"){
			$new_id = mysql_insert_id();
			if(isset($accounthead)){
				$msg = updation($old_id, $new_id, 'accountpayment');
			}
			if(isset($customer)){
				$msg = updation($old_id, $new_id, 'collection');
				$msg = updation($old_id, $new_id, 'creditinvoices');
				$msg = updation($old_id, $new_id, 'itemdemands');
				$msg = updation($old_id, $new_id, 'purchaseorder');
				$msg = updation($old_id, $new_id, 'sale');
				$msg = updation($old_id, $new_id, 'saledetail');
			}
		}
		return $msg;
	}
	
	function updation($old_id,$new_id, $table){
		$query = "SELECT `updates`, fkaccountid FROM $table WHERE fkaccountid={$old_id} ORDER BY ";
		$result = mysql_query($query) or die(mysql_error());
		while($row=mysql_fetch_assoc($result)){
			$query = "UPDATE $table SET fkaccountid={$new_id} AND `updates`=1 WHERE fkaccountid={$old_id} AND `updates` != 1";
			$msg = mysql_query($query) or die(mysql_error());
		}
		return $msg;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php if($msg == 1){
	echo "Data has been copied.";
} 
mysql_close();
?>

</body>
</html>
<?php
//	require_once('includes/classes/AdminDAO.php');
//	$table = 'cashpayment';
	
/*	$kohsar = new DBManager($dbname);
	
	$result = $kohsar->executeQuery("SELECT * FROM fcpayment", $dbname);

	$kohsar = new AdminDAO();
	$result = $kohsar->getrows('fcpayment','*');
	print_r($result);

*/

/*	$query = "SELECT * FROM cashpayment";
	$result = mysql_query($query)or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$db = mysql_select_db($dbname1)or die(mysql_error());
		$query = "INSERT INTO payments(fksaleid, amount, tendered, returned, paytime, paymenttype, paymentmethod, fkclosingid) VALUES ({$row['fksaleid']}, {$row['amount']}, {$row['tendered']}, {$row['returned']}, {$row['paytime']}, '{$row['paymenttype']}', 'c', {$row['fkclosingid']})";
		$msg = mysql_query($query) or die(mysql_error());
}

/*	$db = mysql_select_db($dbname)or die(mysql_error());

	$query = "SELECT * FROM ccpayment";
	$result = mysql_query($query)or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$db = mysql_select_db($dbname1)or die(mysql_error());
		$query = "INSERT INTO payments(fksaleid, amount, tendered, returned, paytime, paymenttype, paymentmethod, fkclosingid, ccno, nameoncc, fkcctypeid, fkbankid, charges) VALUES ({$row['fksaleid']}, {$row['amount']}, {$row['tendered']}, {$row['returned']}, {$row['paytime']}, '{$row['paymenttype']}', 'cc', {$row['fkclosingid']}, {$row['ccno']}, '{$row['nameoncc']}', {$row['fkcctypeid']}, {$row['fkbankid']}, {$row['charges']})";
		$msg = mysql_query($query) or die(mysql_error());
	}

	$db = mysql_select_db($dbname)or die(mysql_error());

	$query = "SELECT * FROM chequepayment";
	$result = mysql_query($query)or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$db = mysql_select_db($dbname1)or die(mysql_error());
		$query = "INSERT INTO payments(fksaleid, amount, tendered, returned, paytime, paymenttype, paymentmethod, fkclosingid, chequeno, chequedate, fkbankid) VALUES ({$row['fksaleid']}, {$row['amount']}, {$row['tendered']}, {$row['returned']}, {$row['paytime']}, '{$row['paymenttype']}', 'ch', {$row['fkclosingid']}, {$row['chequeno']}, {$row['chequedate']}, {$row['fkbankid']})";
		$msg = mysql_query($query) or die(mysql_error());
	}
	
	$db = mysql_select_db($dbname)or die(mysql_error());

	$query = "SELECT * FROM fcpayment";
	$result = mysql_query($query)or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$db = mysql_select_db($dbname1)or die(mysql_error());
		$query = "INSERT INTO payments(fksaleid, fcamount, fctendered, returned, paytime, paymenttype, paymentmethod, fkclosingid, fkcurrencyid, rate, charges) VALUES ({$row['fksaleid']}, {$row['amount']}, {$row['tendered']}, {$row['returned']}, {$row['paytime']}, '{$row['paymenttype']}', 'fc', {$row['fkclosingid']}, {$row['fkcurrencyid']}, {$row['rate']}, {$row['charges']})";
		$msg = mysql_query($query) or die(mysql_error());
	}

	$db = mysql_select_db($dbname)or die(mysql_error());

	$query = "SELECT * FROM accounthead";
	$result = mysql_query($query)or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$db = mysql_select_db($dbname1)or die(mysql_error());
		$query = "INSERT INTO account(id, title, creationdate, status, accountlimit, fkaddressbookid) VALUES ({$row['pkaccountheadid']}, '{$row['accounttitle']}', {$row['creationdate']}, {$row['status']}, {$row['accountlimit']}, {$row['fkaddressbookid']})";
		$msg = mysql_query($query) or die(mysql_error());
	}

	$db = mysql_select_db($dbname)or die(mysql_error());

	$query = "SELECT * FROM customer";
	$result = mysql_query($query)or die(mysql_error());
	while($row = mysql_fetch_assoc($result)){
		$db = mysql_select_db($dbname1)or die(mysql_error());
		$query = "INSERT INTO account(id, title, status, taxnumber, taxable, ntn, ctype, fkaddressbookid, isdeleted) VALUES ({$row['pkcustomerid']}, '{$row['companyname']}', {$row['status']}, '{$row['taxnumber']}', {$row['taxable']}, '{$row['ntn']}', {$row['ctype']}, {$row['fkaddressbookid']}, {$row['isdeleted']})";
		$msg = mysql_query($query) or die(mysql_error());
	}
*/	
?>