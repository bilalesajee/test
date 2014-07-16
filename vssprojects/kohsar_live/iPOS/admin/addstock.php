<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
$id		=	$_REQUEST['id'];
if($id!=-1)
{
	$barcodes	=	$AdminDAO->getrows("barcode","barcode","pkbarcodeid='$id'");
	$barcodex	=	$barcodes[0]['barcode'];
}
else
{
	$barcode	=	filter($_REQUEST['code']);
	$bc			=	filter($_REQUEST['bc']);
	if($bc!='')
	{
		$barcode=$bc;
	}
}
//this is the number of MAX items that can be added to the stock
$itemcount	=	10;
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$storeid	=	$_SESSION['storeid'];
	$empid		=	$_SESSION['employeeid'];
	$addbookid	=	$_SESSION['addressbookid'];
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$stores			=	$AdminDAO->getrows("store","storename,pkstoreid,storedb","storedeleted<>1 AND storestatus=1 ");
	$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:100px;\" onchange=\"showshipments(this.value)\"><option value=\"\">Location</option>";//
	for($i=0;$i<sizeof($stores);$i++)
	{
		$storename	=	$stores[$i]['storename'];
		$storeid	=	$stores[$i]['pkstoreid'];
		$storedb	=	$stores[$i]['storedb'];
		$select		=	"";
		if($storeid == $default_store)
		{
			$select = "selected=\"selected\"";
		}
		$storesel2	.=	"<option value=\"$storeid|$storedb\" $select>$storename</option>";
	}
	$stores			=	$storesel.$storesel2."</select>";
	// end stores
	$empid		=	$_SESSION['employeeid'];	
}//end edit
/****************************PRODUCT DATA*****************************/
$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `barcode`='$barcode'");
$productname 		=	$barcode_array[0]['productname'];
$productid	 		=	$barcode_array[0]['pkproductid'];
$pkbarcodeid 		=	$barcode_array[0]['pkbarcodeid'];
$productdescription =	$barcode_array[0]['productdescription'];
/***********************************Attributes DATA*************************/
if($productid!='')
{
	$attributes_array	=	$AdminDAO->getrows('productattribute,attribute,productinstance ','*',"`pkattributeid`=`fkattributeid` AND `fkproductid`='$productid' AND fkproductattributeid=pkproductattributeid AND fkbarcodeid='$pkbarcodeid' ");
	$proinstance_array	=	$AdminDAO->getrows('productinstance','*',"`fkbarcodeid`='$pkbarcodeid' ");
}
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$empid	=	$_SESSION['addressbookid'];
	$query="SELECT 
				quantity,
				FROM_UNIXTIME(updatetime,'%d-%m-%Y') as updatetime,
				FROM_UNIXTIME(expiry,'%d-%m-%Y') as expiry,
				itemdescription,
				barcode 
			FROM 
				$dbname_detail.stock,
				barcode, 
				addressbook,
				employee
			WHERE 
				fkbarcodeid=pkbarcodeid AND
				fkemployeeid=pkemployeeid AND
				employee.fkaddressbookid=pkaddressbookid AND
				pkaddressbookid='$addbookid'
			ORDER BY pkstockid DESC LIMIT 0,1 ";
	$arraylast	=	$AdminDAO->queryresult($query);
	if(count($arraylast)>0)
	{
	?>
	<div class="topimage" style="height:6px;"><!-- --></div>
	<table width="100%" cellspacing="0">
	<!--<tr>
	<th colspan="5">Last Added Stock</th>
	</tr>-->
	<tr>
		<th>Quantity</th>
		<th>Barcode</th>
		<th>Item Description</th>
		<th>Expiry</th>
		<th>Update Time</th>
	</tr>
	<tr>
		<td><?php echo $arraylast[0]['quantity'];?></td>
		<td><?php echo $arraylast[0]['barcode'];?></td>
		<td><?php echo $arraylast[0]['itemdescription'];?></td>
		<td><?php echo $arraylast[0]['expiry'];?></td>
		<td><?php echo $arraylast[0]['updatetime'];?></td>
	</tr>
	</table>
	<?php
	}
	?>
	<div id="productdiv">
	</div>
	<?php
	$shipment_array			=	$AdminDAO->getrows('shipment','pkshipmentid, shipmentname ',"  fkdeststoreid='$storeid' AND `shipmentdeleted`<>1");
	
	$shipment				=	$Component->makeComponent("d","shipment",$shipment_array,"pkshipmentid","shipmentname",1,$selected_shipment,'onchange=getshipmentgroup(this.value)','class=eselect');
	
	$brands_array			=	$AdminDAO->getrows('brand, barcodebrand, countries',"pkbrandid, CONCAT(brandname,' ',countryname) AS brandname "," branddeleted<>'1' AND fkbrandid=pkbrandid AND fkbarcodeid='$pkbarcodeid' AND fkcountryid=pkcountryid ");
	$brands					=	$Component->makeComponent("d","brands",$brands_array,"pkbrandid","brandname",1,$selected_brands,'onchange=getbrandsupplier(this.value)');
	$suppliers_array		=	$AdminDAO->getrows('supplier s, addressbook ab',"CONCAT(s.companyname, ' (', ab.firstname, ab.lastname, ')') as suppliername, s.pksupplierid"," ab.pkaddressbookid=s.fkaddressbookid GROUP BY pksupplierid");
	$firstsupplierid		=	$suppliers_array[0]['pksupplierid'];
	$suppliers1	=	"<select name=brandsupplier class = \"eselect\" onchange=\"getinvoices(this.value);\">";
	for($i=0;$i<sizeof($suppliers_array); $i++)
	{
		$supplierid		=	$suppliers_array[$i]['pksupplierid'];
		$suppliername	=	$suppliers_array[$i]['suppliername'];
		$suppliers2.=	"<option value=$supplierid>$suppliername</option>";
	}
	$suppliers	=	$suppliers1.$suppliers2."</select>";
	//$q						=	$brandsarray("brand","*',"");
	$damagesarr	=	$AdminDAO->getrows("damagetype","*","1");
	$d1			=	"<select name=\"damagetype[]\" id=\"damagetype[]\" style=\"width:80px;\">";
	for($i=0;$i<sizeof($damagesarr);$i++)
	{
		$d2			.=	"<option value = \"".$damagesarr[$i][pkdamagetypeid]."\">".$damagesarr[$i][damagetype]."</option>";
	}
	$damages		=	$d1.$d2."</select>";
