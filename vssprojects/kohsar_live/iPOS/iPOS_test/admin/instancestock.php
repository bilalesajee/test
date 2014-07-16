<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$stockid	=	$_REQUEST['id'];
?>
<!--<link rel="stylesheet" type="text/css" href="../includes/css/all.css" />
<script src="../includes/js/jquery.js" type="text/javascript"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
<script language="javascript" type="text/javascript">
function viewdetails(id)
{
	var selectedstock	=	getselected();
	var selstock;
	if (selectedstock.length > 1)
	{
		for (i=1; i < selectedstock.length; i++)
		{
			 selstock	=	selectedstock[i];
		} 
		var sel	=	selstock.split('viewinstances');
		//alert(sel);
		//prepareforedit(checks, sb,cdiv);
		jQuery('#instock').load('instancestock.php?id='+sel[0]);
		
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
	if(id=='1')
	{
		jQuery('#viewinstance').load('viewinstancestock.php?id='+sel[0]);	
		jQuery('#viewstockdetails').load('viewstockdetails.php?id='+sel[0]);
		document.getElementById('viewstoredetails').innerHTML='';
	}
}
function viewstoredetails(bc,br)
{
	var selectedstore	=	getselected();
	var selstore;
	if (selectedstore.length > 1)
	{
		for (i=1; i < selectedstore.length; i++)
		{
			 selstore	=	selectedstore[i];
		} 
		var sel	=	selstore.split('viewinstances');
		//alert(selstore);
		//alert(bc);
		//prepareforedit(checks, sb,cdiv);
		jQuery('#instock').load('instancestock.php?id='+sel[0]);		
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
	jQuery('#viewstoredetails').load('viewstorestock.php?barcode='+bc+'&brandid='+br+'&id='+sel[0]);	
}
function submitinstance()
{
	loading('System is ....');
	
	options	=	{	
					url : 'viewinstancestock.php',
					type: 'POST',
					success: response
				}
	jQuery('#instancestock').ajaxSubmit(options);
}
function response(text)
{
	
	if(text!="")
	{

		//document.getElementById('error').innerHTML		=	text;
		document.getElementById('viewstocks').innerHTML				=	text;
		document.getElementById('viewstockdetails').innerHTML		=	'';
		document.getElementById('viewstoredetails').innerHTML		=	'';
		//document.getElementById('error').style.display	=	'block';
		
	}
	else
	{
			document.getElementById('error').style.display	=	'none';
			//jQuery('#maindiv').load('manageproducts.php?id='+text);
	}
			
}
function hideform2()
{
	document.getElementById('instancestock').style.display='none';
}
</script>
<div id="loading" class="loading" style="display:none;">
</div>
<div id="error" class="notice" style="display:none"></div>
<div id="inststock">
<form name="instancestock" id="instancestock" action="" onsubmit="submitinstance(); return false;">
<fieldset>
<legend>
View Stock
</legend>
<input type="text" name="bc" />
<input type="submit" name="viewstock" id="viewstock" value="View Stock" />
</fieldset>
</form>
</div>
<div id="viewstocks">
</div>
<div id="viewstockdetails">
</div>
<div id="viewstoredetails">
</div>
<script language="javascript">
document.instancestock.bc.focus();
loading('Loading Form...');
</script>