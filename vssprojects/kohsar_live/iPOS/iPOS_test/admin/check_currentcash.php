<?php
include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$dest 		= 	'check_currentcash.php';
$div		=	'maindiv';
$form 		= 	"frmquotes";	
//$tablename	=	'purchaseorder';
define(IMGPATH,'../images/');
$labels = array("ID","Counter", "Opening Balance","Net Cash ","Total");
$fields = array("pkclosingid","countername","openingbalance","totalamount","total");
$query="SELECT  pkclosingid,countername,openingbalance,
				round(
					  (SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as amount from $dbname_detail.payments cp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1' AND cp.fkclosingid	=	ci.pkclosingid and cp.paymentmethod='c'),2) as totalamount,(round(
					  (SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) as amount from $dbname_detail.payments cp,$dbname_detail.sale WHERE pksaleid=fksaleid AND status='1' AND cp.fkclosingid	=	ci.pkclosingid and cp.paymentmethod='c'),2)+openingbalance) total
			FROM 
				$dbname_detail.closinginfo  ci 
			WHERE 
				 `closingstatus` = 'i' AND (countername =1 OR countername =2 OR countername =3) group by pkclosingid";
		$cashstatus	=	$AdminDAO->queryresult($query);
?>
<head>
<title>Current Cash at Store</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<table width="100%" border="1" cellspacing="0" cellpadding="0"  class="simple">
 <!-- <tr>
      <th>Counter</th>
    <th>Opening Balance</th>
    <th>Net Cash</th>
    <th>Total</th>
  </tr>-->
   

   <?php /*?> for($i=0;$i<sizeof($cashstatus);$i++)

    { ?>
  <tr>
    <td><?php echo $cashstatus[$i]['countername']?></td>
    <td><?php echo $cashstatus[$i]['openingbalance']?></td>
    <td><?php echo $cashstatus[$i]['totalamount']?></td>
    <td><?php echo $cashstatus[$i]['total']?></td>
  </tr>
  <?php }<?php */?><?php
  
  echo file_get_contents("http://192.168.10.32/admin/checkcash.php");
   echo file_get_contents("http://192.168.10.132/admin/checkcash.php");
    echo file_get_contents("http://192.168.10.130/admin/checkcash.php");
  ?>
</table>

</div>
<br />
<br />
