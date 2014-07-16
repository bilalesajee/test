<?php 
//require_once("db.php");
include_once("../../includes/security/adminsecurity.php");

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

$id	=	$_REQUEST['account_id'];
if($id!="")
{
	$where=" AND a.id=$id ";
}
$query=" SELECT 
				name as cat_name,title, code,a.id 
			FROM 
				$dbname_detail.account a , accountcategory c 
			WHERE
				c.id	=	a.category_id 
				$where 
				ORDER BY cat_name
			";
$res1			=	$AdminDAO->queryresult($query);
//$res1	=mysql_query($query) or die(mysql_error());
foreach($res1 as $row1)
{
	$title		=	$row1['title'];
	$cat_name	=	$row1['cat_name'];
	$code		=	$row1['code'];
	$account_id	=	$row1['id'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added
		$query		=	"SELECT 
								td1.id ,td1.account_id,td1.dr,td1.cr,td1.transaction_id,at, a.title 
							FROM 
								$dbname_detail.`transaction_details` td1,$dbname_detail.`transaction_details` td2, $dbname_detail.transaction t, $dbname_detail.account a 
							where 
								t.at BETWEEN $fromdate AND $todate AND
								td1.account_id	=	'$account_id' AND 
								t.id		=	td1.transaction_id AND
								td1.transaction_id = td2.transaction_id AND
								td2.account_id = a.id AND
								td1.account_id <> td2.account_id
								GROUP BY transaction_id
								ORDER BY at
						";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
		$query		=	"SELECT 
							td.id ,account_id,dr,cr,transaction_id,at 
						FROM 
							$dbname_detail.transaction_details td, $dbname_detail.transaction t 
						where 
							account_id	=	'$account_id' AND 
							t.id		=	td.transaction_id
					";

}
	$res		=	$AdminDAO->queryresult($query);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Ledger</title>
<link rel="stylesheet" type="text/css" href="../../includes/css/style.css" />
<script language="javascript">
 window.print();
</script>
</head>
<body>
<table width="100%" align="center" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">
<tr>	<!--Code added by jafer 20-12-2012-->
<td colspan="7">
Statement from: <?php echo date("F j, Y",$fromdate); ?> To: <?php echo date("F j, Y",$todate);?><br>
Account Title: <?php echo $title;?> 
</td>
</tr>	<!--Code added by jafer 20-12-2012-->
<tr>
<th colspan="7">
<?php echo $title." [".$cat_name."]";?>
</th>
</tr>
<tr>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added ?>
    <th width="3%">Serial</th>
    <th width="5%">#</th>
    <th width="22%">Date</th>
    <th width="25%">Particular</th>
    <th width="15%">Dr</th>
    <th width="15%">Cr.</th>
    <th width="20%">Balance</th>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012 ?>
    <th width="8%">Serial</th>
    <th width="24%">#</th>
    <th width="35%">Date</th>
    <th width="17%">Dr</th>
    <th width="16%">Cr.</th>
<?php } ?>
</tr>
<?php
/*<!--Code added by jafer 20-12-2012-->*/
/*$queryj	="
			SELECT sum( dr ) - sum( cr ) balance
			FROM $dbname_detail.transaction t, $dbname_detail.transaction_details td
			WHERE td.transaction_id = t.id
			AND	account_id	=	'$id'
			AND t.at < $fromdate
		";*/
$queryj	="
			SELECT sum( dr ) - sum( cr ) balance
			FROM $dbname_detail.transaction t, $dbname_detail.transaction_details td
			WHERE td.transaction_id = t.id
			AND	account_id	=	'$id'
			AND t.at < $fromdate			
		";		
$resj	=	$AdminDAO->queryresult($queryj);
$balance=	$resj[0]['balance'];
if($balance>0)
{
	$sign 	= 	"Dr.";
}
else if($balance<0)
{
	$sign 	= 	"Cr.";
}
else
{
	$sign 	= 	"";
}
if($balance<0) 
{
	$unsignedbalance	=	$balance*(-1);
}
else
{
	$unsignedbalance	=	$balance;
}
/*<!--Code added by jafer 20-12-2012-->*/
?>
<tr style="font-weight:bold;"><!--Code added by jafer 20-12-2012-->
    <td colspan="5">&nbsp;</th>
    <td width="15%">Opening Balance</td>
    <td align="right"><div id="opbal"><?php echo number_format($unsignedbalance,2)." ".$sign; ?></div></td>
</tr>		<!--Code added by jafer 20-12-2012-->
<?php
$sumdr =0;
$sumcr =0;	
$newbalance	=	$balance;
foreach($res as $row)
{
	$i++;	
	$dr=$row['dr'];
	$cr=$row['cr'];
	$at=$row['at'];
	$tid=$row['transaction_id'];
	$sumdr +=$dr;
	$sumcr +=$cr;
/*<!--Code added by jafer 20-12-2012-->*/	
	$newbalance=$newbalance+$dr-$cr;	

if($newbalance>0)
{
	$newsign 	= 	"Dr.";
}
else if($newbalance<0)
{
	$newsign 	= 	"Cr.";
}
else
{
	$newsign 	= 	"";
}
if($newbalance<0) 
{
	$unsignednewbalance	=	$newbalance*(-1);
}
else
{
	$unsignednewbalance	=	$newbalance;
}	
/*<!--Code added by jafer 20-12-2012-->*/	
?>	
	<tr>
        <td><?php echo $i; ?></td>
        <td><?php echo $tid; ?></td>
        <td><?php echo date(("j/m/Y h:i:s A"),$at); ?></td>
        <td><?php echo $row['title']; ?></td>
        <td align="right"><?php echo number_format($dr,2); ?></td>
        <td align="right"><?php echo number_format($cr,2); ?></td>
        <td align="right"><?php echo number_format($unsignednewbalance,2)." ".$newsign; ?></td>        
    </tr>
<?php
}
$net	=	$sumdr - $sumcr;
if($net<0)
{
	$colour="red";	
	$netcr=$net;
	$netdr=0.00;
}
else
{
	$colour="#0C0";
	$netdr=$net;
	$netcr=0.00;
}
?>
<tr style="font-weight:bold;">
	<td colspan="4">Sums</td>
	<td align="right"><?php	echo number_format(abs($sumdr),2);?></td>
    <td align="right"><?php echo number_format(abs($sumcr),2);?></td>
    <td>&nbsp;</td>
</tr>
<tr style="font-weight:bold; color:#FFF; ">
	<td align="right" colspan="4">Balance:</td>
	<td align="right" style="color:<?php echo $colour;?>;"><?php echo number_format($netdr,2);?></td>
    <td align="right" style="color:<?php echo $colour;?>;"><?php echo number_format(abs($netcr),2);?></td>
    <td>&nbsp;</td>
</tr>
</table><br>
<?php 
} //while  
?>
</body>
</html>