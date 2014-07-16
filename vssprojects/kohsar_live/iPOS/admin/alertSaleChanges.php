<?php
set_time_limit(0);
$ips = array(1 => 32 , 2 => 132, 3 => 130);

include '../Mail/email.php';
include("config_autoget.php");

foreach($ips as $counter => $ippart)
{
	$server = "192.168.10.{$ippart}";
	
	//$dbh = new mysqli($server, 'esajeeagent', 'esajeeagent', 'main_kohsar');
        $dbh = new mysqli($server, $server_user, $server_pwd , $server_db);
	
	//$dbh = new mysqli('localhost', 'root', '', 'kohsar');
	
	$error = error_get_last();
	
	if( strpos($error['message'], '(HY000/2003)') !== false and strpos($error['message'], $server) !== false)
	{
		$To[] = 'saqibzahir39@gmail.com';
		$To[] = 'fahadbuttqau@gmail.com';
                $To[] = 'batchjobs@gmail.com';           
		$Subject = "POS {$counter} not connecting";
		$emailBody = $error['message'];
		
		email($To, $Subject, $emailBody);
		continue;
	
	}
	
	$q = " select d.firstname, d.lastname, a.*, a.barcode, c.itemdescription from saleitemtemp a left join main.barcode c on c.pkbarcodeid = a.barcodeid left join main.addressbook d on d.pkaddressbookid = a.fkaddressbookid";
	//$q .= " where ( a.quantity < a.originalquantity or a.price < a.originalprice ) ";
	$q .= " where ( a.quantity = 0 ) ";
	$q .= " and  added_at between TIMESTAMPADD(MINUTE,-15,CURRENT_TIMESTAMP) and CURRENT_TIMESTAMP  ";
	$q .= " order by a.added_at desc ";
	
	$result = $dbh->query($q);
	
	
	$row_cnt = $result->num_rows;
	
	if($row_cnt > 0)
	{
		ob_start();
	?>
<title>Changed Sale Report</title>
	
	<table width="100%" border="1" cellspacing="0" cellpadding="2" style="border-collapse:collapse">
	  <tr>
		<td colspan="7" valign="top">Counter: <?php $counter; ?> </td>
	  </tr>
	  <tr>
		<td valign="top">Barcode</td>
		<td valign="top">Item Name</td>
		<td valign="top">Changed Quantity</td>
		<td valign="top"> Quantity</td>
		<td valign="top">Changed Price</td>
		<td valign="top">Price</td>
		<td valign="top">Time</td>
	  </tr>
	<?php
	
	
	
	if ($result)
	{
		while ($row = $result->fetch_assoc())
		{?>
      
      <?php if($row['fkaddressbookid'] != $oldEmp){ ?>
        
	  <tr>
	    <td colspan="7" valign="top">Employee: <?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
      </tr>
      
	  <?php } ?>
      <?php if($row['ip'] != $oldip){ ?>
        
	  <tr>
	    <td colspan="7" valign="top">IP: <?php echo $row['ip']; ?></td>
      </tr>
      
	  <?php } ?>
      <?php if($row['saleid'] != $oldsaleid){ ?>
        
	  <tr>
	    <td colspan="7" valign="top">Bill # <?php echo $row['saleid']; ?></td>
      </tr>
      
	  <?php } ?>
      <tr>
		<td valign="top"><?php echo $row['barcode']; ?></td>
		<td valign="top"><?php echo $row['itemdescription']; ?></td>
		<td valign="top"><?php echo $row['quantity']; ?></td>
		<td valign="top"><?php echo $row['originalquantity']; ?></td>
		<td valign="top"><?php echo $row['price']; ?></td>
		<td valign="top"><?php echo $row['originalprice']; ?></td>
		<td valign="top"><?php echo $row['added_at']; ?></td>
	  </tr>
			
			
	<?php $oldEmp = $row['fkaddressbookid'];$oldip = $row['ip'];$oldsaleid = $row['saleid'];}
		
		$result->close();
	}
	
	$dbh->close();
	?>
	</table>
	<?php 
	
		$emailBody = ob_get_clean();
		//$To[] = 'siddique.ahmad@gmail.com';
		$To[] = 'hesajee@gmail.com';
		$To[] = 'hunaidhalai@gmail.com';
		$To[] = 'm_esajee@hotmail.com';
		$Subject = 'Bill changes at counter ' . $counter;
		email($To, $Subject, $emailBody);
	
	}
}
?>