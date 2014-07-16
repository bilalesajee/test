<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$customerid = $_GET['id'];

$qs	=	$_SESSION['qstring'];
if($customerid !="-1")
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$sql="SELECT 
					pkcustomerid id,
					CONCAT(firstname,' ',lastname) as name,
					companyname title
					
				FROM
					customer 
				WHERE
					pkcustomerid='$customerid'";
		$customer	=	$AdminDAO->queryresult($sql);
		$customername	= $customer[0]['name'];
		$companyname 	= $customer[0]['title'];
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$sql="SELECT 
					pkcustomerid,
					CONCAT(firstname,' ',lastname) as name,
					companyname
					
				FROM
					$dbname_detail.customer c LEFT JOIN $dbname_detail.addressbook  ON (c.fkaddressbookid = pkaddressbookid) 
				WHERE
					c.pkcustomerid='$customerid'";
		$customer	=	$AdminDAO->queryresult($sql);
		$customername	= $customer[0]['name'];
		$companyname 	= $customer[0]['companyname'];	
	}//end edit
	
}
$today	=	date("d-m-Y",time());

/****************************************************************************/
?>

<script language="javascript">
$().ready(function() 
	{
		$("#fromdate").mask("99-99-9999");
		$("#todate").mask("99-99-9999");
		<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
			$("#invoicedate").mask("99-99-9999");
		<?php }//end edit?>
		document.getElementById('fromdate').focus();
	});
function addform()
{
	loading('Please wait while your report is generated ...');
	options	=	{	
					url : 'generatecreditorreport.php',
					type: 'POST',
					success: response
				}
	jQuery('#reportform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Report data has been saved.',0,5000);
		//jQuery('#maindiv').load('managebrands.php?'+'<?php //echo $qs?>');
		hidediv('brandiv');
	}
	else
	{
		jQuery('#report').html(text);
		//adminnotice(text,0,5000);	
	}
	//hideform();
}
function hideform()
{
	
	document.getElementById('brandiv').style.display='none';
}
function chcktype()
{
	var type	=	document.getElementById('reporttype').value;	
	if(type=='3')
	{
		document.getElementById('taxrow').style.display='block';	
		document.getElementById('taxrow').style.display='table-row';
		document.getElementById('copies').style.display='block';	
		document.getElementById('copies').style.display='table-row';	
		document.getElementById('serialrow').style.display='block';	
		document.getElementById('serialrow').style.display='table-row';		
		<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
			document.getElementById('invoicedaterow').style.display='block';	
			document.getElementById('invoicedaterow').style.display='table-row';		
			document.getElementById('adjustmentrow').style.display='none';
			document.getElementById('writeoff').style.display='table-row';		
			document.getElementById('writeoff').style.display='none';
			document.getElementById('favouredby').style.display='table-row';
				
			document.getElementById('favouredby').style.display='none';
			document.getElementById('payment_via1').style.display='table-row';
				
			document.getElementById('payment_via1').style.display='none';
			
		<?php }//end edit?>
		document.getElementById('serialno').focus();
	}
	<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
		else if(type=='2')
		{
			//adjustmentrow
			document.getElementById('adjustmentrow').style.display='block';	
			document.getElementById('adjustmentrow').focus();
			document.getElementById('adjustmentrow').style.display='table-row';
				
			document.getElementById('writeoff').style.display='none';
			
			document.getElementById('favouredby').style.display='table-row';
				
			document.getElementById('favouredby').style.display='none';
			
				
			document.getElementById('payment_via1').style.display='block';
			document.getElementById('payment_via1').style.display='table-row';
			
	
		}
	<?php }//end edit?>
	else
	{
		document.getElementById('taxrow').style.display='none';	
		document.getElementById('copies').style.display='none';	
		document.getElementById('serialrow').style.display='none';
		<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
			document.getElementById('invoicedaterow').style.display='none';
			document.getElementById('adjustmentrow').style.display='none';
			document.getElementById('writeoff').style.display='block';	
			document.getElementById('writeoff').style.display='table-row';		
			document.getElementById('favouredby').style.display='block';		
			document.getElementById('favouredby').style.display='table-row';	
			document.getElementById('payment_via1').style.display='block';		
			document.getElementById('payment_via1').style.display='table-row';
		
		<?php }//end edit?>
	}
}
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
	function chkwriteof(val)
	{
		if(val!='')
		{
				document.getElementById('writeoffmode').checked=true;
		}
		else
		{
			document.getElementById('writeoffmode').checked=false;
		}
	}
