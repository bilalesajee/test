<?php ob_start();
error_reporting(0); 
session_start();
$_SESSION = array(
    "storeid" => 3,
    "siteconfig" => 2,
    "countername" => -1,
    "siteconfig" => 2,
    "addressbookid" => 40,
    "name" => 'System Admin',
    "admin_section" => 'Admin logged in',
    "groupid" => 6,
    "groupname" => 'Administrator',);
	

include("../../includes/security/adminsecurity.php");
global $AdminDAO, $Component;
$date_formate = date('m/d/Y');?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Supplier Report</title>

<link href="style.css" rel="stylesheet" type="text/css" />

<link href="file:///C|/wamp/www/vssprojects/Enq_sys/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<br />
<form name="stockdetails_" id="searchform" method="post" class="form" action="" onSubmit="return false;">
  <table width="80%" align="center" style="border:none">
    <tr style="border:none">
      <td colspan="3" style="border:none"></td>
    </tr>
    <tr>
      <td colspan="4" style="border:none"></td>
    </tr>
    <tr style="border:none">
      <td colspan="4" align="right" class="sersch_table_head" style="border:none"><div align="center" class="topheadinggreen">
        <p><strong>Supplier Report</strong></p>
        <p>&nbsp;</p>
      </div></td>
    </tr>
    <tr style="border:none">
      <td align="right" class="sersch_table_head" style="border:none">&nbsp;</td>
      <td align="center" class="s1" style="border:none">Start Date:</td>
      <td style="border:none"><input type="text" class="accounts_txtField" name="sdate" id="sdate" value="<?php echo $date_formate;?>" style="width:205px" /></td>
      <td class="banner" style="border:none">MM/DD/YY</td>
    </tr>
    <tr style="border:none">
      <td align="right" class="sersch_table_head" style="border:none">&nbsp;</td>
      <td align="center" class="s1" style="border:none">End Date:</td>
      <td style="border:none"><input type="text" class="accounts_txtField"  name="edate" id="edate" value="<?php echo $date_formate;?>" style="width:205px" /></td>
      <td class="banner" style="border:none"><span class="banner" style="border:none">MM/DD/YY</span></td>
    </tr>
    <tr style="border:none">
      <td align="right" style="border:none">&nbsp;</td>
      <td align="center" class="s1" style="border:none">Supplier:</td>
      <td style="border:none"><select name="supplier_name" class="accounts_combo" id="supplier_name" style="width:205px" >
        <option value="">Select Supplier</option>
        <?php  $q=" select * from main.supplier order by companyname asc ";
        $res = $AdminDAO->queryresult($q);
        $row_=count($res);
	    for($i=0;$i<$row_;$i++){
		$bb=$res[$i]['companyname'];   ?>
        <option value="<?php echo $bb;?>"><?php echo $bb;?></option>
        <?php  } ?>
      </select></td>
      <td style="border:none">&nbsp;</td>
    </tr>
    <tr style="border:none">
      <td align="right" style="border:none">&nbsp;</td>
      <td align="center" class="s1" style="border:none">Order By:</td>
      <td style="border:none"><input type="radio" id="asc" name="asc" value="" checked  onclick="disablearr(1);"   />
        <span class="banner">        ASC</span>
        <input type="radio" id="desc" name="desc" value="" onClick="disablearr(2);"   />
      <span class="banner">DESC</span></td>
      <td style="border:none">&nbsp;</td>
    </tr>
    <tr style="border:none">
      <td width="28%" align="right" style="border:none"><!--<select name="searchField"  id="searchField" >
						 	
						  <option value="code" >BarCode</option>
                            <option value="Product" >Product</option>
						</select>--></td>
      <td width="21%" align="left" style="border:none">&nbsp;</td>
      <td width="21%" style="border:none"><button type="button" class="butt" onClick="return show_report();" id="btnfind">Find</button></td>
      <td width="30%" style="border:none">&nbsp;</td>
    </tr>
  </table>
  