?>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
    <div id="productdiv">
    </div>
    <?php
    //$shipment_array			=	$AdminDAO->getrows('shipment','pkshipmentid, shipmentname '," isopened='o' AND fkstoreid='$storeid' AND `shipmentdeleted`<>1");
    
    // made changes in shipment array to show shipments for selected store 
    // removed store id
    /*$shipment_array			=	$AdminDAO->getrows('shipment','pkshipmentid, shipmentname '," isopened='o' AND fkdeststoreid='$storeid' AND `shipmentdeleted`<>1");
    
    
    $shipment				=	$Component->makeComponent("d","shipment",$shipment_array,"pkshipmentid","shipmentname",1,$selected_shipment,'onchange=getshipmentgroup(this.value)','class=eselect');*/
    
    $brands_array			=	$AdminDAO->getrows('brand, barcodebrand, countries',"pkbrandid, CONCAT(brandname,' ',countryname) AS brandname "," branddeleted<>'1' AND fkbrandid=pkbrandid AND fkbarcodeid='$pkbarcodeid' AND fkcountryid=pkcountryid ");
    $brands					=	$Component->makeComponent("d","brands",$brands_array,"pkbrandid","brandname",1,$selected_brands,'onchange=getbrandsupplier(this.value)');
    $suppliers_array		=	$AdminDAO->getrows('supplier s, brandsupplier bs,addressbook ab',"CONCAT(s.companyname, ' (', ab.firstname, ab.lastname, ')') as suppliername, s.pksupplierid"," s.pksupplierid = bs.fksupplierid AND ab.pkaddressbookid=s.fkaddressbookid GROUP BY pksupplierid");
    $suppliers1	=	"<select name=brandsupplier class = \"eselect\">";
    for($i=0;$i<sizeof($suppliers_array); $i++)
    {
        $supplierid		=	$suppliers_array[$i]['pksupplierid'];
        $suppliername	=	$suppliers_array[$i]['suppliername'];
        $suppliers2.=	"<option value=$supplierid>$suppliername</option>";
    }
    $suppliers	=	$suppliers1.$suppliers2."</select>";
    //$q						=	$brandsarray("brand","*',"");
    $damagesarr	=	$AdminDAO->getrows("damagetype","*","1");
    $d1			=	"<select name=\"damagetype[]\" id=\"damagetype[]\" style=\"width:80px;\">";
    for($i=0;$i<sizeof($damagesarr);$i++)
    {
        $d2			.=	"<option value = \"".$damagesarr[$i][pkdamagetypeid]."\">".$damagesarr[$i][damagetype]."</option>";
    }
    $damages		=	$d1.$d2."</select>";
}//end edit?>
<script language="javascript" type="text/javascript">
<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
	 jQuery(function($)
	 {
		 for(j=1;j<=10;j++)
		 {
			 $("#expiry"+j).datepicker({dateFormat: 'yy-mm-dd'});
		 }
		 if(document.getElementById('lock').checked==true)
		 {
			document.getElementById('barcode1').focus();
		 }
		 else
		 {
			 document.getElementById('shipment').focus();
		 }
		 getinvoices(<?php echo $firstsupplierid;?>);
	 });
	/*jQuery().ready(function() 
		{
			function findValueCallback(event, data, formatted) 
			{
				var barcode=document.getElementById('barcode1').value=data[1];
				getitemdetails(document.getElementById('barcode1').value,1);
				//getinstance('instancediv',barcode);
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
	});*/
	function getinvoices(sid)
	{
		$('#invoices').load('getinvoices.php?sid='+sid);
	}
