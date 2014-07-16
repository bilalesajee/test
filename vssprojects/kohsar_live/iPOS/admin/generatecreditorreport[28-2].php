<?php session_start();

if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>

    <html>

    <head>

    <title>Report</title>

    <link rel="stylesheet" type="text/css" href="../includes/css/style.css" />

    <style type="text/css">

    <!--

    .style1 {

        font-size: 16px;

        font-weight: bold;

    }

    .style2 {

        font-size: 18px

    }

    .style4 {

        font-size: 14px;

        font-weight: bold;

    }

    -->

    </style>

    <script language="javascript" src="../includes/js/jquery.js"></script>

    <script language="javascript" src="../includes/js/jquery.form.js"></script>

    <script language="javascript">

        

            function updatenow()

            {

                //loading('Please wait while your report is generated ...');

                            if(confirm("Are you sure to change these dates. Changes may have surely effect your previouse reports."))

                           {

                    options	=	{	

                                    url : 'savecreditorreportdate.php',

                                    type: 'POST',

                                    success: response

                                }

                                jQuery('#creditaccountstatementfrm').ajaxSubmit(options);

                           }

                           else

                           {

                            return false;   

                            }

            }

            function response()

            {

                alert("Changes you made has  been saved.");

                window.location.reload();

            }

            function updatewriteoff(woamount,actualamount,pksaleid,favouredbyid)

            {

                if(confirm("Are you sure to give write off this will surely effect your reports."))

                {

                    //alert('?woamount='+woamount+'&actualamount='+actualamount+'&pksaleid='+pksaleid);

                    var url='givewriteoff.php?woamount='+woamount+'&actualamount='+actualamount+'&pksaleid='+pksaleid+'&favouredbyid='+favouredbyid;

                    jQuery('#writeoffdiv').load(url);

                    window.location.reload();

                }

            }

        </script>

    </head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
    <body>

    <?php

    //echo $_SERVER['QUERY_STRING'].'<br>-------------------------------------------------<br>';

    //tempsaleid=39058&reporttype=3&taxpercentage=16&customerid=31&customercopy=y&serialno=&adjustmentmode=0

    //tempsaleid=39058&reporttype=3&taxpercentage=16&customerid=31&customercopy=y&serialno=&adjustmentmode=0

    error_reporting(7);

    include("../includes/security/adminsecurity.php");
	///////////////////////add by wajid for excel export/////////////////////////////////////////
