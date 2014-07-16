<?php 

function sendPayoutEmail($counter,$closingsession, $empid,$acclimit,$accountname,$totalpayouts,$amount){		

	  require_once "Mail.php";

	  //error_reporting(E_ALL | E_STRICT);

	  //ini_set("display_errors", 1);

	  $date	=	date('d-m-Y',time());

	  $from = "Kohsar SHOP System <gulberg@esajee.com>";

	  $to = "hesajee@gmail.com,m_esajee@hotmail.com";

	//  $to = "uuaqarahmed@gmail.com,fahadbuttqau@gmail.com";

	  $subject = "Esajee Kohsar Payout on $date Closing ID $closingid AccountID $acid";

	  //hesajee@gmail.com,m_esajee@hotmail.com,

	  //$fh = fopen("http://localhost/esajeepos/closingmail.php?id=$closingid", 'r');

	  //echo $body = fread($fh);
////
////	  $body                ///25-06-2012 =file_get_contents("http://210.2.171.236/esajeepos2/payoutmail.php?counter=$counter&closing=$closingsession&cashier=$empid&accountlimit=$acclimit&accounttitle=".urlencode($accountname)."&totalpayouts=$totalpayouts&currentpayout=$amount");
//// 
	  $body =file_get_contents("http://203.223.163.218/payoutmail.php?counter=$counter&closing=$closingsession&cashier=$empid&accountlimit=$acclimit&accounttitle=".urlencode($accountname)."&totalpayouts=$totalpayouts&currentpayout=$amount");

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