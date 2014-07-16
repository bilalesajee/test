<?php

session_start();

$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
	
     include("../../includes/security/adminsecurity.php");

	

     ob_start();



//include_once("../../includes/security/adminsecurity.php");

     global $AdminDAO;

	$invoice_no		=	$_REQUEST['invoice_no'];

	$supplier		=	preg_replace('/^(0)+/', '', $_REQUEST['supplier'] );

	$billnumber		=	$_REQUEST['bill_no'];
	$usid		=	$_REQUEST['uid'];

	$amount		=	(int)$_REQUEST['amount'];
	$accdatetime		=	(int)$_REQUEST['date'];
	$remarks		=	urldecode($_REQUEST['remarks']);
    $paymethod		=	urldecode($_REQUEST['payment_via']);
	//$datetime		=	time();
$addressbookid=1888;
    $fkemployeeid=1422;
	$fkemployeeid2=18;
	//$description	=	filter($_REQUEST['description']);

	$ref_id=$_REQUEST['payment_no'];

	$inv		=	$AdminDAO->getrows("$dbname_detail.supplierinvoice","paidamount","pksupplierinvoiceid='$invoice_no'");	

		$paidamount		=	$inv[0]['paidamount'];
	if(count($inv)==0)

	{

		if($supplier=='')

	{

		$msg.="<li>Please select supplier</li>";   

	}

	if($billnumber=='')

	{

		$msg.="<li>Please enter bill number</li>";    

	}

	if($msg)

	{

		echo $msg;

		exit;

	}

	$fields	=	array('fksupplierid','billnumber','datetime','refrance_id','paidamount','addby','description','payment_via');

	$values	=	array($supplier,$billnumber,$accdatetime,$ref_id,$amount,$usid,$remarks,$paymethod);

	echo $AdminDAO->insertrow("$dbname_detail.supplierinvoice",$fields,$values);
	
	////////////////////////////////////Get supplier/////////////////////////////////////////////////////////////////////////////////////////////////////////	  
                $query_inv="SELECT pksupplierinvoiceid FROM $dbname_detail.supplierinvoice WHERE 1 order by pksupplierinvoiceid desc ";
                $result_inv		=	$AdminDAO->queryresult($query_inv);
                $id__		=	$result_inv[0]['pksupplierinvoiceid'];
		
		        $sub = "Invoice # ($id__) is Added";
          
		       $field		=	array('message','subject','from_user','to_user','datetime');
               $value2		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid2,time());
		       $value		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid,time());
		       $AdminDAO->insertrow("$dbname_detail.messages",$field,$value2);
		       $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);

	

	}

	else

	{

		

	
	    $datetime		=	time();
		$total_amount =((int)$paidamount+$amount);	

				$fields22	=	array('paidamount','edittime','editby','fksupplierid','billnumber','datetime','refrance_id','description','payment_via');

	            $values22	=	array($total_amount,$datetime,$usid,$supplier,$billnumber,$accdatetime,$ref_id,$remarks,$paymethod);

		$AdminDAO->updaterow("$dbname_detail.supplierinvoice",$fields22,$values22,"pksupplierinvoiceid='$invoice_no'");
		
		      $sub = "Invoice # ($invoice_no)  is edited";
              $field		=	array('message','subject','from_user','to_user','datetime');
               $value2		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid2,time());
		       $value		=	array($sub,'Invoice ADD Alert',$addressbookid,$fkemployeeid,time());
		       $AdminDAO->insertrow("$dbname_detail.messages",$field,$value2);
		       $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);


		}

?>