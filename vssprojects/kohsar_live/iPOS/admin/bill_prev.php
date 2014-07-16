<?php

include_once("../includes/security/adminsecurity.php");

include_once("../includes/bc/barcode.php");

global $AdminDAO;




$saleid			=	$_REQUEST['ids'];

//print_r($saleid);exit;

if($saleid	== "")

{

	//for duplicate prints

	$saleid	=	$_GET['saleid'];

}

genBarCode($saleid,'bc.png');


?>

<script src="../includes/js/shortcut.js"></script>

<script language="javascript">

shortcut.add("End",function() 

{

	//fnchotelmode();

	//window.close();

	return false;

});

</script>

<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />



<div style="width:3.0in; margin-left:0px; margin-right:auto;font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif;" align="left">

<div style="width:2.6in;padding:0px;font-size:17px;" align="center">

<img src="../images/esajeelogo.jpg" width="197" height="58">

<br />

<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">

<b>Think globally shop locally</b>

</span>

</div>

<div style="width:2.6in;padding:2px;margin-top:5px;" align="center"><?php echo $fulladdress; ?><br />

</div>

<div style="width:1.3in; padding:2px; float:left; margin-top:5px;" align="center">

Counter: 3

</div>

<div style="width:1.3in; padding:2px; float:right; margin-top:5px;" align="center">

Cashier: 18

</div>

<div style="width:1.3in; padding:2px; float:left; margin-top:5px;" align="center">

Transaction:123456

</div>

<div style="width:1.3in; padding:2px; float:right; margin-top:5px;" align="center">

Items:

12(01)

</div>



<table class="simple" width="280" align="left" style="margin-left:5px;" >

<tr>

<th>Item</th>

<th>Qty</th>

<th>Price</th>

<th>Amt</th>

</tr>

<?php


$query="select itemdescription from $dbname_main.barcode where pkbarcodeid='$saleid'";

$result_bill		=	$AdminDAO->queryresult($query);	

?>

    <tr>

    <td><?php echo $result_bill[0]['itemdescription']; ?></td>

    <td align="right">1</td>

    <td align="right">2000</td>

    <td align="right">40000</td>

    </tr>






<tr align="right">

	<td colspan="3">Sub Total</td>

	<td>40000 </td>

</tr>



</table>



<div align="center" style="clear:both; float:left;margin-bottom:5px;">

<?php echo $billfooter; ?><br /><br />

<?php echo $billtime; ?>

<?php if($billcount>1){$printime	=	date('d-m-y h:i:s', time()); echo "<br /><br />Printing Time: ".$printime;}?>

</div>

<div align="center" style="clear:both;">

<img src="bc.png" />

</div>

</div>

<script language="javascript">

	//window.print();

	//window.close();

</script>