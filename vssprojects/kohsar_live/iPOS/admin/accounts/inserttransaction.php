<?php
include("../../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_REQUEST['id'];
if(sizeof($_POST)>0)
{
	$draccount	=	$_POST['draccount'];
	$dramount	=	$_POST['dramount'];
	$craccount	=	$_POST['craccount'];
	$cramount	=	$_POST['cramount'];
	$drefid		=	$_POST['drefid'];
	$crefid		=	$_POST['crefid'];
    $details 	=	filter($_POST['details']);
	$time		=	time();
	
	$date1		=	$_POST['date1'];
	$date1		=	strtotime($date1);
	
	if (!$draccount[0]){
	 echo "Please select Dr account";
	 exit;
	}
	
	if (!$craccount[0]){
	 echo "Please select Cr account";
	 exit;
	}
		
	$tfields		=	array('details','at','period_id','date1');
	$tvalues		=	array($details,$date1,$periodid,$time);
	$trasactionid	=	$AdminDAO->insertrow("$dbname_detail.transaction",$tfields,$tvalues);
	
	for($d	=	0; $d < sizeof($draccount); $d++)
	{
		
		$dr_amount		=	$dramount[$d];//if some amount is given in a box
		if($dr_amount)
		{
			$dr_account		=	$draccount[$d];
			$dr_refid		=	$drefid[$d];				
			$tdfields		=	array('account_id','dr','transaction_id','refid');
			$tdvalues		=	array($dr_account,$dr_amount,$trasactionid,$dr_refid);
			$AdminDAO->insertrow("$dbname_detail.transaction_details",$tdfields,$tdvalues);
		}
		
	}
	for($c	=	0; $c < sizeof($craccount); $c++)
	{
		$cr_amount	=	$cramount[$c];//if some amount is given in a box
		if($cr_amount)
		{
			$cr_account		=	$craccount[$c];
			$cr_refid		=	$crefid[$c];
			$tcfields		=	array('account_id','cr','transaction_id','refid');
			$tcvalues		=	array($cr_account,$cr_amount,$trasactionid,$cr_refid);
			$AdminDAO->insertrow("$dbname_detail.transaction_details",$tcfields,$tcvalues);
		}
	}
}//if
else
{
	echo "false";
}
?>