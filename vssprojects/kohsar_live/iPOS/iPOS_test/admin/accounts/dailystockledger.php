<?php ob_start();
error_reporting(-1); 
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
    global $AdminDAO;
    $date=time();
    $date	=	date('d-m-Y',(strtotime ( '-1 day' , $date ) ));
    $from = "Kohsar SHOP System <kohsar@esajee.com>";
//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";
   $to = "accounts@esajee.com,hesajee@gmail.com";
//$to = "fahadbuttqau@gmail.com,w3bgrafix@gmail.com,uuaqarahmed@gmail.com,siddique.ahmad@gmail.com";
    $subject = "Esajee Kohsar Stock Ledger of $date ";

   $query_item = "SELECT  fkbarcodeid,itemdescription ,
   (select sum(pd.quantity) from $dbname_detail.purchase_return r left join $dbname_detail.purchase_return_detail pd on (pkpurchasereturnid=fkpurchasereturnid) where FROM_UNIXTIME(r.addtime,'%d-%m-%Y') = '$date' and pd.fkbarcodeid=s.fkbarcodeid)  return_quantity,
   (select sum(quantity) from $dbname_detail.damages d where d.fkstockid=s.pkstockid)  demage_quantity,
   (select sum(quantity) from $dbname_detail.adjustment ad where ad.fkstockid=s.pkstockid and FROM_UNIXTIME(ad.addtime,'%d-%m-%Y') = '$date')  adjustment_quantity,
   (select sum(quantity) from $dbname_detail.stock st where st.pkstockid=s.pkstockid and FROM_UNIXTIME(st.addtime,'%d-%m-%Y') = '$date' and st.fksupplierinvoiceid='' and st.fkconsignmentdetailid!='')  movement_quantity,
   (select sum(quantity) from $dbname_detail.stock st1 where st1.pkstockid=s.pkstockid and FROM_UNIXTIME(st1.addtime,'%d-%m-%Y') = '$date' and st1.fksupplierinvoiceid!='' and st1.fkconsignmentdetailid='')  invoice_quantity,
   (select sum(quantity) from $dbname_detail.saledetail sd where sd.fkstockid=s.pkstockid and FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') = '$date' and sd.quantity > 0)  sale_quantity,
(select sum(quantity) from $dbname_detail.saledetail sd where sd.fkstockid=s.pkstockid and FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') = '$date' and sd.quantity < 0)  salereturn_quantity 

from  $dbname_detail.stock s left join main.barcode on (pkbarcodeid=fkbarcodeid)  where FROM_UNIXTIME(s.addtime,'%d-%m-%Y') = '$date' group by  s.fkbarcodeid ";
	 $reportresult_item = $AdminDAO->queryresult($query_item);
     $row_run_item=count($reportresult_item);

$table ='';
$sql="SELECT 	storephonenumber,storeaddress	from store where pkstoreid='3'";
$storearray=	$AdminDAO->queryresult($sql);
$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];

$table="<link rel='stylesheet' type='text/css' href='https://kohsar.esajee.com/includes/css/style.css' />
<div align='left'>
<div > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />
<b>Think globally shop locally</b> <br />". $storenameadd."</span> </div>";
$table .='
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
 

 
 <tr>
    <th height="50" colspan="16" align="center" style="font-size:14px; border:none " bgcolor="#FFFFFF" ><span style="font-size:24px; ">Daily Stock Ledger</th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="right" style="font-size:14px; border:none " bgcolor="#FFFFFF" ></th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >Date:'.$date.'</th>
  </tr>';
 $table .=' <tr>
    
    <th width="204" bgcolor="#C0944B"  align="right">Barcode</th>
    <th width="275" bgcolor="#C0944B" align="right">Item</th>
      <th width="275" bgcolor="#C0944B" align="center" colspan="8">QUANTITY</th> 
     </tr>';
  $table .=' <tr>
    
    <th width="204" bgcolor="#C0944B"  align="right"></th>
    <th width="275" bgcolor="#C0944B" align="right"></th>
     <th width="275" bgcolor="#C0944B" align="left">Invoice </th>
	 <th width="275" bgcolor="#C0944B" align="left">Movement</th>
	  <th width="275" bgcolor="#C0944B" align="left">Return </th>
      <th width="275" bgcolor="#C0944B" align="left">Damages</th> 
       <th width="275" bgcolor="#C0944B" align="left">Adjusted</th>
	    <th width="275" bgcolor="#C0944B" align="left">Purchase Return</th>
	          <th width="275" bgcolor="#C0944B" align="left">Sale</th>
			         <th width="275" bgcolor="#C0944B" align="left">Sale Return</th>
  </tr>';
   $totalvalue = 0;
  
 
for($i=0;$i<$row_run_item;$i++)
{		

$query_ = "SELECT sum(quantity) noinv_quantity FROM $dbname_detail.`purchase_return`,$dbname_detail.`purchase_return_detail` where fkpurchasereturnid=pkpurchasereturnid  and FROM_UNIXTIME(addtime,'%d-%m-%Y') = '$date' and fkbarcodeid='{$reportresult_item[$i]['fkbarcodeid']}' ";
$reportresult_ = $AdminDAO->queryresult($query_);

$table .='<tr>
   <td width="235" align="left"> '.$reportresult_item[$i]["fkbarcodeid"].'</td>
  
    <td align="right"> '.$reportresult_item[$i]["itemdescription"].'</td>
    <td width="275" align="right">'.$reportresult_item[$i]["invoice_quantity"].'</td>
	<td width="275" align="right">'.$reportresult_item[$i]["movement_quantity"].'</td>
	<td width="275" align="right">'.$reportresult_item[$i]["return_quantity"].'</td>
	<td width="275" align="right">'.$reportresult_item[$i]["damage_quantity"].'</td>
	<td width="275" align="right">'.$reportresult_item[$i]["adjusted_quantity"].'</td>
		<td width="275" align="right">'.$reportresult_[0]["noinv_quantity"].'</td>
	<td width="275" align="right">'.$reportresult_item[$i]["sale_quantity"].'</td>
	<td width="275" align="right">'.$reportresult_item[$i]["salereturn_quantity"].'</td>
	 </tr>';
}


$table.='</table>';


 echo $body = $table;
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
/////////////////////////////////////
file_get_contents("https://pharmadha.esajee.com/admin/accounts/dailystockledger.php");
file_get_contents("https://gulberg.esajee.com/admin/accounts/dailystockledger.php");
file_get_contents("https://dha.esajee.com/admin/accounts/dailystockledger.php");

 //  $stock=file_get_contents("https://warehouse.esajee.com/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");	 
/*file_get_contents("https://dha.esajee.com/admin/accounts/counter_wise_dha.php");	 
file_get_contents("https://gulberg.esajee.com/admin/accounts/counter_wise_gulberg.php");	 
file_get_contents("https://pharmadha.esajee.com/admin/accounts/counter_wise_pharma.php");
file_get_contents("https://warehouse.esajee.com/admin/accounts/wdailymail.php");	
*///////////////////////////////////////

?>