<?php }//end edit?>
</script>
<div id="report"></div>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="reportform" id="reportform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	Credit Report For &rsaquo;&rsaquo; <?php if($customername==''){print"$companyname";}else{print"$companyname";}?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        Generate Report
    </button>
     <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table>
	<tbody>
    <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
        <tr id="serialrow" style="display:none">
          <td>Serial No </td>
          <td colspan="2">
          <input name="serialno" id="serialno" type="text"  onkeydown="javascript:if(event.keyCode==13) {invoicedate.focus();return false;}" size="8" /></td>
          </tr>
          <tr id="invoicedaterow" style="display:none">
          <td>Invoice Date </td>
          <td colspan="2">
          <?php
            $lastday = mktime(0, 0, 0, date('m')+1, 0, date('Y'));
            $lastdayofthemonth	= strftime("%d", $lastday);
            ?>
          <input name="invoicedate" id="invoicedate" type="text"  onkeydown="javascript:if(event.keyCode==13) {fromdate.focus();return false;}" size="8" value="<?php echo $lastdayofthemonth.date('-m-Y');?>"/>dd-mm-yyyy</td>
              </tr>
       <?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
            <tr id="serialrow" style="display:none">
              <td>Serial No </td>
              <td colspan="2">
              <input name="serialno" id="serialno" type="text"  onkeydown="javascript:if(event.keyCode==13) {fromdate.focus();return false;}" size="8" /></td>
              </tr>
		<?php }//end edit?>
	<tr>
		<td>From Date: </td>
		<td colspan="2"><div id="error1" class="error" style="display:none; float:right;"></div>
		<input name="fromdate" id="fromdate" type="text" value="<?php echo $fromdate; ?>" onkeydown="javascript:if(event.keyCode==13) {todate.focus(); return false;}" size="8"> dd-mm-yyyy</td>
	</tr>
	<tr>
		<td>To Date </td>
		<td colspan="2"><div id="error2" class="error" style="display:none; float:right;"></div><input name="todate" id="todate" type="text" value="<?php echo $today; ?>" onkeydown="javascript:if(event.keyCode==13) {showreport();return false;}" size="8"> dd-mm-yyyy</td>
	</tr>
	<!-- -->
	<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
        <tr id="taxrow" style="display:none">
    
              <td>Tax %</td>
              <td>
              <?php
              $taxpercentage	=	$AdminDAO->getrows("$dbname_detail.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");
             $salestaxper		=	$taxpercentage[0]['amount'];
            ?>
              <input name="taxpercentage" id="taxpercentage" type="text"  onkeydown="javascript:if(event.keyCode==13) {showreport(); return false;}" size="8" value="<?php echo $salestaxper;?>" /></td>
              </tr>
	<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
        <tr id="taxrow" style="display:none">
    
              <td>Tax %</td>
              <td><input name="taxpercentage" id="taxpercentage" type="text"  onkeydown="javascript:if(event.keyCode==13) {showreport(); return false;}" size="8" value="16"/></td>
              </tr>
     <?php }//end edit?>        
    <!-- -->
	<tr id="copies" style="display:none">
	  <td>Print Copies </td>
	  <td valign="top">Customer 
	    <input name="customercopy" type="checkbox" id="customercopy" value="y" checked="checked" />
	    Office
	    <input name="officecopy" type="checkbox" id="officecopy" value="y" /> 
	    Sale Tax 
	    <input name="salestaxcopy" type="checkbox" id="salestaxcopy" value="y" /></td>
	  </tr>
	<tr>
		<td>Report Type: </td>				
		<td valign="top"><div id="error3" class="error" style="display:none; float:right;"></div><div id="suppliers">
        <select name="reporttype" id="reporttype" onchange="chcktype()">
        	<option value="1">Credit Account Summary</option>
 			<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
           		<option value="4">Hotel Credit Report</option>
            <?php }//end edit ?>
            <option value="2">Credit Accounts Statement</option>
            <option value="3" >Sales Tax Invoice</option>
        </select>
        </div></td>
		<!--<td valign="top"><input name="nsupplier" id="nsupplier" type="text" value="Add New" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onFocus="if(this.value=='Add New')this.value='';"/></td>-->
	</tr>