<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
	 jQuery(function($)
	 {
		 $('#loadshipment').load('loadshipment.php');
		 for(j=1;j<=10;j++)
		 {
			 $("#expiry"+j).datepicker({dateFormat: 'yy-mm-dd'});
		 }
		 if(document.getElementById('lock').checked==true)
		 {
			document.getElementById('barcode1').focus();
		 }
		 else
		 {
			 document.getElementById('store').focus();
		 }
	 });
	jQuery().ready(function() 
		{
			function findValueCallback(event, data, formatted) 
			{
				var barcode=document.getElementById('barcode1').value=data[1];
				getitemdetails(document.getElementById('barcode1').value,1);
				//getinstance('instancediv',barcode);
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
	function showshipments(id)
	{
		$('#loadshipment').load('loadshipment.php?id='+id);
	}
<?php }//end edit?>
function getitemdetails(bc,itm)
{
		if(bc=='')
		{
			alert("Please enter Barcode.");
			return false;
		}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getitemdata.php?bc='+bc+'&item='+itm);
	document.getElementById('units1').focus();
}
function getshipmentgroup(id)
{
	if(id!='')
	{
		//jQuery('#shipmentgroup').load('loadshipmentgroup.php?id='+id+'&type=shipgroup');
		jQuery('#currency').load('loadcurrency.php?id='+id);
		jQuery('#priceatorigin').load('loadcurrency.php?p=1&id='+id);
		jQuery('#shippercentdiv').load('loadshipmentcharges.php?shipid='+id);
		document.getElementById('barcode1').focus();
/*		for(i=1;i<=10;i++)
		{
				jQuery('#shipmentgroups'+i).load('loadshipmentgroup.php?id='+id+'&type=shipgroup');
		}
*/		clearforms2();
	}
}
function getbrandsupplier(id)
{
	//alert(id);
	if(id!='')
	{
		if(document.getElementById('locksupplier').checked == false)
		{
			jQuery('#brandsupplier').load('loadshipmentgroup.php?id='+id+'&type=brandsupplier');
			document.getElementById('brandsupplier2').style.display	= 'none';
		}
	}
}
function addinstancestock()
{
	
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertstock.php',
					type: 'POST',
					success: addstockresponse
				}
	jQuery('#adstockfrm').ajaxSubmit(options);

}
function addproductinstance(productid,barcode)
{
	jQuery('#instance').load('addproductinstancefrm.php?id='+productid+'&bc='+barcode);
}
function addstockresponse(text)
{
	if(text=='')
	{
		adminnotice('Stock data has been saved.',0,8000);
		clearforms();
		document.getElementById('barcode1').focus();
		jQuery('#maindiv').load('managestocks.php');
	}
	else if (text=='locked')
	{
		adminnotice('Stock data has been saved.',0,8000);
		clearforms();
		document.getElementById('barcode1').focus();
	}
	else
	{
		adminnotice(text,0,8000);	
	}
	/*if(text!='')
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
	if(text=='')
	{
		document.getElementById('instancediv').style.display	=	'none';
		document.getElementById('error').style.display	=	'block';
		document.getElementById('error').innerHTML="Data Saved";
		jQuery('#error').fadeOut(3000);		
		jQuery('#maindiv').load('managestock.php');		
	}*/
}
function hidediv(divid)
{
	document.getElementById(divid).style.display='none';	
}

function calculateprice(val)
{
	var shipmentid	=	document.getElementById('shipment').value;
	if(shipmentid!='')
	{
		var currency	=	document.getElementById('shipmentcurrency').value;
		var rate		=	document.getElementById('exchangerate').value;
		var total		=	rate*val;
		document.getElementById('priceinrs').value=total;
	}
	else
	{
	 alert("Please select a Shipment first");
	 document.getElementById('shipment').focus();
	}
}
function hidethis(id)
{
	id = parseInt(id);
	if(id == 10)
	{
		document.getElementById('btn'+id).style.display = 'none';
	}
	document.getElementById('btn'+id).style.display = 'none';
	id	=	id+1;
	document.getElementById(id).style.display = 'block';
	document.getElementById(id).style.display = 'table-row';
}
function submitfrm()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertstock.php',
					type: 'POST',
					success: addstockresponse
				}
	jQuery('#adstockfrm').ajaxSubmit(options);

}
function addproductinstance(productid,barcode)
{
	jQuery('#instance').load('addproductinstancefrm.php?id='+productid+'&bc='+barcode);
}
function checkin(boxnum)
{
	if(boxnum==1)
	{
		var ppval	=	document.getElementById('pp1').value;
		for(i = 1; i<=10;i++)
		{
			if(document.getElementById(i).style.display != 'none')
			{
				if(document.getElementById('chk1').checked == true)
				{
					document.getElementById('pp'+i).value	=	ppval;
				}
				else
				{
					document.getElementById('pp'+i).value	=	'';
				}
			}
		}
	}
	if(boxnum==2)
	{
		var prval	=	document.getElementById('pr1').value;
		for(i = 1; i<=10;i++)
		{
			if(document.getElementById(i).style.display != 'none')
			{
				if(document.getElementById('chk2').checked == true)
				{
					document.getElementById('pr'+i).value	=	prval;
				}
				else
				{
					document.getElementById('pr'+i).value	=	'';
				}
			}
		}
	}
	if(boxnum==3)
	{
		var cpval	=	document.getElementById('cp1').value;
		for(i = 1; i<=10;i++)
		{
			if(document.getElementById(i).style.display != 'none')
			{
				if(document.getElementById('chk3').checked == true)
				{
					document.getElementById('cp'+i).value	=	cpval;
				}
				else
				{
					document.getElementById('cp'+i).value	=	'';
				}
			}
		}
	}
	if(boxnum==4)
	{
		var spval	=	document.getElementById('sp1').value;		
		for(i = 1; i<=10;i++)
		{
			if(document.getElementById(i).style.display != 'none')
			{
				if(document.getElementById('chk4').checked == true)
				{
					document.getElementById('sp'+i).value	=	spval;
				}
				else
				{
					document.getElementById('sp'+i).value	=	'';
				}
			}
		}
	}
	if(boxnum==5)
	{
		var shipval	=	document.getElementById('shipmentcharges1').value;		
		for(i = 1; i<=10;i++)
		{
			if(document.getElementById(i).style.display != 'none')
			{
				if(document.getElementById('chk5').checked == true)
				{
					document.getElementById('shipmentcharges'+i).value	=	shipval;
				}
				else
				{
					document.getElementById('shipmentcharges'+i).value	=	'';
				}
			}
		}
	}
	
	if(boxnum==6)
	{
		var shipval	=	document.getElementById('boxprice1').value;		
		for(i = 1; i<=10;i++)
		{
			if(document.getElementById(i).style.display != 'none')
			{
				if(document.getElementById('chk6').checked == true)
				{
					document.getElementById('boxprice'+i).value	=	shipval;
				}
				else
				{
					document.getElementById('boxprice'+i).value	=	'';
				}
			}
		}
	}
}

