<html>

<head>

<title>Report</title>

<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />

</head>

<body>

<?php

include("../includes/security/adminsecurity.php");

global $AdminDAO;
$CounterName=$_GET['countername'];  //Added By Fahad 06-06-2012
if($CounterName!=''){
	$cond= " AND countername='$CounterName' ";
	}

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

$payouts			=	$AdminDAO->getrows("$dbname_detail.account,$dbname_detail.accountpayment,addressbook","title accounttitle,pkaccountpaymentid,editreasons,amount,countername,FROM_UNIXTIME(paymentdate,'%d-%m-%Y') paymentdate,description,concat(firstname,' ',lastname) name","id=fkaccountid AND pkaddressbookid=fkemployeeid AND paymentdate BETWEEN '$fromdate' AND '$todate'  $cond ");

if(sizeof($payouts)>0)

{

?>

    <table class="simple" align="center">

    <tr>

    <th>Sr #</th>

    <th>ID</th>

    <th>Date</th>

    <th>Account</th>

    <th>Counter</th>

    <th>Employee</th>

    <th>Description</th>

    <th>Amount</th>
    <th>Reasons</th>

    </tr>

    <?php

    for($i=0;$i<sizeof($payouts);$i++)

    {

        $pkaccountpaymentid	=	$payouts[$i]['pkaccountpaymentid'];

        $accounttitle		=	$payouts[$i]['accounttitle'];

        $amount				=	$payouts[$i]['amount'];
		 $reasons				=	$payouts[$i]['editreasons'];

        $countername		=	$payouts[$i]['countername'];

        $paymentdate		=	$payouts[$i]['paymentdate'];

        $description		=	$payouts[$i]['description'];

        $name				=	$payouts[$i]['name'];

    ?>

    <tr>

    <td><?php echo $i+1;?></td>

    <td><?php echo $pkaccountpaymentid;?></td>

    <td><?php echo $paymentdate;?></td>

    <td><?php echo $accounttitle;?></td>

    <td><?php echo $countername;?></td>

    <td><?php echo $name;?></td>

    <td><?php echo $description;?></td>

    <td align="right"><?php echo $amount;?></td>
    <td><?php echo $reasons;?>&nbsp;</td>

    </tr>

    <?php

        $total	+=	$amount;

    }

    ?>

    <tr>

    <td colspan="7" align="right"><strong>Total</strong></td>
    <td align="right"><?php echo number_format($total,2);?></td>

    <td colspan="2">&nbsp;</td>

    </tr>

    </table>

<?php }else{ ?>

<table class="simple" align="center">

    <tr>

    <th>Sr #</th>

    <th>ID</th>

    <th>Date</th>

    <th>Account</th>

    <th>Counter</th>

    <th>Employee</th>

    <th>Description</th>

    <th>Amount</th>
    <th>Reasons</th>

    </tr>

  
    <tr>

    <td colspan="9" style="text-align:center;">No Result Found</td>

  

    </tr>

   


    </table>


<?php }?>
</body>

</html>