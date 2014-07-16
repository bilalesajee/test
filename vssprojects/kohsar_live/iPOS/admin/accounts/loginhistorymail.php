<?php ob_start();
error_reporting(-1); 
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
$date=time();
 $date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
 
$from = "Kohsar SHOP System <kohsar@esajee.com>";

//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";

//$to = "fahadbuttqau@gmail.com,accounts@esajee.com,hesajee@gmail.com";
//$to = "hasnain@esajeeusa.com";
$to=$tomailaddress__owner;
$subject = "Esajee Employ Login History on $date ";
 $query = "select * from main.loginhistory where logouttime > 0 and fkaddressbookid!=1888 and FROM_UNIXTIME(logintime,'%d-%m-%Y')='$date' order by logintime asc";
$reportresult = $AdminDAO->queryresult($query);
$row_run=count($reportresult);

$table="<link rel='stylesheet' type='text/css' href='https://kohsar.esajee.com/includes/css/style.css' />
<div align='left'>
<div > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />
<b>Think globally shop locally</b> <br /></span> </div>";
$table .='
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
 

 
 <tr>
    <th height="50" colspan="16" align="center" style="font-size:14px; border:none " bgcolor="#FFFFFF" ><span style="font-size:24px; ">Employ Login History</th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="right" style="font-size:14px; border:none " bgcolor="#FFFFFF" ></th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >Date:'.$date.'</th>
  </tr>';
 $table .=' <tr>
    <th width="235" height="31" bgcolor="#C0944B" >Location</th>
    <th width="204" bgcolor="#C0944B"  align="left">Employ Name</th>
    <th width="275" bgcolor="#C0944B" >Login Time</th>
    <th width="275" bgcolor="#C0944B" >Logout Time</th>
     </tr>';
  
for($i=0;$i<$row_run;$i++)
{
	if($reportresult[$i]["loc"]==3){
	$loc="Kohsar";
	
	}else if($reportresult[$i]["loc"]==4){
	
	$loc="Warehouse";
	
	}else if($reportresult[$i]["loc"]==2){
	$loc="Gulberg";
	
	}else if($reportresult[$i]["loc"]==1){
	}else{
	$loc="Pharma";	
		}

		$table .='<tr>
   <td width="235" align="center"> '.$loc.'</td>
  
    <td align="left"> '.$reportresult[$i]["username"].'</td>
    <td width="275" align="left">'.date('g:i a',$reportresult[$i]["logintime"]).'</td>
	<td width="275" align="right">'.date('g:i a',$reportresult[$i]["logouttime"]).'</td>
		 </tr>';
}

$table.='</table>';
 $body = $table;
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
file_get_contents("http://smsserver.esajee.com/daily_sms_chk.php");
//file_get_contents("http://main.esajee.com/admin/accounts/daily_sale_report.php?email=1");
file_get_contents("https://kohsar.esajee.com/admin/accounts/CheckclosingsSent.php");

?>