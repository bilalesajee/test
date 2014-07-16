<?php
require_once "Mail.php";
global $closingid;
if($closingid=='')
{
	//print"i am in if";
	$closingid	=	$_REQUEST['id'];
	print"<h2>Closing ID: $closingid</h2>";
	
}
session_start();
$_SESSION['param']='mailclosing';
$date	=	date('d-m-Y',time());
$from = "Kohsar SHOP System <kohsar@esajee.com>";
//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com,jafer.balti@gmail.com";
$to = "jafer.balti@gmail.com";
$subject = "Esajee Kohsar Closing on $date Closing ID $closingid";
//hesajee@gmail.com,m_esajee@hotmail.com,
//$fh = fopen("http://localhost/esajeepos/closingmail.php?id=$closingid", 'r');
//echo $body = fread($fh); 




























  $body =	'jafer its for you';
 //$body =file_get_contents("http://203.223.163.218/esajeepos/closingmail.php?id=$closingid");

$host = "mail.esajeesolutions.com";
$username = "dha@esajee.com";
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

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }
?>
<script language="javascript">
	window.close();
</script>