function clearforms()
{
	document.getElementById('productname').value = "";
	document.getElementById('barcode1').value = "";
	document.getElementById('itembrands').innerHTML = "";
	document.getElementById('brandsupplier').innerHTML = "";
	document.getElementById('chk1').checked = false;
	document.getElementById('chk2').checked = false;
	document.getElementById('chk3').checked = false;
	document.getElementById('chk4').checked = false;
	document.getElementById('chk5').checked = false;
	document.getElementById('chk6').checked = false;
	for(x=1;x<=10;x++)
	{
		document.getElementById('pp'+x).value="";
		document.getElementById('pr'+x).value="";
		document.getElementById('cp'+x).value="";
		document.getElementById('sp'+x).value="";
		document.getElementById('boxprice'+x).value="";
		document.getElementById('sch'+x).value="";
		document.getElementById('shipmentcharges'+x).value="";
		document.getElementById('batch'+x).value="";
		//document.getElementById('expiry'+x).value="";
		document.getElementById('dd'+x).value="";
		document.getElementById('mm'+x).value="";
		document.getElementById('yy'+x).value="";
		document.getElementById('units'+x).value="";
		document.getElementById('damaged'+x).value="";
	}
}
function clearforms2()
{
	document.getElementById('chk1').checked = false;
	document.getElementById('chk2').checked = false;
	document.getElementById('chk3').checked = false;
	document.getElementById('chk4').checked = false;
	document.getElementById('chk5').checked = false;
	document.getElementById('chk6').checked = false;
	for(x=1;x<=10;x++)
	{
		document.getElementById('pp'+x).value="";
		document.getElementById('pr'+x).value="";
		document.getElementById('cp'+x).value="";
		document.getElementById('sp'+x).value="";
		document.getElementById('boxprice'+x).value="";
		document.getElementById('sch'+x).value="";
		document.getElementById('shipmentcharges'+x).value="";
		document.getElementById('batch'+x).value="";
		//document.getElementById('expiry'+x).value="";
		document.getElementById('dd'+x).value="";
		document.getElementById('mm'+x).value="";
		document.getElementById('yy'+x).value="";
		document.getElementById('units'+x).value="";
		document.getElementById('damaged'+x).value="";
	}
}
function checkunits(num)
{
	var dnum	=	parseInt(document.getElementById('damaged'+num).value);
	var unum	=	parseInt(document.getElementById('units'+num).value);
	if(dnum>unum)
	{
		alert('Damaged Units can not be more than Total Units');
		document.getElementById('damaged'+num).focus();
		return false;
	}
}
function calcprice(pid)
{
	var shipid	=	document.getElementById('shipment').value;
	var pprice	=	document.getElementById('pp'+pid).value;
	if(shipid == '')
	{
		alert('Select Shipment to continue');
		return false;
	}
	else
	{
		pvalue	=	document.getElementById('hprice').value;
		if(pprice!='')
		{
			document.getElementById('pr'+pid).value	=	pprice*pvalue;
		}
		else
		{
			document.getElementById('pr'+pid).value	=	'';
		}
	}
}

// The formula
// Shipment Value
/* 
Value	=	(Total Value) minus ([price*(units-damaged)])
*/
// Shipment Cost
/*
Cost	=	(Total Cost) minus ([price*(units)*percentage]) Plus ([damaged*price])
*/

// The Percentage

