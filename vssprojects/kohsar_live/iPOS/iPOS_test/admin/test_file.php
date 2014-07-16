<?php

//phpinfo();
$customerbalance_=file_get_contents('http://192.168.10.100/accounts/customer_balance.php?customer=505');
	echo $customerbalance_; 

?>