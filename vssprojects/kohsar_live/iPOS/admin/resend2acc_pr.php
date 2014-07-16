<?php
include_once("../includes/security/adminsecurity.php");
include_once("../surl.php");
$id	=	$_REQUEST['ids'];
$idarr	= 	explode(",", $id);

$q = "select * from $dbname_detail.returns where pkreturnid = '$idarr[1]'";
 $rres = mysql_query($q);
 $r=mysql_fetch_array($rres);
$sinvid=$r['fkinvid'];



     $query="select sum(st.priceinrs*rt.quantity)  as invoice_value,st.fksupplierid from  $dbname_detail.returns rt,$dbname_detail.stock st	 where fkinvid='$sinvid' and fkstockid=pkstockid ";
	 $reportresult = $AdminDAO->queryresult($query);
     $ramount=round($reportresult[0]['invoice_value'],2);
     $sid=$reportresult[0]['fksupplierid'];	

  $rdate=time();
   $checkdata=$invoice_return_link= file_get_contents($Url_admin."invoice_return&return={$ramount}&fksupplierid={$sid}&fksupplierinvoiceid={$sinvid}&adddate={$rdate}&location=0");
 		  $addressbookid= 1888;
          $fkemployeeid=1887;
		 
		 if($checkdata>0){
	  
		  mysql_query("update  $dbname_detail.returns set accdatasent = 1  where  fkinvid = '$sinvid' ");
		  echo "Invoice #($sinvid) Return data resent to Accounts ";
		  $sub = "Invoice # ($sinvid),Supplier Id ($sid) ,Amount ($ramount) is returned and rsent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
		
		 }else{
			  echo "Invoice #($sinvid) Return data not resent to Accounts ";
	       $sub = "Invoice # ($sinvid),Supplier Id ($sid) ,Amount ($ramount) is returned and not rsent to accounts";
          $field		=	array('message','subject','from_user','to_user','datetime');
          $value		=	array($sub,'Purchase return Alert',$addressbookid,$fkemployeeid,time());
		  $AdminDAO->insertrow("$dbname_detail.messages",$field,$value);
			 } 
		  


exit;
?>