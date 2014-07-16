<?php
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
global $AdminDAO;
$empid		=	$_SESSION['addressbookid'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;
echo "Under Process, will back to you soon";
exit;
*/if(sizeof($_POST)>0)
{
	$sTore=0;
	$addressbookid		=	$_SESSION['addressbookid'];
	$employeeids		=	$AdminDAO->getrows("employee","*","fkaddressbookid = '$addressbookid'");
	$employeeid			=	$employeeids[0]['pkemployeeid'];
    $storeid			=	$_SESSION['storeid'];
	$returntype			=	$_POST['returntype'];
	$return				=	$_POST['return'];
	$stockid			= 	$_POST['stockid'];
	$totalrecs			=	$_POST['totalrecs'];
	$returnstatus		=	$_POST['returnstatus'];
	$units_rem		    =	$_POST['urem'];

	$fkbarid		    =	$_POST['fkbarid'];
	
	$orginalquantity		    =	$_POST['orgquantity'];
	$orignalprice		    =	$_POST['orgprice'];
	
	$prcinrs		        =	$_POST['prc'];
	$sinvid		        =	$_POST['id'];
	$start_datee				=	strtotime($_POST['datetime']); 
	$flag_not_insert=0;
	$ramount=0;
//echo var_dump($return)."this is the add section";	
	$fields	=	array("fkstockid","quantity","price","addby","addtime","issclose","fksupplierinvoiceid","adjustmenttime","orignalquantity","orignalrate");

if($_REQUEST['action']==1){
	
	
	
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		$unitrem=$units_rem[$i];
		$prce=$prcinrs[$i];
	
		$stock		=	$stockid[$i];
		$status		=	$returnstatus[$i];
		 $orignalprc=   $orignalprice[$i];
		 $orginalqty=   $orginalquantity[$i];
		   
		$data		=	array($stock,$returnamt,$prce,$employeeid,time(),'0',$sinvid,$start_datee,$orginalqty,$orignalprc);
	    $checkduplicate		=	$AdminDAO->getrows("$dbname_detail.adjustment","*","fkstockid='$stock' and quantity='$returnamt' and issclose=0");
       $ddid			    =	   $checkduplicate[0]['pkadjustmentid'];
       if($ddid==''){
	   if($orginalqty!=$returnamt or $orignalprc!=$prce){
	   $AdminDAO->insertrow("$dbname_detail.adjustment",$fields,$data);
	   }
	   }

      }
	 
	 
	}else{

	
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		$unitrem=$units_rem[$i];
		$prce=$prcinrs[$i];
		$stock		=	$stockid[$i];
		$status		=	$returnstatus[$i];
		$orignalprc=   $orignalprice[$i];
		 $orginalqty=   $orginalquantity[$i];
		   
		$data		=	array($stock,$returnamt,$prce,$employeeid,time(),'1',$sinvid,$start_datee,$orginalqty,$orignalprc);
	 
	 $checkduplicate		=	$AdminDAO->getrows("$dbname_detail.adjustment","*","fkstockid='$stock' and quantity='$returnamt'");
	   $ddid			    =	   $checkduplicate[0]['pkadjustmentid'];
       if($ddid==''){
		    if($orginalqty!=$returnamt or $orignalprc!=$prce){
		$AdminDAO->insertrow("$dbname_detail.adjustment",$fields,$data);
			}
	   }else{
		 $fields_ur		=	array('issclose','edittime','editby');
	     $values_ur		=	array(1,time(),$employeeid);
	     $tableu		=	"$dbname_detail.adjustment";
	    $AdminDAO->updaterow($tableu,$fields_ur,$values_ur,"fkstockid='$stock' and quantity='$returnamt'");		  
		   }

  if($orginalqty!=$returnamt or $orignalprc!=$prce){

	 $query2="select quantity from  $dbname_detail.stock where pkstockid='$stock' ";

	 $reportresult2 = $AdminDAO->queryresult($query2);

	 $quan=($reportresult2[0]['quantity']);	
 
      if($quan>$returnamt){
 $vbal=$quan-$returnamt;
		//adjusting stocks
	 $stockquery	=	"UPDATE $dbname_detail.stock SET quantity=$returnamt,unitsremaining=(unitsremaining-$vbal),priceinrs=$prce,purchaseprice=$prce,costprice=$prce WHERE pkstockid='$stock'";
		$AdminDAO->queryresult($stockquery);
	  }else{
		  $vbal=$returnamt-$quan;
$stockquery	=	"UPDATE $dbname_detail.stock SET quantity=$returnamt,unitsremaining=(unitsremaining+$vbal),priceinrs=$prce,purchaseprice=$prce,costprice=$prce WHERE pkstockid='$stock'";
		$AdminDAO->queryresult($stockquery);
		  
		  
		  }
		
  }
	 ///////////////////////////////////////////////////////////////////////////////
	 }
	
	
		}
		exit;
}// end post
?>