include_once("../export/exportdata.php");
///////////////////////////////////////////////////////////////////////////////////



    global $AdminDAO;

        $reporttype			=	$_GET['reporttype'];

        $cid				=	$_GET['customerid'];
		$payment_via		=	$_GET['payment_via'];
		$payment_cond ='';
	if($payment_via!= '1')
{
$payment_cond = " and paymentmethod != 'c' and paymentmethod != 'ch' ";
}

        // retrieving customer type to show either serial no or bill no

        $customers		=	$AdminDAO->getrows("$dbname_main.customer","ctype","pkcustomerid='$cid'");

        // 1=Hotel , 2=creditor

        $ctype				=	$customers[0]['ctype'];

        $taxpercentage		=	$_GET['taxpercentage'];

        $salestaxper		=	$taxpercentage;

        $customercopy		=	$_GET['customercopy'];

        $officecopy			=	$_GET['officecopy'];

        $salestaxcopy		=	$_GET['salestaxcopy'];

        $serialno			=	$_GET['serialno'];

        $invoicedate		=	$_GET['invoicedate'];

        $adjustmentmode		=	$_GET['adjustmentmode'];

        $writeoffmode		=	$_GET['writeoffmode'];

        $favouredbyid		=	$_GET['favouredbyid'];

        if($writeoffmode=='1')

        {

            if($favouredbyid=='')

            {

                ?>

    <script language="javascript">

                alert("Please select the Favoting Authority Name to give writeoff.");

                self.close();

                </script>

    <?php

            }

        }	

        if($invoicedate=='')

        {

            $lastday 			= @mktime(0, 0, 0, date('m')+1, 0, date('Y'));

            $lastdayofthemonth	= @strftime("%d", $lastday);

    

            $invoicedate=$lastdayofthemonth.date('-m-Y');

        }

        $customersql="SELECT 

                            CONCAT(firstname,' ',lastname) as customername,

                            companyname title,taxnumber, ntn

                        FROM 

                            $dbname_main.customer

                            WHERE

                                  pkcustomerid='$cid'";

        $custarr			=	$AdminDAO->queryresult($customersql);

        $customername		=	$custarr[0]['customername'];

        $companyname		=	$custarr[0]['title'];

        $taxno				=	$custarr[0]['taxnumber'];

        $ntn				=	$custarr[0]['ntn'];

        if($companyname=='')

        {

            $companyname=$customername;

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

        if($reporttype==1)//credit account summary

        { 

           $customerbalbf		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd, $dbname_main.customer c","sum(sd.saleprice*sd.quantity) as balbf","s.fkaccountid=pkcustomerid AND s.status=1 AND pkcustomerid='$cid' AND sd.fksaleid=pksaleid  and  s.datetime < '$fromdate' ");

          // print_r($customerbalbf); 

             //$sqltest="SELECT sum(sd.saleprice*sd.quantity) as balbf FROM $dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.account c where s.fkaccountid=id AND s.status=1 AND  id='$cid' AND sd.fksaleid=pksaleid AND s.datetime < '$fromdate'";		

             $totalsale	=	$customerbalbf[0]['balbf'];

           $query		=	"SELECT SUM(amount) amount FROM $dbname_detail.sale,$dbname_detail.payments where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' and status=1 group by fkaccountid,paymentmethod";

    /*

    UNION SELECT 2 as type,pksaleid,creditinvoiceno, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.ccpayment where fkcustomerid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' and status=1 group by fkcustomerid

    

    UNION SELECT 3 as type,pksaleid,creditinvoiceno, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.fcpayment where fkcustomerid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' and status=1 group by fkcustomerid

    

    UNION SELECT 4 as type,pksaleid,creditinvoiceno, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.chequepayment where fkcustomerid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' and status=1 group by fkcustomerid";*/

            //echo $query;

            //exit;

            

            $balance	=	$AdminDAO->queryresult($query);

/*			echo "<pre>";

			print_r($balance);

			echo "</pre>";

*/			foreach ($balance as $bal)

			{

				$totalpaid+=$bal['amount'];

			}



            /*$totalpaid	=	$balance[0]['amount'];

            $totalpaid	+=	$balance[1]['amount'];

            $totalpaid	+=	$balance[2]['amount'];

            $totalpaid	+=	$balance[3]['amount'];*/

            //print"Sale: $totalsale paid:$totalpaid<br>";

            $sqldiscount="select sum(globaldiscount) as gd from $dbname_detail.sale where fkaccountid='$cid' and status=1 and datetime < '$fromdate'"; // added  and datetime < '$fromdate' by Yasir -- 11-07-11

            $discountarr	=	$AdminDAO->queryresult($sqldiscount);

            $gd	=	$discountarr[0]['gd'];//global discount

            $balbf=$totalsale-$totalpaid-$gd;

            //$balbf=$totalpaid-$totalsale;

            

            

            /* query replaced by Yasir -- 11-07-11

                $customerinfo		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_detail.customer c,$dbname_detail.addressbook a","s.pksaleid,s.creditinvoiceno,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,sum(sd.saleprice*sd.quantity) as amount,s.globaldiscount","s.fkcustomerid=pkcustomerid AND s.status=1 AND c.fkaddressbookid=pkaddressbookid AND pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime BETWEEN '$fromdate' AND '$todate' GROUP BY pksaleid");

            */

            

            /*

            $customerinfo		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.customer c,$dbname_detail.addressbook a","s.pksaleid, s.pksaleid as pksid,s.creditinvoiceno,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,(totalamount - globaldiscount - ((SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) FROM $dbname_detail.sale, $dbname_detail.cashpayment WHERE pksaleid = fksaleid AND fkcustomerid = '$cid' AND paymenttype <> 'c' AND paytime BETWEEN '$fromdate' AND '$todate' AND fksaleid = pksid)+(SELECT IF(SUM(amount*rate) IS NULL,0,SUM(amount*rate)) FROM $dbname_detail.sale, $dbname_detail.fcpayment WHERE pksaleid = fksaleid AND fkcustomerid = '$cid' AND paymenttype <> 'c' AND paytime BETWEEN '$fromdate' AND '$todate' AND fksaleid = pksid)+(SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) FROM $dbname_detail.sale, $dbname_detail.ccpayment WHERE pksaleid = fksaleid AND fkcustomerid = '$cid' AND paymenttype <> 'c' AND paytime BETWEEN '$fromdate' AND '$todate' AND fksaleid = pksid)+(SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) FROM $dbname_detail.sale, $dbname_detail.chequepayment WHERE pksaleid = fksaleid AND fkcustomerid = '$cid' AND paymenttype <> 'c' AND paytime BETWEEN '$fromdate' AND '$todate' AND fksaleid = pksid))) as amount,s.globaldiscount","s.fkcustomerid=pkcustomerid AND s.status=1 AND c.fkaddressbookid=pkaddressbookid AND pkcustomerid='$cid' AND s.datetime BETWEEN '$fromdate' AND '$todate'"); // comented by Yasir 02-08-11

            */

    $custo	=	"SELECT s.pksaleid, s.pksaleid as pksid,s.serialno,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,s.globaldiscount  FROM $dbname_detail.sale s, $dbname_main.customer c WHERE s.fkaccountid=pkcustomerid AND s.status=1 AND  pkcustomerid='$cid' AND s.datetime BETWEEN '$fromdate' AND '$todate'";

                $customerinfo_	=	$AdminDAO->queryresult($custo);
/*
          echo  $customerinfo_		=	$AdminDAO->getrows("$dbname_detail.sale s,main.customer c","s.pksaleid, s.pksaleid as pksid,s.serialno,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,s.globaldiscount","s.fkaccountid=pkcustomerid AND s.status=1 AND  pkcustomerid='$cid' AND s.datetime BETWEEN '$fromdate' AND '$todate'");
*/
            

            $i__ 	=	0;// rest remaining -- 28-12-2011

            foreach($customerinfo_ as $customerinfoval){

                 $customeramountsql	=	"SELECT 

                                            pksaleid, 

                                            (totalamount - globaldiscount - (SELECT IF(SUM(amount) IS NULL,0,SUM(amount)) FROM $dbname_detail.sale, $dbname_detail.payments WHERE pksaleid = fksaleid AND fkaccountid = '$cid' AND paymenttype <> 'c' AND fksaleid = '{$customerinfoval['pksaleid']}')) as amount FROM $dbname_detail.sale WHERE pksaleid = '{$customerinfoval['pksaleid']}'";

                $customeramountarr	=	$AdminDAO->queryresult($customeramountsql);

                /*echo "</pre>";

				print_r($customeramountarr);

				echo "</pre>";

				exit;*/

				

                $customerinfo[$i__]['pksaleid']			=  $customerinfoval['pksaleid'];

                $customerinfo[$i__]['pksid']			=  $customerinfoval['pksid'];

                $customerinfo[$i__]['serialno']	=  $customerinfoval['serialno'];

                $customerinfo[$i__]['trdatetime']		=  $customerinfoval['trdatetime'];

                $customerinfo[$i__]['amount']			=  $customeramountarr[0]['amount'];

                $customerinfo[$i__]['globaldiscount']	=  $customerinfoval['globaldiscount'];

                $i__++;						

                }

        

                

        //collections results

        $sql="SELECT s.pksaleid,s.serialno,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,sum(sd.saleprice*sd.quantity) as amount

                    FROM

                        $dbname_detail.sale s,$dbname_detail.saledetail sd, $dbname_main.customer

                    WHERE

                        s.fkaccountid = pkcustomerid AND s.status=1 AND  pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime BETWEEN '$fromdate' AND '$todate' GROUP BY pksaleid

                        ";

                        

        // amount replaced by amount*rate in fcpayment by Yasir -- 11-07-11				

                

        $collectionqry="

                    SELECT paymentmethod type,pksaleid,serialno,pkpaymentid as trid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.payments where fkaccountid='$cid' and fksaleid=pksaleid and paytime BETWEEN '$fromdate'   and 	'$todate' $payment_cond   group by paymentmethod,trdatetime";

    /*

    UNION SELECT 2 as type,pksaleid,creditinvoiceno, pkccpaymentid as trid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.ccpayment where fkcustomerid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime  BETWEEN '$fromdate' AND	'$todate'  group by trdatetime

    

    UNION SELECT 3 as type,pksaleid,creditinvoiceno, pkfcpaymentid as trid,concat('-',SUM(amount*rate)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.fcpayment where fkcustomerid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime  BETWEEN '$fromdate' AND	'$todate' group by trdatetime

    

    UNION SELECT 4 as type,pksaleid,creditinvoiceno, pkchequepaymentid as trid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.chequepayment where fkcustomerid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime BETWEEN '$fromdate' AND	'$todate'  group by chequeno

    ";*/

    

        //for()

        $collectarr	=	$AdminDAO->queryresult($collectionqry);

        /*echo "<pre>";

		print_r($collectarr);

		echo "</pre>";*/

        if(sizeof($collectarr)>0)

        {

            $customerinfo=	@array_merge($customerinfo,$collectarr);

        }

    ?>

    <div id="writeoffdiv"></div>

    <div style="width:8in;padding:0px;font-size:17px;" align="center"> <img src="../images/esajeecologo.jpg" width="197" height="58"> <br />

      <span style="font-size:11px;font-family:'Comic Sans MS', cursive;"> <b>Think globally shop locally</b> </span> </div>

    <table class="simple" style="width:8in">

      <tr>

        <th colspan="5">Credit Account Summary</th>

      </tr>

      <tr>

        <td><b>Detail of <span style="text-transform:uppercase"><?php echo $companyname;?></span></b></td>

        <td colspan="4">From <?php echo date("d-m-Y",$fromdate);?> To Date <?php echo $_GET['todate'];?></td>

      </tr>

      <tr>

        <td><b>B/F</b></td>

        <td colspan="4"><?php echo number_format($balbf,2);?></td>

      </tr>

      <tr>

        <th><?php if($ctype==1) {echo "Serial #";} else { echo "Bill #";}?></th>

        <th>Date</th>

        <th colspan="2">Amount</th>

        <th>Total</th>

      </tr>

      <?php

        }

        if($balbf=='')

        {

            $forward=0;

        }

        else

        {

            $forward=$balbf;

        }

        $range	=	@gregoriantojd($tomon,$today,$toyr)-@gregoriantojd($frommon,$fromday,$fromyr);

        $range	+=1;

        $stdate	=	$fromdate;

        for($j=1;$j<=$range;$j++)

        {

            $sdate	=	date("d-m-Y",$stdate);

            for($k=0;$k<sizeof($customerinfo);$k++)

            {

                if($sdate== $customerinfo[$k]['trdatetime'])

                {

                $pksaleid			=	$customerinfo[$k]['pksaleid'];

                $creditinvoiceno	=	$customerinfo[$k]['serialno'];

                $reportdate			=	$customerinfo[$k]['trdatetime'];

                $amount				=	$customerinfo[$k]['amount'];

               $type				=	$customerinfo[$k]['type'];
				//print_r($type);

                $trid				=	$customerinfo[$k]['trid'];

                $globaldiscount		=	$customerinfo[$k]['globaldiscount'];

                

                $sqlwo="select SUM(amount) as woamount from $dbname_detail.baddebts where fkbillid='$pksaleid'";

                $woarr	=	$AdminDAO->queryresult($sqlwo);

                $totwoamount	=	$woarr[0]['woamount'];

                if($totwoamount>0)

                {

                  $amount=$amount-$totwoamount-$globaldiscount;

                    $bg="bgcolor='gray'";

                }

                

                else

                {

                    $bg="bgcolor='white'";

                }

                $forward	+=	$amount;

                if($type=='c')

                {

                    $type='Payment (cash)';

                }

                if($type=='cc')

                {

                    $type='Payment (cc)';

                }

                if($type=='ff')

                {

                    $type='Payment (fc)';

                }

                if($type=='ch')

                {

                    $sqlchq="select chequeno from $dbname_detail.payments where fksaleid='$pksaleid' and  pkpaymentid='$trid' LIMIT 0,1";

                    $chqarr	=	$AdminDAO->queryresult($sqlchq);

                    $chqueno	=	$chqarr[0]['chequeno'];	

                    $type="Payment (cheque $chqueno)";

                }
				////////////////////////add by wajid////////////////////////////////////
    if($type=='' && $amount<0){
	
	 $retn_tot = $retn_tot+$amount;
	 }
	 
	
	  if($amount<0 && $type!=''){
	
	 $pay_tot2 = $pay_tot2+$amount;}
	
        ?>

      <tr>

        <td><?php echo $pksaleid;//if($ctype==1) {echo $creditinvoiceno;} else {echo $pksaleid;}?></td>

        <td align="center"><?php echo $reportdate;?></td>

        <td ><?php if($amount<0 && $type!=''){print"$type";}elseif($type=='' && $amount<0){print"Return";}?></td>

        <td align="right" <?php echo $bg;?>><?php

                

                $len	=	strlen($amount);

                if($writeoffmode!=1 || $amount<0)

                {

                    echo number_format($amount,2);
$totl_sale =$totl_sale+$amount;
                    //print"gd=$globaldiscount";

                }

                else

                {

                    ?>

          <input type="text" name="woamount" id="woamount" value="<?php echo $amount;?>" size="5" maxlength="<?php echo $len;?>"  onkeydown="javascript:if(event.keyCode==13) {updatewriteoff(this.value,<?php echo $amount;?>,<?php echo $pksaleid;?>,<?php echo $favouredbyid;?>);return false;}">

          <?php

                }

                ?></td>

        <td align="right"><?php echo number_format($forward,2);?></td>

      </tr>

      <?php

                }

            }

            $stdate	=	mktime(0,0,0,$frommon,$fromday+$j,$fromyr);

        }

        ?>

    </table>

    <?php

        if($reporttype==1 && $writeoffmode!=1)

        {

        ?>

    <script language="javascript">

            window.print();

        </script>

    <?php

        }

        //end of if credit account summary

        

        if($reporttype==2)//credit account summary

        {

            $customerbalbf		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_main.customer c","sum(sd.saleprice*sd.quantity) as balbf","s.fkaccountid=pkcustomerid AND s.status=1 AND  pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime < '$fromdate'");

            $totalsale	=	$customerbalbf[0]['balbf'];

            $query		=	"SELECT paymentmethod as type,pksaleid, SUM(amount) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.payments where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid

    ";

            //echo $query;

            //exit;

            

            $balance	=	$AdminDAO->queryresult($query);

            $totalpaid	=	$balance[0]['amount'];

            //print"Sale: $totalsale paid:$totalpaid<br>";

			            

            $totalpaid	+=	$balance[1]['amount'];

            $totalpaid	+=	$balance[2]['amount'];

            $totalpaid	+=	$balance[3]['amount'];

            $balbf=$totalsale-$totalpaid;

    		

            $sql="SELECT

                    pksaleid,

                    serialno,

                    sum(sd.saleprice*sd.quantity) as amount,

                    sd.saleprice,

                    sd.quantity,

                    b.itemdescription,

                    FROM_UNIXTIME(s.datetime,'%d-%m-%Y') as trdatetime

                    FROM

                        $dbname_detail.sale s,

                        $dbname_detail.saledetail sd,

                        $dbname_main.customer c,

                        $dbname_main.barcode b,

                        $dbname_detail.stock st

                    WHERE

                        s.fkaccountid=c.pkcustomerid AND 

                        s.status=1 AND  

                        pkcustomerid='$cid' AND 

                        sd.fksaleid=pksaleid AND 

                        s.datetime BETWEEN '$fromdate' AND '$todate' AND

                        sd.fkstockid=st.pkstockid AND sd.quantity>0 and

                        

                        b.pkbarcodeid=st.fkbarcodeid

                        GROUP BY sd.pksaledetailid

                        

                        order by s.pksaleid ASC

                    ";

                    $customerinfo	=	$AdminDAO->queryresult($sql);

       $collectionqry="

                    SELECT paymentmethod as type,pksaleid,pkpaymentid as trid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.payments where fkaccountid='$cid' and fksaleid=pksaleid  $payment_cond and paytime BETWEEN '$fromdate' AND '$todate' group by paymentmethod,trdatetime

    ";

    	

        $collectarr	=	$AdminDAO->queryresult($collectionqry);

		

		/*echo "<pre>";

		print_r($collectarr);

		echo "</pre>";

		exit;*/

        if(sizeof($collectarr)>0)

        {

            $customerinfo=	@array_merge($customerinfo,$collectarr);

        }

        //$customerinfo=	@array_merge($collectarr,$customerinfo);

        ?>

    <div style="width:8in;padding:0px;font-size:17px;" align="center"> <img src="../images/esajeelogo.jpg" width="197" height="58"> <br />

      <span style="font-size:11px;font-family:'Comic Sans MS', cursive;"> <b>Think globally shop locally</b> </span> </div>

    <form name="creditaccountstatementfrm" id="creditaccountstatementfrm" method="post">

      <?php

            if($adjustmentmode==1)

            {

        ?>

      <input type="button" name="btn" onClick="updatenow()" value="Update">

      <?php

            }

        ?>

      <table class="simple">

        <tr>

          <th colspan="7">Credit Account Statement</th>

        </tr>

        <tr>

          <td><b>Detail of <span style="text-transform:uppercase"><?php echo $companyname;?></span></b></td>

          <td colspan="6">From <?php echo date("d-m-Y",$fromdate);?> To Date <?php echo $_GET['todate'];?></td>

        </tr>

        <tr>

          <td><b>B/F</b></td>

          <td colspan="6"><?php echo number_format($balbf,2);?></td>

        </tr>

        <tr>

          <th><?php if($ctype==1) {echo "Serial #";} else { echo "Bill #";}?></th>

          <th>Date</th>

          <th >itemdescription</th>

          <th >Quantity</th>

          <th >Sale Price</th>

          <th >Amount</th>

          <th >&nbsp;</th>

        </tr>

        <?php

            if($balbf=='')

            {

                $forward=0;

            }

            else

            {

                $forward=$balbf;

            }

        $range	=	gregoriantojd($tomon,$today,$toyr)-gregoriantojd($frommon,$fromday,$fromyr);

        $range	+=1;

        $stdate	=	$fromdate;

        for($j=1;$j<=$range;$j++)

        {

            $sdate	=	date("d-m-Y",$stdate);

            for($k=0;$k<sizeof($customerinfo);$k++)

            {

                if($sdate== $customerinfo[$k]['trdatetime'])

                {

                    $pksaleid			=	$customerinfo[$k]['pksaleid'];

                    $creditinvoiceno	=	$customerinfo[$k]['serialno'];

                    $amount				=	$customerinfo[$k]['amount'];

                    $saleprice			=	$customerinfo[$k]['saleprice'];

                    $quantity			=	$customerinfo[$k]['quantity'];

                    $itemdescription	=	$customerinfo[$k]['itemdescription'];

                     $type				=	$customerinfo[$k]['type'];

                    $reportdate			=	$customerinfo[$k]['trdatetime'];

                    $trid				=	$customerinfo[$k]['trid'];

                     $forward			+=	$amount;

                    if($type=='1')

                    {

                        $typetext='Cash Recieved';

                    }

                    if($type=='2')

                    {

                        $typetext='Credit Card ';

                    }

                    if($type=='3')

                    {

                        $typetext='Foreign Currency';

                    }

                    if($type=='4')

                    {

                        $sqlchq="select chequeno from $dbname_detail.payments where fksaleid='$pksaleid' and  pkpaymentid='$trid' LIMIT 0,1";

                            $chqarr	=	$AdminDAO->queryresult($sqlchq);

                            $chqueno	=	$chqarr[0]['chequeno'];	

                            //$type="(cheque $chqueno)";

                            $typetext="$chqueno Cheque Recieved";

                            

                    }

                    //echo $type;

                    //skip zero quantity

                    if($quantity==0)

                    {

                        continue;

                    }

            ?>

        <tr>

          <td><?php if($pksaleid!=$oldsaleid){ if($ctype==1) {echo $creditinvoiceno;} else {echo $pksaleid;}}else{print"&nbsp;";}?></td>

          <td align="center"><?php 

                        if($adjustmentmode==1 && $type!='')

                        {

                        ?>

            <input type="hidden" name="paymentmode[]" id="paymentmode" value="<?php echo $type;?>" >

            <input type="hidden" name="olddate[]" id="olddate" value="<?php echo $reportdate;?>" >

            <input type="text" name="trdatetime[]" id="trdatetime" value="<?php echo $reportdate;?>" size="10">

            <?php

                        }

                        else

                        {

                            echo $reportdate;

                        }

                    ?></td>

          <td><?php if($type!=''){echo '<b>'.$typetext.'</b>';}else{ echo $itemdescription;}?></td>

          <td align="right"><?php echo $quantity;?></td>

          <td align="right" ><?php echo $saleprice;?></td>

          <td align="right"><?php  echo number_format($amount,2);?></td>

          <td><?php echo number_format($forward,2);?></td>

        </tr>

        <?php

                $oldsaleid=$pksaleid;

                }

            }

            $stdate	=	mktime(0,0,0,$frommon,$fromday+$j,$fromyr);

        }

            //}//end of for

        ?>

        <input type="hidden" name="customerid" id="customerid" value="<?php echo $cid;?>" >

      </table>

    </form>

    <?php

            if($adjustmentmode!=1)

            {

        ?>

    <script language="javascript">

            window.print();

        </script>

    <?php

            }

            else

            {

            ?>

    <input type="button" name="btn" onClick="updatenow()" value="Update">

    <?php	

            }

            //end of adjustmentmode

        }//end of if credit account summary

        

        if($reporttype==3)// Sales Tax Invoice

        {
			    $serialno			=	$_GET['serialno'];
                $invoicedate		=	$_GET['invoicedate'];

            $tempsaleid	=	$_GET['tempsaleid'];

            if($serialno!=''){

                $and=" s.serialno='$serialno' AND";

                

            }else{

            if($invoicedate=''){

                $and=" s.datetime BETWEEN '$fromdate' AND '$todate'  AND ";

            }else{
						$and=" FROM_UNIXTIME(s.datetime,'%d-%m-%Y')= '$invoicedate'  AND ";
				}

			}
             $sql="SELECT

                    

                    pksaleid,

                    sum(sd.saleprice*sd.quantity) as amount,

                    sd.saleprice,

                    sd.quantity,

                    b.itemdescription,

                    FROM_UNIXTIME(s.datetime,'%d-%m-%Y') as trdatetime,

                    sd.taxable

                    FROM

                        $dbname_detail.sale s,

                        $dbname_detail.saledetail sd,

                        $dbname_main.customer c,

                        $dbname_main.barcode b,

                        $dbname_detail.stock st

                    WHERE

                        s.fkaccountid=c.pkcustomerid AND 

                        s.status=1 AND  

                        c.pkcustomerid='$cid' AND 

                        sd.fksaleid=pksaleid and sd.quantity>0 AND 

                        $and

                        sd.fkstockid=st.pkstockid AND 

                        

                        b.pkbarcodeid=st.fkbarcodeid AND 

                        st.fkbarcodeid<>62007

                        GROUP BY sd.pksaledetailid

                        

                        order by s.pksaleid ASC

                    ";

                    

                    $customerinfo	=	$AdminDAO->queryresult($sql);

                    //is_object(

                    //dump($customerinfo);

                    $loader	=	$_SESSION['loader'];

                    if(sizeof($customerinfo)<1 && $loader<2)

                    {

                        ?>

    <script language="javascript">

                            window.location.reload();

                        </script>

    <?php

                        $loader++;

                    }

                    $_SESSION['loader']=$loader;

        ?>

    <div align="center"> <span class="style2">Sales Tax Invoice</span> <br>

      <span id='copytd'>(Customer Copy)</span>

      </th>

    </div>

    <span id="serial" style="position:absolute; margin-top:40px; margin-left:400px"> <b>

    Date: <?php if($tempsaleid!=''){echo date('d-m-Y');}else{echo $invoicedate;} //requested by Ali from kohsar store 19-10-2010?>

    </b> <br>

    <b>Serial No: <?php echo $serialno;?></b> <br>

    <br>

    <strong>Sales Tax Registration No: </strong><br>

    07-01-2100-082-55 <br>

    <strong>NTN: 0344289-6</strong></span>

    <div style="width:8in;padding:0px;margin-left:-200px;font-size:17px;" align="center"> <img src="../images/esajeecologo.jpg" width="286" height="77"> <br />

      <span style="font-size:11px; line-height:15px"> <span class="style1">Think globally shop locally</span><br />

      <span class="style4">Importers & General Order Suppliers </span><strong><br />

      </strong>H. No. 19, Masjid Road, Sector F-6/3, Islamabad.<br />

    Tel:- 2825100 Ext 102, Fax: 051-2279919<br />

    Email: esajee@esajee.com <br />

    Website: www.esajee.com<br />

      </span> </div>

    <div> <br>

      Buyer's Name: <b><span style="text-transform:uppercase"><?php echo $companyname;?></span></b> <br>

      Buyer's NTN:<b>

      <?php if($ntn!='0' || $ntn!=''){echo $ntn;}else{?>

      __________

      <?php } ?>

      </b> <br>

    </div>

    <div align="center">

      <!--<span style="text-transform:uppercase"><?php //if($tempsaleid==''){?>From <b><?php //echo date("d-m-Y",$fromdate);?></b> To <b><?php //echo $_GET['todate'];?></b><?php //} ?></span>-->

    </div>

    <table class="simple">

      <tr>

        <!--<th>#</th>

            <th>Date</th>-->

        <th >itemdescription</th>

        <th >Quantity</th>

        <th >Sale Price</th>

        <?php  

            // $taxpercentage	=	$AdminDAO->getrows("$dbname_detail.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");

             //$salestaxper		=	$taxpercentage[0]['amount'];

             if($salestaxper>0)//when the tax percentage is is 0 will not show these two columns

             {

             ?>

        <th >Amount</th>

        <th >S.Tax <?php echo $salestaxper;?>%</th>

        <?php

            }

            ?>

        <th >Total Value</th>

      </tr>

      <?php

            //dump($customerinfo);

            $taxesandcharges	=	array("Sales Tax","Exempt Tax","Delivery Charges");

            

            for($i=0;$i<count($customerinfo);$i++)

            {

            $pksaleid		=	$customerinfo[$i]['pksaleid'];

            $amount			=	$customerinfo[$i]['amount'];

            $saleprice		=	$customerinfo[$i]['saleprice'];

            $quantity		=	$customerinfo[$i]['quantity'];

            $itemdescription=	$customerinfo[$i]['itemdescription'];

            $type			=	$customerinfo[$i]['type'];

            $reportdate		=	$customerinfo[$i]['trdatetime'];

            $taxable		=	$customerinfo[$i]['taxable'];

            if(in_array($itemdescription,$taxesandcharges))

            {

                continue;

            }

            if($taxable!=1)

            {

                $tax=$salestaxper/100*$amount;

            }

            else

            {

                $tax=0;

            }

            ?>

      <tr>

        <td style="text-transform:capitalize"><?php echo ucfirst(strtolower($itemdescription));?></td>

        <td align="right"><?php echo $quantity;?></td>

        <td align="right"><?php echo number_format($saleprice,2);?></td>

        <?php  

             if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

             {

             ?>

        <td align="right"><?php  echo number_format($amount,2);?></td>

        <td align="right"><?php echo number_format($tax,2);?></td>

        <?php

              }

              ?>

        <td align="right"><?php echo number_format($amount+$tax,2);?></td>

      </tr>

      <?php

            $totalamount+=$amount;

            $totaltax+=$tax;

            }//end of for

        ?>

      <tr>

        <td colspan="3" align="right"><b>Grand Total:</b></td>

        <td align="right"><b>

          <?php  echo number_format($totalamount,2);?>

          </b></td>

        <?php

                 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

                 {

                 ?>

        <td align="right"><b><?php echo number_format($totaltax,2);?></b></td>

        <td align="right"><b><?php echo number_format($totalamount+$totaltax,2);?></b></td>

        <?php

                }

                ?>

      </tr>

    </table>

    <script language="javascript">

        <?php

        if($customercopy=='y')

        {

        ?>

                document.getElementById('copytd').innerHTML='(Customer Copy)';

                //window.print();

            <?php

            }

            

            if($officecopy=='y')

            {

            ?>

                document.getElementById('copytd').innerHTML='(Office Copy)';

                //window.print();

            <?php

            }

            if($salestaxcopy=='y')

            {

            ?>

                document.getElementById('copytd').innerHTML='(Sales Tax Office Copy)';

                //window.print();

            <?php

            }

            ?>

        </script>

    <?php

    

        }//end of if  Sales Tax Invoice

        ?>
        <?php 
		if($reporttype == 1)
		{
			 $re=abs($retn_tot);
		  $pay=abs($pay_tot2);
			 $ba=abs($balbf);
		 $balanc=$totalsale-$re-$pay+$ba;
			 
			 
			 
		?>
        <!--//////////////////////////////////////////////////////add by wajid//////////////////////////////////////////-->
<table width="399" >
  <tr>
    <td width="79" style="border:none"><strong>Total Sale:</strong></td>
     <td width="308" style="border:none"><strong><?php echo $totalsale; ?></strong></td>
  </tr>
  <tr>
    <td style="border:none"><strong>Total Return:</strong></td>
    <td style="border:none"><strong><?php if($retn_tot!=''){ echo $retn_tot;} else {echo "0";}?></strong>&nbsp;</td>
  </tr>
  <tr>
    <td style="border:none"><strong>Total Receipts</strong></td>
    <td style="border:none"><strong><?php if($pay_tot2!=''){ echo $pay_tot2;} else {echo "0";}?></strong>&nbsp;</td>
  </tr>
 <?php /*?> <tr>
    <td style="border:none"><strong>Balance:</strong></td>
    <td style="border:none"><strong><?php echo $balanc; ?></strong></td>
  </tr><?php */?>
</table>
<?php } ?>
</form> <!--end form-->
    </body>

    </html>

<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>

<html>

    <head>

    <title>Report</title>

    <link rel="stylesheet" type="text/css" href="../includes/css/style.css" />

    <style type="text/css">

<!--

.style1 {

	font-size: 16px;

	font-weight: bold;

}

.style2 {font-size: 18px}

.style4 {font-size: 14px; font-weight: bold; }

-->

    </style>

    </head>
<!--/////////////////////////////////Add by wajid for excel export//////////////////////////////////////-->
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
<!--///////////////////////////////////////////////////////////////////////-->
    <body>

<?php

include("../includes/security/adminsecurity.php");

global $AdminDAO;

	$reporttype			=	$_GET['reporttype'];

	$cid				=	$_GET['customerid'];

	$taxpercentage		=	$_GET['taxpercentage'];

	$customercopy		=	$_GET['customercopy'];

	$officecopy			=	$_GET['officecopy'];

	$salestaxcopy		=	$_GET['salestaxcopy'];

	$serialno			=	$_GET['serialno'];

	$customersql="SELECT 

						CONCAT(firstname,' ',lastname) as customername,

						companyname

					FROM 

						$dbname_main.customer

						WHERE

								pkcustomerid='$cid'";

	$custarr			=	$AdminDAO->queryresult($customersql);

	$customername		=	$custarr[0]['customername'];

	$companyname		=	$custarr[0]['companyname'];

	if($companyname=='')

	{

		$companyname=$customername;

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

	$todate				=	mktime(23,59,59,$tomon,$today,$toyr);

	if($reporttype==1)//credit account summary

	{

		$customerbalbf		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_main.customer c","sum(sd.saleprice*sd.quantity) as balbf","s.fkaccountid=pkcustomerid AND s.status=1 AND  pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime < '$fromdate'");

	 $totalsale	=	$customerbalbf[0]['balbf'];

		$query		=	"SELECT 1 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.cashpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid



UNION SELECT 2 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.ccpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid



UNION SELECT 3 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.fcpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid



UNION SELECT 4 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.chequepayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid";

		//echo $query;

		//exit;

		

		$balance	=	$AdminDAO->queryresult($query);

		$totalpaid	=	$balance[0]['amount'];

		//print"Sale: $totalsale paid:$totalpaid<br>";

		$balbf=$totalsale-$totalpaid;



		

		$customerinfo		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_main.customer c,$dbname_detail.addressbook a","s.pksaleid,from_unixtime(s.datetime,'%d-%m-%Y') as trdatetime,sum(sd.saleprice*sd.quantity) as amount","s.fkaccountid=pkcustomerid AND s.status=1 AND c.fkaddressbookid=pkaddressbookid AND pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime BETWEEN '$fromdate' AND '$todate' GROUP BY pksaleid");

	//collections results

	/*echo $sql="SELECT s.pksaleid,

					from_unixtime(s.datetime,'%d-%m-%Y') as datetime,

					sum(sd.saleprice*sd.quantity) as amount

				FROM

					$dbname_detail.sale s,

					$dbname_detail.saledetail sd,

					$dbname_detail.customer c,

					$dbname_detail.addressbook a

				HAVING

					s.fkcustomerid=pkcustomerid AND s.status=1 AND c.fkaddressbookid=pkaddressbookid AND pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime BETWEEN '$fromdate' AND '$todate' GROUP BY pksaleid

					";*/

			

	 $collectionqry="

				SELECT 1 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.cashpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime BETWEEN '$fromdate' AND	'$todate'  group by trdatetime



UNION SELECT 2 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.ccpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime  BETWEEN '$fromdate' AND	'$todate'  group by trdatetime



UNION SELECT 3 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.fcpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime  BETWEEN '$fromdate' AND	'$todate' group by trdatetime



UNION SELECT 4 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.chequepayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime BETWEEN '$fromdate' AND	'$todate'  group by trdatetime





";



	//for()

	$collectarr	=	$AdminDAO->queryresult($collectionqry);

	if(sizeof($collectarr)>0)

	{

		$customerinfo=	@array_merge($customerinfo,$collectarr);

	}

	

	?>

    

	<div style="width:8in;padding:0px;font-size:17px;" align="center">

	<img src="../images/esajeelogo.jpg" width="197" height="58">

	<br />

	<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">

	<b>Think globally shop locally</b>

	</span>

	</div>

	<table class="simple" style="width:8in">

    <tr>

		<th colspan="5">Credit Account Summary</th>

	</tr>

    <tr>

		<td><b>Detail of <span style="text-transform:uppercase"><?php echo $companyname;?></span></b></td>

		<td colspan="4">From <?php echo date("d-m-Y",$fromdate);?> To Date <?php echo $_GET['todate'];?></td>

	</tr>

	<tr>

		<td><b>B/F</b></td>

		<td colspan="4"><?php echo number_format($balbf,2);?></td>

	</tr>

	<tr>

		<th>Bill #</th>

		<th>Date</th>

		<th colspan="2">Amount</th>

		<th>Total</th>

	</tr>

	<?php

	}

	if($balbf=='')

	{

		$forward=0;

	}

	else

	{

		$forward=$balbf;

	}

	$range	=	gregoriantojd($tomon,$today,$toyr)-gregoriantojd($frommon,$fromday,$fromyr);

	$range	+=1;

	$stdate	=	$fromdate;

	for($j=1;$j<=$range;$j++)

	{

		$sdate	=	date("d-m-Y",$stdate);

		for($k=0;$k<sizeof($customerinfo);$k++)

		{

			if($sdate== $customerinfo[$k]['trdatetime'])

			{

			$pksaleid	=	$customerinfo[$k]['pksaleid'];

			$reportdate	=	$customerinfo[$k]['trdatetime'];

			$amount		=	$customerinfo[$k]['amount'];

			$type		=	$customerinfo[$k]['type'];

			$forward	+=	$amount;

			if($type=='1')

			{

				$type='(cash)';

			}

			if($type=='2')

			{

				$type='(cc)';

			}

			if($type=='3')

			{

				$type='(fc)';

			}

			if($type=='4')

			{

				$type='(cheque)';

			}

	?>

	<tr>

		<td><?php echo $pksaleid;?></td>

		<td><?php echo $reportdate;?></td>

		<td><?php if($amount<0){print" Payment $type";}?></td>

		<td align="right"><?php  echo number_format($amount,2);?></td>

		<td align="right"><?php echo number_format($forward,2);?></td>

	</tr>

	<?php

			}

		}

		$stdate	=	mktime(0,0,0,$frommon,$fromday+$j,$fromyr);

	}

	?>

	</table>

		<script language="javascript">

		window.print();

	</script>

	<?php

	//end of if credit account summary

	

	if($reporttype==2)//credit account summary

	{

		$customerbalbf		=	$AdminDAO->getrows("$dbname_detail.sale s,$dbname_detail.saledetail sd,$dbname_main.customer c","sum(sd.saleprice*sd.quantity) as balbf","s.fkaccountid=pkcustomerid AND s.status=1 AND  pkcustomerid='$cid' AND sd.fksaleid=pksaleid AND s.datetime < '$fromdate'");

		$totalsale	=	$customerbalbf[0]['balbf'];

		$query		=	"SELECT 1 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.cashpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid



UNION SELECT 2 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.ccpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid



UNION SELECT 3 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.fcpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid



UNION SELECT 4 as type,pksaleid, SUM(amount)  amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.chequepayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' and paytime < '$fromdate' group by fkaccountid";

		//echo $query;

		//exit;

		

		$balance	=	$AdminDAO->queryresult($query);

		$totalpaid	=	$balance[0]['amount'];

		//print"Sale: $totalsale paid:$totalpaid<br>";

		$balbf=$totalsale-$totalpaid;



		$sql="SELECT

				

				pksaleid,

				sum(sd.saleprice*sd.quantity) as amount,

				sd.saleprice,

				sd.quantity,

				b.itemdescription,

				FROM_UNIXTIME(s.datetime,'%d-%m-%Y') as trdatetime

				FROM

					$dbname_detail.sale s,

					$dbname_detail.saledetail sd,

					$dbname_main.customer c,

					$dbname_main.barcode b,

					$dbname_detail.stock st

				WHERE

					s.fkaccountid=c.pkcustomerid AND 

					s.status=1 AND  

					pkcustomerid='$cid' AND 

					sd.fksaleid=pksaleid AND 

					s.datetime BETWEEN '$fromdate' AND '$todate' AND

					sd.fkstockid=st.pkstockid AND 

					

					b.pkbarcodeid=st.fkbarcodeid

					GROUP BY sd.pksaledetailid

					

					order by s.pksaleid ASC

				";

				$customerinfo	=	$AdminDAO->queryresult($sql);

	 $collectionqry="

				SELECT 1 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.cashpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c'  and paytime BETWEEN '$fromdate' AND '$todate'   group by trdatetime



UNION SELECT 2 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.ccpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c'  and paytime BETWEEN '$fromdate' AND '$todate'   group by trdatetime



UNION SELECT 3 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.fcpayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c'  and paytime BETWEEN '$fromdate' AND '$todate'   group by trdatetime



UNION SELECT 4 as type,pksaleid,concat('-',SUM(amount)) amount,FROM_UNIXTIME(paytime,'%d-%m-%Y') trdatetime FROM $dbname_detail.sale,$dbname_detail.chequepayment where fkaccountid='$cid' and fksaleid=pksaleid and paymenttype='c' group by trdatetime  and paytime BETWEEN '$fromdate' AND '$todate'  order by trdatetime ASC





";



	$collectarr	=	$AdminDAO->queryresult($collectionqry);

	if(sizeof($collectarr)>0)

	{

		$customerinfo=	@array_merge($customerinfo,$collectarr);

	}

	//$customerinfo=	@array_merge($collectarr,$customerinfo);

	?>

	<div style="width:8in;padding:0px;font-size:17px;" align="center">

	<img src="../images/esajeelogo.jpg" width="197" height="58">

	<br />

	<span style="font-size:11px;font-family:'Comic Sans MS', cursive;">

	<b>Think globally shop locally</b>

	</span>

	</div>

	<table class="simple">

    <tr>

		<th colspan="7">Credit Account Statement</th>

	</tr>

		<tr>

		<td><b>Detail of <span style="text-transform:uppercase"><?php echo $companyname;?></span></b></td>

		<td colspan="6">From <?php echo date("d-m-Y",$fromdate);?> To Date <?php echo $_GET['todate'];?></td>

	</tr>

	<tr>

		<td><b>B/F</b></td>

		<td colspan="6"> <?php echo number_format($balbf,2);?></td>

	</tr>

	<tr>

		<th>Bill #</th>

		<th>Date</th>

		<th >itemdescription</th>

		<th >Quantity</th>

		<th >Sale Price</th>

		<th >Amount</th>

        <th >&nbsp;</th>

		

	</tr>



	<?php

		if($balbf=='')

		{

			$forward=0;

		}

		else

		{

			$forward=$balbf;

		}

	$range	=	gregoriantojd($tomon,$today,$toyr)-gregoriantojd($frommon,$fromday,$fromyr);

	$range	+=1;

	$stdate	=	$fromdate;

	for($j=1;$j<=$range;$j++)

	{

		$sdate	=	date("d-m-Y",$stdate);

		for($k=0;$k<sizeof($customerinfo);$k++)

		{

			if($sdate== $customerinfo[$k]['trdatetime'])

			{

		$pksaleid		=	$customerinfo[$k]['pksaleid'];

		$amount			=	$customerinfo[$k]['amount'];

		$saleprice		=	$customerinfo[$k]['saleprice'];

		$quantity		=	$customerinfo[$k]['quantity'];

		$itemdescription=	$customerinfo[$k]['itemdescription'];

		$type			=	$customerinfo[$k]['type'];

		$reportdate		=	$customerinfo[$k]['trdatetime'];;

		$forward	+=	$amount;

		if($type=='1')

		{

			$type='Cash Recieved';

		}

		if($type=='2')

		{

			$type='Credit Card ';

		}

		if($type=='3')

		{

			$type='Foreign Currency';

		}

		if($type=='4')

		{

			$type='Cheque Recieved';

		}

		//echo $type;

		?>

		<tr>

			<td><?php if($pksaleid!=$oldsaleid){echo $pksaleid;}else{print"&nbsp;";}?></td>

			<td><?php echo $reportdate;?></td>

			<td><?php if($type!=''){echo '<b>'.$type.'</b>';}else{ echo $itemdescription;}?></td>

			<td><?php echo $quantity;?></td>

			<td ><?php echo $saleprice;?></td>

			

			<td align="right"><?php  echo number_format($amount,2);?></td>

			<td><?php echo number_format($forward,2);?></td>

		</tr>

		<?php

		$oldsaleid=$pksaleid;

		

		}

		}

		$stdate	=	mktime(0,0,0,$frommon,$fromday+$j,$fromyr);

	}

		//}//end of for

	?>

	</table>

	<script language="javascript">

		window.print();

	</script>

	<?php

	}//end of if credit account summary

	

	if($reporttype==3)// Sales Tax Invoice

	{

		 $sql="SELECT

				

				pksaleid,

				sum(sd.saleprice*sd.quantity) as amount,

				sd.saleprice,

				sd.quantity,

				b.itemdescription,

				FROM_UNIXTIME(s.datetime,'%d-%m-%Y') as trdatetime

				FROM

					$dbname_detail.sale s,

					$dbname_detail.saledetail sd,

					$dbname_main.customer c,

					$dbname_main.barcode b,

					$dbname_detail.stock st

				WHERE

					s.fkaccountid=c.pkcustomerid AND 

					s.status=1 AND  

					pkcustomerid='$cid' AND 

					sd.fksaleid=pksaleid AND 

					s.datetime BETWEEN '$fromdate' AND '$todate'  AND 

					sd.fkstockid=st.pkstockid AND 

					

					b.pkbarcodeid=st.fkbarcodeid

					GROUP BY sd.pksaledetailid

					

					order by s.pksaleid ASC

				";

				$customerinfo	=	$AdminDAO->queryresult($sql);

	?>

			<div align="center">

			<span class="style2">Sales Tax Invoice</span>

			<br> 

		 	 <span id='copytd'>(Customer Copy)</span></th>

			</div>

	<span id="serial" style="position:absolute; margin-top:40px; margin-left:400px">

		<b>Date: <?php echo date('d-m-Y',time());?></b>

	<br>

		<b>Serial No: <?php echo $serialno;?></b>

		<br>

		<br>

		<strong>Sales Tax Registration No: 07-01-2100-082-55

		<br>

	NTN No: 18-01-0344289    </strong></span>

	<div style="width:8in;padding:0px;margin-left:-200px;font-size:17px;" align="center">

	<img src="../images/esajeecologo.jpg" width="286" height="77">

	<br />

	<span style="font-size:11px; line-height:15px">

	

	<span class="style1">Think globally shop locally</span><br />

    <span class="style4">Importers & General Order Suppliers </span><strong><br />

    </strong>Shop # 9, Kohsar Market, F-6/3, Islamabad<br />

    Phone: 051-2872041, Fax: 051-2279919<br />

    Email: esajee@esajee.com <br />

    Website: www.esajee.com<br />

	</span>	</div>

	<div>

	<br>

		Buyer's Name: <b><span style="text-transform:uppercase"><?php echo $companyname;?></span></b>

		<br>

		Sales Tax No:<b>__________</b>

		<br>

		

	</div>

	<div align="center">

		<span style="text-transform:uppercase">From <b><?php echo date("d-m-Y",$fromdate);?></b> To <b><?php echo $_GET['todate'];?></b></span>

	</div>

	<table class="simple">

   

   

	<tr>

		<!--<th>#</th>

		<th>Date</th>-->

		<th >itemdescription</th>

		<th >Quantity</th>

		<th >Sale Price</th>

		 <?php  

		 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

		 {

		 ?>

		<th >Amount</th>

        <th >S.Tax <?php echo $taxpercentage;?>%</th>

        <?php

		}

		?>

		<th >Total Value</th>

	</tr>

	

	<?php

		for($i=0;$i<sizeof($customerinfo);$i++)

		{

		$pksaleid		=	$customerinfo[$i]['pksaleid'];

		$amount			=	$customerinfo[$i]['amount'];

		$saleprice		=	$customerinfo[$i]['saleprice'];

		$quantity		=	$customerinfo[$i]['quantity'];

		$itemdescription=	$customerinfo[$i]['itemdescription'];

		$type			=	$customerinfo[$i]['type'];

		$reportdate		=	$customerinfo[$i]['trdatetime'];

		

		$tax=$taxpercentage/100*$amount;

		?>

		<tr>

			<td><?php echo $itemdescription;?></td>

			<td align="right"><?php echo $quantity;?></td>

			<td align="right"><?php echo number_format($saleprice,2);?></td>

			<?php  

		 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

		 {

		 ?>

            <td align="right"><?php  echo number_format($amount,2);?></td>

            <td align="right"><?php echo number_format($tax,2);?></td>

          <?php

		  }

		  ?> 

		    <td align="right"><?php echo number_format($amount+$tax,2);?></td>

		</tr>

		<?php

		$totalamount+=$amount;

		$totaltax+=$tax;

		}//end of for

	?>

    <tr>

		

			<td colspan="3" align="right"><b>Grand Total:</b></td>

			<td align="right"><b><?php  echo number_format($totalamount,2);?></b></td>

			 <?php

			 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns

			 {

			 ?>

			 <td align="right"><b><?php echo number_format($totaltax,2);?></b></td>

             <td align="right"><b><?php echo number_format($totalamount+$totaltax,2);?></b></td>

			<?php

			}

			?>		

	  </tr>

	</table>

		<script language="javascript">

		<?php

		if($customercopy=='y')

		{

		?>

			document.getElementById('copytd').innerHTML='(Customer Copy)';

			window.print();

		<?php

		}

		

		if($officecopy=='y')

		{

		?>

			document.getElementById('copytd').innerHTML='(Office Copy)';

			window.print();

		<?php

		}

		if($salestaxcopy=='y')

		{

		?>

			document.getElementById('copytd').innerHTML='(Sales Tax Office Copy)';

			window.print();

		<?php

		}

		?>

	</script>

	<?php

	

	}//end of if  Sales Tax Invoice

	?>


</form> <!--end form-->
	</body>

    </html>

<?php }//end edit?>
<?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////
?>