/*
	Percentage	=	Cost/Value*100 
*/
function calculatethis()
{
	var remunitsprice=0, damagedunitsprice=0,percentageonprice=0, pprice=0;
	for(i=1;i<=10;i++)
	{
		units	=	document.getElementById('units'+i).value;
		damaged	=	document.getElementById('damaged'+i).value;
		pprice	=	document.getElementById('pr'+i).value;
		percent	=	document.getElementById('sch'+i).value;
		retail	=	document.getElementById('cp'+i).value;
		shipch	=	document.getElementById('shipmentcharges'+i).value;
				
		//1. remaining units for shipment value
		
		remunits	=	units-damaged;
		remunitsprice	=	remunitsprice + (remunits*pprice);
		
		// to be deducted from shipment value
		document.getElementById('shipvalue').innerHTML	=	remunitsprice;
		
		// to be deducted from the shipment cost
		percentageonprice	=	percentageonprice	+	((remunits*pprice)*(percent/100));
		document.getElementById('minusshipment').innerHTML	=	percentageonprice;
				
		//2. damaged units for shipment cost
		
		// to be added to the shipment cost
		damagedunitsprice	=	damagedunitsprice + (damaged*pprice);
		document.getElementById('plusshipment').innerHTML = damagedunitsprice;
		
		// putting the retail price	
		if(pprice!='')
		{
			document.getElementById('cp'+i).value	=	parseFloat(pprice)+(pprice*percent/100);
		}
		else
		{
			document.getElementById('cp'+i).value	=	'';
		}
		
		if(pprice!='')
		{
			document.getElementById('shipmentcharges'+i).value	=	parseFloat(pprice)*percent/100;
		}
		else
		{
			document.getElementById('shipmentcharges'+i).value	=	'';
		}
	}
	basevalue	=	document.getElementById('baseprice').innerHTML;
	basevalue	=	parseFloat(basevalue);
	basecost	=	document.getElementById('baseexpense').innerHTML;
	basecost	=	parseFloat(basecost);
	
	finalvalue	=	basevalue	-	parseFloat(document.getElementById('shipvalue').innerHTML);
	finalcost	=	basecost	-	parseFloat(document.getElementById('minusshipment').innerHTML)+parseFloat(document.getElementById('plusshipment').innerHTML);
	remainingpercentage	=	finalcost/finalvalue*100;
	document.getElementById('percentagediv').innerHTML	=	remainingpercentage;
}
function alertdate(id)
{
	yy	=	document.getElementById(id).value;
	if(yy!="")
	{
		year	=	parseInt(yy)+2000;
		newid	=	id.substr(2,2);
		mm		=	document.getElementById('mm'+newid).value;
		dd		=	document.getElementById('dd'+newid).value;
		dateval	=	year+'-'+mm+'-'+dd;
		if(dateval<"<?php echo date('Y-m-d')?>")
		{
			alert("The item has already expired, please correct the date.");
		}
		valid	=	dd+'-'+mm+'-'+year;
		if(!isValidDate(valid))
		{
			alert("The date you entered is not valid. Correct format is: day-month-year (dd-mm-yy)");
		}
	}
}
<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
	function itempercentage(id)
	{
		cost	=	document.getElementById('cp'+id).value;
		sale	=	document.getElementById('sp'+id).value;
		percent	=	(sale-cost)/sale*100;
		percent	=	Math.round(percent,0);
		document.getElementById('itempercentage'+id).innerHTML	=	percent+'%';
	}
<?php }//end edit?>
</script>
<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
<link href="../includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/autocomplete/ajax_framework.js"></script>
<?php }//end edit?>
<div id="loaditemscript">
</div>
<div id="cur">
</div>
<div id="instancediv">
<div id="stockitem">
<br />
<div id="error" class="notice" style="display:none"></div>
<div id="shippercentdiv"></div>
<div id="baseprice" style="display:none"></div>
<div id="baseexpense" style="display:none"></div>
<div id="shipvalue" style="display:none"></div>
<div id="minusshipment" style="display:none"></div>
<div id="plusshipment" style="display:none"></div>
<form id="adstockfrm" class="form">
<fieldset>
<legend>Add Stock</legend>
<?php 		if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
     $empid	=	$_SESSION['addressbookid'];
     $query="SELECT 
                        quantity,
                        FROM_UNIXTIME(updatetime,'%d-%m-%Y') as updatetime,
                        FROM_UNIXTIME(expiry,'%d-%m-%Y') as expiry,
                        itemdescription,
                        barcode 
                    FROM 
                        $dbname_detail.stock,
                        barcode 
                    WHERE 
                        fkbarcodeid=pkbarcodeid AND
                        fkemployeeid='$empid'
                    ORDER BY updatetime  DESC LIMIT 0,1 ";
            $arraylast	=	$AdminDAO->queryresult($query);
    if(count($arraylast)>0)
    {
    ?>
    
    <div><b>Last Added Stock:</b> <?php echo $arraylast[0]['barcode'].' '.$arraylast[0]['itemdescription'].' <b><br><br>Quantity: </b> '.$arraylast[0]['quantity'].'<b> Expiry: </b> '.$arraylast[0]['expiry'].' <b>Updated On: </b> '.$arraylast[0]['updatetime']; ?> </div>
    <?php
    }
}//end edit
?>
<div  style="float:right;">
<span class="buttons">
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<br />
<table>
<tr>
<td>Lock Screen</td>
<td><input type="checkbox" name="lock" id="lock" value="locked" /></td>
<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
<td colspan="4">&nbsp;</td>
</tr>
<tr>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
<td>Select Store</td>
<td colspan="3"><?php echo $stores;?></td>
</tr>
<?php }//end edit?>
<td align="right">Shipment</td>
<td width="17%"><?php echo $shipment; ?></td>
<td align="right">Currency</td>
<td width="17%"><div id="currency"></div></td>
<td align="right">Barcode</td>
<td width="17%"><input type="text" name="barcode" class="text" id="barcode1" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0); return false;}" autocomplete="off" onfocus="this.select()" />
<?php
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$itempricedata	=	$AdminDAO->getrows("$dbname_detail.stock","costprice,retailprice","fkbarcodeid='$id' ORDER BY pkstockid DESC LIMIT 0,1");
	$itemprice		=	$itempricedata[0]['retailprice'];
	$costprice		=	$itempricedata[0]['costprice'];
	$itemcounterprice	=	$AdminDAO->getrows("$dbname_detail.pricechange","price","fkbarcodeid='$id'");
	$counterprice		=	$itemcounterprice[0]['price'];
		?>
		<table width="100%">
			<tr>
				<th>Last Trade Price</th>
				<th>Last Sale Price</th>
				<th>Counter Price</th>
			</tr>
			<tr>
				<td><div id="itemcostprice"><?php echo $costprice;?></div></td>    	
				<td><div id="itemsaleprice"><?php echo $itemprice;?></div></td>
				<td><div id="counterprice"><?php echo $counterprice;?></div></td>
			</tr>
		</table>
