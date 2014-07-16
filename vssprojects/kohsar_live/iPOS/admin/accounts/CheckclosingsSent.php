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
	global $AdminDAO;
	$from = "Esajee Solutions <kohsar@esajee.com>";
	$to = "notify@esajeesolutions.com";
	$subject = "Kohsar Closing Not Sent To Accounts System";

$query_supplieri2 = "SELECT * from $dbname_detail.`closinginfo` where `accdatasent`=0 and `closingstatus`='a' ";
$reportresult = $AdminDAO->queryresult($query_supplieri2);
$row_s=count($reportresult);
if($row_s > 0){
	for($i=0;$i<$row_s;$i++)
		{
		
$clarr[]=$reportresult[$i]['pkclosingid'];
		}

$cidz=implode(',',$clarr);
$body="Closing Idz  ".$cidz." Are Not Send To Accounts";
$headers= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
mail($to,$subject,$body,$headers);

}
file_get_contents("https://pharmadha.esajee.com/admin/accounts/CheckclosingsSent.php");
file_get_contents("https://gulberg.esajee.com/admin/accounts/CheckclosingsSent.php");
file_get_contents("https://dha.esajee.com/admin/accounts/CheckclosingsSent.php");
?>