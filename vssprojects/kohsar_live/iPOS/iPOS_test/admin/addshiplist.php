<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$id					=	$_GET['id'];
$qstring			=	$_SESSION['qstring'];
if($id!='-1')
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, added if condition
		$shiplist = $AdminDAO->getrows("shiplist LEFT JOIN currency ON (fkcurrencyid=pkcurrencyid)","*"," pkshiplistid='$id'");
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
		$shiplist = $AdminDAO->getrows("shiplist LEFT JOIN currency ON (fkcurrencyid=pkcurrencyid)","barcode,itemdescription,quantity,weight,fkbrandid,fkagentid,shiplist.fkcountryid,fkstoreid,fkaddressbookid,pkcurrencyid,currencysymbol,lastpurchaseprice,lastsaleprice,deadline,description,defaultimage,clientinfo"," pkshiplistid='$id'");
	}//end edit
/*	echo "<pre>";
	print_r($shiplist);
	echo "</pre>";*/
	foreach($shiplist as $slist)
	{
		$barcode 			= 	$slist['barcode'];
		$itemdescription 	= 	$slist['itemdescription'];
		$quantity 			= 	$slist['quantity'];
		$weight				=	$slist['weight'];
		$selected_brand		=	$slist['fkbrandid'];
		$suppliers			=	$AdminDAO->getrows("shiplistsupplier","*","fkshiplistid='$id'");
		foreach($suppliers as $supplier)
		{
			$selected_suppliers[]	=	$supplier['fksupplierid'];
		}
		if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
			$selected_agent		=	$slist['fkagentid'];
			$lastsaleprice 		= 	$slist['lastsaleprice'];
			$description		=	$slist['description'];
			$clientinfo 		= 	$slist['clientinfo'];
			$defaultimage	 	= 	$slist['defaultimage'];
		}//end edit
		$selected_country	=	$slist['fkcountryid'];
		$selected_store		=	$slist['fkstoreid'];
		$selected_emp		=	$slist['fkaddressbookid'];
		$currencyid			=	$slist['pkcurrencyid'];
		$currencysymbol		=	$slist['currencysymbol'];
		$lastpurchaseprice 	= 	$slist['lastpurchaseprice'];
		$deadline			=	implode("-",array_reverse(explode("-",$slist['deadline'])));
	}
}
else
{
	$selected_store	=	$_SESSION['storeid'];
	$selected_emp	=	$_SESSION['addressbookid'];
}
// selecting suppliers
$suppliersarray		= 	$AdminDAO->getrows("supplier","*", "supplierdeleted<>1");
$suppliersel		=	"<select name=\"supplier[]\" id=\"supplier\" style=\"width:100px;\" multiple=multiple size=5>";
for($i=0;$i<sizeof($suppliersarray);$i++)
{
	$suppliername	=	$suppliersarray[$i]['companyname'];
	$supplierid		=	$suppliersarray[$i]['pksupplierid'];
	$select			=	"";
	if(@in_array($supplierid,$selected_suppliers))
	{
		$select = "selected=\"selected\"";
	}
	$suppliersel2	.=	"<option value=\"$supplierid\" $select>$suppliername</option>";
}
$suppliers			=	$suppliersel.$suppliersel2."</select>";
// end suppliers

if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	// countries
	if($id!='-1' && $selected_country!=0)
	{
		$countrycondition	=	" AND pkcountryid='$selected_country'";
	}
}//end edit

// countries
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$srccountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1");
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$srccountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1 $countrycondition");
}//end edit
$countrysel			=	"<select name=\"country\" id=\"country\" style=\"width:100px;\"><option value=\"\">Select Country</option>";
for($i=0;$i<sizeof($srccountries);$i++)
{
	$countryname	=	$srccountries[$i]['countryname'];
	$countryid		=	$srccountries[$i]['pkcountryid'];
	$select		=	"";
	if($countryid == $selected_country)
	{
		$select = "selected=\"selected\"";
	}
	$countrysel2	.=	"<option value=\"$countryid\" $select>$countryname</option>";
}
$countries			=	$countrysel.$countrysel2."</select>";
// end countries

if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	// stores
	$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1");
	$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:100px;\"><option value=\"\">Location</option>";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	// stores
	$stores			=	$AdminDAO->getrows("store","storename,pkstoreid","storedeleted<>1 AND storestatus=1");
	$storesel		=	"<select name=\"store\" id=\"store\" style=\"width:80px;\"><option value=\"\">Loc</option>";
}//end edit
for($i=0;$i<sizeof($stores);$i++)
{
	$storename	=	$stores[$i]['storename'];
	$storeid	=	$stores[$i]['pkstoreid'];
	$select		=	"";
	if($storeid == $selected_store)
	{
		$select = "selected=\"selected\"";
	}
	$storesel2	.=	"<option value=\"$storeid\" $select>$storename</option>";
}
$stores			=	$storesel.$storesel2."</select>";
// end stores

