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

$query	="SELECT a.title, code, (sum(dr) - sum(cr)) as balance FROM $dbname_detail.transaction as t,  $dbname_detail.account as a
inner join $dbname_detail.transaction_details as td on td.account_id=a.id
WHERE t.id	=	td.transaction_id
AND   t.at BETWEEN $fromdate AND $todate	
group by a.id";
//$res	=mysql_query($query);
$res			=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Trial Balance</title>
<link rel="stylesheet" type="text/css" href="../../includes/css/style.css" />
<script language="javascript">
 window.print();
</script>
</head>
<body>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
<tr>
<th colspan="5">
Trial Balance
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
    	Dr.
    </th>
	<th width="13%">
    	Cr.
    </th>
</tr>
<?php
$sumdr =0;
$sumcr =0;
	
foreach($res as $row)
{
	$dr=$cr=0;
	$i++;	
	$title=$row['title'];
	$code=$row['code'];
	$balance=$row['balance'];
	
	if($balance<0){
		$cr=abs($balance);
		$sumcr+=$cr;
	}else{	
		$dr=$balance;
		$sumdr+=$dr;					
	}
	
?>	
	<tr>
    	<td>
        	<?php echo $i; ?>
        </td>
      
    <td width="58%">
    	<?php echo $title; ?>
      (<?php echo $code; ?>)</td>
   
        <td align="right">
        	<?php if($dr>0){echo number_format($dr,2);}else{echo "&nbsp;";} ?>
        </td>
        <td align="right">
        	<?php  if($cr>0){echo number_format($cr,2);}else{echo "&nbsp;";}?>
        </td>
    </tr>
<?php
}
?>
<tr>
  <td align="right" colspan="2">
        	<strong>Balance</strong>
        </td>
        <td align="right">
        	<strong><?php echo number_format($sumdr,2); ?></strong>
        </td>  <td align="right">
        	<strong><?php echo number_format($sumcr,2); ?></strong>
        </td>

</tr>
</table>
</body>
</html>