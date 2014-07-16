<?php
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
global $AdminDAO;
$empid		=	$_SESSION['addressbookid'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
//echo "Under Process, will back to you soon";
//exit;
if(sizeof($_POST)>0)
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
	$fkinvid		    =	$_POST['fkinvid'];
	$fkbarid		    =	$_POST['fkbarid'];
	$srcid		        =	$_POST['srcid'];
	$sinvid		        =	$_POST['id'];
	$flag_not_insert=0;
	$ramount=0;
//echo var_dump($return)."this is the add section";	
	$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","returnstatus","returndate","fkreturntypeid","issclose","fkinvid");

if($_REQUEST['action']==1){
	
	
	
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		$unitrem=$units_rem[$i];
		if($returnamt	== 0 || $returnamt == '')
		{
			continue;
		}
		if($returnamt>$unitrem){
			echo "Enterd Quantity".$returnamt." is greater than units remaining";
			echo "<br>";
			$flag_not_insert=1;
			continue;
			}
	}
	if($flag_not_insert==0){
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		$unitrem=$units_rem[$i];
		if($returnamt	== 0 || $returnamt == '')
		{
			continue;
		}
		
		$stock		=	$stockid[$i];
		$status		=	$returnstatus[$i];
		$fkinvidd   =   $fkinvid[$i];
		$data		=	array($stock,$returnamt,$storeid,$employeeid,$status,time(),$returntyp,'0',$fkinvidd);
	
	   $checkduplicate		=	$AdminDAO->getrows("$dbname_detail.returns","*","fkstockid='$stock' and quantity='$returnamt' and issclose=0");
	   $ddid			    =	   $checkduplicate[0]['pkreturnid'];
       if($ddid==''){
	   $AdminDAO->insertrow("$dbname_detail.returns",$fields,$data);
	   }

      }
	 
	 
	
	}

	}else{

	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		$unitrem=$units_rem[$i];
		if($returnamt	== 0 || $returnamt == '')
		{
			continue;
		}
		
		if($returnamt>$unitrem){
			echo "Enterd Quantity".$returnamt." is greater than units remaining";
			echo "<br>";
			$flag_not_insert=1;
			continue;
			}
	}
	if($flag_not_insert==0){
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		$unitrem=$units_rem[$i];
		if($returnamt	== 0 || $returnamt == '')
		{
			continue;
		}
        if($returnstatus[$i]=='p')
		{
			echo "Status Must Be Confirm Not Pending";
			echo "<br>";
			continue;
		}
		$stock		=	$stockid[$i];
		$status		=	$returnstatus[$i];
		$fkinvidd   =   $fkinvid[$i];
		$data		=	array($stock,$returnamt,$storeid,$employeeid,$status,time(),$returntyp,'1',$fkinvidd);
		 $checkduplicate		=	$AdminDAO->getrows("$dbname_detail.returns","*","fkstockid='$stock' and quantity='$returnamt' and issclose=0");
	   $ddid			    =	   $checkduplicate[0]['pkreturnid'];
       if($ddid==''){
		$AdminDAO->insertrow("$dbname_detail.returns",$fields,$data);
	   }else{
		 $fields_ur		=	array('issclose');
	     $values_ur		=	array(1);
	     $tableu		=	"$dbname_detail.returns";
	     $AdminDAO->updaterow($tableu,$fields_ur,$values_ur,"fkstockid='$stock' and quantity='$returnamt' and issclose=0");		  
		   }
		//adjusting stocks
		//$stockquery	=	"UPDATE $dbname_detail.stock SET unitsremaining=unitsremaining-$returnamt WHERE pkstockid='$stock'";
		//$AdminDAO->queryresult($stockquery);
		
	$fields_r		=	array('unitsremaining');
	$values_r		=	array($unitrem-$returnamt);
	$table		=	"$dbname_detail.stock";
	$oldres		=	$AdminDAO->updaterow($table,$fields_r,$values_r,"pkstockid='$stock'");		
	///////////////////////////////////////////////////////////Kalling accounts link for invoice return///////////////
     $query="select priceinrs  as invoice_value from  $dbname_detail.stock 	 where pkstockid='$stock' ";
	 $reportresult = $AdminDAO->queryresult($query);
     $ramount+=($reportresult[0]['invoice_value']*$returnamt);

	 $query2="select fksupplierid from  $dbname_detail.supplierinvoice where pksupplierinvoiceid='$sinvid' ";

	 $reportresult2 = $AdminDAO->queryresult($query2);

	 $sid=($reportresult2[0]['fksupplierid']);	
	 ///////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////STOCK MONITOR////////////////////////////////////////////////////////////////////////////////////////////////////

/*$querygm = "INSERT INTO $dbname_detail.stockmonitor ( trans_id,quantity,price,fkbarcodeid ,fksupplierid ,fkstoreid , fkemployeeid ,brand_id ,updatetime ,
  product_id ,  fksupplierinvid,addtime,type,svalue ) SELECT  pkstockid ,'$returnamt',  priceinrs ,fkbarcodeid  ,fksupplierid ,fkstoreid  ,  fkemployeeid ,
  fkbrandid , addtime , fkproduct_id , fksupplierinvoiceid,'".time()."','pnr',(priceinrs*'$returnamt')  FROM    $dbname_detail.stock where pkstockid='$stock'";
  $reportresultg = $AdminDAO->queryresult($querygm);
*/
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	 
if($srcid[$i]==4){
///////////////////////////////////////////////////////////////Returning items to source//////////////////////////////////////
  $src_return_link= file_get_contents("https://warehouse.esajee.com/admin/kohsar_return.php?return_quantity={$returnamt}&Invceid={$fkinvid[$i]}&barcodeid={$fkbarid[$i]}&supid={$sid}");	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	//exit;
	}
if($srcid[$i]==3){
$sTore=1;
}
	 }
	if($sTore==1){ 
	 $rdate=date('d-m-Y');
   $checkdata=$invoice_return_link= file_get_contents($Url_admin."invoice_return&return={$ramount}&fksupplierid={$sid}&fksupplierinvoiceid={$sinvid}&adddate={$rdate}&location=0");
 
		    $addressbookid= 1888;
            $fkemployeeid=1887;
		 
		 if($checkdata>0){
         mysql_query("update  $dbname_detail.returns set accdatasent = 1  where  fkinvid = '$sinvid' ");
		  $sub = "Invoice # ($sinvid),Supplier Id ($sid) ,Amount ($ramount) is returned and sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
			 
	       $sub = "Invoice # ($sinvid),Supplier Id ($sid) ,Amount ($ramount) is returned and not sent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			 } 
		 
		 
	}
	}
	
		}
		exit;
}// end post
?>