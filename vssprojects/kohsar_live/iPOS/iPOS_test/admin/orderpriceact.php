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
	$shipmentid				=	$_POST['shipmentid'];
	$shipmentpercentage		=	$_POST['shipmentpercentage'];
	$shipmentcharges		=	$_POST['shipmentcharges'];
	$costprice				=	$_POST['costprice'];
	$retailpercentage		=	$_POST['retailpercentage'];
	$storeids				=	$_POST['storeids'];
	$storeidarr				=	explode(",",$storeids);
	$purchaseid				=	$_POST['purchaseid'];
	$orderid				=	$_POST['orderid'];
	$addtime				=	date('Y-m-d h:i:s',time());
	//Coding by jafer
	$checked	=	0;
	$msg		=	'';
	for($c=0;$c<sizeof($purchaseid);$c++)
	{
		$r	=	$c+1;
		if($_POST['check'.$c]==1 && ($shipmentpercentage[$c]=='' || $retailpercentage[$c]=='' ))
		{
			$msg.=	"<li>Shipment % Or Retail % is missing in row # $r</li>";
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
	//$fields	=	array('fkstoreid','fkpurchaseid','fkshipmentid','pricedby','pricetime','costprice','retailpercentage','retailprice','shipmentcharges','shipmentpercentage');
	// new
	$fields	=	array('fkaddressbookid','datetime','fkstoreid','fkshipmentid','fkorderid','fkorderpurchaseid','costprice','retailpercentage','retailprice','shipmentcharges','shipmentpercentage');
	for($i=0;$i<sizeof($purchaseid);$i++)
	{
		if($_POST['check'.$i]==1)
		{
			//retrieving stores data
			$storedata	=	$store;
			//receiving store wise
			for($s=0;$s<sizeof($storeidarr);$s++)
			{
				$storeid		=	$storeidarr[$s];
				$retailprice	=	$_POST['store'.$storeid][$i];
				//$data			=	array($storeid,$purchaseid[$i],$shipmentid,$_SESSION['addressbookid'],$addtime,$costprice[$i],$retailpercentage[$i],$retailprice,$shipmentcharges[$i],$shipmentpercentage[$i]);
				$data			=	array($_SESSION['addressbookid'],$addtime,$storeid,$shipmentid,$orderid[$i],$purchaseid[$i],$costprice[$i],$retailpercentage[$i],$retailprice,$shipmentcharges[$i],$shipmentpercentage[$i]);		
				$AdminDAO->insertrow("orderprice",$fields,$data);
			//Change the status of Order to finalized
			$fields4	=	array('fkstatusid');
			$data4		=	array('8');
			//echo 'jafer';
			$AdminDAO->updaterow("`order`",$fields4,$data4,"pkorderid=$orderid[$i]");
				
			}
		}
	}
	//7 	Received
	//echo $shipmentid;
	//change status to close by jafer balti
	$fields	=	array('fkstatusid');
	$data	=	array("2");
	$AdminDAO->updaterow("shipment",$fields,$data,"pkshipmentid=$shipmentid");
	//move the orders in shipment which are not purchased yet to a new shipment
	$orderidarr			=	$AdminDAO->getrows("`order`","pkorderid","fkshipmentid='$shipmentid' AND fkstatusid IN(1,2)");
	if(sizeof($orderidarr)>0)
	{
		//creat a new shipment duplicate of previous shipment
		$shipinfo	=	$AdminDAO->getrows("`shipment`","*","pkshipmentid='$shipmentid'");
		{
			$oldtype				=	$shipinfo[0]['type'];
			$oldshipmentdate		=	$shipinfo[0]['shipmentdate'];
			$oldsdate				=	$shipinfo[0]['sdate'];
			$oldcdate				=	$shipinfo[0]['cdate'];
			$oldshipmentname		=	$shipinfo[0]['shipmentname'];
			$oldfkagentid			=	$shipinfo[0]['fkagentid'];
			$oldfkcountryid			=	$shipinfo[0]['fkcountryid'];
			$oldfkcityid			=	$shipinfo[0]['fkcityid'];
			$oldfkdestcountryid		=	$shipinfo[0]['fkdestcountryid'];
			$oldfkdestcityid		=	$shipinfo[0]['fkdestcityid'];
			$oldshipmentcurrency	=	$shipinfo[0]['shipmentcurrency'];
			$oldexchangerate		=	$shipinfo[0]['exchangerate'];
			$oldtotalvalue			=	$shipinfo[0]['totalvalue'];
			$oldamountinrs			=	$shipinfo[0]['amountinrs'];
			$oldchargesinrs			=	$shipinfo[0]['chargesinrs'];
			$oldshipmentdeleted		=	$shipinfo[0]['shipmentdeleted'];	
			$oldisopened			=	$shipinfo[0]['isopened'];
			$oldfkstoreid			=	$shipinfo[0]['fkstoreid'];
			$oldfkdeststoreid		=	$shipinfo[0]['fkdeststoreid'];	
			$oldfkclientid			=	$shipinfo[0]['fkclientid'];
			$oldweight				=	$shipinfo[0]['weight'];
			$oldshipmentnotes		=	$shipinfo[0]['shipmentnotes'];		
			$oldfkstatusid			=	1;
			$olddatetimetest		=	$shipinfo[0]['datetimetest'];	
			$fields2	=	array('type','shipmentdate','sdate','cdate','shipmentname','fkagentid','fkcountryid','fkcityid','fkdestcountryid','fkdestcityid','shipmentcurrency','exchangerate','totalvalue','amountinrs','chargesinrs','shipmentdeleted','isopened','fkstoreid','fkdeststoreid','fkclientid','weight','shipmentnotes','fkstatusid','datetimetest');
			$data2		=	array($oldtype,$oldshipmentdate,$oldsdate,$oldcdate,$oldshipmentname,$oldfkagentid,$oldfkcountryid,$oldfkcityid,$oldfkdestcountryid,$oldfkdestcityid,$oldshipmentcurrency,$oldexchangerate,$oldtotalvalue,$oldamountinrs,$oldchargesinrs,$oldshipmentdeleted,$oldisopened,$oldfkstoreid,$oldfkdeststoreid,$oldfkclientid,$oldweight,$oldshipmentnotes,$oldfkstatusid,$olddatetimetest);
			$newshipid	=	$AdminDAO->insertrow("shipment",$fields2,$data2);										
		}		
		for($k=0;$k<sizeof($orderidarr);$k++)
		{
			$oid		=	$orderidarr[$k]['pkorderid'];	
			$fields3	=	array('fkshipmentid');
			$data3		=	array($newshipid);
			//echo 'jafer';
			$AdminDAO->updaterow("`order`",$fields3,$data3,"pkorderid=$oid");
		}
		echo "order moved";
	}
}
else
echo "Insufficeint Data.";
?>