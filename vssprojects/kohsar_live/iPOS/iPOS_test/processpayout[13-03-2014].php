<?php 
include_once("includes/security/adminsecurity.php");
include_once("includes/bc/barcode.php");
global $AdminDAO;
$payoutid			=	$_REQUEST['id'];
//$duplicatepayout	=	$_REQUEST['duplicatepayout'];
if($payoutid!='')
{
	genBarCode($payoutid,'pay.png');
	?>
	<script language="javascript" type="text/javascript">
		function printpayoutbillduplicate(text)
		{
		var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
		window.open('generatepayoutbill.php?text='+text,'Invice',display); 
		}
		printpayoutbillduplicate('<?php echo $payoutid;?>');
	</script>
	<?php
	exit;
}
if(sizeof($_POST)>0)
{
	$paymentmethod		=	$_REQUEST['paymentmethod'];
	$amount				=	$_REQUEST['amount'];
	$acid				=	$_REQUEST['accountid'];
	$bank				=	$_REQUEST['bank'];
	$chequenumber		=	$_REQUEST['chequenumber'];
	$chequedate			=	$_REQUEST['chequedate'];
	$chequedate			=	strtotime($chequedate);
	if($acid=='')
	{
		exit;
	}
	// added by Yasir -- 07-07-11
	if($paymentmethod=='c')
	{
		$breakmode				=	$_SESSION['breakmode'];
		if($breakmode==1)
		{
			print"break";
			exit;
		}	
	}
	//
	$paydesc			=	filter($_REQUEST['description']);
	//$accountarray		=	$AdminDAO->getrows("$dbname_detail.account,$dbname_detail.addressbook","CONCAT(firstname, ' ', lastname) as accounttitle,accountlimit", "pkaddressbookid = fkaddressbookid AND id= '$acid'"); 
	$accountarray		=	$AdminDAO->getrows("$dbname_detail.account","title as accounttitle,accountlimit", "id= '$acid'");  // added ,accountlimit by Yasir - 07-07-11 . removed ,$dbname_main.accountpayment
	// added by Yasir - 07-07-11	
	$acclimit			=  	$accountarray[0]['accountlimit'];
	if ($amount > $acclimit){
		$msg	=	'limit';
		//exit;
	}
	//
	//$countername		=	gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$paydate			=	time();
	$fields				=	array("amount","description","countername","paymentdate","fkemployeeid","fkclosingid","fkaccountid","fkstoreid","bankid","chequeno","chequedate","paymentmethod");
	if(!isset($closingsession) || $closingsession=='' || $closingsession==0)
	{
		// this is where we start the closing process
		echo 1;
		exit;
	}
	$values				=	array($amount,$paydesc,$countername,$paydate,$empid,$closingsession,$acid,$storeid,$bank,$chequenumber,$chequedate,$paymentmethod);
	$insertid			=	$AdminDAO->insertrow("$dbname_detail.accountpayment",$fields,$values);	
	if ($paymentmethod	==	'c'){
	  //add accounts entry
	  $AdminDAO->posttransaction($acid,$insertid,$amount,$cashacc,$insertid,$amount,"Cash Payout");
	} else {
	 //add accounts entry
	  $AdminDAO->posttransaction($acid,$insertid,$amount,$machinebankacc,$insertid,$amount,"Cheque Payout");
	}
	$accountname		=	$accountarray[0][accounttitle]."($acid)";
	// get total payouts of the closing and email if exceeds limit by Yasir 28-07-11		
	/*$totalpayouts_array	=	$AdminDAO->getrows("$dbname_detail.accountpayment","SUM(amount) as amount", "fkaccountid= '$acid' AND fkclosingid = '$closingsession'");
	$totalpayouts		=	$totalpayouts_array[0]['amount'];
	if ($totalpayouts > $acclimit){
	file_get_contents("http://smk.esajee.com/sendpayoutemail.php?counter=$countername&closingsession=$closingsession&empid=$empid&acclimit=$acclimit&accountname=".urlencode($accountname)."&totalpayouts=$totalpayouts&amount=$amount");
	}	*/
	//
	genBarCode($insertid,'pay.png');
	$paydesc			=	html_entity_decode($paydesc,ENT_QUOTES);
	//echo "$accountname:$amount:$paydesc";
	if($msg)
	{
		echo $insertid."_".$msg;
	}
	else
	echo $insertid;
	exit;
}
?>