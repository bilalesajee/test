
<script language="javascript" type="text/javascript">
function savedemand()
{
	
		if(document.getElementById('addtime').value == '')
		{
			alert('Please Select Date  to continue ');
			document.getElementById('addtime').focus();
			return false;
		}
		else if(document.getElementById('remarks').value == '')
		{
			alert('Please add Demand to continue ');
			document.getElementById('remarks').focus();
			return false;
		}
		else
		{
			loading('Saving Demand...');
			options	=	{	
					url : 'savedemand.php',
					type: 'POST',
					success: customerresponse
				}
		//alert('now i am saving new customer form');
		jQuery('#newcustomerfrm').ajaxSubmit(options);
		
		}
	jQuery('#mainpanel').load("customers.php");
	
	//notice("Customer data has been saved.",0,3000);
}
function customerresponse(text)
{
	if(text=='')
	{
		notice("Customer data has been saved.",0,3000);
	}
	else
	{
		notice(text,0,10000);
	}
}
</script>
<?php
include("includes/security/adminsecurity.php");
global $AdminDAO,$userSecurity;
if($_GET['param']=='')
{
$customerid		=	$_GET['id'];
$demandid		=	'';//changed $dbname_main to $dbname_detail on line 52, 53 by ahsan 22/02/2012
$query="SELECT pkcustomerid,CONCAT(firstname,' ', lastname) customername from $dbname_main.customer where location=3 order by  customername";
$resultarr		=	$AdminDAO->queryresult($query);

}
else
{
$customerid		=	$_GET['param'];
$demandid		=	$_GET['id'];
$resultarr		=	$AdminDAO->getrows("$dbname_detail.itemdemands i ,barcode b"," b.barcode fkbarcodeid,i.quantity,i.status,addtime,remarks "," itemdemandsid='$demandid' and fkbarcodeid=pkbarcodeid ");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
//print_r($addbookarray);
$quantity		=	$resultarr[0]['quantity'];
$addtime		=	$resultarr[0]['addtime'];
$remarks		=	$resultarr[0]['remarks'];
$barcode		=	$resultarr[0]['fkbarcodeid'];
$status			=	$resultarr[0]['status'];//changed $dbname_main to $dbname_detail on line 67, 68 by ahsan 22/02/2012
$query="SELECT pkcustomerid,CONCAT(firstname,' ', lastname) customername from main.customer where location=3 ";
$resultarr		=	$AdminDAO->queryresult($query);		
					

}
?>
<link href="includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="includes/autocomplete/ajax_framework_mobile.js"></script>
<script language="javascript">

jQuery(function($)
{	

$("#addtime").datepicker({dateFormat: 'dd-mm-yy'});

});
function fill_mob(inputdata)

{ 
	inputdata=trim(inputdata);
	options	=	{	
					url : 'getmobino.php?mcode='+inputdata,
					type: 'GET',
					success: mobiresponse
				}
		//alert('now i am saving new customer form');
		jQuery('#newcustomerfrm').ajaxSubmit(options);
	

}
function mobiresponse(text)
{
	//alert(text);
	//if(text!='')
	//{
	document.getElementById('mobino').value=text;
	//}
}

function show_cusdiv(text)
{
if(document.getElementById('ncus').style.display=='block'){
	
	document.getElementById('ncus').style.display='none';
	}else{
		document.getElementById('ncus').style.display='block'
		}
}

</script>
<div id="newcustomer">
<form id="newcustomerfrm">
<table >
	<tr>
    <th width="20%"><span class="compulsory">Customer Name</span></th>
    <td width="30%"><select name="customerid" id="customerid" onchange="fill_mob(this.value)">
    <option value="">Select Customer</option>
    <?php for($f=0;$f<count($resultarr);$f++){
		$customername	=	$resultarr[$f]['customername'];
		$cusid	=	$resultarr[$f]['pkcustomerid'];
		?>
    <option value="<?php echo $cusid;?>"><?php echo $customername;?></option>
    <?php }?>
    </select>&nbsp;&nbsp;&nbsp;<a href="#" onclick="show_cusdiv()" class="text">Add Walking Customer</a><div id="ncus" style="display:none"><input type="text" name="addnewcustomer" id="addnewcustomer" class="text" ></div></td>
	</tr>
    <tr>
    <th width="20%"><span class="compulsory">Mobile Number</span></th>
    <td width="30%"><input type="text" name="mobino" id="mobino" class="text" ><!--&nbsp;&nbsp;<input type="checkbox" name="updatemobi" value="1" />&nbsp;Update--></td>
	</tr>
	<tr>
    <th width="20%"><span class="compulsory">Required By Date </span></th>
    <td width="30%">   <input type="text" id="addtime" name="addtime" size="10" maxlength="10" value="<?php echo date('d-m-Y',time())?>" />     <div id="results" class="results"></div></td>
	</tr>
	<tr>
    <th><span class="compulsory">Demands</span></th>
    <td><textarea id="remarks" name="remarks"><?php echo $remarks;?></textarea></td>
    </tr>
   <?php /*?> <tr>
    <th width="20%"><span class="compulsory">Status</span></th>
    <td width="30%">
    	<select name="status" id="status" style="width:155px;">
        	<option value="Pending" <?php if($status=='Pending'){echo "selected=\"selected\"";} ?>>Pending</option>
            <option value="Completed" <?php if($status=='Completed'){echo "selected=\"selected\"";} ?>>Completed</option>
    	</select>
    </td>
    </tr><?php */?>
    
	<tr>
    <td colspan="3">
   
    <input type="hidden" name="demandid" value="<?php echo $demandid;?>" />  
     <input type="hidden" name="barcde" id="barcde"/>   
    <span class="buttons">
    <!--<input type="button" name="savenewcust" id="savenewcust" value="Save" onclick="savedemand(1)" />-->
	 <button type="button"name="savenewcust" id="savenewcust" value="Save" onclick="savedemand(1)" title="Save" style="font-size:12px;">
            <img src="images/disk.png" alt=""/> 
           Save
        </button>
	 <button type="button" name="button2" id="button2" onclick="hidediv('childdiv');" title="Cancel" style="font-size:12px;">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
    </span>
	</td>
	</tr>
</table>
</form>
</div>