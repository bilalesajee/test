<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$supplierid			=	$_POST['supplierid'];
	$batch				=	$_POST['batch'];
//	$boxbarcode			=	$_POST['boxbarcode'];
    $barcode			=	$_POST['barcode'];
    $itemdescription	=	$_POST['itemdescription'];
    $weight				=	$_POST['weight'];
    $lasttradeprice		=	$_POST['lasttradeprice'];
    $purchaseprice		=	$_POST['purchaseprice'];
    $quantityval		=	$_POST['quantity'];
    $boxno				=	$_POST['box'];
    $boxitem			=	$_POST['boxtotal'];
    $expiry				=	$_POST['expiry'];
	$shiplistval		=	$_POST['shiplistid'];
	$shiplistdetailsid	=	$_POST['shiplistdetailsid'];
	$currencyid			=	$_POST['shipcurrency'];
	$exchangerate		=	$_POST['exchangerate'];
	$shipmentid			=	$_POST['shipmentid'];
	$addressbookid		=	$_SESSION['addressbookid'];
	$purchasetime		=	time();
	$fields				=	array('fkshiplistid','fkshiplistdetailsid','fkbarcodeid','purchasetime','quantity','purchaseprice','fkcurrencyid','currencyrate','weight','fksupplierid','batch','expiry','fkshipmentid','fkaddressbookid');
	for($i=0;$i<sizeof($barcode);$i++)
	{
		if($_POST['check'.$i]==1)
		{
			$shiplistid			=	$shiplistval[$i];
			$shiplistdetailsval	=	$shiplistdetailsid[$i];
			// decision on barcode ... save in db and extract fkbarcodeid
			$barcodeval			=	$barcode[$i];
			$itemdescriptionval	=	$itemdescription[$i];
			$productid			=	$_POST['productid'];
			// selecting barcode value from db
			$codes				=	$AdminDAO->getrows("barcode","pkbarcodeid","barcode='$barcodeval'");
			if(sizeof($codes)>0)// if barcode exists
			{
				$fkbarcodeid	=	$codes[0]['pkbarcodeid'];
			}
			else
			{
				$bfields		=	array('barcode','itemdescription','fkproductid');
				$bdata			=	array($barcodeval,$itemdescriptionval,$productid);
				$fkbarcodeid	=	$AdminDAO->insertrow("barcode",$bfields,$bdata);
			}
			$quantity			=	$quantityval[$i];
			$price				=	$purchaseprice[$i];
			$currencyval		=	$currencyid[$i];
			$exchangerateval	=	$exchangerate[$i];
			$weightval			=	$weight[$i];
			$supplierval		=	$supplierid[$i];
			$batchval			=	$batch[$i];
			$expirystr			=	$expiry[$i];
			$box				=	$boxno[$i];
			$total				=	$boxitem[$i];
			$expiryval			=	$expiry[$i];
			if($box == '')
			{
				$msg	.=	"<li>Please make sure you have entered box number.</li>";
			}
			if($quantity == '' || $price== '')
			{
				$msg	.=	"<li>Please make sure you have entered quantity and price.</li>";
			}
			if($msg)
			{
				echo $msg;
				exit;
			}
			else
			{
				// saving purchase data
				$data		=	array($shiplistid,$shiplistdetailsval,$fkbarcodeid,$purchasetime,$quantity,$price,$currencyval,$exchangerateval,$weightval,$supplierval,$batchval,implode("-",array_reverse(explode("-",$expiryval))),$shipmentid,$addressbookid);
				$purchaseid	=	$AdminDAO->insertrow("purchase",$fields,$data);
	
				//saving packing data 
				//examine box
				$commapos	=	strpos($box,",");
				$dashpos	=	strpos($box,"-");
				if($commapos)//if comma (,) is found then split
				{
					$boxes		=	explode(",",$box);
					//calculate qty per box
					if($total!='')
					{
						$avgqty	=	ceil($total/sizeof($boxes));
					}
					else
					{
						$avgqty	=	ceil($quantity/sizeof($boxes));
					}
					// entering box data
					for($b=0;$b<sizeof($boxes);$b++)
					{
						$boxname	=	$boxes[$b];
						//checking existing box name for each iteration 
						if($boxname)// not needed but sometimes we have to deal with stupids
						{
							$bfields	=	array('fkpurchaseid','fkshipmentid','packnumber','packtime','packedby','quantity');
							//update and distribute quantity among boxes
							$quantity	=	$quantity-$avgqty;
							if($quantity<=0)
							{
								$avgqty	=	$quantity+$avgqty;
							}
							$bdata		=	array($purchaseid,$shipmentid,$boxname,time(),$_SESSION['addressbookid'],$avgqty);
							//inserting values
							$AdminDAO->insertrow("packinglist",$bfields,$bdata);
						}
					}//end entry section
				}//end comma section
				else if($dashpos)//if hyphen (-) is found then loop
				{
					$boxids		=	explode("-",$box);
					$boxstart	=	$boxids[0];
					$boxend		=	$boxids[1];
					for($j=$boxstart;$j<=$boxend;$j++)
					{
						$boxes2[]	=	$j;
					}
					//calculate qty per box
					if($total!='')
					{
						$avgqty	=	ceil($total/sizeof($boxes2));
					}
					else
					{
						$avgqty	=	ceil($quantity/sizeof($boxes2));
					}
					// entering box data
					for($b=0;$b<sizeof($boxes2);$b++)
					{
						$boxname	=	$boxes2[$b];
						//checking existing box name for each iteration 
						if($boxname)// not needed but sometimes we have to deal with stupids
						{
							$bfields	=	array('fkpurchaseid','fkshipmentid','packnumber','packtime','packedby','quantity');
							//update and distribute quantity among boxes
							$quantity	=	$quantity-$avgqty;
							if($quantity<=0)
							{
								$avgqty	=	$quantity+$avgqty;
							}
							$bdata		=	array($purchaseid,$shipmentid,$boxname,time(),$_SESSION['addressbookid'],$avgqty);
							//inserting values
							$AdminDAO->insertrow("packinglist",$bfields,$bdata);
						}
					}//end entry section
				}//end dash section
				else //if($commapos=='' && $dashpos=='') //one box
				{
					if($total!='')
					{
						$qty	=	$total;
					}
					else
					{
						$qty	=	$quantity;
					}
					$bfields	=	array('fkpurchaseid','fkshipmentid','packnumber','packtime','packedby','quantity');
					//update and distribute quantity among boxes
					$bdata		=	array($purchaseid,$shipmentid,$box,time(),$_SESSION['addressbookid'],$qty);
					//inserting values
					$AdminDAO->insertrow("packinglist",$bfields,$bdata);
				}//end box section
			}
		}
	}
}// end post
?>
