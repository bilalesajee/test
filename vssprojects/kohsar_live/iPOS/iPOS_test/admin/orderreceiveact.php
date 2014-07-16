<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_POST['id'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$purchasedquan	=	$_POST['purchasedquan'];
	$shipmentid		=	$_POST['shipmentid'];
	$damaged		=	$_POST['damaged'];
	$damagetype		=	$_POST['damagetype'];
	$returned		=	$_POST['returned'];
	$returntype		=	$_POST['returntype'];
	$storeids		=	$_POST['storeids'];
	$clientquantity	=	$_POST['clientquantity'];
	$orderid		=	$_POST['orderid'];
	$storeidarr		=	explode(",",$storeids);
	$purchaseid		=	$_POST['purchaseid'];
	$addtime		=	date('Y-m-d h:i:s',time());
	//Coding by jafer
	$checked	=	0;
	for($c=0;$c<sizeof($purchasedquan);$c++)
	{
		// check qty and item description
		$r	=	$c+1;
		if($_POST['check'.$c]==1 && $clientquantity[$c]=='')
		{
			$msg.=	"<li>Client Quantity missing in row # $r</li>";
		}
		else if($_POST['check'.$c]==1 && $clientquantity[$c]>$purchasedquan[$c])
		{
			$msg.=	"<li>Client Quantity should not be greater than Purchased Quantity in row # $r</li>";
		}
		if($_POST['check'.$c]==1)
		{
			$checked	=	1;
		}
	}
	if($checked==0)
	{
		$msg	=	"Please Select at least one row to Save.";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	// preparing data for receiving
	$fields	=	array('fkaddressbookid','datetime','fkstoreid','fkshipmentid','fkorderid','fkorderpurchaseid','receivedquantity','fkdamagetypeid','damagedquantity','fkreturntypeid','returnedquantity','clientquantity');
	for($i=0;$i<sizeof($orderid);$i++)
	{
		if($_POST['check'.$i]==1)
		{
			//retrieving stores data
			$storedata	=	$store;
			//receiving store wise
			for($s=0;$s<sizeof($storeidarr);$s++)
			{
				$storeid	=	$storeidarr[$s];
				$storeqty	=	$_POST['store'.$storeid][$i];
				//echo "store id ".$storeid." has quantity ".$storeqty."<br />";
				$data		=	array($_SESSION['addressbookid'],$addtime,$storeid,$shipmentid,$orderid[$i],$purchaseid[$i],$storeqty,$damagetype[$i],$damaged[$i],$returntype[$i],$returned[$i],$clientquantity[$i]);
				$AdminDAO->insertrow("orderreceive",$fields,$data);
			//Change the status of Order to Received
			$fields4	=	array('fkstatusid');
			$data4		=	array('9');
			//echo 'jafer';
			$AdminDAO->updaterow("`order`",$fields4,$data4,"pkorderid=$orderid[$i]");				
			}
		}
	}
	//7 	Received
	//echo $shipmentid;
	$fields	=	array('fkstatusid');
	$data	=	array("2");
	$AdminDAO->updaterow("shipment",$fields,$data,"pkshipmentid=$shipmentid");
}
else
echo "Insufficeint Data.";
?>