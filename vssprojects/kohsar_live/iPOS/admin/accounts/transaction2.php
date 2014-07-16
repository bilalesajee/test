<?php
session_start();
include_once("../../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$qs		=	$_SESSION['qstring'];
$row 		=	$AdminDAO->getrows("$dbname_detail.account","*"," id= '$id'");
for($i = 0;$i<sizeof($row);$i++)
{
	$title 			=	$row[0]['title'];
	$code 			=  	$row[0]['code'];
	$category_id	=	$row[0]['category_id'];
	$creation_date	=	date("d-M-Y",$row[0]['creationdate']);
	$status			=	$row[0]['status'];		
	$type_id		= 	$row[0]['type_id'];
}
/********************************Categories***********************************/
$categories	=	$AdminDAO->getrows('accountcategory','*','1');
/****************************************************************************/
?>
<script language="javascript" type="text/javascript">
$().ready(function() 
	{
		$("#date1").mask("99-99-9999");
	});		
var ctr	= 4;
function addrow(t)
{
	var mytable=document.getElementById("transactiontable");
	var newrow=mytable.insertRow(ctr);
	newrow.id	=	"tr"+ctr;
	
if(t=='d')//adding dr row
{	

	//column 1
	var newcell=newrow.insertCell(0); //insert new cell to row
	newcell.innerHTML	=	'<a href="javascript:void(0);" onclick="javascript:deltrasactionrow('+ctr+'); calc();"><img src="../images/del_16.gif" /></a><a href="javascript:void(0);" onclick="javascript:addrow(\'d\'); calc();"><img src="../images/add_16.gif" /></a>';//mytable.rows.length

	//var newcell=newrow.insertCell(1); //insert new cell to row
	//newcell.innerHTML	=	'<a href="javascript:void(0);" onclick="javascript:deltrasactionrow('+ctr+'); calc();"><img src="../images/del_16.gif" /></a';//+mytable.rows.length
	
	//column 2
	var newcell1=newrow.insertCell(1); //insert new cell to row
	newcell1.innerHTML	=	'<?php $AdminDAO->dropdown("draccount[]","$dbname_detail.account","id","concat(title,' (',code,')') as accounttitle");?>';//mytable.rows.length
	//column 3
	var newcell2=newrow.insertCell(2); //insert new cell to row
	newcell2.innerHTML	=	'<input type="text" style="width:30px" value=""  id="drefid'+ctr+'" name="drefid[]" size="10" onkeypress="return isNumberKey(event)" />';//+mytable.rows.length

	//column 4
	var newcell3=newrow.insertCell(3); //insert new cell to row
	newcell3.innerHTML	=	'';//+mytable.rows.length

	//column 5
	var newcell4=newrow.insertCell(4); //insert new cell to row
	newcell4.innerHTML	=	'';//+mytable.rows.length
	
	//column 6
	var newcell5=newrow.insertCell(5); //insert new cell to row
	newcell5.innerHTML	=	'<span style=" float:right"><input type="text" value="0.00"  id=dramount'+ctr+' name="dramount[]" dir="rtl" size="10" onkeypress="return isNumberKey(event)" onblur="javascript:calc();"  onfocus="selectamount(this.id);"  /></span>';//+mytable.rows.length
	
	//column 7
	var newcell6=newrow.insertCell(6); //insert new cell to row
	newcell6.innerHTML	=	'';//+mytable.rows.length
	
	//column 8
	var newcell7=newrow.insertCell(7); //insert new cell to row
	newcell7.innerHTML	=	'';//+mytable.rows.length	
	
}//if
else //adding cr row
{	
	//column 1
	var newcell=newrow.insertCell(0); //insert new cell to row
	newcell.innerHTML	=	' <a href="javascript:void(0);" onclick="javascript:deltrasactionrow('+ctr+'); calc();"><img src="../images/del_16.gif" /></a><a href="javascript:void(0);" onclick="javascript:addrow(\'c\')"><img src="../images/add_16.gif" /></a>';//mytable.rows.length
	//column 2
	//var newcell1=newrow.insertCell(1); //insert new cell to row
	//newcell1.innerHTML	=	'<a href="javascript:void(0);" onclick="javascript:deltrasactionrow('+ctr+'); calc();"><img src="../images/del_16.gif" /></a';//+mytable.rows.length
	
	//column 2
	var newcell1=newrow.insertCell(1); //insert new cell to row
	newcell1.innerHTML	=	'';//+mytable.rows.length

	//column 3
	var newcell2=newrow.insertCell(2); //insert new cell to row
	newcell2.innerHTML	=	'';//+mytable.rows.length
	
	//column 4
	var newcel13=newrow.insertCell(3); //insert new cell to row
	newcel13.innerHTML	=	'<?php $AdminDAO->dropdown("craccount[]","$dbname_detail.account","id","concat(title,' (',code,')') as accounttitle");?>';

	//column 5
	var newcell4=newrow.insertCell(4); //insert new cell to row
	newcell4.innerHTML	=	'<input type="text" style="width:30px" value=""  id="crefid'+ctr+'" name="crefid[]" size="10" onkeypress="return isNumberKey(event)" />';//+mytable.rows.length

	//column 6
	var newcell5=newrow.insertCell(5); //insert new cell to row
	newcell5.innerHTML	=	'';//+mytable.rows.length
	
	//column 7
	var newcell6=newrow.insertCell(6); //insert new cell to row
	newcell6.innerHTML	=	'<span style=" float:right"><input type="text" value="0.00"  id=cramount'+ctr+' name="cramount[]" dir="rtl" size="10" onkeypress="return isNumberKey(event)" onblur="javascript:calc();" onfocus="selectamount(this.id);" /></span>';//+mytable.rows.length
	
	//column 8
	var newcell7=newrow.insertCell(7); //insert new cell to row
	newcell7.innerHTML	=	'';//+mytable.rows.length
	
}//elseif
ctr = ctr+1;
}//addrow
function deltrasactionrow(id)
{
	if(confirm("Are you sure to delete this row?")==true)
	{
		var d	=	"tr"+id;
		var tr = document.getElementById(d);
		tr.parentNode.removeChild(tr);
		ctr--;
	}
}//delrow
function calc()
{
	var drsum	=	0.0;
	var crsum	=	0.0;
	var frm	=	document.getElementById('transaction');
	$(':input', frm).each(
					function()
					{
							var type = this.type;
							var tag = this.tagName.toLowerCase(); // normalize case
							var name = this.name;
							if (type == 'text')
							{
								//this.value = parseFloat(this.value).toFixed(<?php //echo $decimalplaces;?>);
								
								//alert(parseFloat(this.value));
								if(name=='dramount[]')
								{
									this.value = parseFloat(this.value).toFixed(<?php echo $decimalplaces;?>);
									drsum	+=	parseFloat(this.value);
								}
								else if(name=='cramount[]')
								{
									this.value = parseFloat(this.value).toFixed(<?php echo $decimalplaces;?>);
									crsum	+=	parseFloat(this.value);
								}
							}
					}
	);
	//alert(drsum+'...'+crsum);
	$('#drsum').html(drsum.toFixed(<?php echo $decimalplaces;?>));
	$('#crsum').html(crsum.toFixed(<?php echo $decimalplaces;?>));
	if(drsum != crsum)
	{
		document.getElementById('drsum1').style.backgroundColor	=	"red";
		document.getElementById('crsum1').style.backgroundColor	=	"red";
		var elements = document.getElementsByClassName('positive');
		elements[0].disabled	=	true;
		elements[1].disabled	=	true;
		
	}
	else
	{
		document.getElementById('drsum1').style.backgroundColor	=	"green";
		document.getElementById('crsum1').style.backgroundColor	=	"green";
		var elements = document.getElementsByClassName('positive');
		elements[0].disabled	=	false;
		elements[1].disabled	=	false;
	}
}
function selectamount(id)
{
	document.getElementById(id).select();
}
function gettypes(catid,typeid)
{
	$('#types').load('gettypes.php?catid='+catid+'&typeid='+typeid);
}
$(document).ready(function()
{
	gettypes(<?php echo "'$category_id'";?>,<?php echo "'$type_id'";?>);
	
		var elements = document.getElementsByClassName('positive');
		elements[0].disabled	=	true;
		elements[1].disabled	=	true;
	//alert($('.positive').text);
	/*$('#transaction').submit(function{
	alert('submitting...');		
		});*/
//	$("#date").mask("99.99");
});
</script>


<div id="error" class="notice" style="display:none"></div>
<div id="transactiondiv" style="margin-left:10px">
<form name="transaction" id="transaction" style="width:920px;" class="form">
<fieldset style="width:96%">
<legend>
Perform A Transaction
</legend>
<table align="right">
 <tr>
        <td colspan="2" align="right">
        <?php buttons("accounts/inserttransaction.php","transaction","64","account/transactions.php",'1');?>
        </td>
    </tr>
</table>   
<br /> <br /> <br /> 
<table width="95%">
</table>
    <table>
   
	<tr>
    <td width="100%">
    <fieldset>
<table id="transactiontable" width="100%">
    <tr>
        <th colspan="8">Transaction Date:<?php echo date('d F Y');?></th>
    </tr>
	<tr>
    	<th>Add New Dr. Row</th>
        <th>Dr.</th>
        <th>Dr. Ref</th>
        <th>Cr.</th>
        <th>Cr. Ref</th>
        <th>Amount</th>
        <th>Amount</th>
        <th>Add New Cr. Row</th>
    </tr>
    <tr>
    <td align="right"><a href="javascript:void(0);" onclick="javascript:addrow('d'); calc();"><img src="../images/add_16.gif" /></a></td>
        <td><?php $AdminDAO->dropdown("draccount[]","$dbname_detail.account","id","concat(title,' (',code,')') as accounttitle");?></td>
        <td><input type="text" style="width:30px" value=""  id='drefid0' name="drefid[]" size="10" onkeypress="return isNumberKey(event)" /></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">
        <input type="text" id="dramount0" onclick="selectamount(this.id);" value="0.00" name="dramount[]" dir="rtl" size="10" onkeypress="return isNumberKey(event)" onblur="javascript:calc();"/>
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        
        
    </tr> 
    <tr>
   <td  align="right"></td>
   <td>&nbsp;</td>
   <td>&nbsp;</td>
   	<td><?php $AdminDAO->dropdown("craccount[]","$dbname_detail.account","id","concat(title,' (',code,')') as accounttitle");?></td>
    <td><input type="text" style="width:30px" value=""  id='crefid0' name="crefid[]" size="10" onkeypress="return isNumberKey(event)" /></td>
    <td>&nbsp;</td>
    <td align="right">
		<input type="text" id="cramount0" onclick="selectamount(this.id);" value="0.00"  name="cramount[]" dir="rtl" size="10" onkeypress="return isNumberKey(event)" onblur="javascript:calc();" />
	</td>
    <td><a href="javascript:void(0);" onclick="javascript:addrow('c'); calc();"><img src="../images/add_16.gif" /></a></td>
    
    </tr>    
    <tr height="30px">
    <th colspan="5" align="right">Total: </th>
   <th  align="right" id="drsum1"><?php echo $currency;?> <span id="drsum">0.00</span></th>
   <th  align="right" id="crsum1"><?php echo $currency;?> <span id="crsum">0.00</span></th>
   <th>&nbsp;</th>
</tr> 
	<tr >
	<th>Description:</th>
    <td colspan="7">
          <textarea cols="100" rows="1" name="details" id="details"></textarea>
    </td>
</tr>
	<tr >
	<th>Date:</th>
    <td colspan="7">
	<input name="date1" id="date1" type="text" value="<?php echo $date1; ?>" onkeydown="javascript:if(event.keyCode==13) {todate.focus(); return false;}" size="8"> dd-mm-yyyy
    </td>
</tr>
</table>
	</fieldset>
    </td>
    <td>
    </td>
    </tr>
    </table>
    
    
        <table>
    	<tr >
        <td colspan="2" align="center">
        <?php buttons("accounts/inserttransaction.php","transaction","64","accounts/transactions.php",'0');?>
        </td>
    </tr>
    </table>
</fieldset>
</form>
</div>
<script language="javascript">
//document.addstore.storename.focus();
loading('Loading Form...');
</script>
<br />