if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	// brands
	$brands			=	$AdminDAO->getrows("brand","brandname,pkbrandid","branddeleted<>1");
	$brandsel		=	"<select name=\"brand\" id=\"brand\" style=\"width:100px;\"><option value=\"\">Brand</option>";
	for($i=0;$i<sizeof($brands);$i++)
	{
		$brandname	=	$brands[$i]['brandname'];
		$brandid	=	$brands[$i]['pkbrandid'];
		$select		=	"";
		if($brandid == $selected_brand)
		{
			$select = "selected=\"selected\"";
		}
		$brandsel2	.=	"<option value=\"$brandid\" $select>$brandname</option>";
	}
	$brands			=	$brandsel.$brandsel2."</select>";
	// end brands
	// Selecting stores size to accomodate stores info
	$stores			=	$AdminDAO->getrows("store","count(*) as size","storestatus=1");
	$storesize		=	$stores[0]['size'];
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	// brands
	if($id!='-1' && $selected_brand!=0)
	{
		$brandcondition	=	" AND pkbrandid='$selected_brand'";
	}
	
	
	
	function getallbrands($brandid='0'){
		global $AdminDAO,$brandcondition	;
		//$brands			=	$AdminDAO->getrows("brand","brandname,pkbrandid","branddeleted<>1 $brandcondition and fkparentbrandid=.'"$brandid."'");
		$sql="SELECT brandname,pkbrandid,fkparentbrandid from brand where branddeleted<>1 $brandcondition and fkparentbrandid='".$brandid."' order by pkbrandid";
		$brands=$AdminDAO->queryresult($sql);
		
		if(count($brands)>0){		
			if($brandid=='0')
				$brands1		=	"<select name='brand' id='brand' style='width:80px;'>
				<option value=''>Brand</option>";
			for($i=0;$i<sizeof($brands);$i++){
				$brandname	=	$brands[$i]['brandname'];
				$brandidin	=	$brands[$i]['pkbrandid'];
				$fkparentbrandid	=	$brands[$i]['fkparentbrandid'];
				$selected=(($brandid == $selected_brand)?' selected=selected ':''); 
				if($fkparentbrandid==0)
					$brands1	.=	"<option value='".$brandidin."' $selected  style='font-weight: bold;'>$brandname</strong>";
				else
					$brands1	.=	"<option value='".$brandidin."' $selected  style='font-weight: italic;'>&nbsp;&nbsp;&nbsp;$brandname</option>";
				$brands1	.=	getallbrands($brandidin);
			}
		}else{		
			return '';			
		}
		return $brands1;
		//if($brandid=='0')
		//$brands			=	$brandsel.$brandsel2."</select>";
	}
	
	$brands=getallbrands($brandid=0)."</select>";
	
	// end brands
}//end edit
?>
<script language="javascript">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
	jQuery().ready(function() 
		{
			$("#expiry").mask("99-99-9999");
			$("#deadline").mask("99-99-9999");
			function findValueCallback(event, data, formatted) 
			{
				var barcode=document.getElementById('barcode').value=data[1];
				document.getElementById('quantity').focus();
				getitemdetails(document.getElementById('barcode').value,1);
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
				jQuery("#itemdescription").autocomplete("productautocomplete.php") ;
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
function getitemdetails(bc,itm)
{
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getshipitemdata.php?bc='+bc+'&item='+itm);
	document.getElementById('quantity').focus();
}
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
jQuery().ready(function() 
	{
		$("#expiry").mask("99-99-9999");
		$("#deadline").mask("99-99-9999");
		<?php
		if($id!='-1')
		{
			?>
			getitemdetails(document.getElementById('barcode').value,0,1);
			<?php
		}
		?>
		//alert(document.getElementById('clientinfo').focus())
		
		document.getElementById('barcode').focus();
		function findValueCallback(event, data, formatted) 
		{
			var barcode=document.getElementById('barcode').value=data[1];
			document.getElementById('quantity').focus();			
			if(document.getElementById('focusv').value=="clientinfo")
			{
				//alert("called");	
				return false;
			}else{
				getitemdetails(document.getElementById('barcode').value,1,0);			
			}
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
		jQuery("#itemdescription").autocomplete("productautocomplete.php") ;
		jQuery(":text, textarea").result(findValueCallback).next().click(function() 
		{
			$(this).prev().search();
		});
		jQuery("#clear").click(function() 
		{
			jQuery(":input").unautocomplete();
		});			
		jQuery("#clientinfo").autocomplete("clientautocomplete.php") ;	
});
function getitemdetails(bc,itm,ed)
{
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getshipitemdata.php?bc='+bc+'&item='+itm+'&edit='+ed);
	document.getElementById('quantity').focus();
}
<?php }//end edit?>
function addshiplist(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertshiplist.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#shiplistform').ajaxSubmit(options);
}
function response(text)
{
	<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
		if(text=='')
		{
			adminnotice('Wish List has been saved.',0,5000);
			jQuery('#maindiv').load('manageshiplist.php');
			
		}
	<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
		if(text==1)
		{
			adminnotice('Order has been saved.',0,5000);
			jQuery('#maindiv').load('manageshiplist.php?lock=1');
			
		}
	<?php }//end edit?>
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
    <div id="loaditemscript">
    </div>
    <div id="error" class="notice" style="display:none"></div>
    <div id="shipfrmdiv" style="display: block;">
    <br>
    <form id="shiplistform" style="width: 920px;" action="insertshiplist.php?id=-1" class="form">
    <fieldset>
    <legend>
        <?php
        if($id!="-1")
        { echo "Edit Item"." ".$packingname;}
        else
        { echo "Add Item";}	
        ?>
    </legend>
    <div style="float:right">
    <span class="buttons">
        <button type="button" class="positive" onclick="addshiplist(-1);">
            <img src="../images/tick.png" alt=""/> 
            <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
         <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
    </span>
    </div>
    <table width="100%">
    <tr>
    <td height="10" valign="top">
    <div class="topimage2" style="height:6px;"><!-- --></div>
    <table cellpadding="2" cellspacing="0" width="100%" >
    <tbody>
        <tr>
            <th>Barcode</th>
            <th>Item</th>
            <th>Weight</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Deadline</th>
            <th>Country of Origin</th>
            <th>Brand</th>
            <th>Supplier</th>
            <th>Store</th>
        </tr>
        <tr class="even">
            <td><input name="barcode" id="barcode" class="text" size="10" value="<?php echo $barcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
            <td><input name="itemdescription" id="itemdescription" class="text" size="20" value="<?php echo $itemdescription; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" ></td>
            <td><input name="weight" id="weight" class="text" size="5" value="<?php echo $weight; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" ></td>
            <td><?php for($j=0;$j<$storesize;$j++){ ?><span id="currency<?php echo $j;?>"><?php echo $currencysymbol;?></span><?php }?><span id="lastpurchaseprice"><?php echo $lastpurchaseprice;?></span></td>
            <td><input name="quantity" id="quantity" class="text" size="5" value="<?php echo $quantity; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" onkeypress="return isNumberKey(event)" ></td>
            <td><input name="deadline" id="deadline" class="text" size="8" value="<?php echo $deadline; ?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" type="text"></td>
            <td><?php echo $countries; ?></td>
            <td><?php echo $brands; ?></td>
            <td><?php echo $suppliers; ?></td>
            <td><?php echo $stores; ?></td>
        </tr>
        <tr>
          <td colspan="9" align="center">
            <div class="buttons">
              <button type="button" class="positive" onclick="addshiplist(-1);">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                </button>
              <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
                </a>
              </div>
            </td>				
          </tr>
    </tbody>    
    </table>
    </td>
    </tr>
    </table>
    <input type="hidden" name="id" value ="<?php echo $id;?>"/>
    <input type="hidden" name="addressbookid" id="addressbookid" value="<?php echo $selected_emp; ?>" />
    <?php
    for($j=0;$j<$storesize;$j++)
    {
    ?>
    <input type="hidden" name="lastpprice" id="lastpprice<?php echo $j;?>" value ="<?php echo $lastpurchaseprice;?>"/>
    <input type="hidden" name="currencyid" id="currencyid<?php echo $j;?>" value="<?php echo $currencyid; ?>" />
    <?php
    }
    ?>
    </fieldset>	
    </form>
    </div>
<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
<div id="loaditemscript"></div>
<div id="loading" style="display:none;"></div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;"> <br>
  <form id="shiplistform" style="width: 920px;" action="insertshiplist.php?id=-1" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      <?php
    if($id!="-1")
    { echo "Edit Order"." ".$packingname;}
    else
    { echo "Add Order";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="addshiplist(-1);"> <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
        <div id="lockdiv">
        <input type="checkbox" checked="checked" name="lock" id="lock" value="1" />
        Lock Screen
        <table width="129%">
        <tr>
          <td height="12" valign="top"><div class="topimage2" style="height:6px;">
              <!-- -->
            </div>
            <table  cellpadding="2" cellspacing="0" border="0">
              <tbody>
                <tr>
                  <th width="10%">Barcode</th>
                  <th width="20%">Item</th>
                  <th width="10%">Weight (gm)</th>
                  <th width="10%">Brand</th>
                  <th width="10%">Country</th>
                  <th width="10%">Quantity</th>
                  <th width="10%">Deadline</th>
                  <th width="10%">Supplier</th>
                  <th width="10%">Source</th>
                </tr>
                <tr class="even">
                  <td><input name="barcode" id="barcode" class="text" size="16" value="<?php echo $barcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
                  <td><input name="itemdescription" id="itemdescription" class="text" size="30" value="<?php echo $itemdescription; ?>" onkeydown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" onfocus="javascript:document.getElementById('focusv').value=this.name;" /></td>
                  <td><input name="weight" id="weight" class="text" size="8" value="<?php echo $weight; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" ></td>
                  <td><span id="brandsdiv"><?php echo $brands; ?></span></td>
                  <td><span id="countriesdiv"><?php echo $countries; ?></span></td>
                  <!--<td><span id="currency"><?php //echo $currencysymbol;?></span><input type="text" name="lastpurchaseprice" readonly="readonly" value="<?php //echo $lastpurchaseprice;?>" id="lastpurchaseprice" size="2" /></td>
                  <td><input type="text" name="lastsaleprice" value="<?php //echo $lastsaleprice;?>" id="lastsaleprice" size="3" /></td>-->
                  <td><input name="quantity" id="quantity" class="text" size="8" value="<?php echo $quantity; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" onkeypress="return isNumberKey(event);" ></td>
                  <td><input name="deadline" id="deadline" class="text" size="8" value="<?php echo $deadline; ?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" type="text" ></td>
                  <td><span id="suppliersdiv"><?php echo $suppliers; ?></span></td>
                  <td><?php echo $stores; ?></td>
                </tr>
                
                 <tr>
                  <th colspan="3">Picture</th>
                  <th colspan="4">Description</th>
                  <th colspan="2">Client Info</th>
                
                  
                </tr>
                 <tr>
                  <td colspan="2" align="left"><img id="defaultimage" src="<?php echo $defaultimage; ?>" height="100px" width="100px" /><input type="file" name="defaultimage" id="defaultimage" /></td>
                  <td colspan="5"><textarea name="description" id="description" style="width:400px; height:50px;"> <?php echo $description; ?></textarea></td>
                  <td colspan="2"><input name="clientinfo" id="clientinfo" class="text" size="16" value="<?php echo $clientinfo; ?>" type="text" autocomplete="off" onfocus="this.select();document.getElementById('focusv').value=this.name;" /><input type="hidden" value="" name="focusv" id="focusv" /></td>
                  
                 
                </tr>
                <tr>
                  <td colspan="9">
                  <table width="98%" border="0">
                  	<tr>
                    	<th colspan="4">Locations</th>
                    </tr>
                    <tr>
                    	<th>Location</th>
                        <th>Last Trade Price</th>
                        <th>Current Sale Price</th>
                        <th>Remaining Units</th>
                    </tr>
                    <?php
					$storesinfo	=	$AdminDAO->getrows("store","count(*) as size","storestatus=1");
					$storesize	=	$storesinfo[0]['size'];
					for($k=0;$k<$storesize;$k++)
					{
					?>
                    <tr>
                    	<td><div id="storename<?php echo $k;?>"></div></td>
                        <td><div id="tradeprice<?php echo $k;?>" align="right"></div></td>
                        <td><div id="saleprice<?php echo $k;?>" align="right"></div></td>
                        <td><div id="squantity<?php echo $k;?>" align="right"></div></td>
                    </tr>
                    <?php
					}
					?>
                  </table>
                  </td>
                </tr>
                <tr>
                  <td colspan="9" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="addshiplist(-1);"> <img src="../images/tick.png" alt=""/>
                      <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                      </button>
                      <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
                </tr>
              </tbody>
            </table></td>
        </tr>
      </table>
      <input type="hidden" name="id" value ="<?php echo $id;?>"/>
      <input type="hidden" name="lastpprice" id="lastpprice" value ="<?php echo $lastpurchaseprice;?>"/>
      <input type="hidden" name="addressbookid" id="addressbookid" value="<?php echo $selected_emp; ?>" />
      <input type="hidden" name="currencyid" id="currencyid" value="<?php echo $currencyid; ?>" />
    </fieldset>
  </form>
  <div id="similaritemsdiv"></div>
</div>
<script language="javascript">
	focusfield('barcode');
</script>
<?php }//end edit?>