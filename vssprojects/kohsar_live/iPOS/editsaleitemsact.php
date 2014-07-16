<?php

session_start();

include_once("includes/security/adminsecurity.php");
$customerid	=	$_SESSION['customerid'];
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

	$taxable			=	$_POST['taxable']; // Added By fahad 14-06-2012
	$fields					=	array('quantity','saleprice','taxamount');

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

			///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//calculating tax amount coding by jafer 14-12-11
			$taxpercentage		=	$AdminDAO->getrows("$dbname_detail.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");
			$salestaxper		=	$taxpercentage[0]['amount'];		
				
		
				
			if($taxable!=1 && $customerid!=''){
			
				 $tax=($saleprice*$salestaxper/100)*$quantity;
			
			}else if($taxable==1 && $customerid!=''){
			
				$tax	=	0;
			
			}
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
			/*echo "<script>alert(\"$quantity!=$originalquantity : $saleprice!=$originalprice : $i\")</script>";*/

				$values			=	array($quantity,$saleprice,$tax);

				$AdminDAO->updaterow("$dbname_detail.saledetail",$fields,$values,"pksaledetailid='$saledetailid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012

			}

		}
                
                // added by siddique 04/05/2013
                $fields_temp = array('fkaddressbookid', 'ip', 'fkstockid', 'originalquantity', 'quantity', 'originalprice', 'price');
                $fkaddressbookid = $_SESSION['addressbookid'];
                if(!$fkaddressbookid)
                {
                    $fkaddressbookid = 0;
                }
                
                $ip = $_SERVER['REMOTE_ADDR'];
                if(!$ip)
                {
                    $ip = 'no-ip';
                }
                
                $values_temp = array($fkaddressbookid, $_SERVER['REMOTE_ADDR'], $fkstockid, $originalquantity, $quantity, $originalprice, $saleprice );
                $AdminDAO->insertrow("$dbname_detail.saleitemtemp", $fields_temp, $values_temp);

	}

}

?>