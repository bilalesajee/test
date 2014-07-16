<?php 
function sendPayoutEmail($counter,$closingsession, $empid,$acclimit,$accountname,$totalpayouts,$amount){		
	  require_once "Mail.php";
	  //error_reporting(E_ALL | E_STRICT);
	  //ini_set("display_errors", 1);
	  $date	=	date('d-m-Y',time());
	  $from = "Kohsar SHOP System <kohsar@esajee.com>";
	  $to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com,jafer@esajeesolutions.com";
	  //$to = "jafer.balti@gmail.com";
	  $subject = "Esajee Kohsar Payout on $date Closing ID $closingid AccountID $acid";
	  //hesajee@gmail.com,m_esajee@hotmail.com,
	  //$fh = fopen("http://localhost/esajeepos/closingmail.php?id=$closingid", 'r');
	  //echo $body = fread($fh);
	  //$body =file_get_contents("http://203.223.163.218/esajeepos/payoutmail.php?counter=$counter&closing=$closingsession&cashier=$empid&accountlimit=$acclimit&accounttitle=".urlencode($accountname)."&totalpayouts=$totalpayouts&currentpayout=$amount");	
	  
	$body = "<link rel='stylesheet' type='text/css' href='http://203.223.163.162/esajeepos/includes/css/style.css' />
<div align='left'>
<div > <img src='http://203.223.163.162/esajeepos/images/esajeelogo.jpg' width='150' height='50'><br />
   <b>Think globally shop locally</b> <br />
  ".$storenameadd."</span> </div>
<div > Date: ".$date."</div>
<div > Counter: ".$counter."</div>
<div > Closing #: ".$closingsession." </div>
<div > Cashier: ".$empid." </div>
<table width='300' style='font-size:9px;font-family:Lucida Sans Unicode, Lucida Grande, sans-serif; border-collapse:collapse; border-color:#FFF;margin-top:10px;' class='simple'>
  <tr>
  	<th align='left'>Account Title</th>
    <th align='right'>".$accountname."</th>
  </tr>
  <tr>
  	<th align='left'>Account Limit</th>
    <th align='right'>".$acclimit."</th>
  </tr>
  <tr>
    <th align='left'>Total Payouts</th>
    <th align='right'>".$totalpayouts."</th>
  </tr>
  <tr>
    <th align='left'>Current Payout</th>
    <th align='right'>".$amount."</th>
  </tr>
</table>
<div align='center'>
".date('Y-m-d h:i:s')."
</div>";
	  
	  	    
	  $host = "mail.esajeesolutions.com";
	  $username = "gulberg@esajee.com";
	  $password = "esajee1901";
	  $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
	  //,'Content-type'=>'text/html\r\n'
	  $headers = array ('From' => $from,
		'To' => $to,
		'Subject' => $subject,'Content-type'=>'text/html; charset=iso-8859-1\r\n');
	  $smtp = Mail::factory('smtp',
		array ('host' => $host,
		  'auth' => true,
		  'username' => $username,
		  'password' => $password));
	  //echo $body. "is the body";
	  //$body  = "something";
	  $mail = $smtp->send($to, $headers, $body);
	  //echo $body. "is the body";
	  if (PEAR::isError($mail)) {
		echo("<p>" . $mail->getMessage() . "</p>");
	   } else {
		echo("<p>Message successfully sent!</p>");
	   }
}
$counter = $_GET['counter'];
$closingsession = $_GET['closingsession'];
$empid = $_GET['empid'];
$acclimit = $_GET['acclimit'];
$accountname = $_GET['accountname'];
$totalpayouts = $_GET['totalpayouts'];
$amount = $_GET['amount'];
sendPayoutEmail($counter,$closingsession, $empid,$acclimit,$accountname,$totalpayouts,$amount);
 ?>