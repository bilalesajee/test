<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
error_reporting(7);
$id		=	$_GET['id'];
$stype	=	$_GET['type'];
$start	=	$_GET['start'];
//echo $stype." is the type ".$id;
	switch($stype)
	{
		case 1:
		//$query	=	"SELECT SQL_CALC_FOUND_ROWS barcode,itemdescription FROM barcode WHERE fkproductid='$id' LIMIT $start,50";
		$query	=	"SELECT SQL_CALC_FOUND_ROWS pkorderid,fkstoreid,b.barcode,b.itemdescription,quantity,lastsaleprice,weight,DATE_FORMAT(deadline,'%d-%m-%Y') deadline,fkbrandid,o.fkcountryid,fkcustomerid,pricelimit,agreedprice FROM `order` o,barcode b WHERE fkproductid='$id' AND o.barcode=b.barcode LIMIT $start,50";
		break;
		case 2:
		//$query	=	"SELECT SQL_CALC_FOUND_ROWS  barcode,itemdescription FROM barcode,brand,barcodebrand WHERE pkbrandid='$id' AND fkbarcodeid=pkbarcodeid and fkbrandid=pkbrandid LIMIT $start,50";
		$query	=	"SELECT SQL_CALC_FOUND_ROWS pkorderid,fkstoreid,barcode,itemdescription,quantity,lastsaleprice,weight,DATE_FORMAT(deadline,'%d-%m-%Y') deadline,fkbrandid,o.fkcountryid,fkcustomerid,pricelimit,agreedprice FROM `order` o,brand WHERE fkbrandid=pkbrandid AND pkbrandid='$id' LIMIT $start,50";
		break;
		case 3:
		$query	=	"SELECT SQL_CALC_FOUND_ROWS pkorderid,fkstoreid,barcode,itemdescription,quantity,lastsaleprice,weight,DATE_FORMAT(deadline,'%d-%m-%Y') deadline,fkbrandid,fkcountryid,fkcustomerid,pricelimit,agreedprice FROM `order`,ordersupplier WHERE fkorderid=pkorderid AND fksupplierid='$id' LIMIT $start,50";
		break;
		case 4:
		$query	=	"SELECT SQL_CALC_FOUND_ROWS pkorderid,fkstoreid,barcode,itemdescription,quantity,lastsaleprice,weight,DATE_FORMAT(deadline,'%d-%m-%Y') deadline,fkbrandid,fkcountryid,fkcustomerid,pricelimit,agreedprice FROM `order` WHERE fkcountryid='$id' LIMIT $start,50";
		break;
		case 5:
		$query	=	"SELECT SQL_CALC_FOUND_ROWS pkorderid,fkstoreid,barcode,itemdescription,quantity,lastsaleprice,weight,DATE_FORMAT(deadline,'%d-%m-%Y') deadline,fkbrandid,fkcountryid,fkcustomerid,pricelimit,agreedprice FROM `order` WHERE fkshipmentid='$id' LIMIT $start,50";
		break;
		case 6:
		$query	=	"SELECT SQL_CALC_FOUND_ROWS pkorderid,fkstoreid,barcode,itemdescription,quantity,lastsaleprice,weight,DATE_FORMAT(deadline,'%d-%m-%Y') deadline,fkbrandid,fkcountryid,fkcustomerid,pricelimit,agreedprice FROM `order` WHERE barcode='$id' LIMIT $start,50";
		break;
		case 7:
		$query	=	"SELECT SQL_CALC_FOUND_ROWS pkorderid,fkstoreid,barcode,itemdescription,quantity,lastsaleprice,weight,DATE_FORMAT(deadline,'%d-%m-%Y') deadline,fkbrandid,fkcountryid,fkcustomerid,pricelimit,agreedprice FROM `order` WHERE barcode='$id' LIMIT $start,50";
		break;				
	}
	//echo $query; exit;
	$orders 	= $AdminDAO->queryresult($query);
	$totalrows	=	$AdminDAO->queryresult('SELECT FOUND_ROWS() as totalrows');
	$totalrecs	=	$totalrows[0][totalrows];
// stores
$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1 AND storestatus=1");
$storesel		=	"<select name=\"gstore\" id=\"gstore\" style=\"width:186px;\"><option value=\"\">Location</option>";
for($i=0;$i<sizeof($stores);$i++)
{
	$storename	=	$stores[$i]['storename'];
	$storeid	=	$stores[$i]['pkstoreid'];
	$storesel2	.=	"<option value=\"$storeid\" >$storename</option>";
}
$stores			=	$storesel.$storesel2."</select>";
// end stores	
?>

