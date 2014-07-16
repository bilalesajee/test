<?php ob_start();
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
global $AdminDAO, $Component;
$addressbookid= 1888;

$query_supplieri2 = "SELECT  st.fkemployeeid as empid,
 pksupplierinvoiceid,
 sp.companyname,
 si.billnumber,
 FROM_UNIXTIME(si.datetime,'%d-%m-%Y') addtime
   from $dbname_detail.stock st left join $dbname_detail.supplierinvoice si on (pksupplierinvoiceid=st.fksupplierinvoiceid) left join main.supplier sp on (si.fksupplierid=pksupplierid) where  si.invoice_status=0 GROUP BY pksupplierinvoiceid";
$reportresult = $AdminDAO->queryresult($query_supplieri2);

	for($i=0;$i<count($reportresult);$i++)
		{
		
		 $fkemployeeid=$reportresult[$i]["empid"];
		 $pksupplierinvoiceid=$reportresult[$i]["pksupplierinvoiceid"];
		 $companyname=$reportresult[$i]["companyname"];
		 $billnumber=$reportresult[$i]["billnumber"];
	     $addtime=$reportresult[$i]["addtime"];
	  
		  $sub = "Invoice # ($pksupplierinvoiceid ),Supplier Name ($companyname) ,bill no ($billnumber) and date ($addtime) status is  still open   ";
		  
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Invoice Status Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
	

		}

file_get_contents("https://warehouse.esajee.com/admin/accounts/invoices_open_status.php");
file_get_contents("https://kohsar.esajee.com/admin/accounts/reorder_level_check.php");
?>