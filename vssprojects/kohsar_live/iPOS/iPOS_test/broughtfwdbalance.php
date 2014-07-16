<?php
//by ahsan 
	include_once("includes/classes/AdminDAO.php");

	set_time_limit(0);

	$uname = 'root';
	$passwrd = '051Koh[^]db';
	$dbhost = 'localhost';
	$dbname = 'main_kohsar';

	$dbname_main = $dbname_detail = 'main_kohsar';
	$AdminDAO = new AdminDAO;
	
	$conn = mysql_connect($dbhost,$uname,$passwrd) or die(mysql_error());
	$db = mysql_select_db($dbname)or die(mysql_error());
	
	$query = "SELECT id, title, ctype FROM account";
	$res = mysql_query($query) or die(mysql_error());
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Brought Forward Balance</title>
</head>

<body>
    <table>
    <?php /*
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Balance</th>
        </tr>
    <?php*/
	$num = 1;
    while($row = mysql_fetch_object($res)){
		if($row->ctype == 0){
			$query = "SELECT sum(amount) amount FROM accountpayment WHERE fkaccountid = ".$row->id;
		}else{
			$query = "SELECT customerbalance amount FROM sale WHERE fkaccountid = ".$row->id . " ORDER BY pksaleid DESC LIMIT 1";
		}
			$result = mysql_query($query);
			$amount = mysql_fetch_assoc($result);
	?>
    <?php $insert_transaction = $AdminDAO->posttransaction($row->id,'0',$amount['amount'],$row->id,'0',$amount['amount'],"Brought Forward Balance");
		$num ++;
	/*?>


        <tr>
            <td><?php echo $row->id;?></td>
            <td><?php echo $row->title;?></td>
            <td><?php echo $amount['amount'];?></td>
        </tr>
    <?php*/
	}
	echo $num.' - Rows Inserted.<br />';
    ?>
    </table>
</body>
</html>