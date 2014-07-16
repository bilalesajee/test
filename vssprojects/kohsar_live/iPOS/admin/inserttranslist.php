<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$barcode			=	$_POST['barcode'];
	$itemdescription 	=	$_POST['itemdescription'];
	$lastpurchaseprice	=	$_POST['lastpurchaseprice'];
	$quantity			=	$_POST['quantity'];
	$purchasequantity	=	$_POST['purchasequantity'];
	$expiry				=	$_POST['expiry'];
	$currency			=	$_POST['currency'];
	$purchaseprice		=	$_POST['purchaseprice'];
	$salestax			=	$_POST['salestax'];
	$surcharge			=	$_POST['surcharge'];
	$weight				=	$_POST['weight'];
	$charges			=	$_POST['charges'];
	$shiplistid			=	$_POST['shiplistid'];

	$fields = array('barcode','itemdescription','lastpurchaseprice','quantity','purchasequantity','expiry','fkcurrencyid','purchaseprice','salestax','surcharge','weight','charges');
	for($i=0;$i<sizeof($barcode);$i++)
	{
		$pkshiplist	=	$shiplistid[$i];
		$aexpiry	=	explode("-",$expiry[$i]);
		$iexpiry	=	array_reverse($aexpiry);
		$pexpiry	=	implode("-",$iexpiry);
		$values 	= 	array($barcode[$i],$itemdescription[$i],$lastpurchaseprice[$i],$quantity[$i],$purchasequantity[$i],$pexpiry,$currency[$i],$purchaseprice[$i],$salestax[$i],$surcharge[$i],$weight[$i],$charges[$i]);
		$AdminDAO->updaterow("shiplist",$fields,$values," pkshiplistid='$pkshiplist'");
	}
}// end post
?>