<?php
$headers='';
$from = "Kohsar SHOP System <kohsar@esajee.com>";
$to = "fahadbuttqau@gamil.com,siddique.ahmad@gmail.com";
$subject = "Testing Email ";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$body="Its testying email";
echo $msent=mail($to,$subject,$body,$headers);

?>