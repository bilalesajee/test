<?php
include_once("includes/security/adminsecurity.php");
global $AdminDAO;
//session_start();

?>
<script language="javascript">
/*$(document).ready(function() 
{
function findValueCallback(event, data, formatted)
		{
			document.getElementById('customername').value=data[0];
			document.getElementById('customerid').value=data[1];			
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
			//alert('abc');
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi, '');
		}
			document.getElementById('customername').focus();
			jQuery("#customername").autocomplete("getcustomers.php");
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
 });*/
</script>
<form  name="customerfrm" id="customerfrm" method="post" style="float:left">
<table class="pos">
<tr>
<th colspan="2">Customer Form</th>
</tr>
<tr>
  <td colspan="2" style="color:#39C">Please Select Select customer process Credit sale.</td>
  </tr>
<tr>
<td>Customer Name</td>
<td>
<?php
session_start();
include("includes/security/adminsecurity.php");
global $AdminDAO;
//$customername	=	trim(filter($_REQUEST['q'])," ");
/****************************PRODUCT DATA*****************************///changed $dbname_main to $dbname_detail on line 55 by ahsan 22/02/2012
$sql=" SELECT CONCAT( firstname,' ', lastname,' (',nic,')') as customername,id as pkcustomerid
			FROM $dbname_detail.account, $dbname_detail.addressbook
			WHERE fkaddressbookid = pkaddressbookid AND ctype=1 order by customername ASC
			
	";
			
	$customer_array	=	$AdminDAO->queryresult($sql);
	//$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `productname` LIKE '%$productname%' group by barcode");
	?>
    <select name="customerid" id="customerid" style="font-size:16px; font-weight:bold; color:#999; border:none;">
    <?php
	for($a=0;$a<count($customer_array);$a++)
	{
		$customername	=	$customer_array[$a]['customername'];
		$id				=	$customer_array[$a]['pkcustomerid'];		
		//echo "$customername|$id\n";
	?>
    
    	<option value="<?php echo $id;?>"><?php echo $customername;?></option>
   
    <?php
	} 
	?>
	</select>
	<?php

?>
<!--<input type="text" name="customername" class="text" id="customername" autocomplete="off" />-->

</td>
<!--<input type="hidden" name="customerid" id="customerid" />-->
</tr>

<tr>
<td colspan="2"><label>&nbsp;</label>
            <button type="button" name="button" id="button" onclick="processcreditsale();" title="Click to process sales on credit for selected cutomer.">
                <img src="images/disk.png" alt=""/> 
               Proceed
            </button>
            <button type="button" name="button2" id="button2" onclick="cancelcustomer();" title="Cancel Closing">
                <img src="images/cross.png" alt=""/> 
               Cancel
            </button></td>
</tr>
</table>
</form>
<script language="javascript">
document.getElementById('customerid').focus();

function processcreditsale()
{
	var	customerid	=	document.getElementById('customerid').value;	
	var w = document.customerfrm.customerid.selectedIndex;
   	var	customername=	encodeURI(document.customerfrm.customerid.options[w].text);

	//var	customername=	encodeURI(document.getElementById('customername').value);	
	if(customerid=='')
	{
		alert("Select customer first to process credit sale.");
		document.getElementById('customername').focus();
		return false;
	}
	else
	{
		loadsection('main-content','sale.php?customerid='+customerid+'&customername='+customername);
		document.getElementById('closingfrmdiv').style.display='none';
	}
}
function cancelcustomer()
{
	
	hidediv('closingfrmdiv');
	document.getElementById('Hotel_tab').className='';
	//loadsection("main-content","sale.php?customerid=''&customername=''&tpmode=0");
}
</script>
<?php
if($_SESSION['closingsession']=='')
{//changed $dbname_main to $dbname_detail on line 133 by ahsan 22/02/2012
	$closingquery	=	"SELECT pkclosingid from $dbname_detail.closinginfo where closingstatus ='i' AND countername='$countername' AND fkaddressbookid='$empid' AND fkstoreid = '$storeid' ";
	$closingarray	=	$AdminDAO->queryresult($closingquery);
	$closingsession	=	$closingarray[0][pkclosingid];
	if($closingsession=='')
	{
		?>
        <script language="javascript" type="text/javascript">
		$('#openingfrmdiv').load('openingfrm.php');
		document.getElementById('openingfrmdiv').style.display	=	'block';
		</script>
        <?php
	}
}
?>