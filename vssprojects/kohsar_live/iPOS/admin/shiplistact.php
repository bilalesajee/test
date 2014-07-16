<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$ids			=	explode(",",$_POST['ids']);
		$shipmentid		=	$_POST['shipment'];
		if($shipmentid == '')
		{
			echo "<li>Please make sure you have selected shipment.</li>";
			exit;
		}
		for($i=0;$i<sizeof($ids);$i++)
		{
			$shiplistid	=	$ids[$i];
			$fields		=	array('fkshipmentid','fkstatusid');
			$data		=	array($shipmentid,5);
			$AdminDAO->updaterow("shiplist",$fields,$data,"pkshiplistid='$shiplistid'");
		}
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$flag			=	0;
		$ids			=	explode(",",$_POST['ids']);
		$shipmentid		=	$_POST['shipment'];
		if($shipmentid == '')
		{
			echo "<li>Please make sure you have selected shipment.</li>";
			exit;
		}
		for($i=0;$i<sizeof($ids);$i++)
		{
			$shiplistid	=	$ids[$i];
			//checking purchases against this shiplist
			$shiplistdetails	=	$AdminDAO->getrows("shiplistdetails","1","fkshiplistid='$shiplistid'");
			if(sizeof($shiplistdetails)<1)
			{
				$flag	=	1;
				continue;
			}
			$fields		=	array('fkshipmentid','fkstatusid');
			$data		=	array($shipmentid,4);
			$AdminDAO->updaterow("shiplist",$fields,$data,"pkshiplistid='$shiplistid'");
		}
		if($flag	==	1)
		{
			echo "<li>Some orders were not purchased yet and couldn't be moved to the selected shipment.</li>";
			exit;
		}
		//now updating shipment value
		$query					=	"SELECT 
											lastpurchaseprice,
											rate,
											sd.quantity
										FROM 
											shiplist s, currency,shiplistdetails sd
										WHERE 
											pkshiplistid=sd.fkshiplistid AND 
											s.fkshipmentid='$shipmentid' AND
											fkcurrencyid=pkcurrencyid
										";
		$shiplistdata			=	$AdminDAO->queryresult($query);
		if($shiplistdata)
		{
			foreach($shiplistdata as $sldata)
			{
				$sprice				=	$sldata['lastpurchaseprice'];
				$qty				=	$sldata['quantity'];
				$rate				=	$sldata['rate'];
				$price				=	$sprice*$qty;
				$totalvalue			+=	($price*$rate);
			}
			$selected_rate			=	$AdminDAO->getrows("shipment","exchangerate","pkshipmentid='$shipmentid'");
			$shipment_rate			=	$selected_rate[0]['exchangerate'];
			$totalvalue				=	round(($totalvalue/$shipment_rate),2);
		}
		$sfields	=	array('totalvalue');
		$sdata		=	array($totalvalue);
		$AdminDAO->updaterow("shipment",$sfields,$sdata,"pkshipmentid='$shipmentid'");
	}//end edit
}
?>