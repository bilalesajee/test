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
global $AdminDAO, $Component;
//////////////////////////////////////////////////////////////////////////////////////
	  
/*$query	=	"SELECT SUM(s.totalamount) as totalamount, SUM(p.amount) as amount,IF(ad.firstname=NULL,ad.firstname,c.title ) as name
FROM  $dbname_detail.payments p
LEFT JOIN $dbname_detail.sale s ON s.pksaleid = p.fksaleid
LEFT JOIN $dbname_detail.account c ON ( c.id = s.fkaccountid )
LEFT JOIN $dbname_detail.addressbook ad  ON (c.fkaddressbookid = ad.pkaddressbookid)
WHERE  c.ctype = '2' group by s.fkaccountid ";*/

 $query	=	"SELECT FROM_UNIXTIME(paytime,'%d-%m-%Y') as paytime,sum(totalamount) as total_sale,fkaccountid,sum(amount) as total_payment FROM $dbname_detail.account,$dbname_detail.sale,$dbname_detail.payments where id=fkaccountid and ctype=2 and fksaleid=pksaleid and FROM_UNIXTIME(paytime,'%d-%m-%Y') <= '31-12-2012'  group by fkaccountid";


$reportresult		=	$AdminDAO->queryresult($query);

echo json_encode($reportresult);

?>