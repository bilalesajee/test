<?php
if($_GET['type']=='coll'){
file_get_contents("https://main.esajee.com/admin/accounts/update_bal_main.php?type=".$_GET['type']."&customerid=".$_GET['customerid'].'&amount='.$_GET['amount'].'&paymentmethod='.$_GET['paymentmethod'].'&charges='.$_GET['charges'].'&bank='.$_GET['bank'].'&chequedate='.$_GET['chequedate'].'&chequeno='.$_GET['chequeno'].'&ccno='.$_GET['ccno']);
}else{
file_get_contents("https://main.esajee.com/admin/accounts/update_bal_main.php?type=".$_GET['type']."&customerid=".$_GET['fkaccountid']."&sale=".$_GET['sale']."&return=".$_GET['return']."");
}
?>