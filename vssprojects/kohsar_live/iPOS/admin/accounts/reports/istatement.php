<?php //require_once("db.php");
include_once("../../includes/security/adminsecurity.php");
$id=$_REQUEST['id'];
if($id!="")
{
	$where=" where id=$id ";
}

$arr_from 	=	explode('-',$_GET['fromdate']);
$fromdate	=	mktime(0,0,0,$arr_from[1],$arr_from[0],$arr_from[2]);

$arr_to	 	=	explode('-',$_GET['todate']);
$todate		=	mktime(23,59,59,$arr_to[1],$arr_to[0],$arr_to[2]);

if ($fromdate == ''){
	$fromdate	=	0;
}

if ($todate == ''){
	$todate	=	time();
}
if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition
	$query	="SELECT acc.id,acc.code,acc.title, sum(cr) as cr FROM $dbname_detail.transaction as t,  `accountcategory` as c
	inner join $dbname_detail.account acc on acc.category_id=c.id
	inner join $dbname_detail.transaction_details as td on acc.id=td.account_id
	where c.id=4
	AND t.id = td.transaction_id
	AND t.at BETWEEN $fromdate AND $todate
	group by acc.id";
	//$res	=mysql_query($query);
	$res			=	$AdminDAO->queryresult($query);
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
	$query	="SELECT acc.id,acc.code,acc.title, sum(cr) as cr FROM `accountcategory` as c
	inner join $dbname_detail.account acc on acc.category_id=c.id
	inner join $dbname_detail.transaction_details as td on acc.id=td.account_id
	where c.id=4
	group by acc.id";
	$res	=mysql_query($query);
}//end edit

if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition
	$query	="SELECT acc.id,acc.code,acc.title, sum(dr) as dr FROM $dbname_detail.transaction as t, `accountcategory` as c
	inner join $dbname_detail.account acc on acc.category_id=c.id
	inner join $dbname_detail.transaction_details as td on acc.id=td.account_id
	where c.id=5
	AND t.id = td.transaction_id
	AND t.at BETWEEN $fromdate AND $todate
	group by acc.id";
	//$res2	=mysql_query($query);
	$res2			=	$AdminDAO->queryresult($query);
}elseif($_SESSION['siteconfig']!=3){
	$query	="SELECT acc.id,acc.code,acc.title, sum(dr) as dr FROM `accountcategory` as c
	inner join $dbname_detail.account acc on acc.category_id=c.id
	inner join $dbname_detail.transaction_details as td on acc.id=td.account_id
	where c.id=5
	group by acc.id";
	$res2	=mysql_query($query);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Income Statement</title>
<link rel="stylesheet" type="text/css" href="../../includes/css/style.css" />
<script language="javascript">
 window.print();
</script>
</head>
<body>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
<tr>
<th colspan="5">
Income Summary
</th>
</tr>
<tr>
<th colspan="5">
Income
</th>
</tr>
<tr>
<th width="14%">
Serial
</th>
	

    <th width="58%">
    	Account
    </th>

    
	<th width="13%">
    	Amount
    </th>
</tr>
<?php

$sumcr =0;
	
foreach($res as $row)
{
	$dr=$cr=0;
	$i++;	
	$title=$row['title'];
	$code=$row['code'];
	$cr=$row['cr'];
	
	
		$sumcr+=$cr;

?>	
	<tr>
    	<td>
        	<?php echo $i; ?>
        </td>
      
    <td width="58%">
    	<?php echo $title; ?>
      (<?php echo $code; ?>)</td>
   
        <td align="right">
        	<?php if($cr>0){echo number_format($cr,2);}else{echo "&nbsp;";} ?>
        </td>
       
    </tr>
<?php
}
?>
<tr>
  <td align="right" colspan="2">
        	<strong>Total</strong>
        </td>
        <td align="right">
        	<strong><?php echo number_format($sumcr,2); ?></strong>
        </td>  

</tr>
<tr>
<th colspan="5">
Expense
</th>
</tr>

<tr>
<th width="14%">
Serial
</th>
	

    <th width="58%">
    	Account
    </th>

    
	<th width="13%">
    	Amount
    </th>
</tr>
<?php
$sumdr =0;

	
foreach($res2 as $row)
{
	$dr=$cr=0;
	$i++;	
	$title=$row['title'];
	$code=$row['code'];
	$dr=$row['dr'];
	
	
		$sumdr+=$dr;

?>	
	<tr>
    	<td>
        	<?php echo $i; ?>
        </td>
      
    <td width="58%">
    	<?php echo $title; ?>
      (<?php echo $code; ?>)</td>
   
        <td align="right">
        	<?php {echo number_format($dr,2);}?>&nbsp;
        </td>
       
    </tr>
<?php
}
?>
<tr>
  <td align="right" colspan="2">
        	<strong>Total</strong>
    </td>
        <td align="right">
        	<strong><?php echo number_format($sumdr,2); ?></strong>
        </td>  

</tr>
<tr>
<td width="14%" height="27" colspan="2" align="right">
<?php 
if(($sumcr-$sumdr) > 0)
{
	echo "<strong>Net Profit</strong>";
}
else
{
	echo "<strong>Net Loss</strong>";
}

?>
    
	<td width="13%" align="right">
    	<strong><?php echo number_format(abs($sumcr-$sumdr),2); ?></strong>
    </td>
</tr>
</table>
</body>
</html>