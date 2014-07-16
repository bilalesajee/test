<?php

 ob_start();
error_reporting(0); 
session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
	
include("../../includes/security/adminsecurity.php");

global $AdminDAO, $Component;
 $query="SELECT unitsremaining stock_quantity,reorderlevel,b.itemdescription,b.barcode FROM $dbname_detail.re_order_level rl,$dbname_detail.stock st,main.barcode b where st.fkbarcodeid=rl.fkbarcodeid and  unitsremaining <= reorderlevel and rl.fkbarcodeid=b.pkbarcodeid order by pkstockid desc limit 1";
$result=$AdminDAO->queryresult($query); 
$body='';
$body.='<link rel="stylesheet" type="text/css" href="../../includes/css/style.css"><table class="simple">
<tr>
		<th>Barcode</th>
		<th>Item Description</th>
		<th>Reorder Level</th>
		<th>Remaining Quantity</th>
	  </tr>';

 for($x=0;$x<sizeof($result);$x++){ 

	$body.='<tr>
				<td>'. $result[$x]['barcode'].'</td>
				<td>'. $result[$x]['itemdescription'].'</td>
				<td align="right">'. $result[$x]['reorderlevel'].'</td>
				<td align="center">'. $result[$x]['stock_quantity'].'</td>
		    </tr>';
      
	  
		

	  
	  }
$body.='</table>';
if(sizeof($result)>0){
          $addressbookid= 1888;
		  $fkemployeeid=1110;
		   $sub =($body);
		  
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Stock Reorder Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
}
?>
