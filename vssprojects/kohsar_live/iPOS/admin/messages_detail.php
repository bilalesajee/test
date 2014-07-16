<?php
include_once("../includes/security/adminsecurity.php");
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	include_once("dbgrid.php");
}//end edit
global $AdminDAO;
/*************************DATE CHECKS**************************/
/* $sdate				=	strtotime($_GET['sdate']); 
 $edate				=	strtotime($_GET['edate']);

 if($sdate != '' && $edate!='')
  {
   $cond = "  datetime  between '$sdate' and '$edate'"; 
  }
*/
$message_id = $_GET['message_id'];
$query = "select FROM_UNIXTIME(m.datetime,'%d-%m-%Y  %h:%i %p') as datetime,m.message_id,m.message,
				
				IF (m.status=1,'Read','Unread') as status,CONCAT(b.firstname ,', ', b.lastname) as from_user,CONCAT(bb.firstname ,', ', bb.lastname) as to_user,m.subject
from $dbname_detail.messages m
left join $dbname_main.addressbook b on b.pkaddressbookid = m.from_user
left join $dbname_main.addressbook bb on bb.pkaddressbookid = m.to_user

where  m.message_id ='$message_id'";
$reportresult = $AdminDAO->queryresult($query);

$from_user = $reportresult[0]['from_user'];
$to_user = $reportresult[0]['to_user'];
$datetime = $reportresult[0]['datetime'];
$message = $reportresult[0]['message'];
$status = $reportresult[0]['status'];
$subject = $reportresult[0]['subject'];

	$field1		=	array('status');
	$value1		=	array(1);
		$AdminDAO->updaterow("$dbname_detail.messages",$field1,$value1,"`message_id`='$message_id'");

/**************************************************************/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Messages</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body rightmargin="0" bottommargin="0" topmargin="0" leftmargin="0">
<table width="80%" border="0" align="center" bgcolor="#F4E7BB" class="border">
  <tr>
    <td height="21" colspan="2" align="left" valign="top" bgcolor="#EDF7FE" class="topmenu" ><a href="messages.php" class="butt" >Back To Inbox</a></td>
  </tr>
  <tr>
    <td align="left" bgcolor="#EDF7FE" class="s1">Subject:</td>
    <td height="10" align="left" valign="top" bgcolor="#EDF7FE" class="s4">&nbsp;<?php echo $subject;?></td>
  </tr>
  <tr>
    <td width="164" align="left" bgcolor="#EDF7FE" class="s1">From User:</td>
    <td height="10" align="left" valign="top" bgcolor="#EDF7FE" class="s4">&nbsp;<?php echo $from_user;?></td>
  </tr>
  <td align="left" valign="top" bgcolor="#EDF7FE" class="s1">To User:</td>
    <td height="10" align="left" valign="top" bgcolor="#EDF7FE" class="s4">&nbsp;<?php echo $to_user;?></td>
  </tr>
  <tr>
    <td align="left" valign="top" bgcolor="#EDF7FE" class="s1">Date:</td>
    <td height="10" align="left" valign="top" bgcolor="#EDF7FE" class="s4">&nbsp;<?php echo $datetime;?></td>
  </tr>
  <tr>
    <td align="left" valign="top" bgcolor="#EDF7FE" class="s1">Message</td>
    <td height="100" align="left" valign="top" bgcolor="#EDF7FE" class="s4">&nbsp;<?php echo $message;?></td>
  </tr>
</table>
<br />
<br />
