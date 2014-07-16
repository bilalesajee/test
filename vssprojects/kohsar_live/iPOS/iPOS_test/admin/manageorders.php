<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$qs;
$query="SELECT * FROM countries order by countryname ASC";
$countryarray	=	$AdminDAO->queryresult($query);
$invoiceid		=	$_REQUEST['id'];
$param			=	$_REQUEST['param'];	

if($param=='edit')
{
	print"xxxx";
	$pkinvoicepackagingid	=	$invoiceid;
	$query="SELECT
					pkinvoicepackagingid,
					fkbarcodeid,
					barcode,
					units,
					unitprice,
					totalprice,
					from_unixtime(expiry,'%d-%m-%y') as expiry,
					origin,
					boxno,
					
					fkinvoiceid,
					(
					 SELECT 
				CONCAT( productname, 
					   ' (', GROUP_CONCAT( IFNULL(attributeoptionname,'') 
												  ORDER BY attributeposition) ,')',
					   brandname
					   ) PRODUCTNAME 
			FROM 
				productattribute pa RIGHT JOIN (product p, attribute a) ON ( pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid ) , 
			attributeoption ao LEFT JOIN productinstance pi ON (pkattributeoptionid = pi.fkattributeoptionid), barcode b,brand br,barcodebrand bb 
			WHERE 
				pkproductid = pa.fkproductid 
				AND pkattributeid = pa.fkattributeid 
				AND pkproductattributeid = fkproductattributeid 
				AND pkattributeid = ao.fkattributeid 
				AND b.fkproductid = pkproductid 
				AND pi.fkbarcodeid = b.pkbarcodeid 
				AND br.pkbrandid=bb.fkbrandid
				AND bb.fkbarcodeid=b.pkbarcodeid
				AND b.pkbarcodeid = i.fkbarcodeid
				
				
			
					group by  PRODUCTNAME) as productname
				FROM 
					invoicespackaging i
				WHERE
					pkinvoicepackagingid='$pkinvoicepackagingid' 
					";
	
	
	$invoicedetailarray	=	$AdminDAO->queryresult($query);	
	$barcodeid	=	$invoicedetailarray[0]['fkbarcodeid'];
	$barcode	=	$invoicedetailarray[0]['barcode'];	
	$units		=	$invoicedetailarray[0]['units'];	
	$unitprice	=	$invoicedetailarray[0]['unitprice'];	
	$totalprice	=	$invoicedetailarray[0]['totalprice'];	
	$expiry		=	$invoicedetailarray[0]['expiry'];	
	$origin		=	$invoicedetailarray[0]['origin'];	
	$boxno		=	$invoicedetailarray[0]['boxno'];	
	$invoiceid	=	$invoicedetailarray[0]['fkinvoiceid'];	
	$productname=	$invoicedetailarray[0]['productname'];	
}
if($param!=='' && $param!='edit' && $invoiceid=='')
{
	echo  $invoiceid =$param;	
}
?>
<script language="javascript">
jQuery(function($)
{
	$("#expiry").datepicker({dateFormat: 'dd-mm-y'});
	<?php 
	if($param!='edit')
	{
	?>
		jQuery('#invoicesdiv').load('invoicespackaging.php?id=<?php echo $invoiceid;?>');
	<?php
	}//end of if
	?>
});

