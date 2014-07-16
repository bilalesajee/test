<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
?>
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
<script language="javascript" src="../includes/js/jquery.js"></script>
<script language="javascript" src="../includes/js/jquery.form.js"></script>
<script language="javascript" src="../includes/js/common.js"></script>
<script language="javascript">
function postinvoice()
{
	//loading('Please wait while your report is generated ...');
			//frminvoice" method="post" action="postinvoice.php
	   if(confirm("Are You sure to post this invoice."))
	   {
			options	={	
				url : 'postinvoice.php',
				type: 'POST',
				success: response
			}
			jQuery('#frminvoice').ajaxSubmit(options);
	   }
	   else
	   {
			return false;   
	   }
}
function response(text)
{
	//adminnotice('Attribute data has been saved.',0,5000);
	adminnotice("Invoice posted successfully.",0,5000);
}
function delsale(saleid)
{
	if(confirm("Are you sure to remove this sale no "+saleid+" from invoice."))
	{
		$("."+saleid).remove();	
	}
	$('#calc').load('recalculate.php?saleid='+saleid);
}
</script>
    </head>
    <body>
    <div id="msg"  class="notice" style="display:none"></div>
    <div id="calc"></div>
<?php
	$id					=	$_GET['id'];
	$customerid			=	$_GET['customerid'];
	$taxpercentage		=	$_GET['taxpercentage'];
	$customercopy		=	$_GET['customercopy'];
	$officecopy			=	$_GET['officecopy'];
	$salestaxcopy		=	$_GET['salestaxcopy'];
	$serialno			=	$_GET['serialno'];
	$invoicedate		=	$_GET['invoicedate'];
	$serialno			=	$_GET['serialno'];
	$reporttype=3;
	if($invoicedate=='')
	{
		$lastday 			= @mktime(0, 0, 0, date('m')+1, 0, date('Y'));
		$lastdayofthemonth	= @strftime("%d", $lastday);

		$invoicedate=$lastdayofthemonth.date('-m-Y');
	}
	$customersql="SELECT 
						CONCAT(firstname,' ',lastname) as customername,
						title,taxnumber, ntn
					FROM 
						$dbname_detail.account,$dbname_detail.addressbook
						WHERE
							fkaddressbookid=pkaddressbookid AND
							id='$customerid'";
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
	if($reporttype==3)// Sales Tax Invoice
	{
		$tempsaleid	=	$_GET['tempsaleid'];
		if($tempsaleid!='')
		{
			$and=" pksaleid='$tempsaleid' AND";
			
		}
		else
		{
			$and=" s.datetime BETWEEN '$fromdate' AND '$todate'  AND ";
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
					$dbname_detail.account c,
					main.barcode b,
					$dbname_detail.stock st
				WHERE
					s.fkaccountid=c.id AND 
					s.status=1 AND  
					id='$customerid' AND 
					sd.fksaleid=pksaleid AND 
					$and
					sd.fkstockid=st.pkstockid AND 
					b.pkbarcodeid=st.fkbarcodeid AND 
					st.fkbarcodeid<>62007
					GROUP BY sd.pksaledetailid
					order by s.pksaleid ASC
				";
				
				$customerinfo	=	$AdminDAO->queryresult($sql);
				$size			=	sizeof($customerinfo);
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
			<div align="center">
			<span class="style2">Sales Tax Invoice</span>
			<br> 
		 	 <span id='copytd'>(Customer Copy)</span></th>
			</div>
	<span id="serial" style="position:absolute; margin-top:40px; margin-left:400px">
		<b><!--Date: <?php //if($tempsaleid!=''){echo date('d-m-Y');}else{echo $invoicedate;}?>--></b>
	<br>
		<b>Serial No: <?php echo $serialno;?></b>
		<br>
		<br>
		<strong>Sales Tax Registration No: </strong><br>07-01-2100-082-55
		<br>
	<strong>NTN No: 01-01-2754403-6    </strong></span>
	<div style="width:8in;padding:0px;margin-left:-200px;font-size:17px;" align="center">
	<img src="../images/esajeelogo.jpg" width="286" height="77">
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
		Buyer's NTN:<b><?php if($ntn!='0' || $ntn!=''){echo $ntn;}else{?>__________<?php } ?></b>
		<br>
		
	</div>
	<div align="center">
		<!--<span style="text-transform:uppercase"><?php //if($tempsaleid==''){?>From <b><?php //echo date("d-m-Y",$fromdate);?></b> To <b><?php //echo $_GET['todate'];?></b><?php //} ?></span>-->
	</div>
	<form name="frminvoice" id="frminvoice" method="post" action="postinvoice.php">
    <table class="simple">
   
   
	<tr>
	  <th >SaleID</th>
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
        <th >Delete</th>
	</tr>
	
	<?php
		//dump($customerinfo);
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
			if($taxable!=1)
			{
				$tax=$taxpercentage/100*$amount;
			}
			else
			{
				$tax=0;
			}
		?>
		<tr bgcolor="<?php if($pksaleid!=$oldsaleid){print"#CCCCCC";}?>" class="<?php echo $pksaleid;?>">
		  <td style="text-transform:capitalize">
		  <?php 
		  if($pksaleid!=$oldsaleid)
		  {
			  echo $pksaleid;
			  ?>
               <input type="hidden" name="saleids[]" id="saleids" value="<?php echo $pksaleid;?>">
              <?php
			}else
			{
				print"&nbsp;";
			}
			?>
         
          </td>
			<td style="text-transform:capitalize"><?php echo ucfirst(strtolower($itemdescription));?></td>
			<td align="right"><?php echo $quantity;?></td>
			<td align="right"><?php echo number_format($saleprice,2);?></td>
			<?php  
		 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns
		 {
		 ?>
            <td align="right"><div id="saleamt_<?php echo $i;?>" style="display:none;"><?php echo $amount;?></div><?php  echo number_format($amount,2);?></td>
            <td align="right"><div id="saletax_<?php echo $i;?>" style="display:none;"><?php echo $tax;?></div><?php echo number_format($tax,2);?></td>
          <?php
		  }
		  ?> 
		    <td align="right"><?php echo number_format($amount+$tax,2);?></td>
            <td align="center">
             	<?php 
				if($pksaleid!=$oldsaleid)
				{
				?>
                	<a href="javascript:void(0)" onClick="delsale(<?php echo $pksaleid;?>);">
                		<img src="../images/cross.png" border="0">
                    </a>
                <?php
				}
				?>
             </td>
		</tr>
		<?php
				$totalamount	+=	$amount;
				$totaltax		+=	$tax;
				$stax			+=	$tax;
				if($oldsaleid!=$pksaleid)
				{
					$stax	=	'';
				}
				$saledetail		=	$AdminDAO->getrows("$dbname_detail.saledetail,$dbname_detail.sale","fksaleid,(saleprice*quantity) sq,sum(taxamount) tax","fksaleid='$pksaleid' and fksaleid=pksaleid and status=1 group by pksaledetailid");
				$salestax		=	$AdminDAO->getrows("$dbname_detail.saledetail","sum(taxamount) tax","fksaleid='$pksaleid'");
				$stax			=	$salestax[0]['tax'];
				$sp	=	0;
				foreach($saledetail as $saledet)
				{
					$sq		=	$saledet['sq'];
					$sp		+=	$sq;
				}
				?>
				<div id="taxdiv_<?php echo $pksaleid;?>" style="display:none;"><?php echo $stax;?></div>
				<div id="saledetaildiv_<?php echo $pksaleid;?>" style="display:none;"><?php echo $sp;?></div>
	<?php
				$oldsaleid		=	$pksaleid;			
		}//end of for
	?>
    <tr>
		
			<td colspan="4" align="right"><b>Grand Total:</b></td>
			<td align="right"><div id="grandtotal" style="display:none;"><?php echo $totalamount;?></div><b><div id="grandtotalval"><?php  echo number_format($totalamount,2);?></div></b></td>
			 <?php
			 if($taxpercentage>0)//when the tax percentage is is 0 will not show these two columns
			 {
			 ?>
			 <td align="right"><div id="totaltax" style="display:none;"><?php echo $totaltax;?></div><b><div id="totaltaxval"><?php echo number_format($totaltax,2);?></div></b></td>
             <td align="right"><div id="totalvalue" style="display:none;"><?php echo $totalamount+$totaltax;?></div><b><div id="totalvalueval"><?php echo number_format($totalamount+$totaltax,2);?></div></b></td>
             <td>&nbsp;</td>
			<?php
			}
			?>		
	  </tr>
	</table>
     	<input type="hidden" name="id" id="id" value="<?php echo $id;?>">
   		<input type="hidden" name="customerid" id="customerid" value="<?php echo $customerid;?>">
    	<input type="hidden" name="taxpercentage" id="taxpercentage" value="<?php echo $taxpercentage;?>">
    	<input type="hidden" name="serialno" id="serialno" value="<?php echo $serialno;?>">
    	<input type="hidden" name="invoicedate" id="invoicedate" value="<?php echo $_GET['invoicedate'];?>">
    	<input type="hidden" name="fromdate" id="fromdate" value="<?php echo $_GET['fromdate'];?>">
     	<input type="hidden" name="todate" id="todate" value="<?php echo $_GET['todate'];?>">
   		<input type="button" name="btn" value="POST INVOICE" id="invpost" onClick="postinvoice();">&nbsp;&nbsp;
    	<input type="button" name="btn" value="PRINT INVOICE" id="invpost" onClick="window.print();">
  </form>
	<?php
	}//end of if  Sales Tax Invoice
	?>
    <div id="salesize" style="display:none;"><?php echo $size;?></div>
</body>
</html>