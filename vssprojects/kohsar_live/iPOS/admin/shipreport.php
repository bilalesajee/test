<link rel="stylesheet" type="text/css" href="../../store/includes/css/style.css">
<table class="simple">
<tr>
	<th>Barcode</th>
    <th>Item</th>
   <th>Units Sent</th>
    <th>Units Recieved</th>
    <th>Unit Diffirence </th>
    <th>Diffirence Reason </th>
    <th>Units Remaining</th>
    <th>Expiry</th>
    <th>Trade Price</th>
    <th>Sale Price</th>
    <th>Added by</th>
</tr>
<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_REQUEST['ids'];
$id	=	trim($id,',');
$idarr	=	explode(',',$id);
//print_r($idarr);
//echo"------".sizeof($idarr);
$newid	=	$idarr[(sizeof($idarr)-1)];
$shupmentinfo	=	$AdminDAO->getrows("shipment","pkshipmentid,shipmentname,shipmentdate ","pkshipmentid='$newid'");
$shipmentname	=	$shupmentinfo[0]['shipmentname'];
$shipmentdate	=	$shupmentinfo[0]['shipmentdate'];
$pkshipmentid	=	$shupmentinfo[0]['pkshipmentid'];

echo"<b>Shipment Name: $shipmentname &nbsp;&nbsp;&nbsp;Date: ".date('d-m-Y',$shipmentdate)."</b><br>
<div id=supplierdiv></div>
";
// iterate through the stores
	
	?>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
    <tr>
    	<th colspan="11" bgcolor="#FF3300">
        	<?php echo $storename;?>        </th>
    </tr>
    <?php
	
	$sql	=	"
							SELECT
								barcode,
								itemdescription,
								quantity,
								unitsremaining,
								IF(expiry='0','--------',FROM_UNIXTIME(expiry,'%d-%m-%y')) as expiry,
								purchaseprice,
								priceinrs,
								fksupplierid,
								concat(firstname,' ',lastname) name 
							FROM 
								$storedb.stock,main.barcode,main.addressbook 
							WHERE 
								fkshipmentid='$newid' AND 
								fkemployeeid=pkaddressbookid AND 
								fkbarcodeid=pkbarcodeid
							";
	$result	=	$AdminDAO->queryresult($res);
	for($i=0;$i<count($result);$i++)
	{
		//$supplierids[]=$result['fksupplierid'];
		?>
        <tr>
        	<td><?php echo $result[$i]['barcode'];?></td>
        	<td><?php echo $result[$i]['itemdescription'];?></td>
         	<td>______</td>
            <td><?php echo $result[$i]['quantity'];?></td>
        	<td>______</td>
        	<td>---------</td>
        	<td><?php echo $result[$i]['unitsremaining'];?></td>
        	<td><?php echo $result[$i]['expiry'];?></td>
        	<td><?php echo $result[$i]['purchaseprice'];?></td>
        	<td><?php echo $result[$i]['priceinrs'];?></td>
        	<td><?php echo $result[$i]['name'];?></td>
        </tr>
        <?php
		
	}
			}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
// iterate through the stores
$storeinfo	=	$AdminDAO->getrows("store","storename,storedb,storeip,username,password","storestatus=1");
for($i=0;$i<sizeof($storeinfo);$i++)
{
	//connect
	$storedb	=	$storeinfo[$i]['storedb'];
	$storeip	=	$storeinfo[$i]['storeip'];
	$storename	=	$storeinfo[$i]['storename'];
	$username	=	$storeinfo[$i]['username'];
	$password	=	$storeinfo[$i]['password'];
	
	?>
    <tr>
    	<th colspan="11" bgcolor="#FF3300">
        	<?php echo $storename;?>        </th>
    </tr>
    <?php
	mysql_connect($storeip,$username,$password) or die("could not connect to db".mysql_error());
	//select db
	mysql_select_db($storedb) or die("could not select db".mysql_error());
	//select rec
	
	$res	=	mysql_query("
							SELECT
								barcode,
								itemdescription,
								quantity,
								unitsremaining,
								IF(expiry='0','--------',FROM_UNIXTIME(expiry,'%d-%m-%y')) as expiry,
								purchaseprice,
								priceinrs,
								fksupplierid,
								concat(firstname,' ',lastname) name 
							FROM 
								$storedb.stock,main.barcode,main.addressbook 
							WHERE 
								fkshipmentid='$newid' AND 
								fkemployeeid=pkaddressbookid AND 
								fkbarcodeid=pkbarcodeid
							") or die("could not execute query".mysql_error());
	while($result	=	mysql_fetch_assoc($res))
	{
		$supplierids[]=$result['fksupplierid'];
		?>
        <tr>
        	<td><?php echo $result['barcode'];?></td>
        	<td><?php echo $result['itemdescription'];?></td>
         	<td>______</td>
            <td><?php echo $result['quantity'];?></td>
        	<td>______</td>
        	<td>---------</td>
        	<td><?php echo $result['unitsremaining'];?></td>
        	<td><?php echo $result['expiry'];?></td>
        	<td><?php echo $result['purchaseprice'];?></td>
        	<td><?php echo $result['priceinrs'];?></td>
        	<td><?php echo $result['name'];?></td>
        </tr>
        <?php
	}
}
}//end edit

$supplierids	=	array_unique($supplierids);
//print_r($supplierids);
foreach($supplierids as $suppid)
{
	$supids.=$suppid.",";
}
$supids	=	trim($supids,',');
$supplierinfo	=	$AdminDAO->getrows("supplier","companyname","pksupplierid 	IN($supids)");
for($i=0;$i<count($supplierinfo);$i++)
{
	$companyname.=$supplierinfo[$i]['companyname'].', ';
}
$companyname	=	trim($companyname,', ');

//$supplierids	=	 list($supplierids);
?>
</table>
<script language="javascript">
	document.getElementById('supplierdiv').innerHTML="<br><b>Supplier/Agent: <?php echo $companyname;?></b>";
	window.print();
</script>