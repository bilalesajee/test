<?php //require_once("db.php");
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

/*
$query=" SELECT title, code,id FROM `account` $where ";
$res1	=mysql_query($query);

while($row1	=mysql_fetch_assoc($res1))
{
$title	=$row1['title'];
$code	=$row1['code'];
$account_id	=$row1['id'];
$query	="SELECT td.id ,account_id,	dr,cr,transaction_id,at FROM `transaction_details` td, transaction t where account_id='$account_id' and t.id=td.transaction_id";
$res	=mysql_query($query);
}*/
if($_SESSION['siteconfig']!=1){//edit by ahsan 13/02/2012, if condition added
	$query=" SELECT c.*, sum(td.dr), sum(td.cr) ,(sum(td.cr)-sum(td.dr)) as bal
	FROM $dbname_detail.transaction as t, `accountcategory` as c 
	inner join $dbname_detail.account as ac on c.id = ac.category_id
	inner join $dbname_detail.transaction_details as td on ac.id = td.account_id
	WHERE t.id = td.transaction_id
	AND t.at BETWEEN $fromdate AND $todate
	group by c.name ";
}elseif($_SESSION['siteconfig']!=3){//from main, start edit by ahsan 13/02/2012
	$query=" SELECT c.*, sum(td.dr), sum(td.cr) ,(sum(td.cr)-sum(td.dr)) as bal
	FROM `accountcategory` as c 
	inner join $dbname_detail.account as ac on c.id = ac.category_id
	inner join $dbname_detail.transaction_details as td on ac.id = td.account_id
	group by c.name ";
}//end edit
//$res	=mysql_query($query);
$res	=	$AdminDAO->queryresult($query);

foreach($res as $row){
//echo "<pre>";	
//	print_r($row);
//echo "</pre>";
	$name=$row['name'];
//	echo $res['bal'];
	switch ($name){	
		case "Asset":
			$Assets=$row['bal'];
			break;
		case "Liability":
			$Liabilities=$row['bal'];
			break;			
		case "Owner's Equity":
			$Owners=$row['bal'];
			break;			
		case "Revenu":
			$Revenues=$row['bal'];
			break;			
		case "Expense":
			$Expenses=$row['bal'];
			break;			
		case "Gain":
			$Gains=$row['bal'];
			break;			
		case "Loss":
			$Losses=$row['bal'];
			break;			
		case "Contributions":
			$Contributions=$row['bal'];
			break;			
		case "Withdrawals":
			$Withdrawals=$row['bal'];
			break;			
	}
}


?>
<!--Assets 	   =      	Liabilities 	  +   	Owners' Equity
 	 	  	  +   	Revenues
 	 	  	  -   	Expenses
 	 	  	  +   	Gains
 	 	  	  -   	Losses
 	 	  	  +   	Contributions
 	 	  	  -   	Withdrawals-->
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Accounts Equation</title>
<link rel="stylesheet" type="text/css" href="../../includes/css/style.css" />
<script language="javascript">
 window.print();
</script>
</head>
<body>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
	<tr>
        <th colspan="10">
	        Accounting Equation 
        </th>
    </tr>
    <tr>
        <th>Assets</th>
        <th>Liabilities</th>
        <th>Owners' Equity</th>
        <th>Revenues</th>
        <th>Expenses</th>
        <th>Gains</th>
        <th>Losses</th>
        <th>Contributions</th>
        <th>Withdrawals</th>        
    </tr>    
    <tr>
        <td><?php echo $Assets; ?></td>
        <td><?php echo $Liabilities; ?></td>
        <td><?php echo $Owners; ?></td>
        <td><?php echo $Revenues; ?></td>
        <td><?php echo $Expenses; ?></td>
        <td><?php echo $Gains; ?></td>
        <td><?php echo $Losses; ?></td>
        <td><?php echo $Contributions; ?></td>
        <td><?php echo $Withdrawals ; ?></td>        
    </tr>
</table>
</body>
</html>