<div style="margin-top:-12px;">
           <a href="javascript:void(0)" onclick="hidediv('itemgrid')"> <img src="../images/x.jpeg" />
</div>
<?php 
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
require_once("../OpenCrypt/ajax_tree.php");
print ajax_tree(0,1, 1);
?>
<div style="margin-top:-5px;">
           <a href="javascript:void(0)" onclick="hidediv('itemgrid')"> <img src="../images/x.jpeg" />
</div>