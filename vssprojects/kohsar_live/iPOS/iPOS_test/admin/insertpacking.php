<?php

error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
*/
$id 		= 	$_REQUEST['id'];
$qs			=	$_SESSION['qstring'];
$shipmentid	=	$_REQUEST['shipmentid'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$quantity 	= filter($_POST['quantity']);
	$packnumber = filter($_POST['packnumber']);
	//$packs = $AdminDAO->getrows("packinglist","pkpackinglistid, fkpurchaseid, fkshipmentid, packnumber, packtime, packedby, quantity"," pkpackinglistid='$id'");
	if($quantity=='' || $quantity==0)
	{
		echo $msg.="Please enter valid packing quantity.";
		exit;
	}
	if($packnumber=='' )
	{
		echo  $msg.="Packing packnumber can not be left Blank.";
		exit;
	}
}//end edit
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	if($id!="-1")
	{
		// this is the edit section
		$packs = $AdminDAO->getrows("packing","*"," pkpackingid='$id'");
		foreach($packs as $pack)
		{
			$packingname = $pack['packingname'];
		}
	}
	if(sizeof($_POST)>0)
	{
			$newpackname = filter($_POST['packname']);
			if($newpackname=='')
			{
				echo"Packing Name can not be left Blank.";
				exit;
			}
			$fields = array('packingname','fkshipmentid');
			$values = array($newpackname,$shipmentid);
	
		if($id!='-1')//updates records 
		{
			$fields = array('packingname');
			$values = array($newpackname);
			$AdminDAO->updaterow("packing",$fields,$values," pkpackingid='$id' ");
		}
		else
		{
			// this is the add section	
			$id = $AdminDAO->insertrow("packing",$fields,$values);
		}//end of else
	exit;
	}// end post
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	if($id!="-1")
	{
		// this is the edit section
		//$packs = $AdminDAO->getrows("packinglist","pkpackinglistid, fkpurchaseid, fkshipmentid, packnumber, packtime, packedby, quantity"," pkpackinglistid='$id'");
		$qury="SELECT pkpackinglistid, fkpurchaseid, fkshipmentid, packnumber, packtime, packedby, quantity from packinglist where pkpackinglistid='$id'";
		$pack = $AdminDAO->queryresult($qury);
		//foreach($packs as $pack)
		//{
			//$quantity 		= $pack[0]['quantity'];
			$oldquantity 	= $pack[0]['quantity'];
			$fkshipmentid	= $pack[0]['fkshipmentid'];
			$fkpurchaseid 	= $pack[0]['fkpurchaseid'];		
			$oldpacknumber 	= $pack[0]['packnumber'];
			
			$qury="SELECT pkpurchaseid,quantity from purchase where pkpurchaseid='$fkpurchaseid' and fkshipmentid='$fkshipmentid'";
			//$purcahse = $AdminDAO->getrows("purchase","quantity"," pkpurchaseid='$fkpurchaseid'");
			$purcahse = $AdminDAO->queryresult($qury);
			
			$purchasequantity=$purcahse[0]['quantity'];
			//print_r($purcahse);
			//echo "purchaseqty=".$purchasequantity;
	
		//}
	}
	
	
	if(sizeof($_POST)>0)
	{
			$oldquantity 	= filter($_POST['oldquantity']);
			if($oldpacknumber!=$packnumber){
				$qury="SELECT pkpurchaseid,quantity from purchase where pkpurchaseid='$fkpurchaseid' and fkshipmentid='$fkshipmentid' and packnumber=$packnumber";
				$isunique = $AdminDAO->queryresult($qury);
				if(count($isunique)>0)
				{
					$msg.="Pack Number is not unique.";
						
				}
			}
			
			//$fields = array('packingname','fkshipmentid');
			//$values = array($newpackname,$shipmentid);
		if($msg){		
			echo $msg;
			exit;			
		}
		elseif($id!='-1')//updates records 
		{
			$fields = array('quantity','packnumber');
			$values = array($quantity,$packnumber);
			
			
			
			if($oldquantity==$quantity){
				$AdminDAO->updaterow("packinglist",$fields,$values," pkpackinglistid='$id' ");
				//echo "called 1";
			}else{
				$AdminDAO->updaterow("packinglist",$fields,$values," pkpackinglistid='$id' ");						
				$diff=($purchasequantity+(($oldquantity>$quantity)?(-($oldquantity-$quantity)):($quantity-$oldquantity)));
				//echo "oldquantity$oldquantity<br>diff =$diff";
				$fields = array('quantity');
				$values = array($quantity);			
				$AdminDAO->updaterow("purchase",$fields,$values," pkpurchaseid='$fkpurchaseid' ");
			}		
		}
	exit;
	}// end post	
}//end edit
?>