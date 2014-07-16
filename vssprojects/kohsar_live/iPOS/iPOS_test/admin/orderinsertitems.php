<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;

/*echo "<pre>";
foreach ($_POST as $key=>$val)
{
	echo '$'."$key	=	".'$_POST[\''.$key.'\'];'."\n";
}
echo "</pre>";
echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$gdeadline			=	$_POST['gdeadline'];
	$gstore				=	$_POST['gstore'];	
	$barcode			=	$_POST['barcode'];
	$itemdescription	=	$_POST['itemdescription'];
	$quantity			=	$_POST['quantity'];
	$price				=	$_POST['price'];
	$weight				=	$_POST['weight'];
	$deadline			=	$_POST['deadline'];
	$brand				=	$_POST['brandid'];
	$supplier			=	$_POST['supplierid'];
	$country			=	$_POST['countryid'];
	$customername		=	$_POST['client'];
	$client				=	$_POST['clientid'];
	$pricelimit			=	$_POST['pricelimit'];
	$agreedprice		=	$_POST['agreedprice'];
	$store				=	$_POST['store'];
	$description		=	'Re-Ordered';
	$comments			=	'Re-Ordered';
	$fields		=	array('fkaddressbookid', 'datetime', 'fkstoreid', 'fkcustomerid', 'fkshipmentid', 'barcode', 'itemdescription', 'quantity', 'deadline', 'lastsaleprice', 'pricelimit', 'agreedprice', 'weight', 'fkbrandid', 'fkcountryid', 'clientinfo', 'fkstatusid', 'description','comments');
	$deadlinestr	=	"";
	$gdeadlineflag	=	0;
	$msg	=	"";
	$flag	=	0;
	for($c=0;$c<sizeof($barcode);$c++)
	{
		// check qty and item description
		if($_POST['check'.$c]==1 && ($itemdescription[$c]=='' || $quantity[$c]==''))
		{
			$r	=	$c+1;
			$msg.=	"<li>Item Name or Quantity missing in $r</li>";
			if(implode("-",array_reverse(explode("-",$deadline[$c])))<date("Y-m-d",time()))
			{
				$deadlinestr	.=",".$r;
			}
		}
		if($_POST['check'.$c]==1)
		{
			$flag++;
		}
		if(implode("-",array_reverse(explode("-",$gdeadline)))<date("Y-m-d",time()))
		{
			$gdeadlineflag	=	1;
		}
	}
/*	if($gdeadlineflag)//commented by jafer on the order of hasnain sahb
	{
		$msg.=	"<li>Invalid Global Deadline.</li>";
	}*/
	if($gstore=='')
	{
		$msg.=	"<li>Please Select a Global Source.</li>";
	}	
	if($flag<1)
	{
		$msg.=	"<li>Please Select at least one order to Reorder.</li>";
	}
	if($deadlinestr)
	{
		$deadlinestr	=	trim($deadlinestr,",");
		$msg.=	"<li>Invalid deadline in $deadlinestr</li>";
	}
	if($msg)
	{
		echo $msg;
		exit;
	}
	for($i=0;$i<sizeof($barcode);$i++)
	{
		if($_POST['check'.$i]==1)
		{
			/*echo $i."....";
			continue;*/
			if($gdeadline)
			{
				$ndeadline	=	implode("-",array_reverse(explode("-",$gdeadline)));
			}
			else
			{
				$ndeadline	=	implode("-",array_reverse(explode("-",$deadline[$i])));
			}
			$nbarcode		=	$barcode[$i];
			$nitemdesc		=	$itemdescription[$i];
			$nquantity		=	$quantity[$i];
			$nweight		=	$weight[$i];
			$nbrand			=	$brand[$i];
			//verifying existing item and brand information from db
			// checking if the barcode exists in db -- adjusting itemdescription
			$ifexists	=	$AdminDAO->getrows("barcode","pkbarcodeid,itemdescription","barcode='$nbarcode'");
			$exists		=	$ifexists[0]['itemdescription'];
			if($exists)
			{
				//changing item description with original item description
				$pkbarcodeid	=	$ifexists[0]['pkbarcodeid'];
				$nitemdesc		=	$exists;
				$existingbrands	=	$AdminDAO->getrows("barcodebrand","fkbrandid","fkbarcodeid='$pkbarcodeid'");
				$nbrand			=	$existingbrands[0]['fkbrandid'];
			}
			$nsupplier		=	$supplier[$i];
			$ncountry		=	$country[$i];
			$nclient		=	$client[$i];
			$nprice			=	$price[$i];
			$npricelimit	=	$pricelimit[$i];
			$nagreedprice	=	$agreedprice[$i];
			$nstore			=	$store[$i];
			if($nstore=='')
			{
				$nstore		=	$gstore;
			}
			$ncustomer		=	$customername[$i];
			$addressbookid	=	$_SESSION['addressbookid'];
			$addtime		=	date('Y-m-d h:i:s',time());
			$values			=	array($addressbookid,$addtime,$nstore,$nclient,0,$nbarcode,$nitemdesc,$nquantity,$ndeadline,$nprice,$npricelimit,$nagreedprice,$nweight,$nbrand,$ncountry,$ncustomer,1,$description,$comments);
			$id	=	$AdminDAO->insertrow("`order`",$fields,$values);
			//adding suppliers
			$sfields	=	array('fkorderid','fksupplierid');
			$svalues	=	array($id,$nsupplier);
			$AdminDAO->insertrow("ordersupplier",$sfields,$svalues);
		}
	}
	echo 1;
}// end post
?>