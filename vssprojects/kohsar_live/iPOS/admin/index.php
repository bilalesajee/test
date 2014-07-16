<?php
error_reporting(7);
include_once("../includes/security/adminsecurity.php");
require_once("header.php");
/*echo "<pre>";
print_r($_SESSION);
echo "</pre>";
*/
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	include_once("ui-modal.php");
}//end edit
?>
<div id="msg"></div>
<div id="contentarea" class="clearfix"> 
<div id="contentarea2" class="clearfix">
   <div id="middle_left" ></div>
   <div id="center-column" ></div>
  <div id="middle_right"></div>
 </div><!--contentarea2-->
 </div><!--contentarea-->
<?php
	require_once("../includes/menu/footer.php");	
?>
<script language="javascript">
id	=	'<?php echo $firstscreen."_tab";?>';
id2	=	'<?php echo $firstscreen."_tab_b";?>';
url	=	'<?php echo $firsturl;?>';
document.getElementById(id).className="active";
document.getElementById(id2).className="active";
$('#center-column').load(url);
previous  = id;
</script>