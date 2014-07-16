<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
/*
payment method
1=cash
2=cc
3=fc
4=cheque
*/
if(sizeof($_POST)>0)
{
	$customerid		=	$_POST['customerid'];
	$olddatearr		=	$_POST['olddate'];
	//print_r($_POST);
	if(count($olddatearr)>0)
	{
		for($d=0;$d<count($olddatearr);$d++)
		{
			//echo $val;
			
			$paymentmode		=	$_POST['paymentmode'][$d];
			$olddate			=	$_POST['olddate'][$d];
			$trdatetime			=	$_POST['trdatetime'][$d];
			if($paymentmode==1)
			{
				$table="cashpayment";
			}
			elseif($paymentmode==2)
			{
				$table="ccpayment";
			}
			elseif($paymentmode==3)
			{
				$table="fcpayment";
			}
			elseif($paymentmode==4)
			{
				$table="chequepayment";
			}
			$datearr	=	explode('-',$trdatetime);
			$datearr	=	array_reverse($datearr);
			$trdatetime	=	implode('-',$datearr);
			$datestr	=	strtotime($trdatetime);
				$olddate			=	explode("-",$olddate);
				$day				=	$olddate[0];
				$mon				=	$olddate[1];
				$yr					=	$olddate[2];
				$olddate				=	mktime(23,59,59,$mon,$day,$yr);
				$olddate2				=	mktime(00,00,00,$mon,$day,$yr);
				$sql="select * from $dbname_detail.$table t,$dbname_detail.sale s where t.paymenttype='c' and s.pksaleid=t.fksaleid and s.fkcustomerid='$customerid' and paytime between '$olddate2' and '$olddate'";
				$resarr	=	$AdminDAO->queryresult($sql);
				$indexnameid	=	'pk'.$table.'id';
				for($i=0;$i<count($resarr);$i++)
				{
					$trid	=	$resarr[$i][$indexnameid];
					//$sql="update $dbname_detail.$table set paytime='$datestr' where $indexnameid='$trid'";
					//$AdminDAO->queryresult($sql);
					
	$fields		=	array('paytime');
	$values		=	array($datestr);
	$table		=	"$dbname_detail.$table";

	$AdminDAO->updaterow($table,$fields,$values,"$indexnameid='$trid'");
						
					//print"<br>";
				}
		//	$c++;
		}//for
	}//if
}
?>