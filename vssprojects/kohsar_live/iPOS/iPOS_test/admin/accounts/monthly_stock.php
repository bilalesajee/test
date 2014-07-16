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
 $date	=	date('m-Y',(strtotime ( '-1 month' , $date ) ));
 $date1=date('F Y',(strtotime ( '-1 month' , time()) ));
$from = "Kohsar SHOP System <kohsar@esajee.com>";
//$to = "hesajee@gmail.com,m_esajee@hotmail.com,esajeeco@gmail.com";
//$to = "fahadbuttqau@gmail.com,accounts@esajee.com,hesajee@gmail.com";
$to = "fahadbuttqau@gmail.com";
$subject = "Esajee Kohsar Monthly Stock Report of ".$date1;
//////////////////////////////////////////////////////////stock query///////////////////////////////////////////////////////////////////////////////////
 /* $query_supplier = "SELECT  s.fksupplierid , s.fksupplierinvoiceid ,round(SUM(s.quantity * s.priceinrs),2) as amount_value , sl.companyname , si.billnumber from  $dbname_detail.stock s , main.supplier sl, $dbname_detail.supplierinvoice si

	 where s.fksupplierid=sl.pksupplierid and s.fksupplierinvoiceid=si.pksupplierinvoiceid and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date' group by  s.fksupplierid , s.fksupplierinvoiceid ";
*/	 
	  $query_supplier = "SELECT  s.fksupplierid ,
	   s.fksupplierinvoiceid ,
	   round(SUM(s.quantity * s.priceinrs),2) as amount_value ,
	    sl.companyname ,
		 si.billnumber,
	 (select sum(r.quantity*s.priceinrs) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_value
	 from  $dbname_detail.stock s , main.supplier sl, $dbname_detail.supplierinvoice si
	 where s.fksupplierid=sl.pksupplierid and s.fksupplierinvoiceid=si.pksupplierinvoiceid and FROM_UNIXTIME(s.addtime,'%m-%Y') = '$date' group by  s.fksupplierid  ";

	 $reportresult_supplier = $AdminDAO->queryresult($query_supplier);
     $row_run_supplier=count($reportresult_supplier);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////Higest Sale Return Items///////////////////////////////////////////////////////////////////
$query = "SELECT 
					s.pksaleid,
					CONCAT(a.firstname,' ',a.lastname) cashiername,
					FROM_UNIXTIME(s.datetime,'%d-%m-%Y') datetime,
					b.barcode,
					b.itemdescription,
					sum(sd.quantity) as quantity,
					sum(sd.saleprice) as saleprice
					
				FROM 
					$dbname_detail.sale s
					left join $dbname_detail.saledetail sd on sd.fksaleid = s.pksaleid 
					left join $dbname_detail.stock stk on stk.pkstockid = sd.fkstockid 
					left join $dbname_main.barcode b on b.pkbarcodeid = stk.fkbarcodeid
					left join $dbname_main.addressbook a on a.pkaddressbookid = s.fkuserid
				WHERE
					
					s.status = 1 AND
					sd.quantity<0  AND  FROM_UNIXTIME(s.datetime,'%m-%Y') = '$date'
				GROUP BY 
					stk.fkbarcodeid
				ORDER BY 
					sum(sd.quantity) ASC limit 10";

$reportresult_return = $AdminDAO->queryresult($query);
$row_run_return=count($reportresult_return);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $query_purchase = "SELECT 
					r.pkreturnid,
					sl.companyname,
					FROM_UNIXTIME(r.returndate,'%d-%m-%Y') datetime,
					b.barcode,
					b.itemdescription,
					sum(r.quantity) as quantity,
					round(sum(stk.priceinrs),2) as purchaseprice
					
				FROM 
					$dbname_detail.stock stk,$dbname_main.barcode b ,$dbname_detail.returns r,main.supplier sl
						                               				
				WHERE
				  FROM_UNIXTIME(r.returndate,'%m-%Y') = '$date' and b.pkbarcodeid = stk.fkbarcodeid and r.fkstockid=stk.pkstockid and stk.fksupplierid=sl.pksupplierid
				GROUP BY 
					stk.fkbarcodeid
				ORDER BY 
					sum(r.quantity) DESC limit 10";

$reportresult_purchase = $AdminDAO->queryresult($query_purchase);
$row_run_purchase=count($reportresult_purchase);
/////////////////////////////////////////////////////////////////////////////////////////



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
    <th height="50" colspan="2" align="center" style="font-size:14px; border:none " bgcolor="#FFFFFF" ><span style="font-size:24px; ">Stock Report of '.$date1.'</span></th>
  </tr>';
 $table .=' <tr>
    <th width="435" height="31" bgcolor="#C0944B" >Supplier</th>';
  /*  <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>
    <th width="275" bgcolor="#C0944B" >Bill #</th>*/
      $table .='<th width="275" bgcolor="#C0944B" align="right">Invoice Amount</th>
	            <th width="275" bgcolor="#C0944B" align="right">Return Amount</th>
                </tr>';
   $totalvalue = 0;
  
 
for($i=0;$i<$row_run_supplier;$i++)
{
		$totalvalue += $reportresult_supplier[$i]["amount_value"];
		$totalrvalue += $reportresult_supplier[$i]["return_value"];
		
		$table .='<tr>
   <td width="435" align="left"> '.$reportresult_supplier[$i]["companyname"].' [ '.$reportresult_supplier[$i]["fksupplierid"].' ]</td>';
  
/*    <td align="left"> '.$reportresult_supplier[$i]["fksupplierinvoiceid"].'</td>
    <td width="275" align="left">'.$reportresult_supplier[$i]["billnumber"].'</td>
*/	$table .='<td width="275" align="right">'.round($reportresult_supplier[$i]["amount_value"],2).'</td>
              <td width="275" align="right">'.round($reportresult_supplier[$i]["return_value"],2).'</td>
	 </tr>';
}

$table .='<tr>
   <td width="235"  align="right" style="font-weight:bold"> Total</td>
   <td width="275" align="right">'.round($totalvalue,2).'</td>
   <td width="275" align="right">'.round($totalrvalue,2).'</td>
	 </tr>';
$table.='</table>';

$table.='<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
<tr>

<th colspan="8" align="center" bgcolor="#FFFFFF"><span style="font-size:16px; ">Heighest Sale Return</span></th>
</tr>

<tr>
<th width="50" bgcolor="#C0943B">Sr #</th>
<th width="100" bgcolor="#C0943B">Bill #</th>
<th width="100" bgcolor="#C0943B">Date</th>
<th width="150" bgcolor="#C0943B">Barcode</th>
<th width="300" bgcolor="#C0943B">Item</th>
<th width="100" bgcolor="#C0943B">Quantity</th>
<th width="100" bgcolor="#C0943B">Amount</th>
<th width="150" bgcolor="#C0943B">Cashier</th>

</tr>';

 $ii=1 ;
for($i=0;$i<$row_run_return;$i++)
{

$table.='<tr>
<td align="left" width="50">'.$ii.'</td>
<td width="100" align="left">'.$reportresult_return[$i]['pksaleid'].'</td>
<td width="100" align="left">'. $reportresult_return[$i]['datetime'].'</td>
<td width="150" align="left">'.  $reportresult_return[$i]['barcode'].'</td>
<td width="300" align="left">'.$reportresult_return[$i]['itemdescription'].'</td>
<td width="100" align="left">'.$reportresult_return[$i]['quantity'].'</td>
<td width="100" align="right">'.$reportresult_return[$i]['saleprice'].'</td>
<td width="150" align="left">'.$reportresult_return[$i]['cashiername'].'</td>

</tr>';
$ii++;
}

$table .='</table><p>&nbsp;</p>';
$table .='<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
<tr>
<th colspan="8" align="center" bgcolor="#FFFFFF"><span style="font-size:16px; ">Highest Purchase Return</span></th>
</tr>
<tr>
<th width="50" bgcolor="#C0943B">Sr #</th>
<th width="100" bgcolor="#C0943B">Bill #</th>
<th width="100" bgcolor="#C0943B">Supplier</th>
<th width="100" bgcolor="#C0943B">Date</th>
<th width="150" bgcolor="#C0943B">Barcode</th>
<th width="300" bgcolor="#C0943B">Item</th>
<th width="100" bgcolor="#C0943B">Quantity</th>
<th width="100" bgcolor="#C0943B">Amount</th>

</tr>';


 $j=1 ;
for($i=0;$i<$row_run_purchase;$i++)
{

$table.='<tr>
<td align="left" width="50">'.$j.'</td>
<td width="100" align="left">'.$reportresult_purchase[$i]['pkreturnid'].'</td>
<td width="100" align="left">'.$reportresult_purchase[$i]['companyname'].'</td>
<td width="100" align="left">'. $reportresult_purchase[$i]['datetime'].'</td>
<td width="150" align="left">'.  $reportresult_purchase[$i]['barcode'].'</td>
<td width="300" align="left">'.$reportresult_purchase[$i]['itemdescription'].'</td>
<td width="100" align="left">'.$reportresult_purchase[$i]['quantity'].'</td>
<td width="100" align="right">'.$reportresult_purchase[$i]['purchaseprice'].'</td>


</tr>';
$j++;
}
$table.='</table>';
$body = $table;
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
$body = addslashes($body);	
$time_to_send=date('Y-m-d h:i:s');
$created=date('Y-m-d');
$query= mysql_query("INSERT INTO $dbname_detail.closing_email (subject, body, `to`, `from`, time_to_send, login_id,created_at)
VALUES ('$subject','".$body."','$to','$from','$time_to_send','$addressbookid','$time_to_send')");

?>