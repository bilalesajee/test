<?php
$html	=	file_get_contents("http://localhost/store/admin/printcreditorinvoice.php?id=$id");
$html	=	str_replace("../","../../../",$html);
	$dirname = date('Y-m-d',time());  
    $filename = ("/creditinvoices/" . "$dirname" . "/");  
     if (!file_exists($filename)) 
	 {  
         @mkdir("creditinvoices/" . "$dirname", 0777);  
	   // echo "The directory $dirname exists";  
	 }
$myFile = "creditinvoices/$dirname/".$id."_".$customerid."_".$empid."-".date('His',time()).".html";
$fh 	= fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $html);
fclose($fh);
?>