<?php 		if($_SESSION['siteconfig']==19){//edit by ahsan 17/02/2012, added if condition?>
    <tr id="adjustmentrow" style="display:none">
		<td>Date Adjustment Mode</td>				
		<td valign="top">
       	<input type="checkbox" name="adjustmentmode" value="1" id="adjustmentmode"/>
        </td>
		
	</tr>
	 <tr id="writeoff" >
		<td>Payment Writeoff Mode</td>				
		<td valign="top">
       		<input type="checkbox" name="writeoffmode" value="1" id="writeoffmode"/>
        </td>
		
	</tr>
	 <tr id="favouredby" >
		<td>Favoured By</td>				
		<td valign="top">
       		<select name="favouredbyid" id="favouredbyid" onchange="chkwriteof(this.value)">
				<option value="">Select Authority</option>
				<?php
					$sql="select 
								pkaddressbookid,
								firstname,
								lastname 
							FROM 
								addressbook,
								employee 
							WHERE 
								pkaddressbookid=fkaddressbookid AND 
								fkgroupid IN(6,7,9,1) group by pkaddressbookid";
				$employee		= $AdminDAO->queryresult($sql);
				for($e=0;$e<count($employee);$e++)
				{
					$pkaddressbookid	= $employee[$e]['pkaddressbookid'];
					$firstname 			= $employee[$e]['firstname'];
					$lastname 			= $employee[$e]['lastname'];
				?>
					<option value="<?php echo $pkaddressbookid;?>"><?php echo $firstname.' '.$lastname;?></option>
				<?php
				}
				?>
			</select>
        </td>
		
	</tr>
	 
<?php }//end edit?>    
<tr id="payment_via1" >
	   <td >Payment Via</td>
	   <td valign="top">  <select name="payment_via" id="payment_via" >
        	<option value="1">Viewing With Payment</option>
 			
            <option value="2">Viewing Without Payment</option>
            
        </select>&nbsp;</td>
	   </tr>
	<tr>
		<td colspan="3"  align="left">
		<div class="buttons">
            <button type="button" class="positive" onclick="showreport();">
                <img src="../images/tick.png" alt=""/> 
                Generate Report            </button>
             <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel            </a>          </div>        </td>				
	</tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="customerid" value = <?php echo $customerid?> />	
</form>
</div><br />
<script language="javascript">
function showreport()
{
	var fromdate		=	document.getElementById('fromdate').value;
	var todate			=	document.getElementById('todate').value;
	var reporttype		=	document.getElementById('reporttype').value;
	var taxpercentage	=	document.getElementById('taxpercentage').value;
	var serialno		=	document.getElementById('serialno').value;
	var payment_via		=	document.getElementById('payment_via').value;
	//alert(payment_via);
	<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
		var invoicedate		=	document.getElementById('invoicedate').value;
		var writeoffmode		=	document.getElementById('writeoffmode').value;
		var favouredbyid		=	document.getElementById('favouredbyid').value;
		var adjustmentmode;
	<?php }//end edit?>
	var customercopy='n';
	var officecopy='n';
	var salestaxcopy='n';
	
	<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
		if(document.getElementById('writeoffmode').checked==true)
		{
				writeoffmode=1;
		}
		else
		{
			writeoffmode=0;	
		}
		if(document.getElementById('adjustmentmode').checked==true)
		{
				adjustmentmode=1;
		}
		else
		{
			adjustmentmode=0;	
		}
	<?php }//end edit?>
	if(document.getElementById('customercopy').checked==true)
	{
		customercopy='y';
	}
	if(document.getElementById('salestaxcopy').checked==true)
	{
		salestaxcopy='y';
	}
	if(document.getElementById('officecopy').checked==true)
	{
		officecopy='y';
	}
	var wid				=	800;
	var hig				=	600;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
		if(reporttype==4)
		{
				window.open('hotelcreditsummary.php?fromdate='+fromdate+'&todate='+todate+'&reporttype='+reporttype+'&taxpercentage='+taxpercentage+'&customerid='+'<?php echo $customerid;?>'+'&customercopy='+customercopy+'&officecopy='+officecopy+'&salestaxcopy='+salestaxcopy+'&serialno='+serialno+'&invoicedate='+invoicedate+'&adjustmentmode='+adjustmentmode+'&writeoffmode='+writeoffmode+'&favouredbyid='+favouredbyid,'Closing',display); 	 
		}
		else
		{
		
			window.open('generatecreditorreport.php?fromdate='+fromdate+'&todate='+todate+'&reporttype='+reporttype+'&taxpercentage='+taxpercentage+'&customerid='+'<?php echo $customerid;?>'+'&customercopy='+customercopy+'&officecopy='+officecopy+'&salestaxcopy='+salestaxcopy+'&serialno='+serialno+'&invoicedate='+invoicedate+'&adjustmentmode='+adjustmentmode+'&writeoffmode='+writeoffmode+'&favouredbyid='+favouredbyid+'&payment_via='+payment_via,'Closing',display); 	 
		}
	<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
		 	window.open('generatecreditorreport.php?fromdate='+fromdate+'&todate='+todate+'&reporttype='+reporttype+'&taxpercentage='+taxpercentage+'&customerid='+'<?php echo $customerid;?>'+'&customercopy='+customercopy+'&officecopy='+officecopy+'&salestaxcopy='+salestaxcopy+'&serialno='+serialno+'&payment_via='+payment_via,'Closing',display); 	 
	<?php }//end edit?>
}
</script>