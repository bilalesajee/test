<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if(sizeof($_POST)>0)
{
	$ids		=	explode(",",$_POST['ids']);
	for($i=0;$i<sizeof($ids);$i++)
	{
		$shiplistid		=	$ids[$i];
		$box			=	$_POST['box_'.$ids[$i]];
		$total			=	$_POST['boxtotal_'.$ids[$i]];
		$quantity		=	$_POST['quantity_'.$ids[$i]];
		if($box == '')
		{
			echo "Please make sure you have entered Box Number";
			exit;
		}
		else
		{
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
						$packingid	=	$AdminDAO->getrows("packing","pkpackingid","packingname='$boxname'");
						$boxid		=	$packingid[0]['pkpackingid'];
						if($boxid) //case when box exists
						{
							$bfields	=	array('fkpackingid','fkshiplistid','reserved');
							//update and distribute quantity among boxes
							$quantity	=	$quantity-$avgqty;
							if($quantity<=0)
							{
								$avgqty	=	$quantity+$avgqty;
							}
							$bdata		=	array($boxid,$shiplistid,$avgqty);
							//inserting values
							$AdminDAO->insertrow("packinglist",$bfields,$bdata);
						}
						else
						{
							$pfield		=	array("packingname");
							$pdata		=	array($boxname);
							$packingid	=	$AdminDAO->insertrow("packing",$pfield,$pdata);
							$bfields	=	array('fkpackingid','fkshiplistid','reserved');
							//update and distribute quantity among boxes
							$quantity	=	$quantity-$avgqty;
							if($quantity<=0)
							{
								$avgqty	=	$quantity+$avgqty;
							}
							$bdata		=	array($packingid,$shiplistid,$avgqty);
							//inserting values
							$AdminDAO->insertrow("packinglist",$bfields,$bdata);
						}
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
						$packingid	=	$AdminDAO->getrows("packing","pkpackingid","packingname='$boxname'");
						$boxid		=	$packingid[0]['pkpackingid'];
						if($boxid) //case when box exists
						{
							$bfields	=	array('fkpackingid','fkshiplistid','reserved');
							//update and distribute quantity among boxes
							$quantity	=	$quantity-$avgqty;
							if($quantity<=0)
							{
								$avgqty	=	$quantity+$avgqty;
							}
							$bdata		=	array($boxid,$shiplistid,$avgqty);
							//inserting values
							$AdminDAO->insertrow("packinglist",$bfields,$bdata);
						}
						else
						{
							$pfield		=	array("packingname");
							$pdata		=	array($boxname);
							$packingid	=	$AdminDAO->insertrow("packing",$pfield,$pdata);
							$bfields	=	array('fkpackingid','fkshiplistid','reserved');
							//update and distribute quantity among boxes
							$quantity	=	$quantity-$avgqty;
							if($quantity<=0)
							{
								$avgqty	=	$quantity+$avgqty;
							}
							$bdata		=	array($packingid,$shiplistid,$avgqty);
							//inserting values
							$AdminDAO->insertrow("packinglist",$bfields,$bdata);
						}
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
				$packingid	=	$AdminDAO->getrows("packing","pkpackingid","packingname='$box'");
				$boxid		=	$packingid[0]['pkpackingid'];
				if($boxid)
				{
					$bfields	=	array('fkpackingid','fkshiplistid','reserved');
					$bdata		=	array($boxid,$shiplistid,$qty);
					//inserting values
					$AdminDAO->insertrow("packinglist",$bfields,$bdata);
				}
				else
				{
					$pfield		=	array("packingname");
					$pdata		=	array($box);
					$packingid	=	$AdminDAO->insertrow("packing",$pfield,$pdata);
					$bfields	=	array('fkpackingid','fkshiplistid','reserved');
					//update and distribute quantity among boxes
					$bdata		=	array($packingid,$shiplistid,$qty);
					//inserting values
					$AdminDAO->insertrow("packinglist",$bfields,$bdata);
				}
			}//end box section
		}// end box entry check
	}//end iterations
}//end post
?>