<?php

//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 			= 	$_REQUEST['id'];
$qs				=	$_SESSION['qstring'];
$storeid		=	$_SESSION['storeid'];
$unitsreserved	=	$_POST['unitsreserved'];
$demandunits	=	$_POST['demandunits'];
$deststoreid	=	$_POST['deststore'];
$pkdemanddetailid=	$_POST['pkdemanddetailid'];

if($_POST)
{
	$flag	==	0;		
	for($s=0;$s<count($_POST['stockid']);$s++)
	{
			$remunits		=	$_POST['unitsremaining'][$s];
			$unitstomove	=	$_POST['unitstomove'][$s];
			$flag			+=	$unitstomove;
			$stockid		=	$_POST['stockid'][$s];
			$codeid			=	$_POST['codeid'];
			if($remunits<$unitstomove)
			{
				echo"<li>You are trying to move ($unitstomove) more than remaining ($remunits) units</li>";
				exit;
			}
		if($unitstomove>0)
		{
			$pkstockid	=	$AdminDAO->pkey('stock','pkstockid');
			
			$sql="INSERT 
						INTO 
							stock(SELECT 
								  		'$pkstockid' as pkstockid,
										batch,
										$unitstomove as quantity,
										$unitstomove as unitsremaining,
										expiry,
										purchaseprice,
										costprice,
										retailprice,
										priceinrs,
										shipmentcharges,
										suggestedsaleprice,
										fkshipmentgroupid,
										fkshipmentid,
										'$codeid' as fkbarcodeid,
										fkorderid,
										fksupplierid,
										fkagentid,
										fkcountryid,
										'$deststoreid' as fkstoreid,
										'$empid' as fkemployeeid,
										fkbrandid,".time()." as updatetime,
										0 as unitsreserved,
										shipmentpercentage,
										boxprice
									FROM 
										stock 
									WHERE 
										pkstockid='$stockid'
									)";
			$AdminDAO->queryresult($sql);
			$fullfillstockid	=	mysql_insert_id();
			if($unitstomove<=$unitsreserved)
			{
				/*$query="UPDATE 
							stock 
						SET
							unitsreserved=(unitsreserved-$unitstomove) 
						WHERE 
							pkstockid='$stockid'
						";*/
				
				//$AdminDAO->queryresult($query);
				if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
							$fields		=	array('unitsreserved');
							$values		=	array("unitsreserved-$unitstomove");
							$table		=	"stock";
			
							$AdminDAO->updaterow($table,$fields,$values,"pkstockid='$stockid'");			
				}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
						$tblj	= 	'stock';
						$field	=	array('unitsreserved');
						$value	=	array('(unitsreserved-$unitstomove)');
						
						$AdminDAO->updaterow($tblj,$field,$value,"pkstockid='$stockid'");	
				}//end edit
			}
			$moved=$moved+$unitstomove;
			
			if(fullfillstockid!='' && $unitstomove>0)
			{
				
				
			$pkdemandfulfillmentid	=	$AdminDAO->pkey('demandfulfilment','pkdemandfulfilmentid');
			/*$query="INSERT INTO 
							demandfulfilment 
						SET
							pkdemandfulfilmentid	=	'$pkdemandfulfillmentid',
							fkdemanddetailid='$pkdemanddetailid' ,
							unitssent		='$unitstomove',
							sendingdate	=	".time().",
							fkstockid	= '$fullfillstockid',
							fkemployeeid='$empid',
							increasedprice='0',
							demandstatus='p'
						";
				//$AdminDAO->queryresult($query);*/
				if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
							
							$fields		=	array('pkdemandfulfilmentid','fkdemanddetailid','unitssent','sendingdate','fkstockid','fkemployeeid','increasedprice','demandstatus');
							$values		=	array($pkdemandfulfillmentid,$pkdemanddetailid,$unitstomove,time(),$fullfillstockid,$empid,0,p);
				
							$insertid 	=	$AdminDAO->insertrow('demandfulfilment',$fields,$values);		
							/*$query="UPDATE 
										stock 
									SET
										unitsremaining=(unitsremaining-$unitstomove) 
									WHERE 
										pkstockid='$stockid'
									";
							
							//$AdminDAO->queryresult($query);*/
							
							$fields		=	array('unitsremaining');
							$values		=	array("unitsremaining-$unitstomove");
							$table		=	"stock";
						
							$AdminDAO->updaterow($table,$fields,$values,"pkstockid='$stockid'");				
				}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
						$sendingdate	=	time();	
						$tblj	= 	'demandfulfilment';
						$field	=	array('pkdemandfulfilmentid,fkdemanddetailid,unitssent,sendingdate,fkstockid,fkemployeeid,increasedprice,demandstatus');
						$value	=	array($pkdemandfulfillmentid,$pkdemanddetailid,$unitstomove,$sendingdate,$fullfillstockid,$empid,'0','p');
						$AdminDAO->insertrow($tblj,$field,$value);
							/*$query="UPDATE stock SET unitsremaining=(unitsremaining-$unitstomove) WHERE pkstockid='$stockid'";
							$AdminDAO->queryresult($query);*/
			
						$tblj	= 	'stock';
						$field	=	array('unitsremaining');
						$value	=	array('(unitsremaining-$unitstomove)');
						
						$AdminDAO->updaterow($tblj,$field,$value,"pkstockid='$stockid'");	
				}//end edit
			}
			$sql="select sum(unitssent) as totalunits from demandfulfilment where fkdemanddetailid='$pkdemanddetailid' ";
			$demarray	=	$AdminDAO->queryresult($sql);
			$totalunits	=	$demarray[0]['totalunits'];
			if($totalunits==$demandunits)
			{
				$status='c';	
			}
			else
			{
				$status='p';	
			}
			$sql="select fkdemandid from demanddetails where pkdemanddetailid='$pkdemanddetailid' ";	
			$demarray	=	$AdminDAO->queryresult($sql);
			$demandid	=	$demarray[0]['fkdemandid'];
			
				
				if($totalunits==$demandunits)
				{
					/*$query="UPDATE 
								demand 
							SET
								demandstatus ='f' 
							WHERE 
								pkdemandid='$demandid'
							";
					
					//$AdminDAO->queryresult($query);*/
					if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
										
						$fields		=	array('demandstatus');
						$values		=	array('f');
						$table		=	"demand";
					
						$AdminDAO->updaterow($table,$fields,$values,"pkdemandid='$demandid'");
						
										/*$query="UPDATE 
													demandfulfilment 
												SET
													demandstatus ='c' 
												WHERE 
													fkdemanddetailid='$pkdemanddetailid'
												";
										//$AdminDAO->queryresult($query);*/
					
						$fields		=	array('demandstatus');
						$values		=	array('c');
						$table		=	"demandfulfilment";
					
						$AdminDAO->updaterow($table,$fields,$values,"fkdemanddetailid='$pkdemanddetailid'");
					}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
							$tblj	= 	'demand';
							$field	=	array('demandstatus');
							$value	=	array('f');
							
							$AdminDAO->updaterow($tblj,$field,$value,"pkdemandid='$demandid'");	
									
							$tblj	= 	'demandfulfilment';
							$field	=	array('demandstatus');
							$value	=	array('c');
							
							$AdminDAO->updaterow($tblj,$field,$value,"fkdemanddetailid='$pkdemanddetailid'");						
					}//end edit
						
				}
		}//end of if
	}//end of for
if($flag==0)
	echo "You did not move any units.";
	exit;
}//end of post
?>