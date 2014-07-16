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
//$to = "fahadbuttqau@gmail.com,siddique.ahmad@gmail.com";
    $subject = "Esajee Kohsar Stock Report of $date ";
//////////////////////////////////////////////////////////stock query///////////////////////////////////////////////////////////////////////////////////
    $query_supplier = "SELECT  s.fksupplierid , s.fksupplierinvoiceid ,round(SUM(s.quantity * s.priceinrs),2) as amount_value , sl.companyname , si.billnumber ,
 (select sum(r.quantity*s.priceinrs) from $dbname_detail.returns r where r.fkstockid=s.pkstockid)  return_value
  from  $dbname_detail.stock s , main.supplier sl, $dbname_detail.supplierinvoice si
 where s.fksupplierid=sl.pksupplierid and s.fksupplierinvoiceid=si.pksupplierinvoiceid and FROM_UNIXTIME(s.addtime,'%d-%m-%Y') = '$date' group by  s.fksupplierid , s.fksupplierinvoiceid ";
	 $reportresult_supplier = $AdminDAO->queryresult($query_supplier);
     $row_run_supplier=count($reportresult_supplier);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////Invoice query///////////////////////////////////////////////////////////////////////////////////
 $query_supplieri = "SELECT  si.pksupplierinvoiceid,si.fksupplierid  , sl.companyname , si.billnumber , FROM_UNIXTIME(si.datetime,'%d-%m-%Y') addtime from main.supplier sl, $dbname_detail.supplierinvoice si
where si.fksupplierid=sl.pksupplierid and si.pksupplierinvoiceid not in (select s.fksupplierinvoiceid from $dbname_detail.stock s where s.fksupplierinvoiceid > 0) and FROM_UNIXTIME(si.datetime,'%d-%m-%Y')!='01-01-1970' and si.invoice_status=0 group by   si.pksupplierinvoiceid,si.fksupplierid order by si.datetime desc ";

	 $reportresult_supplieri = $AdminDAO->queryresult($query_supplieri);

 $row_run_supplieri=count($reportresult_supplieri);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////Invoice query///////////////////////////////////////////////////////////////////////////////////
  $query_supplieri2 = "SELECT  si.pksupplierinvoiceid,si.fksupplierid  , sl.companyname , si.billnumber , FROM_UNIXTIME(si.datetime,'%d-%m-%Y') addtime,(select CONCAT(firstname ,' ', lastname) from main.addressbook,$dbname_detail.stock st where st.fkemployeeid > 0 and st.fkemployeeid=pkaddressbookid and si.pksupplierinvoiceid=st.fksupplierinvoiceid limit 1) as name from main.supplier sl, $dbname_detail.supplierinvoice si
where si.fksupplierid=sl.pksupplierid  and FROM_UNIXTIME(si.datetime,'%d-%m-%Y')!='01-01-1970' and si.invoice_status=0  group by   si.pksupplierinvoiceid,si.fksupplierid order by si.datetime desc ";

	 $reportresult_supplieri2 = $AdminDAO->queryresult($query_supplieri2);

 $row_run_supplieri2=count($reportresult_supplieri2);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


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
    <th height="50" colspan="16" align="center" style="font-size:14px; border:none " bgcolor="#FFFFFF" ><span style="font-size:24px; ">Daily Stock Report</th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="right" style="font-size:14px; border:none " bgcolor="#FFFFFF" ></th>
  </tr>
  <tr>
    <th height="18" colspan="16" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >Date:'.$date.'</th>
  </tr></table>';
$table .='<br>
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
  <tr>
    <th height="18" colspan="6" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >Stock Report of '.$date.'</th>
  </tr>';
 $table .=' <tr>
    <th width="235" height="31" bgcolor="#C0944B" align="left">Supplier</th>
    <th width="204" bgcolor="#C0944B"  align="right">Invoice#</th>
    <th width="275" bgcolor="#C0944B" align="right">Bill #</th>
     <th width="275" bgcolor="#C0944B" align="right">Invoice Amount</th>
	  <th width="275" bgcolor="#C0944B" align="right">Return Amount</th>
  </tr>';
   $totalvalue = 0;
  
 
