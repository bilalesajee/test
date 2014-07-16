<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
?>
<script language="javascript">
$().ready(function(){
	document.getElementById('reordertext').focus();
	function findValueCallback(event, data, formatted) 
	{
		document.getElementById('stype').value=data[2];
		document.getElementById('stypeid').value=data[1];
		$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}
	function formatItem(row) 
	{
		return row[0] + " (<strong>id: " + row[0] + "</strong>)";
	}
	function formatResult(row) 
	{
		return row[0].replace(/(<.+?>)/gi,'');
	}
	$("#reordertext").autocomplete("ordereorderautocomplete.php", {extraParams: {stype: function() { return $("#reordertype").val(); } }});
	$(":text, textarea").result(findValueCallback).next().click(function() 
	{
		$(this).prev().search();
	});
	$("#clear").click(function() 
	{
		$(":input").unautocomplete();
	});			
});
function addorder(id)
{
	options	=	{	
					url : 'orderaddaction.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#orderform').ajaxSubmit(options);
}
function response(text)
{
	if(text==1)
	{
		adminnotice('Order has been saved.',0,5000);
		document.getElementById('subsection').innerHTML='';
		jQuery('#maindiv').load('manageshiplist.php?lock=1');
	}
	else
	{
		adminnotice('Order has been saved.',0,5000);
		document.getElementById('subsection').innerHTML='';		
		jQuery('#maindiv').load('manageshiplist.php');
	}
}
function loaditems(v)
{
	var type	=	document.getElementById('stype').value;
	var typeid	=	document.getElementById('stypeid').value;
	if(v==1)
	{
		start		=	0;
	}
	else if(document.getElementById('start').value!='')
	{
		var start	=	document.getElementById('start').value;
	}
	else
	{
		var start	=	0;
	}
	$('#reorderitems').load('orderadditems.php?type='+type+'&id='+typeid+'&start='+start);
	$('#reorderitems').show();
}
</script>
<br />
<form id="reorderform" name="reorderform" style="width: 920px;" action="" class="form">
<fieldset>
<legend>
Reorder Items
</legend>
<table width="100%">
  <tr>
   <td width="11%">
    <select name="reordertype" id="reordertype" onchange="">
        <option value="7">Barcode</option>
        <option value="2">Brand</option>
        <option value="6">Item Name</option>
        <option value="3">Supplier</option>
        <option value="1">Product</option>
        <option value="4">Country</option>
        <option value="5">Shipment</option>
    </select>
   </td>
   <td width="16%"><input type="text" size="100" name="reordertext" id="reordertext" value="" class="text" onKeyDown="javascript:if(event.keyCode==13){return false;}"></td>
   <td width="73%">
   <span class="buttons">
    <button type="button" class="positive" onClick="loaditems(1);"><img src="../images/tick.png" alt=""/>
        Search
      </button>
    <button type="button" onclick="hidediv('subsection');" class="negative"><img src="../images/cross.png" alt=""/> Cancel </button>
    </span>
   </td>
  </tr>
</table> <div id="reorderitems" style="display:none;"></div></fieldset>
 <input type="hidden" name="stypeid" id="stypeid" value="" >
 <input type="hidden" name="stype" id="stype" value="" >
 <input type="hidden" name="start" id="start" value="" >
<span class="buttons" style="float:right;margin-top:-20px;">
<button type="button" class="positive" onclick="viewhistory();"> <img src="../images/add.png" alt=""/>
View History
</button>
</span>
</form>
<div id="similaritemsdiv"></div>
<script language="javascript" type="text/javascript">
function viewhistory()
{
	bc	=	document.getElementById('barcodes').value;
	$('#similaritemsdiv').load('orderhistory.php?bcid='+bc);
}
</script>