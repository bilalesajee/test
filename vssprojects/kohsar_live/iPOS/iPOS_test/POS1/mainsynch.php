<?php
include_once("includes/security/adminsecurity.php");
$sql="select * from main_stock.log where logstatus='p' order by pklogid ASC";
$logarr	=	$AdminDAO->queryresult($sql);
$count=0;
$conn	=	mysql_connect('192.168.1.2','root','');
mysql_select_db('main_stock');

for($i=0;$i<count($logarr);$i++)
{
	$pklogid=	$logarr[$i]['pklogid'];	
	$query	=	addslashes($logarr[$i]['query']);	
	$table	=	$logarr[$i]['table'];	
	$db		=	$logarr[$i]['db'];	
	$time	=	$logarr[$i]['time'];	
	
		

		if(mysql_query($query))
		{
			//$sqlupdate="UPDATE log set logstatus='u' where pklogid='$pklogid'";
			//mysql_query($sqlupdate);
		}
		print"Record ID: <b>$pklogid<b> is updated<br>";
		$count++;

}
mysql_close();

print"<hr><br>Total Records: $count";
//$AdminDAO->updaterow("attribute",$field,$value," pkattributeid ='$pos'");
?>