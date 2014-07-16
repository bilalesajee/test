<?php
include_once("../includes/security/adminsecurity.php");
include_once("../includes/mail/htmlMimeMail5.php");
global $AdminDAO;
$mail = new htmlMimeMail5();
/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/
echo $_POST['mailtext'];
if(sizeof($_POST)>0)
{
	//sending email
	$from		=	$_POST['fromemails'];
	$to			=	$_POST['toemails'];
	$subject	=	$_POST['subject'];
	$cc			=	$_POST['ccemails'];
	$bcc		=	$_POST['bccemails'];
	$message	=	$_POST['message'];
	$attachment	=	$_POST['mailtext'];
	// headers
	$headers .= "To: $to" . "\r\n";
	$headers .= "From: $from" . "\r\n";
	$headers .= "Cc: $cc" . "\r\n";
	$headers .= "Bcc: $bcc" . "\r\n";
	/**
    * Set the from address of the email
    */
    $mail->setFrom($from);
    
    /**
    * Set the subject of the email
    */
    $mail->setSubject($subject);
    

    /**
    * Set the text of the Email
    */
    $mail->setText($message);
    
	/**
    * Set the HTML of the email. Any embedded images will be automatically found as long as you have added them
    * using addEmbeddedImage() as below.
    */
    $mail->setHTML($attachment);
	
    /**
    * Send the email. Pass the method an array of recipients.
    */
    $address = $to;
    $result  = $mail->send(array($address));
?>
Email has been sent to <?php echo $address?>. Result: <?php var_dump($result);?>
<?php
}
?>