jQuery().ready(function() 
	{
		function findValueCallback(event, data, formatted) 
		{
			var barcode=document.getElementById('barcode').value=data[1];
			//alert(data[2]);
			//getitemdetails(document.getElementById('barcode1').value,1);
			document.getElementById('barcodeid').value=data[2];
			//getinstance('instancediv',barcode);
			loadinstances2(data[1],demandname);
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi,'');
		}
			jQuery("#productname").autocomplete("productautocomplete.php") ;
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
			//document.adstockfrm.reset(); 
});
function addinvoice()
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'insertinvoice.php',
					type: 'POST',
					success: response
				}
	jQuery('#invoicefrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice("Invoice has ben saved.","0",5000);
		//hidediv("invoicesdiv");
		jQuery('#invoicegrid').load('manageorders.php?id=<?php echo $invoiceid;?>');		
		document.invoicefrm.reset();
		//hidediv("demanddiv");
	}
	else
	{
		adminnotice(text,"0",5000);	
	}
}
function getinstance(inputdata)
{
	if(inputdata=='')
	{
		if(inputdata=='')
		{
			alert("Please enter Barcode.");
			return false;
		}
		inputdata=document.getElementById('barcode').value;
		//alert(inputdata);
	}
	//alert(code);
	jQuery('#pname').load('productlist.php?barcode='+inputdata);
	document.getElementById('productname').focus();
	document.getElementById('barcodeid').value='';
	
}
function calculateprice()
{
	var units		=	document.getElementById('units').value;
	var unitprice	=	document.getElementById('unitprice').value;
	var totalprice	=	units*unitprice;
	document.getElementById('totalprice').value=totalprice;

}
</script>
<div id="emailinvoice"></div>
<br />
<div id="orderdiv">
<div class="breadcrumbs" id="breadcrumbs">Add Items to Invoice</div>
  <form id="invoicefrm" name="invoicefrm" method="post" action="" class="form">
  	<table width="97%">
	  <tbody>
	  
	    <tr>
	      <th >BarCode</th>
	      <th >Product Name</th>
	      <th >Units</th>
	      <th >Unit Price</th>
	      <th >Total Price</th>
	      <th >Expiry</th>
	      <th >Origin</th>
	      <th >Box NO</th>
	      <td >&nbsp;</td>
        </tr>
	    <tr>
	      <td width="90" align="center">
          	<input name="barcode" type="text" id="barcode" onkeydown="javascript:if(event.keyCode==13) {getinstance(this.value); return false;}" class="text" value="<?php echo $barcode;?>" size="15"/>
          </td>
	      <td width="90" align="center">
          	<div id="pname">
            <input name="productname" type="text" class="text"  id="productname" size="15"  value="<?php echo $productname;?>"/>
           </div>
           </td>
	      <td width="36" align="center"><input name="units" type="text" class="text"  id="units" onblur="calculateprice()" size="6"  value="<?php echo $units;?>"/></td>
	      <td width="36" align="center"><input name="unitprice" type="text" class="text"  id="unitprice" onblur="calculateprice()"  size="6"  value="<?php echo $unitprice;?>"/></td>
	      <td width="60" align="center"><input name="totalprice" type="text" class="text"  id="totalprice" size="10"  value="<?php echo $totalprice;?>"/></td>
	      <td width="60" align="center"><input name="expiry" type="text"  id="expiry" size="10" class="text" readonly="readonly"  value="<?php echo $expiry;?>"/></td>
	      <td width="100" align="center">
          <select name="origin"  id="origin" style="width:100px;">
	        <?php
			for($c=0;$c<count($countryarray);$c++)
			{
			?>
	        <option value="<?php echo $countryarray[$c]['pkcountryid'];?>"> <?php echo $countryarray[$c]['countryname'];?> </option>
	        <?php
			}
			?>
          </select></td>
	      <td width="36" align="center"><input name="boxno" type="text"  id="boxno" size="6" class="text"  value="<?php echo $boxno;?>"/></td>
	      <td align="center">
          	<input type="hidden" name="pkinvoicepackagingid" id="pkinvoicepackagingid" value="<?php echo $pkinvoicepackagingid;?>"/>
            <input type="hidden" name="invoiceid" id="invoiceid" value="<?php echo $invoiceid;?>"/>
            <input type="hidden" name="barcodeid" id="barcodeid"  value="<?php echo $barcodeid;?>"/>
            <span class="buttons">
            <button type="button" name="btn" class="positive" onclick="addinvoice();">
                <img src="../images/tick.png" alt=""/> 
                Save
            </button>
            </span>
          </td>
        </tr>
    </tbody>
  </table>
  </form>
 </div>
 <div id="invoicesdiv">
</div>
