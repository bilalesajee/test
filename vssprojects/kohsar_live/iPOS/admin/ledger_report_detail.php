<?php include_once("../includes/security/adminsecurity.php");

///////////////////////add by wajid for excel export/////////////////////////////////////////

include_once("../export/exportdata.php");

    global $AdminDAO;
    $sdate				=	strtotime($_GET['sdate']. ' 00:00:00'); 

    $edate				=	strtotime($_GET['edate'].' 23:59:59');


   
/*$query_item = "SELECT  fkbarcodeid,itemdescription ,
   (select sum(quantity) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_quantity,
   (select sum(quantity) from $dbname_detail.damages d where d.fkstockid=s.pkstockid)  demage_quantity,
   (select sum(quantity) from $dbname_detail.adjustment ad where ad.fkstockid=s.pkstockid and FROM_UNIXTIME(ad.addtime,'%d-%m-%Y') = '$date')  adjustment_quantity,
   (select sum(quantity) from $dbname_detail.stock st where st.pkstockid=s.pkstockid and FROM_UNIXTIME(st.addtime,'%d-%m-%Y') = '$date' and st.fksupplierinvoiceid='' and st.fkconsignmentdetailid!='')  movement_quantity,
   (select sum(quantity) from $dbname_detail.stock st1 where st1.pkstockid=s.pkstockid and FROM_UNIXTIME(st1.addtime,'%d-%m-%Y') = '$date' and st1.fksupplierinvoiceid!='' and st1.fkconsignmentdetailid='')  invoice_quantity,
   (select sum(quantity) from $dbname_detail.saledetail sd where sd.fkstockid=s.pkstockid and FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') = '$date' and sd.quantity > 0)  sale_quantity,
(select sum(quantity) from $dbname_detail.saledetail sd where sd.fkstockid=s.pkstockid and FROM_UNIXTIME(sd.timestamp,'%d-%m-%Y') = '$date' and sd.quantity < 0)  salereturn_quantity 

	 from  $dbname_detail.stock s left join main.barcode on (pkbarcodeid=fkbarcodeid)  where FROM_UNIXTIME(s.addtime,'%d-%m-%Y') = '$date' group by  s.fkbarcodeid ";
*/

$query_item = "SELECT  fkbarcodeid,itemdescription ,
   (select sum(quantity) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_quantity,
   (select sum(quantity) from $dbname_detail.damages d where d.fkstockid=s.pkstockid)  demage_quantity,
   (select sum(quantity) from $dbname_detail.adjustment ad where ad.fkstockid=s.pkstockid and ad.addtime between '$sdate' and  '$edate')  adjustment_quantity,
   (select sum(quantity) from $dbname_detail.stock st where st.pkstockid=s.pkstockid and st.addtime between '$sdate' and  '$edate' and st.fksupplierinvoiceid='' and st.fkconsignmentdetailid!='')  movement_quantity,
   (select sum(quantity) from $dbname_detail.stock st1 where st1.pkstockid=s.pkstockid and st1.addtime between '$sdate' and  '$edate' and st1.fksupplierinvoiceid!='' and st1.fkconsignmentdetailid='')  invoice_quantity,
   (select sum(quantity) from $dbname_detail.saledetail sd where sd.fkstockid=s.pkstockid and sd.timestamp between '$sdate' and  '$edate' and sd.quantity > 0)  sale_quantity,
(select sum(quantity) from $dbname_detail.saledetail sd1 where sd1.fkstockid=s.pkstockid and sd1.timestamp between '$sdate' and  '$edate' and sd1.quantity < 0)  salereturn_quantity 

	 from  $dbname_detail.stock s left join main.barcode on (pkbarcodeid=fkbarcodeid)  where s.addtime between '$sdate' and  '$edate' group by  s.fkbarcodeid ";



	 $reportresult_item = $AdminDAO->queryresult($query_item);
     $row_run_item=count($reportresult_item);

$table ='';
$sql="SELECT 	storephonenumber,storeaddress	from store where pkstoreid='3'";
$storearray=	$AdminDAO->queryresult($sql);
$storenameadd=	$storearray[0]['storeaddress'].', '.$storearray[0]['storephonenumber'];

$table="<link rel='stylesheet' type='text/css' href='https://kohsar.esajee.com/includes/css/style.css' />

<div align='left'>
<div align='center' > <img src='https://kohsar.esajee.com/images/esajeelogo.jpg' width='150' height='50'><br />
<b>Think globally shop locally</b> <br />". $storenameadd."</span> </div>";
$table .='
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
 

 
 <tr>
    <th height="50" colspan="16" align="center" style="font-size:14px; border:none " bgcolor="#FFFFFF" ><span style="font-size:24px; ">Stock Ledger</th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="right" style="font-size:14px; border:none " bgcolor="#FFFFFF" ></th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >From Date:'.$_GET['sdate'].' To '.$_GET['edate'].'</th>
  </tr></table>';
$table .='<br>
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
  ';
 $table .=' <tr>
    
    <th width="204" bgcolor="#C0944B"  align="right">Barcode</th>
    <th width="275" bgcolor="#C0944B" align="right">Item</th>
      <th width="275" bgcolor="#C0944B" align="center" colspan="8">QUANTITY</th> 
     </tr>';
  $table .=' <tr>
    
    <th width="204" bgcolor="#C0944B"  align="right"></th>
    <th width="275" bgcolor="#C0944B" align="right"></th>
     <th width="275" bgcolor="#C0944B" align="right">Invoice </th>
	 <th width="275" bgcolor="#C0944B" align="right">Movement</th>
	  <th width="275" bgcolor="#C0944B" align="right">Return </th>
      <th width="275" bgcolor="#C0944B" align="right">Damages</th> 
       <th width="275" bgcolor="#C0944B" align="right">Adjusted</th>
	    <th width="275" bgcolor="#C0944B" align="right">Purchase Return</th>
	          <th width="275" bgcolor="#C0944B" align="right">Sale</th>
			         <th width="275" bgcolor="#C0944B" align="right">Sale Return</th>
  </tr>';
   $totalvalue = 0;
  
 
for($i=0;$i<$row_run_item;$i++)
{		


$query_ = "SELECT sum(quantity) noinv_quantity FROM $dbname_detail.`purchase_return`,$dbname_detail.`purchase_return_detail` where fkpurchasereturnid=pkpurchasereturnid  and addtime between '$sdate' and  '$edate' and fkbarcodeid='{$reportresult_item[$i]['fkbarcodeid']}' ";
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


 echo  $body = $table;

?>