<?php ob_start();
error_reporting(-1); 
session_start();
include("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;
$date=time();
$month1 =$_GET['month1'];
$year1 = $_GET['year1'];
$date = "".$month1."-".$year1." " ;
$queryrpy="select * from $dbname_detail.monthly_reports where searchdate='$date'";
$reslt=$AdminDAO->queryresult($queryrpy);
$row_r=count($reslt);?>
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<?php
if($row_r >0){
for($i=0;$i<$row_r;$i++)

{
echo "<br>";
echo stripslashes($reslt[$i]['body']);
echo "<br>";
} 
}else{
	
	echo "No Record Found";
	}
?>
</form>
<?php

//echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>