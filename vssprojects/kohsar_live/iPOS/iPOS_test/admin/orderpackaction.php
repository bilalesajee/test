<?php
session_start();
error_reporting(7);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$quantityval		=	$_POST['quantity'];
    $boxno				=	$_POST['box'];
    $boxitem			=	$_POST['boxtotal'];// made it as remaining qty by jafer
	$productorderid		=	$_POST['orderid'];
	$shipmentid			=	$_POST['id'];
	//print_r($boxitem);
	//exit;
	$checked	=	0;
	for($c=0;$c<sizeof($boxitem);$c++)
	{
		// check qty and item description
		$r	=	$c+1;
		if($_POST['check'.$c]==1 && ($boxno[$c]=='' || $boxno[$c]==0 || $quantityval[$c]=='' || $quantityval[$c]==0))
		{
			$msg.=	"<li>Pack # or Pack Qty missing in row # $r</li>";
		}
		else if($_POST['check'.$c]==1 && $boxitem[$c]<$quantityval[$c])
		{
			$msg.=	"<li>Packing Quantity should not be greater than Remaining Quantity in row # $r</li>";
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
	$bfields	=	array('fkaddressbookid','datetime','fkshipmentid','fkorderid','packnumber','quantity');
	for($i=0;$i<sizeof($boxitem);$i++)
	{
		if($_POST['check'.$i]==1)
		{
			$orderid		=	$productorderid[$i];
			$addressbookid	=	$_SESSION['addressbookid'];
			//$addtime		=	date('Y-m-d h:i:s',time());
			$datetime		=	date("Y-m-d H:i:s");
			$quantity		=	$quantityval[$i];
			$box			=	$boxno[$i];
			//saving packing data 
			$commapos	=	strpos($box,",");
			$dashpos	=	strpos($box,"-");
			if($commapos)//if comma (,) is found then split
			{
				unset($boxes);
				$boxes		=	explode(",",$box);
				//calculate qty per box
				if($quantity!='')
				{
					$avgqty	=	floor($quantity/sizeof($boxes));
					if($avgqty==0)
					{
						$avgqty	= 1;
					}							
				}
				// entering box data
				$quantity2	=	$quantity;
				for($b=0;$b<sizeof($boxes);$b++)
				{
					$boxname	=	$boxes[$b];
					//checking existing box name for each iteration 
					if($boxname)// not needed but sometimes we have to deal with stupids
					{
						$lastiteration	=	sizeof($boxes)-1;
						if($b==$lastiteration)
						{
							$avgqty	=	$quantity2;
						}							
						$quantity2	=	$quantity2-$avgqty;
						if($quantity2<$avgqty)
						{
							$avgqty	=	$quantity2+$avgqty;
						}
						if($avgqty<0)
						{
							$avgqty	=	0;
						}						
						//$bdata		=	array($fkaddressbookid,$addtime,$shipmentid,$orderid,$boxname,$avgqty);
						$bdata		=	array($addressbookid,$datetime,$shipmentid,$orderid,$boxname,$avgqty);						
						//inserting values
						$AdminDAO->insertrow("orderpack",$bfields,$bdata);
					}
				}//end entry section
			}//end comma section
			else if($dashpos)//if hyphen (-) is found then loop
			{
				unset($boxes2);
				$boxids		=	explode("-",$box);
				$boxstart	=	$boxids[0];
				$boxend		=	$boxids[1];
				for($j=$boxstart;$j<=$boxend;$j++)
				{
					$boxes2[]	=	$j;
				}
				//calculate qty per box
				if($quantity!='')
				{
					$avgqty	=	floor($quantity/sizeof($boxes2));
					if($avgqty==0)
					{
						$avgqty	= 1;
					}					
				}
				// entering box data
				$quantity2	=	$quantity;
				for($b=0;$b<sizeof($boxes2);$b++)
				{
					$boxname	=	$boxes2[$b];
					//checking existing box name for each iteration 
					if($boxname)// not needed but sometimes we have to deal with stupids
					{
						$lastiteration	=	sizeof($boxes2)-1;						
						if($b==$lastiteration)
						{
							$avgqty	=	$quantity2;
						}						
						$quantity2	=	$quantity2-$avgqty;
						if($quantity2<$avgqty)
						{
							$avgqty	=	$quantity2+$avgqty;
						}
						if($avgqty<0)
						{
							$avgqty	=	0;
						}
						//$bdata		=	array($fkaddressbookid,$addtime,$shipmentid,$orderid,$boxname,$avgqty);
						$bdata		=	array($addressbookid,$datetime,$shipmentid,$orderid,$boxname,$avgqty);						
						//inserting values
						$AdminDAO->insertrow("orderpack",$bfields,$bdata);
					}					
				}//end entry section
			}//end dash section
			else //if($commapos=='' && $dashpos=='') //one box
			{
				//update and distribute quantity among boxes
				$bdata		=	array($addressbookid,$datetime,$shipmentid,$orderid,$box,$quantity);
				//inserting values
				$AdminDAO->insertrow("orderpack",$bfields,$bdata);
			}	
		}
	}
}// end post
else
echo "Insufficeint Data.";
?>