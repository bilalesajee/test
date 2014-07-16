<?php
//error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$empid		=	$_SESSION['addressbookid'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/

if(sizeof($_POST)>0)
{
	
$arrdata=json_encode($_POST);	
	?>
<script>
jQuery("#stockdetailsdiv").load('addpurchase_return_inv.php?check_button=1&stdata=<?php echo urlencode($arrdata)?>');
</script>
<?php
exit;
	
	$addressbookid		=	$_SESSION['addressbookid'];
	$employeeids		=	$AdminDAO->getrows("employee","*","fkaddressbookid = '$addressbookid'");
	$employeeid			=	$employeeids[0]['pkemployeeid'];
	$storeid			=	$_SESSION['storeid'];
	$returntype			=	$_POST['returntype'];
	$return				=	$_POST['return'];
	$stockid			= 	$_POST['stockid'];
	$totalrecs			=	$_POST['totalrecs'];
	$returnstatus		=	$_POST['returnstatus'];
	$fkinvid		    =	$_POST['fkinvid'];
	$fkbarid		    =	$_POST['fkbarid'];
	$srcid		        =	$_POST['srcid'];
	$sid		        =	$_POST['supid'];
	// this is the add section	
	$fields	=	array("fkstockid","quantity","fkstoreid","fkemployeeid","returnstatus","returndate","fkreturntypeid");	
	for($i=0;$i<$totalrecs;$i++)
	{
		$returntyp	=	$returntype[$i];
		$returnamt	=	$return[$i];
		if($returnamt	== 0 || $returnamt == '')
		{
			continue;
		}
		$stock		=	$stockid[$i];
		$status		=	$returnstatus[$i];
		$data		=	array($stock,$returnamt,$storeid,$employeeid,$status,time(),$returntyp);
		$AdminDAO->insertrow("$dbname_detail.returns",$fields,$data);
		//adjusting stocks
				
	$fields		=	array('unitsremaining');
	$values		=	array("unitsremaining-$returnamt");
	$table		=	"$dbname_detail.stock";
if($srcid[$i]==0){
   	echo "Stock With SUPPLIER $sid[$i] and INVOICE ID $fkinvid[$i] has been Returned"; 
    exit;
}

if($_REQUEST['check_button']==2){
 ///////////////////////////////////////////////////////////////////////////////
if($srcid[$i]==4){
///////////////////////////////////////////////////////////////Returning items to source//////////////////////////////////////
   $src_return_link= file_get_contents("https://warehouse.esajee.com/admin/estore_return.php?return_quantity={$returnamt}&Invceid={$fkinvid[$i]}&barcodeid={$fkbarid[$i]}&supid={$sid[$i]}&stid={$stock}&srid=3");	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
echo "Stock With SUPPLIER $sid[$i] and INVOICE ID $fkinvid[$i] Returned To Warehouse";
	//exit;
	}

if($srcid[$i]==1){
///////////////////////////////////////////////////////////////Returning items to source//////////////////////////////////////
 $src_return_link= file_get_contents("https://dha.esajee.com/admin/estore_return.php?return_quantity={$returnamt}&Invceid={$fkinvid[$i]}&barcodeid={$fkbarid[$i]}&supid={$sid[$i]}&stid={$stock}&srid=3");	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
echo "Stock With SUPPLIER $sid[$i] and INVOICE ID $fkinvid[$i] Returned To DHA";
$stockquery	=	"UPDATE $dbname_detail.stock SET unitsremaining=unitsremaining-$returnamt WHERE pkstockid='$stock'";
$AdminDAO->queryresult($stockquery);

	//exit;
	}

if($srcid[$i]==2){
///////////////////////////////////////////////////////////////Returning items to source//////////////////////////////////////
   $src_return_link= file_get_contents("https://gulberg.esajee.com/admin/estore_return.php?return_quantity={$returnamt}&Invceid={$fkinvid[$i]}&barcodeid={$fkbarid[$i]}&supid={$sid[$i]}&stid={$stock}&srid=3");	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	echo "Stock With SUPPLIER $sid[$i] and INVOICE ID $fkinvid[$i] Returned To Gulberg";
	$stockquery	=	"UPDATE $dbname_detail.stock SET unitsremaining=unitsremaining-$returnamt WHERE pkstockid='$stock'";
    $AdminDAO->queryresult($stockquery);
	//exit;
	}
if($srcid[$i]==5){
///////////////////////////////////////////////////////////////Returning items to source//////////////////////////////////////
   $src_return_link= file_get_contents("https://pharmadha.esajee.com/admin/estore_return.php?return_quantity={$returnamt}&Invceid={$fkinvid[$i]}&barcodeid={$fkbarid[$i]}&supid={$sid[$i]}&stid={$stock}&srid=3");	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	echo "Stock With SUPPLIER $sid[$i] and INVOICE ID $fkinvid[$i] Returned To Pharma";
	$stockquery	=	"UPDATE $dbname_detail.stock SET unitsremaining=unitsremaining-$returnamt WHERE pkstockid='$stock'";
    $AdminDAO->queryresult($stockquery);
	//exit;
	}

}
if($srcid[$i]==3){
$sTore=1;
}
	 }
	if($sTore==1){ 
	 $rdate=date('d-m-Y');
   //$checkdata=$invoice_return_link= file_get_contents($Url_admin."invoice_return&return={$ramount}&fksupplierid={$sid}&fksupplierinvoiceid={$sinvid}&adddate={$rdate}&location=0");
  
	}
	
exit;
}// end post
?>