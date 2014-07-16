<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
if(sizeof($_POST)>0)
{
		$id				=	$_POST['id'];
		$customerid		=	$_POST['customerid'];
		$taxpercentage	=	$_POST['taxpercentage'];
		$serialno 		= 	filter($_POST['serialno']);
		$invoicedate	=	strtotime($_POST['invoicedate']);
		$fromdate		=	strtotime($_POST['fromdate']);
		$todate			=	strtotime($_POST['todate']);
		$saleids		=	$_POST['saleids'];
		if($serialno=='')
		{
			echo"Note Please enter serial not be left Blank.";
			exit;
		}
		if($serialno)
		{
				$unique = $AdminDAO->isunique("$dbname_detail.creditinvoices", 'pkcreditinvoiceid', $id, 'serialno', $serialno);
				if($unique=='1')
				{
						echo"Invoice with this Serial <b><u>$serialno</u></b> already exists. Please choose another serial.";	
						exit;
				}
		}
		
		if($customerid=='')
		{
			echo"Please select customer.";
			exit;
		}
		$addressbookid	=	$_SESSION['addressbookid'];
		
		$fields = array('serialno','datetime','invoicedate','fkaddressbookid','fkaccountid','taxpercentage','fromdate','todate');
		$values = array($serialno, time(),$invoicedate,$addressbookid, $customerid,$taxpercentage,$fromdate,$todate);

	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("$dbname_detail.creditinvoices",$fields,$values," pkcreditinvoiceid='$id' ");
		//$oldrecords	=	"UPDATE $dbname_detail.sale SET fkcreditinvoiceid='' WHERE fkcreditinvoiceid='$id'";
		//$oldres		=	$AdminDAO->queryresult($oldrecords);


	$fields		=	array('fkcreditinvoiceid');
	$values		=	array('');
	$table		=	"$dbname_detail.sale";

	$oldres		=	$AdminDAO->updaterow($table,$fields,$values,"fkcreditinvoiceid='$id'");
	
			
		if(count($saleids)>0)
		{
			foreach($saleids as $sid)
			{
				//$sql2="UPDATE $dbname_detail.sale set fkcreditinvoiceid='$id' where pksaleid='$sid'";
				//$AdminDAO->queryresult($sql2);
				
	$fields		=	array('fkcreditinvoiceid');
	$values		=	array($id);
	$table		=	"$dbname_detail.sale";

	$oldres		=	$AdminDAO->updaterow($table,$fields,$values,"pksaleid='$sid'");
					
			}
		}//if
	}
	else
	{
		// this is the add section	
		//print"Pakistan Zinda baad";
		$id 		= $AdminDAO->insertrow("$dbname_detail.creditinvoices",$fields,$values);
		if(count($saleids)>0)
		{
			foreach($saleids as $sid)
			{
				//$sql2="UPDATE $dbname_detail.sale set fkcreditinvoiceid='$id' where pksaleid='$sid'";
				//$AdminDAO->queryresult($sql2);
				
	$fields		=	array('fkcreditinvoiceid');
	$values		=	array($id);
	$table		=	"$dbname_detail.sale";

	$oldres		=	$AdminDAO->updaterow($table,$fields,$values,"pksaleid='$sid'");				
				
			}
		}//if
	}//end of else
	include_once("creditinvoicewritefile.php");
exit;
}// end post
?>