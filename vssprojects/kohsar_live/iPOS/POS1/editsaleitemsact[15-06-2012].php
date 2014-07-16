<?php
session_start();
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$postquantity			=	$_POST['quantity'];
	$postsaleprice			=	$_POST['saleprice'];
	$postsaledetailid		=	$_POST['saledetailid'];
	$postoriginalprice		=	$_POST['originalprice'];
	$postoriginalquantity	=	$_POST['originalquantity'];
	$poststockid			=	$_POST['fkstockid'];
	$fields					=	array('quantity','saleprice');
	for($i=0;$i<sizeof($_POST['quantity']);$i++)
	{				
		$quantity			=	$postquantity[$i];
		$originalquantity	=	$postoriginalquantity[$i];
		$saleprice			=	$postsaleprice[$i];
		$originalprice		=	$postoriginalprice[$i];
		$saledetailid		=	$postsaledetailid[$i];
		$fkstockid			=	$poststockid[$i];
		
		// added by Yasir -- 04-07-11
		if ($quantity > 0 && $saleprice < 0){
			echo 'Please enter positive value for price.';
			continue;
		}
		
		if ($quantity > 3000){
			echo 'You can not add more than 3000 units at once'; 
			continue;
		}
		
		//		
		
		if(($quantity!=$originalquantity) || ($saleprice!=$originalprice))
		{
			//adjusting stocks
			if($quantity!=$originalquantity)
			{
				$remaining	=	$originalquantity-$quantity;
				$query		=	"UPDATE $dbname_detail.stock SET unitsremaining=unitsremaining+'$remaining' WHERE pkstockid='$fkstockid'";//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				$AdminDAO->queryresult($query);
			}
			//removing units from sale when quantity is zero
			if($quantity==0)
			{
				$AdminDAO->deleterows("$dbname_detail.saledetail","pksaledetailid='$saledetailid'",1);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
			}
			else
			{
			/*echo "<script>alert(\"$quantity!=$originalquantity : $saleprice!=$originalprice : $i\")</script>";*/
				$values			=	array($quantity,$saleprice);
				$AdminDAO->updaterow("$dbname_detail.saledetail",$fields,$values,"pksaledetailid='$saledetailid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
			}
		}
	}
}
?>