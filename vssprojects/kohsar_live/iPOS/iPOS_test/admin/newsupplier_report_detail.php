<?php

include_once("../includes/security/adminsecurity.php");

///////////////////////add by wajid for excel export/////////////////////////////////////////

include_once("../export/exportdata.php");

///////////////////////////////////////////////////////////////////////////////////

global $AdminDAO;

/*************************DATE CHECKS**************************/

 $sdate				=	strtotime($_GET['sdate']. ' 00:00:00'); 

 $edate				=	strtotime($_GET['edate'].' 23:59:59');



$supplier          =  $_GET['supplier'];

$suppliers		=	$AdminDAO->getrows("supplier","pksupplierid,companyname","pksupplierid='$supplier'");

 if($sdate != '' && $edate!='')

  {

   $date_check = " si.datetime  between '$sdate' and  '$edate'"; 

  }

 $query = "select 1 n,
 sum(st.quantity*st.priceinrs) inv_amount,
 si.datetime date FROM
  $dbname_detail.supplierinvoice si left join $dbname_detail.stock st on (pksupplierinvoiceid=fksupplierinvoiceid) left join $dbname_detail.returns rt on (pkstockid=fkstockid)     WHERE si.fksupplierid='$supplier' and si.datetime  between '$sdate' and  '$edate'  group by date
  UNION ALL
  select 2 n,
 sum(rt.quantity*st.priceinrs) inv_amount,
 rt.returndate date FROM
   $dbname_detail.stock st  left join $dbname_detail.returns rt on (pkstockid=fkstockid) WHERE st.fksupplierid='$supplier' and rt.returndate  between '$sdate' and  '$edate' 
 group by date 
    UNION ALL
  select 3 n,
 sum(pd.quantity*pd.price) inv_amount,
 pr.addtime date FROM
   $dbname_detail.purchase_return pr  left join $dbname_detail.purchase_return_detail pd on (pkpurchasereturnid=fkpurchasereturnid) WHERE pr.fksupplierid='$supplier' and pr.addtime  between '$sdate' and  '$edate' 
  
  group by date order by date asc";
 
$reportresult = $AdminDAO->queryresult($query);
 
if($_GET['ob'] == 1){ 

 $query_opening = "select  round(sum(st.quantity*st.priceinrs),2) inv_amount,
 si.datetime date FROM
  $dbname_detail.supplierinvoice si left join $dbname_detail.stock st on (pksupplierinvoiceid=fksupplierinvoiceid) left join $dbname_detail.returns rt on (pkstockid=fkstockid)     WHERE si.fksupplierid='$supplier' and si.datetime  < '$sdate' ";
 
$reportresultop = $AdminDAO->queryresult($query_opening);

$query_opening1 = "select  round(sum(rt.quantity*st.priceinrs),2) inv_amount1,
 rt.returndate date FROM
   $dbname_detail.stock st  left join $dbname_detail.returns rt on (pkstockid=fkstockid) WHERE st.fksupplierid='$supplier' and rt.returndate  < '$sdate'  ";
 
$reportresultop1 = $AdminDAO->queryresult($query_opening1);



$query_opening2 = " select  round(sum(pd.quantity*pd.price),2) inv_amount2,
 pr.addtime date FROM
   $dbname_detail.purchase_return pr  left join $dbname_detail.purchase_return_detail pd on (pkpurchasereturnid=fkpurchasereturnid) WHERE pr.fksupplierid='$supplier' and pr.addtime  < '$sdate'";
 
$reportresultop2 = $AdminDAO->queryresult($query_opening2);

}else{
    $reportresultop[0] = 0;
    $reportresultop[0] = 0;
    $reportresultop2[0] = 0;
}


 
 $row_run=count($reportresult);






/**************************************************************/



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<title>Supplier Report</title>

<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />

<link rel="stylesheet" type="text/css" href="../includes/css/style.css">

</head>

<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->

<form id="reportdata" method="post">

<input type="hidden" name="data" id="data" />

<!--///////////////////////////////////////////////////////////////////////-->

<body>

<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b>

</div>

<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div><br />

<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br />

<br /> 

<h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Report of <i><u><?php echo $suppliers[0]['companyname'];?></u></i> Supplier <br></h3></div>

<br />

 <div align="left"><b>From : </b><?php echo $_GET['sdate'];?><b>  To : </b><?php echo $_GET['edate'];?></div>

<table width="100%" class="simple">


<tr>

		     <th width="146">Date</th>

		<th width="146">Purchase Amount</th>

		<th width="160">Return Amount</th>

		

  </tr>
<tr>

		
				<td align="left"><b>Opening Balance</b></td>

				<td align="left"><?php  if($reportresultop[0]['inv_amount']==''){ echo 0;}else{echo $reportresultop[0]['inv_amount'];}?>&nbsp;</td>

				<td align="right"><?php echo ($reportresultop1[0]['inv_amount1']+$reportresultop2[0]['inv_amount2'])?>&nbsp;</td>

			

		

		    </tr>    

<?php	

$tinv=0;
$tdv=0;
$df=0;
for($i=0;$i<$row_run;$i++){
	
	if($reportresult[$i]['n']==1){
	$tinv+=$reportresult[$i]['inv_amount'];
	}
	if($reportresult[$i]['n']==2 or $reportresult[$i]['n']==3){
	$tdv+=$reportresult[$i]['inv_amount'];
	}
	$df=$reportresult[$i]['inv_amount'];
	$tdf+=$df;
	?>


				<tr>

		
				<td align="left"><?php echo date('d-m-Y',$reportresult[$i]['date']);?>&nbsp;</td>

				<td align="left"><?php if($reportresult[$i]['n']==1){ if($reportresult[$i]['inv_amount']==''){ echo 0;}else{echo $reportresult[$i]['inv_amount'];}}else{ echo 0;}?>&nbsp;</td>

				<td align="right"><?php if($reportresult[$i]['n']==2 or $reportresult[$i]['n']==3){if($reportresult[$i]['inv_amount']==''){ echo 0;}else{echo $reportresult[$i]['inv_amount'];}}else{ echo 0;}?>&nbsp;</td>

			

		

		    </tr>    

   <?php } ?>


<tr>

			
				<td align="right" ><b>Total</b></td>

				<td align="left"><?php echo $tinv=$tinv+$reportresultop[0]['inv_amount'];?>&nbsp;</td>

				<td align="right"><?php echo $tdv=$tdv+$reportresultop1[0]['inv_amount1']+$reportresultop2[0]['inv_amount2'];?>&nbsp;</td>

				
		

		    </tr>    

<tr>

			
				<td align="right" colspan="2"><b>Balance</b></td>

				<td align="right" ><?php echo $tinv-$tdv;?>&nbsp;</td>

				
		

		    </tr>    

		</table>

   


<p>&nbsp;</p>

<p>&nbsp;</p>

      



</body>

</form> <!--end form-->

</html>

    <?php 

//////////////////////add by wajid for excel export/////////////////////////

echo $exporactions;


//////////////////////////////////////////////////////////////////////////

?>