<?php }//end edit?></td>
</tr>
<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
    <tr>
        <td align="right">Item</td>
        <td><!--<input type="text" name="productname" class="text" id="productname" autocomplete="off" onkeydown="javascript:if(event.keyCode==13) {return false;}" />-->
        
         <input name="productname" id="productname" type="text" onkeyup="suggestnow(event)" class="text" autocomplete="off" onfocus="this.select()" onkeydown="javascript:if(event.keyCode==13) {return false;}"/>
            <div id="results" style="width:200px"></div>
            <!--<input name="barcodeid" id="barcodeid" type="hidden"/>-->
        
         
        </td>
        <td align="right">Brand</td>
        <td><div id="itembrands"><?php echo $brands; ?></div></td>
        <td align="right">Suppliers</td>
        <td><div id="brandsupplierdiv"><div id="brandsupplier2"><?php echo $suppliers; ?></div></div><div id="brandsupplier"></div><span id="invoices"></span><input type="checkbox" id="locksupplier" />Lock</td>
    </tr>
<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
    <tr>
        <td align="right">Item</td>
        <td><input type="text" name="productname" class="text" id="productname" autocomplete="off" /></td>
        <td align="right">Brand</td>
        <td><div id="itembrands"><?php echo $brands; ?></div></td>
        <td align="right">Suppliers</td>
        <td><div id="brandsupplierdiv"><div id="brandsupplier2"><?php echo $suppliers; ?></div></div><div id="brandsupplier"></div><input type="checkbox" id="locksupplier" />Lock</td>
    </tr>
<?php }//end edi?>
<tr>
	<td colspan="4"><br /><div id="attributes"></div></td>
    <td>Shipment</td>
    <td><span id="percentagediv"></span>%</td>
