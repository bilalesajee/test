<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$packingid	=	$_POST['packinglistid'];
    $packing	=	$_POST['box'];
    $shiplistid	=	$_POST['shiplist'];
    $reserved	=	$_POST['remquantity'];
	$max		=	$_POST['maxquantity'];
	if($reserved=="" || $reserved<1)
	{
		echo "1";
		exit;
	}
	$fields =	array('fkshiplistid','fkpackingid','reserved');
	$values	=	array($shiplistid,$packing,$reserved);
	if($packingid!="")
	{
		$AdminDAO->updaterow("packinglist",$fields,$values," pkpackinglistid='$packingid'");
	}
	else
	{
		$AdminDAO->insertrow("packinglist",$fields,$values," 1");
	}
	echo $max-$reserved;
}// end post
?>