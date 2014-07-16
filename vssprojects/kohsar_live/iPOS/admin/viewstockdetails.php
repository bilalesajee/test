<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/
$id	=	$_REQUEST['id'];
if($id)
{
	$ids	=	$AdminDAO->getrows("barcodebrand","fkbarcodeid, fkbrandid", " pkbarcodebrandid = '$id'");
	$barcodeid	=	$ids[0]['fkbarcodeid'];
	$brandid	=	$ids[0]['fkbrandid'];
	$row	=	$AdminDAO->getrows("stock s, store st","st.pkstoreid, st.storename, s.updatetime, SUM(s.quantity) as quantity, SUM(s.unitsremaining) as unitsremaining, MIN(s.expiry) as expiry", " fkbrandid = '$brandid' AND fkbarcodeid = '$barcodeid' AND s.fkstoreid = st.pkstoreid GROUP BY pkstoreid");
/*echo "<pre>";
print_r($row);
echo "</pre>";*/
	if(!$row)
	{
		echo "<div class=notice>No record found.</div>";
		exit;
	}
?>
<table border="0" cellpadding="0" cellspacing="2" width="100%">
    <tr>
    	<th>&nbsp;</th>
        <th>Location</th>
        <th>Last Update</th>
        <th>Units Sent</th>
        <th>Remaining Units</th>        
        <th>Nearest Expiry</th>                
    </tr>
    <?php
	foreach($row as $data)
	{
		$location	=	$data['storename'];
		$time		=	$data['updatetime'];
		$time		=	date("d M Y", strtotime($time));		
		$quantity	=	$data['quantity'];		
		$units		=	$data['unitsremaining'];
		$expiry		=	$data['expiry'];
		$expiry		=	date("d M Y", strtotime($expiry));
		$storeid	=	$data['pkstoreid'];		
	?>
     <tr id="tr<?php echo $storeid; ?>viewinstances" onmousedown="highlight('<?php echo $storeid; ?>','even','row','viewinstances')" class="even">
    	<td><input onclick="highlight('<?php echo $storeid; ?>','even','chk','viewinstances')" name="checks" id="cb<?php echo $storeid; ?>viewinstances" value="<?php echo $storeid; ?>" type="checkbox"></td>
        <td><?php echo $location; ?></td>
        <td><?php echo $time;?></td>
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
<table width="650">
<tr>
<td>
<input type="button" name="viewstoredetails" value="View Details" onClick="viewstoredetails(<?php echo $barcodeid; ?>,<?php echo $brandid; ?>)">
</td>
</tr>
</table>