</tr>
</table>
<table>
	<tr  align="center">
    	<td colspan="3">
    	<td width="46">
           	<input type="checkbox" name="checkall" onclick="checkin(1)" id="chk1" />
        </td>
        <td width="42">
           	<input type="checkbox" name="checkall" onclick="checkin(2)" id="chk2" />
        </td>
        <td width="66">&nbsp;</td>
        <td width="66">
        	<input type="checkbox" name="checkall" onclick="checkin(5)" id="chk5" />
        </td>
    	<td width="42">
    	  <input type="checkbox" name="checkall" onclick="checkin(3)" id="chk3" />
  	  </td>
    	<td width="42">
    	  <input type="checkbox" name="checkall" onclick="checkin(4)" id="chk4" />
  	  </td>
		<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
        	<td colspan="3">&nbsp;</td><?php }//end edit?>
    	<td width="42"><input type="checkbox" name="chk6" onclick="checkin(6)" id="chk6" /></td>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <th width="42">Units</th>
        <th width="64">Damaged</th>
        <th width="60">Damage Type</th>
        <th>Price @ Origin in <span id="priceatorigin"></span></th>
        <th>Price in <?php echo $defaultcurrency;?></th>
        <th>Shipment Group %</th>
        <th>Shipment Charges</th>
        <th>Cost Price</th>
        <th>Sale Price</th>
        <?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
			<th>%</th>
        <?php }//end edit?>
        <th>Box Sale Price</th>
        <th width="42">Batch</th>
        <th width="184">Expiry</th>
        <td width="50">&nbsp;</td>
    </tr>
    <?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
    <tr id="1">
            <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units1" size="3" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged1" size="3" onblur="checkunits('1');" /></td>
        <td><?php echo $damages;?></td>
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp1" size="3" onblur="calcprice('1');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr1" size="3" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch1" size="3" onblur="calculatethis();" /></td>
		<td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges1" size="3" readonly="readonly" /></td>                
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp1" size="3" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp1" size="3" onblur="itempercentage(1)" /></td>
        <td><div id="itempercentage1"></div></td>
        <td><input type="text" name="boxprice[]"  onkeypress="return isNumberKey(event)" class="text" id="boxprice1" size="3" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch1" size="3" /></td>
        <td>
            <input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd1" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm1" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy1" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td>&nbsp;</td>        
    </tr>
        <tr id="2">
        <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units2" size="3" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged2" size="3" onblur="checkunits('2');" /></td>
        <td><?php echo $damages;?></td>
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp2" size="3" onblur="calcprice('2');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr2" size="3" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch2" size="3" onblur="calculatethis();" /></td>
        <td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges2" size="3" readonly="readonly" /></td>
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp2" size="3" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp2" size="3" /></td>
        <td><div id="itempercentage2"></div></td>
        <td><input type="text" name="boxprice[]" onkeypress="return isNumberKey(event)" class="text" id="boxprice2" size="3" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch2" size="3" /></td>
        <td>
        	<input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd2" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm2" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy2" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td>&nbsp;</td>        
    </tr>
    <tr id="3">
	    <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units3" size="3" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged3" size="3" onblur="checkunits('3');" /></td>
        <td><?php echo $damages;?></td> 
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp3" size="3" onblur="calcprice('3');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr3" size="3" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch3" size="3" onblur="calculatethis();" /></td>
        <td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges3" size="3" readonly="readonly" /></td>
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp3" size="3" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp3" size="3" /></td>
        <td><div id="itempercentage3"></div></td>
        <td><input type="text" name="boxprice[]" onkeypress="return isNumberKey(event)" class="text" id="boxprice3" size="3" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch3" size="3" /></td>
        <td>
        	<input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd3" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm3" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy3" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td id="addmore3"><div id="btn3">
            <button type="button" id="3" onclick="hidethis(this.id)" title="Add More"><img src="../images/add.png" alt="Add More"/></button>
<!--        <input type="button" name="addmore" value="More" id="3" onclick="hidethis(this.id)" />-->
        </div></td>
    </tr>
    <?php for($i=3;$i<$itemcount;$i++)
	{
	?>
    <tr id="<?php echo $i+1;?>" style="display:none;">
	    <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units<?php echo $i+1;?>" size="3" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged<?php echo $i+1;?>" size="3" onblur="checkunits('<?php echo $i+1; ?>');" /></td>
        <td><?php echo $damages;?></td>
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp<?php echo $i+1?>" size="3"  onblur="calcprice('<?php echo $i+1;?>');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr<?php echo $i+1?>" size="3" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch<?php echo $i+1?>" size="3" onblur="calculatethis('<?php echo $i+1;?>');" /></td>
        <td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges<?php echo $i+1;?>" size="3" readonly="readonly" /></td> 
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp<?php echo $i+1?>" size="3" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp<?php echo $i+1?>" size="3" /></td>
        <td><div id="itempercentage<?php echo $i+1?>"></div></td>
        <td><input type="text" name="boxprice[]" onkeypress="return isNumberKey(event)" class="text" id="boxprice<?php echo $i+1?>" size="3" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch<?php echo $i+1;?>" size="3" /></td>
        <td>
	        <input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd<?php echo $i+1;?>" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm<?php echo $i+1;?>" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy<?php echo $i+1;?>" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td id="addmore<?php echo $i+1;?>"><div id="btn<?php echo $i+1;?>">
            <button type="button" id="<?php echo $i+1;?>" onclick="hidethis(this.id)" title="Add More"><img src="../images/add.png" alt="Add More"/></button>
        <!--<input type="button" name="addmore<?php echo $i+1;?>" value="More" id="<?php echo $i+1;?>" onclick="hidethis(this.id)" />-->
        </div></td>
    </tr>
   <?php
	}
   }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
   <tr id="1">
            <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units1" size="4" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged1" size="4" onblur="checkunits('1');" /></td>
        <td><?php echo $damages;?></td>
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp1" size="4" onblur="calcprice('1');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr1" size="4" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch1" size="4" onblur="calculatethis();" /></td>
		<td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges1" size="4" readonly="readonly" /></td>                
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp1" size="4" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp1" size="4" /></td>
        <td><input type="text" name="boxprice[]"  onkeypress="return isNumberKey(event)" class="text" id="boxprice1" size="4" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch1" size="4" /></td>
        <td>
            <input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd1" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm1" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy1" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td>&nbsp;</td>        
    </tr>
        <tr id="2">
        <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units2" size="4" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged2" size="4" onblur="checkunits('2');" /></td>
        <td><?php echo $damages;?></td>
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp2" size="4" onblur="calcprice('2');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr2" size="4" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch2" size="4" onblur="calculatethis();" /></td>
        <td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges2" size="4" readonly="readonly" /></td>
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp2" size="4" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp2" size="4" /></td>
        <td><input type="text" name="boxprice[]" onkeypress="return isNumberKey(event)" class="text" id="boxprice2" size="4" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch2" size="4" /></td>
        <td>
        	<input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd2" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm2" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy2" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td>&nbsp;</td>        
    </tr>
    <tr id="3">
	    <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units3" size="4" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged3" size="4" onblur="checkunits('3');" /></td>
        <td><?php echo $damages;?></td> 
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp3" size="4" onblur="calcprice('3');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr3" size="4" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch3" size="4" onblur="calculatethis();" /></td>
        <td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges3" size="4" readonly="readonly" /></td>
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp3" size="4" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp3" size="4" /></td>
        <td><input type="text" name="boxprice[]" onkeypress="return isNumberKey(event)" class="text" id="boxprice3" size="4" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch3" size="4" /></td>
        <td>
        	<input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd3" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm3" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy3" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td id="addmore3"><div id="btn3">
            <button type="button" id="3" onclick="hidethis(this.id)" title="Add More"><img src="../images/add.png" alt="Add More"/></button>
