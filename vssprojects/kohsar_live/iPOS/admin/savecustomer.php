<?php
 include("../surl.php");
 $mData=json_encode($_REQUEST,true);
 $mData=urlencode($mData);
 file_get_contents($customerUrl_.$mData);

?>