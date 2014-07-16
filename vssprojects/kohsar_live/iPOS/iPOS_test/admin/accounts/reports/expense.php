<?php 
//require_once("db.php");
include_once("../../includes/security/adminsecurity.php");
$id=$_REQUEST['id'];

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

if($id!="")
{
	$where=" where id=$id ";
}
$query	="SELECT 
					id,name 
			FROM 
				`type` 
			WHERE  
				`category_id` =5
		";
//$res	=mysql_query($query);
$res			=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Expense Report</title>
<link rel="stylesheet" type="text/css" href="../../includes/css/style.css" />
<script language="javascript">
 window.print();
</script>
</head>
<body>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
<tr>
<th colspan="5">
Expense Report
</th>
</tr>
<?php
foreach($res as $obj)
{
	$id	=	$obj['id'];
?>
<tr>
	<th colspan="3">
    	<?php
			echo $obj['name'];	
		?>
    </th>
</tr>
<tr>
	<th width="14%">
		Serial
	</th>
    <th width="30%">
    	Account Title
    </th>
	<th width="13%">
    	Amount
    </th>
</tr>
<?php
if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition
	$query	="SELECT 
					a.id, concat(a.title,'(',a.code,')') account, SUM( dt.dr ) total, t.name
				FROM
					$dbname_detail.account a, $dbname_detail.transaction_details dt,  `type` t
				WHERE
					a.id 		=	dt.account_id	AND
					t.id		=	a.type_id	AND
					t.id		=	'$id'
				GROUP BY a.id";
}elseif($_SESSION['siteconfig']!=3){//from main, start edit by ahsan 14/02/2012
		$query	="SELECT 
					a.id, concat(a.title,'(',a.code,')') account, SUM( dt.dr ) total, t.name
				FROM
					$dbname_detail.account a, $dbname_detail.transaction tr, $dbname_detail.transaction_details dt, `type` t
				WHERE
					a.id 		=	dt.account_id	AND
					t.id		=	a.type_id	AND
					tr.id		=	dt.transaction_id AND
					tr.at BETWEEN $fromdate AND $todate AND
					t.id		=	'$id'
				GROUP BY a.id";
}//end edit
	//$res1	=mysql_query($query);
	$res1	=	$AdminDAO->queryresult($query);
	$total	=	0;
	foreach($res1 as $row)
	{
		$i++;	
		$total+=$row['total'];;
		
	?>
    <tr>
	<td width="14%">
		<?php
			echo $i;
		?>
	</td>
    <td width="30%">
    	<?php
			echo $row['account'];
		?>
    </td>
	<td width="13%" align="right">
    	<?php
			echo number_format($row['total'],2);
		?>
    </td>
</tr>
	<?php
        }//account
	  ?>
<tr>
	<td align="right" colspan="2">
		<strong><?php
			echo "Total ".$obj->name;	
		?></strong>
    </td>
    <td align="right">
    	<strong><?php
			$grand_total	+=$total;
			echo number_format($total,2);
		?></strong>
    </td> 
</tr>
<?php
}//type
?>	
<tr>
<td width="14%" colspan="2" align="right">
<strong><?php 
	echo "Net Expense";

?></strong>
    
	<td width="13%" align="right">
    	<strong><?php echo number_format(abs($grand_total),2); ?></strong>
    </td>
</tr>
</table>
</body>
</html>