</form>
<div  id="maindiv"></div>
<script type="text/javascript" src="includes/js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script language="javascript">
function disablearr(opt)
{
	if(opt==1){
	if(document.getElementById('asc').style.display=='block'){
		
		}else{
			
			
			
			document.getElementById('desc').value='';
			//document.getElementById('customer_account').value='';
			
			
			document.getElementById('desc').checked=false;
			//document.getElementById('customer_account').checked=false;
			
			}	
			
	}else if(opt==2){
		if(document.getElementById('desc').style.display=='block'){
		
		}else{
			
			
			document.getElementById('asc').checked=false;
			//document.getElementById('customer_account').checked=false;
		   document.getElementById('asc').value='';
			
			//document.getElementById('customer_account').value='';
			}	

	}

	
	
}
function show_report()
{
	if(document.getElementById('sdate').value == '')
		{
			alert('Please Enter Start Date');
			document.getElementById('sdate').focus();
			return false;
		}
		else if(document.getElementById('edate').value == '')
		{
			alert('Please Select End date');
			document.getElementById('edate').focus();
			return false;
		}
		

	/*	else if(document.getElementById('asc').value == '')
		{
			alert('Please Check Only One checkbox');
			document.getElementById('asc').focus();
			return false;
		}*/
	
   var sdate		=	document.getElementById('sdate').value;
   var edate			=	document.getElementById('edate').value;
	var asc		=	document.getElementById('asc').value;
	var desc		=	document.getElementById('desc').value;
	var supplier_name		=	document.getElementById('supplier_name').value;
	
if(document.getElementById('asc').checked==true	)
	{
		asc = 1;
		
		}
		else if(document.getElementById('desc').checked==true	)
	{
		desc = 2;
}
if(asc == '' && desc == '')
	{
		alert('Please Checked  One checkbox');
		return false;
		}
	//$("#maindiv").load('supplierreport_2.php?sdate='+sdate+'&edate='+edate);
window.open('showsupplierreport.php?sdate='+sdate+'&edate='+edate+'&supplier_name='+supplier_name+'&asc='+asc+'&desc='+desc,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
	}

</script>
 <link href="datepicker/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="datepicker/jquery.min.js"></script>
<script src="datepicker/jquery-ui.min.js"></script>
	   
<script>
	  $(document).ready(function() {
	    $("#sdate").datepicker();
		$("#edate").datepicker();
	  });
	  </script>
<?php 


//include_once("connection.php");
$exl =$_REQUEST['exl'];

 $date_formate1 = strtotime(date('d-m-Y'));
$option = strtotime('01/01/2013');
$id = $_REQUEST['id'];
date_default_timezone_set('Asia/karachi');
$date2 = time();
$date = $option;
$query = "SELECT su.invoice_status,sl.pksupplierid, su.billnumber,s.fksupplierinvoiceid, FROM_UNIXTIME(s.addtime,'%d-%m-%Y') adddate, s.fksupplierid ,SUM(s.quantity * s.priceinrs) as invoice_value , sl.companyname from $dbname_detail.supplierinvoice su
	 left join $dbname_detail.stock s on su.pksupplierinvoiceid = s.fksupplierinvoiceid left join main.supplier sl on su.fksupplierid=sl.pksupplierid
	 where  s.addtime >= '$date_formate1' and su.invoice_status =1 group by sl.companyname,s.fksupplierinvoiceid,su.billnumber order by sl.pksupplierid,s.addtime ";

$reportresult = $AdminDAO->queryresult($query);
 $date_formate = date('d/m/Y');
 $row_run=count($reportresult);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title></title>
<?php if(!$exl) { ?>
<link href="style.css" rel="stylesheet" type="text/css" />
<p>
  <?php } ?>
  </head>
  <body>
</p>
<p>&nbsp; </p>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="table" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
  <tr>
    <td align="center" style="font-size:18px; " colspan="6"></td>
  </tr>
  <tr>
    <td align="center" style="font-size:18px; " colspan="6"> Supplier Report Of Date <?php echo $date_formate;?></td>
  </tr>
  <tr>
    <td align="center" style="font-size:18px; " colspan="6"> </td>
  </tr>
  <tr>
    <th bgcolor="#C0944B" colspan="2" style="height:30px;">Supplier</th>
    <th bgcolor="#C0943B" colspan="4">Bill</th>
    
  </tr>
 
  <tr>
    <th width="55" >Id</th>
    <th width="207" >Name</th>
    <th width="110" >#</th>
    <th width="114" >Supplier Invoice ID</th>
    <th width="103" >Date</th>
    <th width="118">Amount</th>
   
  </tr>
  <?php
 
for($i=0;$i<$row_run;$i++)
{
	
?>
  
   <?php 
   if($i==1 && $i==0){ 
   $total_value='';
   $total_value=$reportresult[$i]['invoice_value'];
   ?>
   <tr>
	<td width="55" align="left">&nbsp;<?php echo $reportresult[$i]['fksupplierid'];?></td>
    <td width="207" align="left">&nbsp;<?php echo $reportresult[$i]['companyname'];?></td>
    <td align="left">&nbsp;<?php echo $reportresult[$i]['billnumber'];?></td>
    <td width="114" align="center"><?php echo $reportresult[$i]['fksupplierinvoiceid'];?></td>
    <td width="103" align="center">&nbsp;<?php echo $reportresult[$i]['adddate'];?></td>
    <td width="118" align="right">&nbsp;<?php echo round($reportresult[$i]['invoice_value'],2);?></td>
  </tr>
       
	   <?php }else{
    
	
	if($reportresult[$i]['fksupplierid']!=$reportresult[$i-1]['fksupplierid'] ){
		//$total_value='';
		$total_value=$reportresult[$i]['invoice_value'];
		?>
    
    <tr>
    <td width="55" align="left">&nbsp;<?php echo $reportresult[$i]['fksupplierid'];?></td>
    <td width="207" align="left">&nbsp;<?php echo $reportresult[$i]['companyname'];?></td>
    <?php }else{ ?>
	<td width="50" align="left"></td>
    <td width="50" align="center">&nbsp;</td>
	
	<?php
	
	$total_value=$total_value+$reportresult[$i]['invoice_value'];
		}	?>
    <td align="left">&nbsp;<?php echo $reportresult[$i]['billnumber'];?></td>
    <td width="118" align="center"><?php echo $reportresult[$i]['fksupplierinvoiceid'];?></td>
    <td width="134" align="center">&nbsp;<?php echo $reportresult[$i]['adddate'];?></td>
    <td width="100" align="right">&nbsp;<?php echo round($reportresult[$i]['invoice_value'],2)?></td>
  </tr>
		<?php

if($reportresult[$i]['fksupplierid']!=$reportresult[$i+1]['fksupplierid'] ){?>
<tr><td align="right" colspan="5">&nbsp;<b> Total Value </b></td><td width="118" align="right" >&nbsp;<b><?php echo round($total_value,2)?></b></td></tr>
<?php }

}


}
?>
</table>
<?php

if($exl)
{
	header("Content-type: application/octet-stream");
	# replace excelfile.xls with whatever you want the filename to default to
	header("Content-Disposition: attachment; filename=Report.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
}



?>





