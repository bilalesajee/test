<html>
<head>
<title>Report</title>
<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
</head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
<body>
<?php
include("../includes/security/adminsecurity.php");
///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////
global $AdminDAO;
if($_GET['fromdate']=='')
{
	$fromdatex			=	date('d-m-Y');
	$fromdate			=	explode("-",$fromdatex);
	$fromday			=	$fromdate[0];
	$frommon			=	$fromdate[1];
	$fromyr				=	$fromdate[2];
	$fromdate			=	mktime(0,0,0,$frommon,$fromday,$fromyr);
}
else
{
	$fromdate			=	explode("-",$_GET['fromdate']);
	$fromday			=	$fromdate[0];
	$frommon			=	$fromdate[1];
	$fromyr				=	$fromdate[2];
	$fromdate			=	mktime(0,0,0,$frommon,$fromday,$fromyr);
}
$todate				=	explode("-",$_GET['todate']);
$today				=	$todate[0];
$tomon				=	$todate[1];
$toyr				=	$todate[2];
$todate				=	@mktime(23,59,59,$tomon,$today,$toyr);
$collections			=	$AdminDAO->getrows("$dbname_detail.payments,$dbname_detail.sale,customer c,addressbook ab","pkpaymentid, companyname,amount,countername,FROM_UNIXTIME(paytime,'%d-%m-%Y') paymentdate,concat(ab.firstname,' ',ab.lastname) name,concat(c.firstname,' ',c.lastname) customername","fksaleid=pksaleid AND paymenttype='c' AND paymentmethod = 'c' AND $dbname_detail.sale.status=1  AND ab.pkaddressbookid=fkuserid AND fkaccountid=pkcustomerid AND paytime BETWEEN '$fromdate' AND '$todate'");
if(sizeof($collections)>0)
{
?>
<table class="simple">
    <tr>
        <th>Sr #</th>
        <th>ID</th>
        <th>Date</th>
        <th>Customer</th>
        <th>Company</th>
        <th>Counter</th>
        <th>Employee</th>
        <th>Amount</th>
    </tr>
    <?php
    for($i=0;$i<sizeof($collections);$i++)
    {
        $pkcashpaymentid	=	$collections[$i]['pkpaymentid'];
        $customername		=	$collections[$i]['customername'];
        $companyname		=	$collections[$i]['companyname'];
        $amount				=	$collections[$i]['amount'];
        $countername		=	$collections[$i]['countername'];
        $paymentdate		=	$collections[$i]['paymentdate'];
        $name				=	$collections[$i]['name'];
    ?>
    <tr>
        <td><?php echo $i+1;?></td>
        <td><?php echo $pkcashpaymentid;?></td>
        <td align="center"><?php echo $paymentdate;?></td>
      <td><?php echo $customername;?></td>
        <td><?php echo $companyname;?></td>
        <td><?php echo $countername;?></td>
        <td><?php echo $name;?></td>
        <td align="right"><?php echo number_format($amount,2);?></td>
    </tr>
    <?php
        $total	+=	$amount;
    }
    ?>
    <tr>
        <td colspan="7" align="right"><strong>Total</strong></td>
        <td align="right"><?php echo number_format($total,2);?></td>
    </tr>
</table>
</form> <!--end form-->
<?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>
<?php
}
else
echo "No results found";
?>
</body>
</html>