<!--        <input type="button" name="addmore" value="More" id="3" onclick="hidethis(this.id)" />-->
        </div></td>
    </tr>
    <?php for($i=3;$i<$itemcount;$i++)
	{
	?>
    <tr id="<?php echo $i+1;?>" style="display:none;">
	    <td><input type="text" name="units[]" onkeypress="return isNumberKey(event)" class="text" id="units<?php echo $i+1;?>" size="4" /></td>
        <td><input type="text" name="damaged[]" onkeypress="return isNumberKey(event)" class="text" id="damaged<?php echo $i+1;?>" size="4" onblur="checkunits('<?php echo $i+1; ?>');" /></td>
        <td><?php echo $damages;?></td>
        <td><input type="text" name="purchaseprice[]" onkeypress="return isNumberKey(event)" class="text" id="pp<?php echo $i+1?>" size="4"  onblur="calcprice('<?php echo $i+1;?>');" /></td>
        <td><input type="text" name="priceinrs[]" onkeypress="return isNumberKey(event)" class="text" id="pr<?php echo $i+1?>" size="4" readonly="readonly" /></td>
        <td><input type="text" name="shipmentpercentage[]" onkeypress="return isNumberKey(event)" class="text" id="sch<?php echo $i+1?>" size="4" onblur="calculatethis('<?php echo $i+1;?>');" /></td>
        <td><input type="text" name="shipmentcharges[]" onkeypress="return isNumberKey(event)" class="text" id="shipmentcharges<?php echo $i+1;?>" size="4" readonly="readonly" /></td> 
        <td><input type="text" name="costprice[]" onkeypress="return isNumberKey(event)" class="text" id="cp<?php echo $i+1?>" size="4" readonly="readonly" /></td>
        <td><input type="text" name="saleprice[]" onkeypress="return isNumberKey(event)" class="text" id="sp<?php echo $i+1?>" size="4" /></td>
        <td><input type="text" name="boxprice[]" onkeypress="return isNumberKey(event)" class="text" id="boxprice<?php echo $i+1?>" size="4" /></td>
        <td><input type="text" name="batch[]" class="text" id="batch<?php echo $i+1;?>" size="4" /></td>
        <td>
	        <input type="text" name="dd[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="dd<?php echo $i+1;?>" maxlength="2" />
            <input type="text" name="mm[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="mm<?php echo $i+1;?>" maxlength="2" />
        	<input type="text" name="yy[]" onkeypress="return isNumberKey(event)" size="1" class="text" id="yy<?php echo $i+1;?>" maxlength="2" onblur="alertdate(this.id);" />
        </td>    
        <td id="addmore<?php echo $i+1;?>"><div id="btn<?php echo $i+1;?>">
            <button type="button" id="<?php echo $i+1;?>" onclick="hidethis(this.id)" title="Add More"><img src="../images/add.png" alt="Add More"/></button>
        <!--<input type="button" name="addmore<?php echo $i+1;?>" value="More" id="<?php echo $i+1;?>" onclick="hidethis(this.id)" />-->
        </div></td>
    </tr>
   <?php
	}
   }//end edit
   ?>
</table>
<div class="buttons">
<button type="button" class="positive" onclick="submitfrm();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
 <a href="javascript:void(0);" onclick="hidediv('instancediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</div>
</fieldset>
</form>
<br />
</div>
</div>
<script language="javascript" type="text/javascript">
<?php
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition

	if($barcodex!='')
	{
		?>
		document.getElementById('barcode1').value='<?php echo $barcodex; ?>';
		getitemdetails('<?php echo $barcodex; ?>',0);
		<?php
	}
	?>
	document.getElementById('barcode1').focus();
<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
if(<?php echo $barcodex; ?>!='')
{
	document.getElementById('barcode1').value='<?php echo $barcodex; ?>';
	getitemdetails('<?php echo $barcodex; ?>',0);
}
document.getElementById('barcode1').focus();
<?php }//end edit?>
</script>