<script type="text/javascript">
$().ready(function() 
	{
		function findValueCallback(event, data, formatted) 
		{
			if(data[1]=='typebrand')
			{
				document.getElementById('brandid_'+data[3]).value	=	data[2];
			}
			else if(data[1]=='typesupplier')
			{
				document.getElementById('supplierid_'+data[3]).value=	data[2];
			}
			else if(data[1]=='typecountry')
			{
				document.getElementById('countryid_'+data[3]).value	=	data[2];
			}
			else if(data[1]=='typeclient')
			{
				document.getElementById('clientid_'+data[3]).value	=	data[2];
			}
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
		total	=	document.getElementById('totalrecs').value;
		for(i=1;i<=total;i++)
		{
			$('#deadline_'+i).mask('99-99-9999');
			$("#brand_"+i).autocomplete("orderbrandautocomplete.php", {extraParams: {cid: function() { return $("#ccid").val(); } }});
			$("#supplier_"+i).autocomplete("ordersupplierautocomplete.php", {extraParams: {cid: function() { return $("#ccid").val(); } }});
			$("#country_"+i).autocomplete("ordercountryautocomplete.php", {extraParams: {cid: function() { return $("#ccid").val(); } }});
			$("#client_"+i).autocomplete("orderclientautocomplete.php", {extraParams: {cid: function() { return $("#ccid").val(); } }});
			
		}
		$('#gdeadline').mask('99-99-9999');
		document.getElementById('start').value	=	'<?php echo $start+50;?>';
	});
function addorder(id)
{
	options	=	{	
					url : 'orderinsertitems.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#reorderform').ajaxSubmit(options);
}
function response(text){
	if(text==1)	
	{
		adminnotice('Order has been saved.',0,3000);
		jQuery('#maindiv').load('manageshiplist.php');
	}
	else
	{
		adminnotice(text,0,3000);
	}
}
function addhistory(id)
{
	document.getElementById('barcodes').value	=	document.getElementById('barcodes').value+','+document.getElementById('barcode'+id).value;
}
function delhistory(id)
{
	var bc			=	","+document.getElementById('barcode'+id).value;
	var barcodes 	= document.getElementById('barcodes').value;
	document.getElementById('barcodes').value	=	barcodes.replace(bc,"");
}
function viewnext(type,typeid,start)
{
	$('#reorderitems').load('orderadditems.php?type='+type+'&id='+typeid+'&start='+start);
	$('#reorderitems').show();
}
function viewprev(type,typeid,start)
{
	$('#reorderitems').load('orderadditems.php?type='+type+'&id='+typeid+'&start='+start);
	$('#reorderitems').show();
}
function alertdate(val,id)
{
	if(val!="")
	{
		dtval	=	val.split('-');
		dateval	=	dtval[2]+'-'+dtval[1]+'-'+dtval[0];
		if(dateval<"<?php echo date('Y-m-d')?>")
		{
			alert("The deadline can not be a past date, please correct the date.");
		}
		if(!isValidDate(val))
		{
			alert("The date you entered is not valid. Correct format is: (dd-mm-yyyy)");
		}
	}
}
</script>
<div id="loaditemscript"></div>
<div id="loading" style="display:none;"></div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;"> <br>
      <div style="float:right"> <span class="buttons">
      <?php
	  if($start>=50)
	  {
	  ?>
      <a href="javascript:void(0);" onclick="viewprev('<?php echo $stype;?>','<?php echo $id;?>','<?php echo $start-50;?>')" class="positive"><img src="../images/tick.png" alt=""/>
       View Prev 50 <<
        </a>
       <?php
	  }
	   ?>
        <button type="button" class="positive" onclick="addorder(-1);"><img src="../images/tick.png" alt=""/>
			Reorder
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> 
        <?php
	  if($start+50<$totalrecs)
	  {
	  ?>
        <a href="javascript:void(0);" onclick="viewnext('<?php echo $stype;?>','<?php echo $id;?>','<?php echo $start+50;?>')" class="positive"><img src="../images/tick.png" alt=""/>
        View Next 50 >>
        </a>
      <?php
	  }
	  ?>
        </span> </div>
        Global Deadline                  
                  <input name="gdeadline" id="gdeadline" class="text" size="8" value="<?php echo $deadline; ?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" type="text" onblur="alertdate(this.value,this.id);" />
    	Global Source <?php echo $stores; ?>                  
<table width="100%">
    <tr>
      <td height="6" valign="top"><div class="topimage2" style="height:6px;"><!-- -->
        </div>
        <table  cellpadding="2" cellspacing="0" border="0" width="100%">
          <tbody>
            <tr>            
				<th>&nbsp;</th>
                <th align="left"><input type="checkbox" onclick="toggleChecked(this.checked)" id="chkAllreorder" name="chkAllreorder"></th>
                <th>Barcode</th>
                <th>Item<span style="color:#F00;">*</span></th>
                <th>Qty<span style="color:#F00;">*</span></th>
                <th>Price</th>              
                <th>Weight</th>
                <th>Deadline</th> 
                <th>Brand</th>
                <th>Supplier</th>
                <th>Country</th>
                <th>Client</th>
                <th>Price Limit</th>
                <th>Agreed Price</th>
                <th>Source</th>
            </tr>                   
        <?php  
		$i=0;
		if(count($orders)>0)
		{	
			foreach($orders as $order)
			{
				$orderid			=	$order['pkorderid'];
				$barcode 			= 	$order['barcode'];
				$itemdescription 	= 	$order['itemdescription'];
				$quantity			=	$order['quantity'];
				$lastsaleprice		=	$order['lastsaleprice'];
				$weight				=	$order['weight'];
				$deadline			=	$order['deadline'];
				$fkbrandid			=	$order['fkbrandid'];
				$fkcountryid		=	$order['fkcountryid'];
				$fkcustomerid		=	$order['fkcustomerid'];
				$selected_store		=	$order['fkstoreid'];
				$pricelimit			=	$order['pricelimit'];
				$agreedprice		=	$order['agreedprice'];
				//selecting customer
				if($fkcustomerid)
				{
					$clientdata		=	$AdminDAO->getrows("customer","companyname","pkcustomerid='$fkcustomerid'");
					$customername	=	$clientdata[0]['companyname'];
				}
				//selecting brand
				if($fkbrandid)
				{
					$branddata	=	$AdminDAO->getrows("brand","brandname","pkbrandid='$fkbrandid'");
					$brandname	=	$branddata[0]['brandname'];
				}
				else if($stype==2)
				{
					$branddata	=	$AdminDAO->getrows("brand","pkbrandid,brandname","pkbrandid='$id'");
					$fkbrandid	=	$branddata[0]['pkbrandid'];
					$brandname	=	$branddata[0]['brandname'];
				}
				// this was making the system too slow hence commented
				// $productid variable also skipped from the original query
				/*else if($stype==1)
				{
					$branddata	=	$AdminDAO->getrows("barcode,barcodebrand,brand","pkbrandid,brandname","fkproductid='$productid' AND fkbrandid=pkbrandid and fkbarcodeid=pkbarcodeid");
					$fkbrandid	=	$branddata[0]['pkbrandid'];
					$brandname	=	$branddata[0]['brandname'];
				}*/
				//selecting supplier
				if($orderid)
				{
					if($stype!=3)
					{
						$supplierdata	=	$AdminDAO->getrows("ordersupplier,supplier","fksupplierid,companyname","fksupplierid=pksupplierid AND fkorderid='$orderid'");
						$fksupplierid	=	$supplierdata[0]['fksupplierid'];
						$suppliername	=	$supplierdata[0]['companyname'];
					}
					else
					{
						$supplierdata	=	$AdminDAO->getrows("supplier","pksupplierid,companyname","pksupplierid='$id'");
						$fksupplierid	=	$supplierdata[0]['pksupplierid'];
						$suppliername	=	$supplierdata[0]['companyname'];
					}
				}
				//selecting country
				if($fkcountryid)
				{
					$countrydata	=	$AdminDAO->getrows("countries","code3","pkcountryid='$fkcountryid'");
					$countryname	=	$countrydata[0]['code3'];
				}
				//selecting stores
				$storesel		=	"";
				$storesel2		=	"";
				$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1 AND storestatus=1");
				$storesel		=	"<select name=\"store[]\" id=\"store\" style=\"width:80px;\"><option value=\"\">Location</option>";
				for($s=0;$s<sizeof($stores);$s++)
				{
					$storename	=	$stores[$s]['storename'];
					$storeid	=	$stores[$s]['pkstoreid'];
					$select		=	"";
					if($storeid == $selected_store)
					{
						$select = "selected=\"selected\"";
					}
					$storesel2	.=	"<option value=\"$storeid\" $select>$storename</option>";
				}
				$stores			=	$storesel.$storesel2."</select>";
				// end stores
				?>        
						
			<tr class="even">
            	<td><?php echo $i+1;?></td>
            	<td><input type="checkbox" name="check<?php echo $i;?>" id="<?php echo $i;?>" value="1" onchange="if(this.checked==true){addhistory(this.id);} else {delhistory(this.id);}" class="checkbox" />
</td>       
				<td><input type="text" name="barcode[]" id="barcode<?php echo $i;?>" class="text" size="9" value="<?php echo $barcode;?>" readonly="readonly" title="<?php echo $barcode;?>" /></td>
                <td><input type="text" name="itemdescription[]" id="itemdescription" size="15" class="text" value="<?php echo $itemdescription;?>" readonly="readonly" title="<?php echo $itemdescription;?>" /></td>
                <td><input type="text" name="quantity[]" id="quantity" size="1" class="text" value="<?php echo $quantity;?>" onkeypress="return isNumberKey(event);" /></td>
                <td><input type="text" name="price[]" id="price" size="1" class="text" value="<?php echo $lastsaleprice;?>" onkeypress="return isNumberKey(event);" /></td>
                <td><input type="text" name="weight[]" id="weight" size="1" class="text" value="<?php echo $weight;?>" onkeypress="return isNumberKey(event);" /></td>
                <td><input type="text" name="deadline[]" id="deadline_<?php echo $i+1;?>" size="8" class="text" value="<?php echo $deadline;?>" onblur="alertdate(this.value,this.id);" /></td>
                <td><input type="text" name="brand[]" id="brand_<?php echo $i+1;?>" size="5" class="text" onfocus="document.getElementById('ccid').value='<?php echo $i+1;?>'" value="<?php echo $brandname;?>" title="<?php echo html_entity_decode($brandname);?>" /><input type="hidden" name="brandid[]" id="brandid_<?php echo $i+1;?>" value="<?php echo $fkbrandid;?>" /></td>
                <td><input type="text" name="supplier[]" id="supplier_<?php echo $i+1;?>" size="5" class="text" onfocus="document.getElementById('ccid').value='<?php echo $i+1;?>'" value="<?php echo $suppliername;?>" title="<?php echo $suppliername;?>" /><input type="hidden" name="supplierid[]" id="supplierid_<?php echo $i+1;?>" value="<?php echo $fksupplierid;?>" /></td>
                <td><input type="text" name="country[]" id="country_<?php echo $i+1;?>" size="5" class="text" onfocus="document.getElementById('ccid').value='<?php echo $i+1;?>'" value="<?php echo $countryname;?>" title="<?php $countryname;?>" /><input type="hidden" name="countryid[]" id="countryid_<?php echo $i+1;?>" value="<?php echo $fkcountryid;?>" /></td>
                <td><input type="text" name="client[]" id="client_<?php echo $i+1;?>" size="5" class="text" onfocus="document.getElementById('ccid').value='<?php echo $i+1;?>'" value="<?php echo $customername;?>" title="<?php echo $customername;?>" /><input type="hidden" name="clientid[]" id="clientid_<?php echo $i+1;?>" value="<?php echo $fkcustomerid;?>" /></td>
                <td><input type="text" name="pricelimit[]" id="pricelimit" size="1" class="text" value="<?php echo $pricelimit;?>" onkeypress="return isNumberKey(event);" /></td>
                <td><input type="text" name="agreedprice[]" id="agreedprice" size="1" class="text" value="<?php echo $agreedprice;?>" onkeypress="return isNumberKey(event);" /></td>
                <td><?php echo $stores;?></td>
			</tr>
		<?php $i++;
			} 
		}
		else
		{
		?><tr>
			<td colspan="15" align="center"><strong>No Item Found</strong></td>
		</tr> 		
		<?php 
		}	
		?>
            <tr>
              <td colspan="15" align="center">
				<div class="buttons">
                  <button type="button" class="positive" onclick="addorder(-1);"> <img src="../images/tick.png" alt=""/>
                  	Reorder
                  </button>
                  <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"><img src="../images/cross.png" alt=""/> Cancel </a> 
				</div>
            </td>
            </tr>
          </tbody>
        </table></td>
    </tr>
</table>
<input type="hidden" name="ccid" id="ccid" value="" />
<input type="hidden" name="barcodes" id="barcodes" value="" />
<input type="hidden" name="totalrecs" id="totalrecs" value="<?php echo sizeof($orders);?>" />
</div>