<?php
 $mData=json_encode($_REQUEST,true);
 $mData=urlencode($mData);
 file_get_contents("https://main.esajee.com/admin/addremotecustomer.php?get=".$mData);

?>