<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/
//$id	=	$_REQUEST['id'];
//$barcode	=	$_REQUEST['barcode'];
//$brandid	=	$_REQUEST['brandid'];
if($id)
{
	$store	=	$AdminDAO->getrows("store","storename"," pkstoreid = '$id'");
	$res	=	$AdminDAO->getrows("stock","quantity,unitsremaining,expiry"," fkstoreid = '$id' AND fkbarcodeid = '$barcode' AND fkbrandid = '$brandid' ORDER BY expiry asc");
	if(!$res)
	{
		echo "<div class=notice>No record found.</div>";
		exit;
	}
	?>
	<table border="0" cellpadding="0" cellspacing="2" width="100%">
   
    <tr>
    	<td colspan="3"><div class="notice">Unit Details for <b> <?php echo $store[0]['storename'];?></div></b></td>
    </tr>
    
    <tr>
        <th>Units Sent</th>
        <th>Remaining Units</th>
        <th>Expiry</th>
    </tr>
    <?php
	foreach($res as $data)
	{
		$quantity	=	$data['quantity'];		
		$units		=	$data['unitsremaining'];
		$expiry		=	$data['expiry'];
		$expiry		=	date("d M Y", strtotime($expiry));		
	?>
    <tr>
        <td><?php echo $quantity;?></td>
        <td><?php echo $units;?></td>
        <td><?php echo $expiry;?></td>     
    </tr>
    <?php
	}
	?>
	</table>
   	<?php
}
else
{
	echo "No Result found";
}
?>