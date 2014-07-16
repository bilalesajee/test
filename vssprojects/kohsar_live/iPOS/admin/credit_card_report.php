<?php
include_once("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;
/*************************DATE CHECKS**************************/
 $sdate				=	strtotime($_GET['sdate']. ' 00:00:00'); 
 $edate				=	strtotime($_GET['edate'].' 23:59:59');
 
  $counter          =  $_GET['counter'];
  $cctype          =  $_GET['cctype'];
  $bnk          =  $_GET['bank'];
  $paymentmethod          =  $_GET['paymentmethod'];
  $summary          =  $_GET['summary'];
  $cond="";
 if($sdate != '' && $edate!='')
  {
   $date_check = " s.datetime  between '$sdate' and  '$edate'"; 
  }
 if($counter !='')
	{
	$cond = " and s.countername IN ($counter)  ";
    }
	if($cctype !=''){
		
		$cond.=" and cc.fkcctypeid='{$cctype}'  ";
		}
	
		if($bnk !=''){
		
		$cond.=" and cc.fkbankid='{$bnk}'  ";
		}

	switch ($paymentmethod) {
    case "cc":
        $str= "Credit Card ";
        break;
    case "fc":
        $str= "Foreign Currency ";
        break;
    case "ch":
        $str= "Cheque ";
        break;
	case "c":
        $str= "Cash ";
        break;

}
//$AdminDAO->dq=1;
if($paymentmethod =='cc')
{
 $query=	$AdminDAO->getrows("$dbname_detail.sale s, $dbname_detail.payments cc LEFT JOIN bank ON (pkbankid=cc.fkbankid) LEFT JOIN cctype ON (cc.fkcctypeid=pkcctypeid)","pkpaymentid, round( sum( amount ) , 2 ) amount,s.countername,FROM_UNIXTIME(s.datetime,'%d-%m-%Y') datetime, from_unixtime( s.updatetime, '%d-%m-%Y-%H:%i:%s' ) dtime, typename, ccno, cc.fksaleid, (SELECT totalamount FROM $dbname_detail.sale WHERE cc.fksaleid=pksaleid) stotal, bankname bank","$date_check  AND cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1  $cond  AND paymentmethod='cc' GROUP BY cc.fksaleid,pkpaymentid order by s.countername,cc.fksaleid ");
}
 elseif($paymentmethod =='c')
   {
//$AdminDAO->dq=0;
  $cashpayment		=	$AdminDAO->getrows("$dbname_detail.sale s, $dbname_detail.payments cc ","pkpaymentid, round( sum( amount ) , 2 ) amount,s.countername,FROM_UNIXTIME(s.datetime,'%d-%m-%Y') datetime, from_unixtime( s.updatetime, '%d-%m-%Y-%H:%i:%s' ) dtime, tendered, returned, cc.fksaleid","$date_check  AND cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1  $cond  AND paymentmethod='c' GROUP BY cc.fksaleid,pkpaymentid order by s.countername,cc.fksaleid ");
  
   }
  elseif($paymentmethod =='ch')
   { 
 
  $chequebybank		=	$AdminDAO->getrows("$dbname_detail.sale s, $dbname_detail.payments cc LEFT JOIN bank ON (pkbankid=cc.fkbankid)","pkpaymentid, round( sum( amount ) , 2 ) amount,s.countername,FROM_UNIXTIME(s.datetime,'%d-%m-%Y') datetime, from_unixtime( s.updatetime, '%d-%m-%Y-%H:%i:%s' ) dtime,  chequeno, cc.fksaleid, bankname bank","$date_check AND  cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1  $cond  AND paymentmethod='ch' GROUP BY cc.fksaleid,pkpaymentid order by s.countername,cc.fksaleid ");
   }
   elseif($paymentmethod =='fc')
   {
  
   
    $fcbycurrency		=	$AdminDAO->getrows("$dbname_detail.sale s, $dbname_detail.payments cc LEFT JOIN currency ON (pkcurrencyid=cc.fkcurrencyid)","pkpaymentid, round( sum( amount ) , 2 ) amount,s.countername,FROM_UNIXTIME(s.datetime,'%d-%m-%Y') datetime, from_unixtime( s.updatetime, '%d-%m-%Y-%H:%i:%s' ) dtime,  currencyname, cc.fksaleid, cc.rate","$date_check AND  cc.amount<>0 AND cc.fksaleid=pksaleid AND s.status=1  $cond  AND paymentmethod='fc' GROUP BY cc.fksaleid,pkpaymentid order by s.countername,cc.fksaleid ");
   }
//$AdminDAO->dq=0;



/**************************************************************/

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Payment Wise Sale</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
<link rel="stylesheet" type="text/css" href="../includes/css/style.css">
</head>

<body>
<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif" align="center"><b>ESAJEE'S</b>
</div>
<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;" align="center"><b>Think globally shop locally</b></div><br />
<div style="width:8.0in;font-size:12px;font-weight:bold;" align="center"><?php echo $storename."<br />".$storeaddress1;?><br />
<br /> 
<h3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Report of <i><u><?php echo $str;?></u></i> Sales <br></div>
<br />
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
 <?php

 
 if($paymentmethod=='cc' && count($query) == 0)
{
	echo "<div style='width:8.0in;font-size:12px;font-weight:bold;' align='center'>No Records Found";
	exit;
}
 if($paymentmethod=='c' && count($cashpayment) == 0)
{
	echo "<div style='width:8.0in;font-size:12px;font-weight:bold;' align='center'>No Records Found";
	exit;
}
 if($paymentmethod=='ch' && count($chequebybank) == 0 )
{
	echo "<div style='width:8.0in;font-size:12px;font-weight:bold;' align='center'>No Records Found";
	exit;
}
 if($paymentmethod=='fc' && count($fcbycurrency) == 0)
{
	echo "<div style='width:8.0in;font-size:12px;font-weight:bold;' align='center'>No Records Found";
	exit;
}
 


?>
<?php 
 if($summary=='')
 {
if($paymentmethod=='cc')
{
?>

<table width="100%" class="simple">
<?php
	$old_date ='';
        
		for($i=0;$i<count($query);$i++)
		{
		
			$amount_total1 = $amount_total1+$query[$i]['amount'];
			?>
            <?php 
	if($query[$i]['countername']!=$query[$i-1]['countername'])	{?>
<tr>
		<th width="55">ID</th>
		<th width="146">Date & Time</th>
		<th width="160">Card Number</th>
		<th width="63">Sale</th>
		<th width="63">Counter</th>
		<th width="105"> CC Type</th>
		<th width="102">Bank</th>
		<th width="146">CC Paid</th>
	
  </tr>
   <?php } ?>
				<tr>
				<td><?php echo $query[$i]['fksaleid'];?>&nbsp;</td>
				<td align="left"><?php echo $query[$i]['dtime'];?>&nbsp;</td>
				<td align="left"><?php echo $query[$i]['ccno'];?>&nbsp;</td>
				<td align="right"><?php echo numbers($query[$i]['stotal']);?>&nbsp;</td>
				<td align="left"><?php echo $query[$i]['countername'];?>&nbsp;&nbsp;</td>
				<td align="left"><?php echo $query[$i]['typename'];?>&nbsp;</td>
				<td align="left"><?php echo $query[$i]['bank'];?>&nbsp;</td>
				<td align="right"><?php echo numbers($query[$i]['amount']);?>&nbsp;</td>
		
		    </tr>    
            <?php if($query[$i]['countername']!=$query[$i+1]['countername']){
				
				?>
           <tr>
			  <td colspan="7" align="right">Total</td>
			  <td align="right"><?php echo numbers($amount_total1);?>&nbsp;</td>
  </tr> 
   <tr>
          <td width="55" colspan="8" ></td>
       
  </tr>
          
			<?php $amount_total1=0;}
			
			
			?>
		
			
			<?php 
		
		}
	
	?>
			
     
			   
   
   

	
	</table>
    <?php } ?>
   <!-- /////////////////////////////////////////////////////////////////////////////////////////////////-->
 <?php   if($paymentmethod=='c')
{
?>
<table width="70%" class="simple">
<?php
	$old_date ='';
        
		for($i=0;$i<count($cashpayment);$i++)
		{
		
			$amount_total2 = $amount_total2+$cashpayment[$i]['amount'];
			?>
            <?php 
	if($cashpayment[$i]['countername']!=$cashpayment[$i-1]['countername'])	{?>
<tr>
		<th width="91">ID</th>
		<th width="146">Date & Time</th>
		<th width="160"><strong>Tendered</strong></th>
		<th width="63"><strong>Returned</strong></th>
		<th width="113">Counter</th>
		<th width="212"><strong>Amount</strong></th>
	
	  </tr>
   <?php } ?> 
				<tr>
				<td><?php echo $cashpayment[$i]['fksaleid'];?>&nbsp;</td>
				<td align="left"><?php echo $cashpayment[$i]['dtime'];?>&nbsp;</td>
				<td align="right"><?php echo $cashpayment[$i]['tendered'];?>&nbsp;</td>
				<td align="right"><?php echo $cashpayment[$i]['returned'];?>&nbsp;</td>
				<td align="left"><?php echo $cashpayment[$i]['countername'];?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td align="right"><?php echo numbers($cashpayment[$i]['amount']);?>&nbsp;</td>
		
		    </tr>    
            <?php if($cashpayment[$i]['countername']!=$cashpayment[$i+1]['countername']){
				
				?>
           <tr>
			  <td align="right">Total</td>
			  <td align="right">&nbsp;</td>
			  <td align="right">&nbsp;</td>
			  <td align="right">&nbsp;</td>
			  <td align="right">&nbsp;</td>
			  <td align="right"><?php echo numbers($amount_total2);?>&nbsp;</td>
  </tr> 
   <tr>
          <td colspan="6" align="center" bgcolor="#666666"><strong>Counter Summary&nbsp;</strong></td>
          </tr>
            <tr>
          <td colspan="6"><strong>Counter <?php echo $amount_total[$i]['countername'] ;?>:&nbsp;</strong><?php echo $amount_total2;?>&nbsp;</td>
          </tr>
			<?php $amount_total2=0;}
			
			
			?>
		
			
			<?php 
		
		}
	
	?>
			
     
			   
   
   

	
	</table>
    <?php } ?>
   <!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
   <?php   if($paymentmethod=='ch')
{
?>
<table width="50%" class="simple">
     <?php
	$old_date ='';
        
		for($i=0;$i<count($chequebybank);$i++)
		{
		
			$amount_total3 = $amount_total3+$chequebybank[$i]['amount'];
			?>
     <?php 
	if($chequebybank[$i]['countername']!=$chequebybank[$i-1]['countername'])	{?>
     <tr>
       <th width="91">ID</th>
       <th width="146">Date & Time</th>
       <th width="160"><strong>Bank Name</strong></th>
       <th width="63"><strong>Check No</strong></th>
       <th width="113">Counter</th>
       <th width="212"><strong>Amount</strong></th>
     </tr>
     <?php } ?>
     <tr>
       <td><?php echo $chequebybank[$i]['fksaleid'];?>&nbsp;</td>
       <td align="left"><?php echo $chequebybank[$i]['dtime'];?>&nbsp;</td>
       <td align="left"><?php echo $chequebybank[$i]['bank'];?>&nbsp;</td>
       <td align="left"><?php echo $chequebybank[$i]['chequeno'];?>&nbsp;</td>
       <td align="left"><?php echo $chequebybank[$i]['countername'];?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td align="right"><?php echo numbers($chequebybank[$i]['amount']);?>&nbsp;</td>
     </tr>
     <?php if($chequebybank[$i]['countername']!=$chequebybank[$i+1]['countername']){
				
				?>
     <tr>
       <td align="right">Total</td>
       <td align="right">&nbsp;</td>
       <td align="right">&nbsp;</td>
       <td align="right">&nbsp;</td>
       <td align="right">&nbsp;</td>
       <td align="right"><?php echo numbers($amount_total3);?>&nbsp;</td>
     </tr>
     <tr>
       <td colspan="6" align="center" bgcolor="#666666"><strong>Counter Summary&nbsp;</strong></td>
     </tr>
     <tr>
       <td colspan="6"><strong>Counter <?php echo $chequebybank[$i]['countername'] ;?>:&nbsp;</strong><?php echo $amount_total3;?>&nbsp;</td>
     </tr>
     <?php $amount_total3=0;}
			
			
			?>
     <?php 
		
		}
	
	?>
   </table>
   <?php } ?>
   <!-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
   <?php   if($paymentmethod=='fc')
{
?>
   <table width="50%" class="simple">
     <?php
	$old_date ='';
        
		for($i=0;$i<count($fcbycurrency);$i++)
		{
		
			$amount_total4 = $amount_total4+$fcbycurrency[$i]['amount'];
			?>
     <?php 
	if($fcbycurrency[$i]['countername']!=$fcbycurrency[$i-1]['countername'])	{?>
     <tr>
       <th width="91">ID</th>
       <th width="146">Date & Time</th>
       <th width="160"><strong>Currency Name</strong></th>
       <th width="63"><strong>Rate</strong></th>
       <th width="113">Counter</th>
       <th width="212"><strong>Amount</strong></th>
     </tr>
     <?php } ?> 
     <tr>
       <td><?php echo $fcbycurrency[$i]['fksaleid'];?>&nbsp;</td>
       <td align="left"><?php echo $fcbycurrency[$i]['dtime'];?>&nbsp;</td>
       <td align="left"><?php echo $fcbycurrency[$i]['currencyname'];?>&nbsp;</td>
       <td align="left"><?php echo $fcbycurrency[$i]['rate'];?>&nbsp;</td>
       <td align="left"><?php echo $fcbycurrency[$i]['countername'];?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
       <td align="right"><?php echo numbers($fcbycurrency[$i]['amount']);?>&nbsp;</td>
     </tr>
     <?php if($fcbycurrency[$i]['countername']!=$fcbycurrency[$i+1]['countername']){
				
				?>
     <tr>
       <td align="right">Total</td>
       <td align="right">&nbsp;</td>
       <td align="right">&nbsp;</td>
       <td align="right">&nbsp;</td>
       <td align="right">&nbsp;</td>
       <td align="right"><?php echo numbers($amount_total4);?>&nbsp;</td>
     </tr>
     <tr>
       <td colspan="6" align="center" bgcolor="#666666"><strong>Counter Summary&nbsp;</strong></td>
     </tr>
     <tr>
       <td colspan="6"><strong>Counter <?php echo $fcbycurrency[$i]['countername'] ;?>:&nbsp;</strong><?php echo $amount_total4;?>&nbsp;</td>
     </tr>
     <?php $amount_total=0;}
			
			
			?>
     <?php 
		
		}
	
	?>
   </table>
   <?php } ?>
   <?php 
 }
 else if($summary==1)
   {
	   
		  if($paymentmethod=='cc')
{
	   for($i=0;$i<count($query);$i++)
		{
		if($query[$i]['countername'] ==1)
		{
			  $amount_total1_sum = $amount_total1_sum+$query[$i]['amount'];
		}
		if($query[$i]['countername'] ==2)
		{
			  $amount_total2_sum = $amount_total2_sum+$query[$i]['amount'];
		}
		if($query[$i]['countername'] ==3)
		{
			  $amount_total3_sum = $amount_total3_sum+$query[$i]['amount'];
		}
		if($query[$i]['countername'] ==4)
		{
			 $amount_total4_sum = $amount_total4_sum+$query[$i]['amount'];
		}
		}
}
		  if($paymentmethod=='c')
{
		for($i=0;$i<count($cashpayment);$i++)
		{
			
		
			if($cashpayment[$i]['countername'] ==1)
		{
			  $amount_total1_sum = $amount_total1_sum+$cashpayment[$i]['amount'];
		}
		if($cashpayment[$i]['countername'] ==2)
		{
			  $amount_total2_sum = $amount_total2_sum+$cashpayment[$i]['amount'];
		}
		if($cashpayment[$i]['countername'] ==3)
		{
			  $amount_total3_sum = $amount_total3_sum+$cashpayment[$i]['amount'];
		}
		if($cashpayment[$i]['countername'] ==4)
		{
			 $amount_total4_sum = $amount_total4_sum+$cashpayment[$i]['amount'];
		}
		}
}
	
			  if($paymentmethod=='ch')
{	for($i=0;$i<count($chequebybank);$i++)
		{
		
			if($chequebybank[$i]['countername'] ==1)
		{
			  $amount_total1_sum = $amount_total1_sum+$chequebybank[$i]['amount'];
		}
		if($chequebybank[$i]['countername'] ==2)
		{
			  $amount_total2_sum = $amount_total2_sum+$chequebybank[$i]['amount'];
		}
		if($chequebybank[$i]['countername'] ==3)
		{
			  $amount_total3_sum = $amount_total3_sum+$chequebybank[$i]['amount'];
		}
		if($chequebybank[$i]['countername'] ==4)
		{
			 $amount_total4_sum = $amount_total4_sum+$chequebybank[$i]['amount'];
		}
		}}
				  if($paymentmethod=='fc')
{
	
		for($i=0;$i<count($fcbycurrency);$i++)
		{
		
			if($fcbycurrency[$i]['countername'] ==1)
		{
			  $amount_total1_sum = $amount_total1_sum+$fcbycurrency[$i]['amount'];
		}
		if($fcbycurrency[$i]['countername'] ==2)
		{
			  $amount_total2_sum = $amount_total2_sum+$fcbycurrency[$i]['amount'];
		}
		if($fcbycurrency[$i]['countername'] ==3)
		{
			  $amount_total3_sum = $amount_total3_sum+$fcbycurrency[$i]['amount'];
		}
		if($fcbycurrency[$i]['countername'] ==4)
		{
			 $amount_total4_sum = $amount_total4_sum+$fcbycurrency[$i]['amount'];
		}
		}
}
   ?>
   <?php
   		  if($paymentmethod=='cc')
{
   ?>
  
<table width="40%"  class="simple">
  
	
     <tr>
       <td colspan="2" align="center" bgcolor="#666666"><strong>Counter Summary&nbsp;</strong></td>
     </tr>
     <?php
	 if($counter==1 || $counter=='' )
	 {
	 ?>
     <tr>
       <td width="38%"><strong>Counter 1:&nbsp;</strong>&nbsp;</td>
       <td width="62%" align="right"><?php echo $amount_total1_sum;?>&nbsp;</td>
     </tr>
     <?php } 
	if($counter==2 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 2:&nbsp;</strong>&nbsp;</td>
       <td align="right"><?php echo $amount_total2_sum;?>&nbsp;</td>
     </tr>
     <?php }
	if($counter==3 || $counter=='')
	 {
	 ?>
     <tr>
       <td><strong>Counter 3:&nbsp;</strong>&nbsp;</td>
       <td align="right"><?php echo $amount_total3_sum;?>&nbsp;</td>
     </tr>
     <?php }
	 if($amount_total4_sum>0){
	if($counter==4 || $counter=='')
	 {
	 ?>
     <tr>
       <td><strong>Counter 4:&nbsp;</strong>&nbsp;</td>
       <td align="right"><?php echo $amount_total4_sum;?>&nbsp;</td>
     </tr>
     <?php 
	 }
	 }
	 ?>
 
</table>
<?php }?>

  <?php
   		  if($paymentmethod=='c')
{
   ?>
<table width="40%" class="simple">
  
	
     <tr>
       <td colspan="2" align="center" bgcolor="#666666"><strong>Counter Summary&nbsp;</strong></td>
     </tr>
     <?php
	 if($counter==1 || $counter==''  )
	 {
	 ?>
     <tr>
       <td width="38%"><strong>Counter 1:&nbsp;</strong>&nbsp;</td>
       <td width="62%" align="right"><?php echo $amount_total1_sum;?>&nbsp;</td>
     </tr>
     <?php } 
	  if($counter==2 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 2:&nbsp;</strong>&nbsp;</td>
       <td align="right"><?php echo $amount_total2_sum;?>&nbsp;</td>
     </tr>
     <?php }
	   if($counter==3 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 3:&nbsp;</strong>&nbsp;</td>
       <td align="right"><?php echo $amount_total3_sum;?>&nbsp;</td>
     </tr>
     <?php }
	  if($counter==4 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 4:&nbsp;</strong>&nbsp;</td>
       <td align="right"><?php echo $amount_total4_sum;?>&nbsp;</td>
     </tr>
     <?php 
	 }?>
 
</table>
<?php }?>
  <?php
   		  if($paymentmethod=='ch')
{
   ?>
<table width="40%" class="simple">
  
	
     <tr>
       <td colspan="2" align="center" bgcolor="#666666"><strong>Counter Summary&nbsp;</strong></td>
     </tr>
     <?php
	 if($counter==1 || $counter==''  )
	 {
	 ?>
     <tr>
       <td width="38%"><strong>Counter 1:&nbsp;</strong>&nbsp;</td>
       <td width="62%"><?php echo $amount_total1_sum;?>&nbsp;</td>
     </tr>
     <?php } 
	 if($counter==2 || $counter==''  )
	 {
	 ?>
     <tr>
       <td><strong>Counter 2:&nbsp;</strong>&nbsp;</td>
       <td><?php echo $amount_total2_sum;?>&nbsp;</td>
     </tr>
     <?php }
	   if($counter==3 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 3:&nbsp;</strong>&nbsp;</td>
       <td><?php echo $amount_total3_sum;?>&nbsp;</td>
     </tr>
     <?php }
	  if($counter==4 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 4:&nbsp;</strong>&nbsp;</td>
       <td><?php echo $amount_total4_sum;?>&nbsp;</td>
     </tr>
     <?php 
	 }?>
 
</table>
<?php }?>
  <?php
   		  if($paymentmethod=='fc')
{
   ?>
<table width="40%" class="simple">
  
	
     <tr>
       <td colspan="2" align="center" bgcolor="#666666"><strong>Counter Summary&nbsp;</strong></td>
     </tr>
     <?php
	 if($counter==1 || $counter==''  )
	 {
	 ?>
     <tr>
       <td width="37%"><strong>Counter 1:&nbsp;</strong>&nbsp;</td>
       <td width="63%"><?php echo $amount_total1_sum;?>&nbsp;</td>
     </tr>
     <?php } 
	 if($counter==2 || $counter==''  )
	 {
	 ?>
     <tr>
       <td><strong>Counter 2:&nbsp;</strong>&nbsp;</td>
       <td><?php echo $amount_total2_sum;?>&nbsp;</td>
     </tr>
     <?php }
	   if($counter==3 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 3:&nbsp;</strong>&nbsp;</td>
       <td><?php echo $amount_total3_sum;?>&nbsp;</td>
     </tr>
     <?php }
	  if($counter==4 || $counter=='' )
	 {
	 ?>
     <tr>
       <td><strong>Counter 4:&nbsp;</strong>&nbsp;</td>
       <td><?php echo $amount_total4_sum;?>&nbsp;</td>
     </tr>
     <?php 
	 }?>
 
</table>
<?php }?>
<p>&nbsp;</p>
<p>&nbsp;</p>
      
</form> <!--end form-->
</body>

</html>
    <?php 
//////////////////////add by wajid for excel export/////////////////////////

}
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>
