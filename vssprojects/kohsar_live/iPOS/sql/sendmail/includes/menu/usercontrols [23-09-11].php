<div id="user">
<?php
if(file_exists("../security/adminsecurity.php"))
{
	require_once("../security/adminsecurity.php");
}
else
{
	require_once("includes/security/adminsecurity.php");
}
global $AdminDAO;
session_start();

//$countername	=	gethostbyaddr($_SERVER['REMOTE_ADDR']);
 $userid				=	$_SESSION['addressbookid'];
 $customerid			=	$_GET['customerid'];
 $creditcustomername	=	$_GET['creditcustomername'];
 if($customerid!='' && $creditcustomername!='')
 {
 	$_SESSION['customerid']=$customerid;
	$_SESSION['creditcustomername']=$creditcustomername;
 }
 $customerid			=	$_SESSION['customerid'];
 $creditcustomername	=	$_SESSION['creditcustomername'];
if(!isset($userid) || $userid == "" || $userid == 0)
{
	/*print"<script>
			function Func1()
			{
				window.location='../admin/userlogin.php';		
			}
			function Func1Delay()
			{
				setTimeout('Func1()', 2000);
			}
			Func1Delay();

		</script>";*/
		header("Location:userlogin.php");
		exit;
}
$date			=	date("d-m-y");
$name			=	$_SESSION['name'];
$userid			=	$_SESSION['addressbookid'];
$closingsalesarray	=	$AdminDAO->getrows("$dbname_main.closingsales","fksaleid"," countername='$countername' ");
for($i=0;$i<count($closingsalesarray);$i++)
{
	$fksaleid=$closingsalesarray[$i]['fksaleid'];
	$closedids	.=	"'".$fksaleid."',";
}
$closedids		=	rtrim($closedids,",");
if(count($closingsalesarray)>0)
{
	$andclosed="  AND pksaleid NOT IN($closedids) ";	
}
$sale			=	$AdminDAO->getrows("$dbname_main.sale","count(*) as bills", "fkuserid = '$userid' AND fkclosingid='$closingsession' AND status='1' $andclosed  AND countername='$countername' ");
$totalbills		=	$sale[0]['bills'];
/*for($i=0;$i<sizeof($sale);$i++)
{
	$saleid			=	$sale[$i]['pksaleid'];
	$salesum 		=	$AdminDAO->getrows("$dbname_main.saledetail","SUM(saleprice * quantity) as price"," from_unixtime(timestamp,'%Y-%m-%d')=CURDATE() AND fksaleid = '$saleid'");
	$price			+=	$salesum[0]['price'];
	$discount		+=	$sale[$i]['globaldiscount'];
}*/
//$price			=	$salesum[0]['price'];
//$total			=	$price-$discount;
/*echo "<pre>";
print_r($sale);
echo "</pre>";*/
/*for($i=0;$i<sizeof($sales);$i++)
{
	$totalsales+	=	$sales[$i][*/
  // echo "$closingsession is the closingsession";
?>
    <!--$userid changed and date removed by Yasir - 01-07-11-->
    <b><span style="color:#EC7600">Welcome <?php echo "<b>{$_SESSION['name']}</b>";?></span></b>
    <b><span style="color:#063">Counter: <?php echo $_SESSION['countername'];?></span></b>
    <!--<b>Total Sales:</b> Rs. <?php //echo $total; ?>-->
    <b><span style="color:#C0C">Total Bills: <?php echo $totalbills;?></span></b>
    <!--<b>Opening Balance:</b>--> 
    <b><span style="color:#3C6">Last Return:
   <?php
	$returnquery	=	"SELECT MAX(pksaleid) as saleid FROM $dbname_main.sale WHERE fkuserid = '$userid' AND fkclosingid='$closingsession' AND status='1' AND countername='$countername'";
	$returnqueryres	=	$AdminDAO->queryresult($returnquery);
	$saleid			=	$returnqueryres[0]['saleid'];
   	$adjustquery	=	"SELECT ((SELECT ((IF(sum(tendered)IS NULL,0,sum(tendered)))-(IF(sum(amount)IS NULL,0,sum(amount)))) as amt FROM $dbname_main.cashpayment ,$dbname_main.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)
							+
							(SELECT ((IF(sum(tendered)IS NULL,0,sum(tendered)))-(IF(sum(amount)IS NULL,0,sum(amount)))) as amt FROM $dbname_main.ccpayment ,$dbname_main.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)
							+
							(SELECT ((IF(sum(tendered*rate)IS NULL,0,sum(tendered*rate)))-(IF(sum(amount*rate)IS NULL,0,sum(amount*rate)))) as amt FROM $dbname_main.fcpayment ,$dbname_main.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)
							+
							(SELECT (IF(sum(tendered)IS NULL,0,sum(tendered)))-(IF(sum(amount)IS NULL,0,sum(amount))) as amt FROM $dbname_main.chequepayment ,$dbname_main.sale s1 WHERE s1.pksaleid =  '$saleid' AND pksaleid = fksaleid)) AS adjustment
							";
		//$adjustquery	=	"SELECT adjustment FROM $dbname_main.sale WHERE pksaleid='$saleid'";
		//$adjustquery	=	"SELECT adjustment FROM $dbname_main.sale ORDER by pksaleid DESC LIMIT 0,1";
		$adjustments	=	$AdminDAO->queryresult($adjustquery);
		$adjustment		=	$adjustments[0]['adjustment'];
	echo $adjustment;
//	echo " closing = $closingsession";
	?>
    </span></b>
   
</div>
<div id="creditcutomerdiv"><div   style="position:absolute;background-color:#000;padding:3px;-moz-border-radius:3px;-webkit-border-radius:4px;font-weight:bold;color:#fff;float:right;margin-left:600px;" ><?php if($creditcustomername!=''){ echo "Credit for: ".$creditcustomername;}?></div></div>