for($i=0;$i<$row_run_supplier;$i++)
{
		$totalvalue += $reportresult_supplier[$i]["amount_value"];
		$totalrvalue += $reportresult_supplier[$i]["return_value"];
		if($reportresult_supplier[$i]["return_value"]==''){
			$rm=0;
			}else{
				$rm=$reportresult_supplier[$i]["return_value"];
				}
		$table .='<tr>
   <td width="235" align="left"> '.$reportresult_supplier[$i]["companyname"].' [ '.$reportresult_supplier[$i]["fksupplierid"].' ]</td>
  
    <td align="right"> '.$reportresult_supplier[$i]["fksupplierinvoiceid"].'</td>
    <td width="275" align="right">'.$reportresult_supplier[$i]["billnumber"].'</td>
	<td width="275" align="right">'.$reportresult_supplier[$i]["amount_value"].'</td>
	<td width="275" align="right">'.$rm.'</td>
	 </tr>';
}

$table .='<tr>
   <td width="235" colspan="3" align="right" style="font-weight:bold"> Total</td>
	<td width="275" align="right">'.$totalvalue.'</td>
	<td width="275" align="right">'.$totalrvalue.'</td>
	 </tr>';
$table.='</table>';

$table .='<br>
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
  <tr>
    <th height="18" colspan="6" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >Supplier Invoices With Open Staus</th>
  </tr>';
 $table .=' <tr>
    
    <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>
    <th width="235" height="31" bgcolor="#C0944B" align="left" >Supplier</th>
	<th width="275" bgcolor="#C0944B" align="right">Bill #</th>
     <th width="275" bgcolor="#C0944B" align="right">Invoice Added</th>
	 <th width="275" bgcolor="#C0944B" align="right">Added By</th>
  </tr>';
 
for($i=0;$i<$row_run_supplieri2;$i++)
{
	$table .='<tr>
 
  
    <td align="left"> '.$reportresult_supplieri2[$i]["pksupplierinvoiceid"].'</td>
   <td width="235" align="left"> '.$reportresult_supplieri2[$i]["companyname"].' [ '.$reportresult_supplieri2[$i]["fksupplierid"].' ]</td>
    <td width="275" align="right">'.$reportresult_supplieri2[$i]["billnumber"].'</td>
	<td width="275" align="right">'.$reportresult_supplieri2[$i]["addtime"].'</td>
	<td width="275" align="right">'.$reportresult_supplieri2[$i]["name"].'</td>
	 </tr>';
}

$table.='</table>';
$table .='<br>
<table width="100%" border="1" align="left" cellpadding="0" cellspacing="0" class="simple" style="font-size:12px;font-family:Arial, Helvetica, sans-serif;">
  <tr>
    <th height="18" colspan="6" align="center" style="font-size:14px; " bgcolor="#FFFFFF" >Supplier Invoices With no Stock</th>
  </tr>';
 $table .=' <tr>
    
    <th width="204" bgcolor="#C0944B"  align="left">Invoice#</th>
    <th width="235" height="31" bgcolor="#C0944B" align="left" >Supplier</th>
	<th width="275" bgcolor="#C0944B" align="right">Bill #</th>
     <th width="275" bgcolor="#C0944B" align="right">Invoice Added</th>
  </tr>';
 
for($i=0;$i<$row_run_supplieri;$i++)
{
	$table .='<tr>
 
  
    <td align="left"> '.$reportresult_supplieri[$i]["pksupplierinvoiceid"].'</td>
   <td width="235" align="left"> '.$reportresult_supplieri[$i]["companyname"].' [ '.$reportresult_supplieri[$i]["fksupplierid"].' ]</td>
    <td width="275" align="right">'.$reportresult_supplieri[$i]["billnumber"].'</td>
	<td width="275" align="right">'.$reportresult_supplieri[$i]["addtime"].'</td>
	 </tr>';
}

$table.='</table>';

 $body = $table;
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From:" . $from;
$msent=mail($to,$subject,$body,$headers);
/////////////////////////////////////
file_get_contents("https://pharmadha.esajee.com/admin/accounts/dailystockreportmail.php");
file_get_contents("https://gulberg.esajee.com/admin/accounts/dailystockreportmail.php");
file_get_contents("https://dha.esajee.com/admin/accounts/dailystockreportmail.php");
file_get_contents("https://kohsar.esajee.com/admin/accounts/dailystockledger.php");
 //  $stock=file_get_contents("https://warehouse.esajee.com/admin/accounts/remaining_stock_rpt.php?barcode={$barcode}&loc={$loc}");	 
/*file_get_contents("https://dha.esajee.com/admin/accounts/counter_wise_dha.php");	 
file_get_contents("https://gulberg.esajee.com/admin/accounts/counter_wise_gulberg.php");	 
file_get_contents("https://pharmadha.esajee.com/admin/accounts/counter_wise_pharma.php");
file_get_contents("https://warehouse.esajee.com/admin/accounts/wdailymail.php");	
*///////////////////////////////////////

?>