<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
//$counter_array	=	$AdminDAO->getrows('counter','countername');
?>
<script language="javascript" type="text/javascript">
function loaditeminfo(val)
{
	$('#iteminfo').load('loaditem.php?bc='+val);
}
$().ready(function() 
{
	document.getElementById('barcode').focus();
	function findValueCallback(event, data, formatted) 
	{
		document.getElementById('barcode').value=data[1];
		document.getElementById('itemdescription').value=data[0];
		document.getElementById('barcodeid').value=data[2];
		document.getElementById('btn2').focus();			
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
	$(":text, textarea").result(findValueCallback).next().click(function() 
	{
		$(this).prev().search();
	});
	$("#clear").click(function() 
	{
		$(":input").unautocomplete();
	});
	$("#itemdescription").autocomplete("itemautocomplete.php") ;
});

function viewreport()
{
	$('#displayreport').load('lastpurchases.php?barcodeid='+document.getElementById('barcodeid').value);
}
	/*options	=	{	
					url : 'lastpurchases.php',
					type: 'POST',
					success: response
				}
	jQuery('#frmreport').ajaxSubmit(options);
}
function disablearr()
{
	if(document.getElementById('arrangement').disabled==false)
		document.getElementById('arrangement').disabled	=	true;
	else
		document.getElementById('arrangement').disabled	=	false;		
}
function showreport()
{
	//document.getElementById('displayreport').innerHTML=text;
	//doWin();
	sd	=	document.getElementById('sdate').value;
	ed	=	document.getElementById('edate').value;
	arr	=	document.frmreport.arrangement.value;
	ord	=	document.frmreport.sortorder.value;
	pro	=	document.getElementById('productname').value;
	if(document.getElementById('productcat').checked==true)
	{
		var cat	=	1;
	}
	window.open('showreport.php?sdate='+sd+'&edate='+ed+'&arrangement='+arr+'&sortorder='+ord+'&cat='+cat+'&pro='+pro,"myWin","menubar,scrollbars,left=30px,top=40px,height=400px,width=600px");
}*/
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="iteminfo" style="display:none;"></div>
<div id="reportsdiv">
<form name="frmreport" id="frmreport" style="width:920px;" class="form">
<fieldset>
<legend>
	Purchase Report</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="viewreport();">
        <img src="../images/tick.png" alt=""/> 
        Search
    </button>
     <a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
	<table width="100%">
	    <tr>
        	<td width="8%">Barcode</td>
            <td><input type="text" class="text" id="barcode" name="barcode"  onkeydown="javascript:if(event.keyCode==13) {loaditeminfo(this.value); return false;}" onfocus="this.select();" /></td>
        </tr>
		<tr>
        	<td width="8%">Item Name</td>
        	<td><input type="text" class="text" autocomplete="off" id="itemdescription" name="itemdescription" onFocus="this.select();" /></td>
        </tr>
        <tr>
        	<td colspan="2">
            	<div class="buttons">
                    <button type="button" class="positive" id="btn2" onclick="viewreport();">
                        <img src="../images/tick.png" alt=""/> 
                        Search
                    </button>
                     <a href="javascript:void(0);" onclick="hidediv('reportsdiv');" class="negative">
                        <img src="../images/cross.png" alt=""/>
                        Cancel
                    </a>
                  </div>
            </td>
        </tr>
    </table>
    </fieldset><input type="hidden" name="barcodeid" id="barcodeid" value="" />
</form>
</div>
<div id="displayreport"></div>