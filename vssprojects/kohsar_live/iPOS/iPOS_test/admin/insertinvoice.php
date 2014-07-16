<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$qs;
$barcode	=	$_POST['barcode']; 
$barcodeid	=	$_POST['barcodeid'];
if($barcodeid=='')
{
	
	 $barcodeid	=	$_POST['productname'];
}

$units		=	$_POST['units']; 
$unitprice	=	$_POST['unitprice']; 
$totalprice	=	$_POST['totalprice']; 
$expiry		=	strtotime($_POST['expiry']); 
$origin		=	$_POST['origin'];
$boxno		=	$_POST['boxno'];
$invoiceid	=	$_POST['invoiceid'];
$pkinvoicepackagingid	=	$_POST['pkinvoicepackagingid'];
$err="";
if($barcode=='')
{
	$err="<li>Barcode is left Blank.</li>";	
}
if($units=='')
{
	$err.="<li>Units is left Blank.</li>";	
}

if($unitprice=='')
{
	$err.="<li>Unit Price is left Blank.</li>";	
}

if($totalprice=='')
{
	$err.="<li>Total Price is left Blank.</li>";	
}

if($expiry=='')
{
	$err.="<li>Expiry is left Blank.</li>";	
}
if($origin=='')
{
	$err.="<li>Origin is left Blank.</li>";	
}
if($boxno=='')
{
	$err.="<li>Box No is left Blank.</li>";	
}
if($err!=='')
{
	echo $err;
	exit;
}
if($pkinvoicepackagingid=='')
 {
/*  $sql="INSERT 
				INTO 
					invoicespackaging
				SET
					pkinvoicepackagingid='".$AdminDAO->pkey('invoicespackaging','pkinvoicepackagingid')."',
					barcode='$barcode',
					fkbarcodeid='$barcodeid',
					units='$units',
					unitprice='$unitprice',
					totalprice='$totalprice',
					expiry='$expiry',
					origin='$origin',
					boxno='$boxno',
					fkinvoiceid='$invoiceid'
				";*/
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		$pkinpackid	=	$AdminDAO->pkey('invoicespackaging','pkinvoicepackagingid');			
		$fields		=	array('pkinvoicepackagingid','barcode','fkbarcodeid','units','unitprice','totalprice','expiry','origin','boxno','fkinvoiceid');
		$values		=	array($pkinpackid,$barcode,$barcodeid,$units,$unitprice,$totalprice,$expiry,$origin,$boxno,$invoiceid);
		$table		=	"invoicespackaging";
	
		$AdminDAO->insertrow($table,$fields,$values);	
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
			$idj	=	$AdminDAO->pkey('invoicespackaging','pkinvoicepackagingid');		
			$tblj	= 	'invoicespackaging';
			$field	=	array('pkinvoicepackagingid','barcode','fkbarcodeid','units','unitprice','totalprice','expiry','origin','boxno','fkinvoiceid');
			$value	=	array($idj,$barcode,$barcodeid,$units,$unitprice,$totalprice,$expiry,$origin,$boxno,$invoiceid);
			
			$AdminDAO->insertrow($tblj,$field,$value);						
	}//end edit
 }
 else
 {
	 /*$sql="	UPDATE 
					invoicespackaging
				SET
					barcode='$barcode',
					fkbarcodeid='$barcodeid',
					units='$units',
					unitprice='$unitprice',
					totalprice='$totalprice',
					expiry='$expiry',
					origin='$origin',
					boxno='$boxno'
				WHERE
					pkinvoicepackagingid='$pkinvoicepackagingid'
					";*/
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		$fields		=	array('barcode','fkbarcodeid','units','unitprice','totalprice','expiry','origin','boxno');
		$values		=	array($barcode,$barcodeid,$units,$unitprice,$totalprice,$expiry,$origin,$boxno);
		$table		=	"invoicespackaging";
	
		$AdminDAO->updaterow($table,$fields,$values,"pkinvoicepackagingid='$pkinvoicepackagingid'");							
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
			$tblj	= 	'invoicespackaging';
			$field	=	array('barcode','fkbarcodeid','units','unitprice','totalprice','expiry','origin','boxno');
			$value	=	array($barcode,$barcodeid,$units,$unitprice,$totalprice,$expiry,$origin,$boxno);
			
			$AdminDAO->updaterow($tblj,$field,$value,"pkinvoicepackagingid='$pkinvoicepackagingid'");						
	}//end edit
 }
//$AdminDAO->queryresult($sql);
?>
