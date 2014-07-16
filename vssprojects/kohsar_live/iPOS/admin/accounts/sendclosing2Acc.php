<?php
session_start();
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$date	=	date('d-m-Y',(strtotime ( '-1 day') ));
$dbname_detail = 'main_kohsar';
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
try
{
$dbh = new PDO('mysql:host=localhost;dbname='.$dbname_detail, 'posapp', 'posapp');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
mail('fahadbuttqau@gmail.com','Failed connecting to Kohsar - Accounts - Daily Sale File',serialize($e));

exit;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$sql="SELECT pkclosingid from $dbname_detail.closinginfo where  closingstatus='a'  and `accdatasent`=0  ";
$stmt = $dbh->prepare($sql);
$ret = $stmt->execute();
$count = $stmt->rowCount();
if($count > 0){
$closingID=$stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($closingID as $clid){
if($clid['pkclosingid']!=NULL)	
file_get_contents("https://kohsar.esajee.com/admin/accounts/get_accclosing.php?clid=".$clid['pkclosingid']);
	
	}

}
file_get_contents("https://dha.esajee.com/admin/accounts/sendclosing2Acc.php");
file_get_contents("https://gulberg.esajee.com/admin/accounts/sendclosing2Acc.php");
file_get_contents("https://pharmadha.esajee.com/admin/accounts/sendclosing2Acc.php");

?>