<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
error_reporting(7);
$id					=	$_GET['id'];
//exit;
$qstring			=	$_SESSION['qstring'];
$param=$_REQUEST['param'];
if($id!='-1')
{
	if($param=="product"){
		$shiplist = $AdminDAO->getrows("barcode","pkbarcodeid,barcode,itemdescription, 	shortdescription, 	itemdetails ,	fkproductid ,	barcodedeleted 	,boxquantity ,boxbarcode ,barcodestatus"," fkproductid='$id'");
	}if($param=="brand"){
		$shiplist = $AdminDAO->getrows("barcode inner join barcodebrand on pkbarcodeid=fkbarcodeid ","pkbarcodeid,barcode,itemdescription, 	shortdescription, 	itemdetails ,	fkproductid ,	barcodedeleted 	,boxquantity ,boxbarcode ,barcodestatus"," fkbrandid='$id'");
		 $fkbrandid=$id;
	}if($param=="supplier"){
		$shiplist = $AdminDAO->getrows("barcode 
									   inner join barcodebrand on pkbarcodeid=fkbarcodeid 
									   inner join brandsupplier on brandsupplier.fkbrandid = barcodebrand.fkbrandid","pkbarcodeid,barcode,itemdescription, 	shortdescription, 	itemdetails ,	fkproductid ,	barcodedeleted 	,boxquantity ,boxbarcode ,barcodestatus"," barcodebrand.fkbrandid='$id'");
	}
}

$stores			=	$AdminDAO->getrows("store","storecode,pkstoreid","storedeleted<>1 AND storestatus=1");
$storesel		=	"<select name=\"store[]\" id=\"store\" style=\"width:40px;\"><option value=\"\">Loc</option>";
for($i=0;$i<sizeof($stores);$i++)
{
	$storename	=	$stores[$i]['storecode'];
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
// brands
/*
if($id!='-1' && $selected_brand!=0)
{
	$brandcondition	=	" AND pkbrandid='$selected_brand'";
}
*/




//$brands=getallbrands($brandid=0)."</select>";

// end brands
?>
<!-------------------------------------------------------------------------------->
<link href="../includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/autocomplete/ajax_framework.js"></script>
<!-------------------------------------------------------------------------------->
<div id="loaditemscript"></div>
<div id="loading" style="display:none;"></div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;"> <br>
  <form id="shiplistform" name="shiplistform" style="width: 920px;" action="insertshiplist.php?id=-1" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      <?php
    //if($id!="-1")
    //{ echo "Edit Order"." ".$packingname;}
    //else
    { echo "Add Order";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="addshiplist(-1);"> <img src="../images/tick.png" alt=""/>
        <?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
        <div id="lockdiv">
        Deadline                  
                  <input name="gdeadline" id="gdeadline" class="text" size="8" value="<?php echo $deadline; ?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" type="text" />
<table width="129%">
    <tr>
      <td height="12" valign="top"><div id="results" style=" float:right; text-align:left; width:200px"></div><div class="topimage2" style="height:6px;">
          <!-- -->
        </div>
        <table  cellpadding="2" cellspacing="0" border="0" width="100%">
          <tbody>
    
            <tr>
            <th width="10%">&nbsp;</th>
              <th width="24%">Item</th>
              <th width="13%">Barcode</th>
              <th width="8%">Quantity</th>
              
              <th width="9%">Weight (gm)</th>
              <th width="19%">Description</th>
              <th width="9%">Client Info</th>
              <th width="4%">Source</th>
              <th width="4%">Dead Line (DD-MM-YYYY)
              <input type="hidden" name="fkbrandid" id="fkbrandid" value="<?php echo $fkbrandid;?>" />
              </th> 
              
            </tr>                   
        <?php  $numb=0;
		
		if(count($shiplist)>0){	
			foreach($shiplist as $slist)
			{
				//pkbarcodeid,barcode,itemdescription, 	shortdescription, 	itemdetails ,	fkproductid ,	barcodedeleted 	,boxquantity ,boxbarcode ,barcodestatus
				$pkbarcodeid 		= 	$slist['pkbarcodeid'];
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
				$selected_agent		=	$slist['fkagentid'];
				$selected_country	=	$slist['fkcountryid'];
				$selected_store		=	$slist['fkstoreid'];
				$selected_emp		=	$slist['fkaddressbookid'];
				$currencyid			=	$slist['pkcurrencyid'];
				$currencysymbol		=	$slist['currencysymbol'];
				$lastpurchaseprice 	= 	$slist['lastpurchaseprice'];
				$lastsaleprice 		= 	$slist['lastsaleprice'];
				$description		=	$slist['description'];
				$clientinfo 		= 	$slist['clientinfo'];
				$defaultimage	 	= 	$slist['defaultimage'];		
				
				$deadline			=	implode("-",array_reverse(explode("-",$slist['deadline'])));
				
				// selecting suppliers
		/*		$suppliersarray		= 	$AdminDAO->getrows("supplier","*", "supplierdeleted<>1");
				$suppliersel		=	"<select name=\"supplier[]\" id=\"supplier\" style=\"width:65px;\" multiple=multiple size=5>";
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
		*/	
				?>        
						
			<tr class="even">        
				<td valign="top">
  					<div style="float:left; width:30px;" ><?php echo ($numb+1);?><input type="checkbox" value="<?php echo $pkbarcodeid; ?>" name="productid[<?php echo $numb;?>]" id="productid"/></div>
                </td>
				<td valign="top"><input name="itemdescription[]" id="itemdescription" class="text" value="<?php echo $itemdescription;?>"  style='width:200px;' type="text" readonly="readonly"/></td>
				<td valign="top"><input name="barcode[]" id="barcode" class="text"  value="<?php echo $barcode; ?>" type="text" readonly="readonly"/></td>
				<td valign="top"><input name="quantity[]" id="quantity" class="text" size="2" value="<?php echo $quantity; ?>" type="text" onkeypress="return isNumberKey(event);" /></td>
				<td valign="top"><input name="weight[]" id="weight" class="text" size="2" value="<?php echo $weight; ?>" type="text"  onkeypress="return isNumberKey(event);" /></td>                  
				<td valign="top"><textarea name="description[]" id="description" style="width:150px; height:30px;"> <?php echo $description; ?></textarea></td>
				<td valign="top">
                
               	<!-- <input name="clientinfo[]" id="clientinfo" class="text" size="16" value="<?php echo $clientinfo; ?>" type="text" />-->
                <input name="clientinfo[]" id="clientinfo<?php echo ($numb);?>"  value="<?php echo $clientinfo; ?>" type="text" onkeyup="suggestnow(event,'clientinfo<?php echo ($numb);?>','clientid<?php echo ($numb);?>','results','clients')" class="text" autocomplete="off"  onkeydown="javascript:if(event.keyCode==13) {return false;}"/>                
                <input type="hidden" name="clientid[]"  id="clientid<?php echo ($numb);?>" />
                
                </td>
                

                

                
				<td valign="top"><?php echo $stores; ?></td>
				<td valign="top"><input name="deadline[<?php echo $numb;?>]" id="deadline_<?php echo $numb;?>" class="text" size="8" value="<?php echo $deadline; ?>" type="text" /></td>
			</tr>
						
		<?php $numb++;} 
		}else{
			?><tr>
	            <td colspan="8" align="center"><strong>No Item Found</strong></td>
            </tr> 		
		<?php 
			}	
		?>
                    
            <tr>
              <td colspan="15">
              </td>
            </tr>
            <tr>
              <td colspan="15" align="center"><div class="buttons">
                  <button type="button" class="positive" onclick="addshiplist(-1);"> <img src="../images/tick.png" alt=""/>
                  <?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>
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
<?php 
function getallbrands($brandid='0'){
	global $AdminDAO,$brandcondition	;
	//$brands			=	$AdminDAO->getrows("brand","brandname,pkbrandid","branddeleted<>1 $brandcondition and fkparentbrandid=.'"$brandid."'");
	$sql="SELECT brandname,pkbrandid,fkparentbrandid from brand where branddeleted<>1 $brandcondition and fkparentbrandid='".$brandid."' order by pkbrandid";
	$brands=$AdminDAO->queryresult($sql);
	
	if(count($brands)>0){		
		if($brandid=='0')
			$brands1		=	"<select name='brand' id='brand' style='width:65px;'>
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
}?>


<script type="text/javascript">
	function validate() {			
	var frm=document.getElementById("shiplistform");
		var total=""
		var contrl=frm.productid;
		alert("total="+frm.productid.length);
		for(var i=0; i < contrl.length; i++){
			if(contrl[i].checked)
				total +="=> "+contrl[i].value + "  id=>"+contrl[i].name+"\n";
			//else
				//alert("unchecked==="+frm.productid[i].value + "\n");
		}		
		alert(total);	
		return false;
	} 
	
</script>


<script language="javascript">
jQuery().ready(function() 
	{		
		var frm=document.getElementById("shiplistform");
		var total=""
		var contrl=frm.deadline;
		for(var i=0; i < frm.productid.length; i++){
			if(i==0)
				continue;
			else
				$("#deadline_"+i).mask("99-99-9999");						
		}
		$("#deadline_0").mask("99-99-9999");	
		$("#gdeadline").mask("99-99-9999");	
		//$("#deadline_0").mask("99-99-9999");
/*
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
		jQuery("#clientinfo").autocomplete("clientautocomplete.php") ;	*/
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





function addshiplist(id){
	//validate();
	//alert("called");
	false;
	options	=	{	
					url : 'insertshiplistproduct.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#shiplistform').ajaxSubmit(options);
}
function response(text){
	if(text==1)	{
		adminnotice('Order has been saved.',0,5000);
		//jQuery('#maindiv').load('manageproducts.php');	
		jQuery('#shiplistform').slideUp();
	}else{
		adminnotice(text,0,5000);
	}
}
</script>

<script language="javascript">
	focusfield('gdeadline');
</script>








<?php /*else
{
	$selected_store	=	$_SESSION['storeid'];
	$selected_emp	=	$_SESSION['addressbookid'];
}

// selecting agents
$agentarray		= 	$AdminDAO->getrows("supplieroragent","*", "supplieroragentdeleted<>1 AND isagent='a'");
$agentsel		=	"<select name=\"agents\" id=\"agents\" style=\"width:65px;\" ><option value=\"\">Select Agent</option>";
for($i=0;$i<sizeof($agentarray);$i++)
{
	$agentname	=	$agentarray[$i]['suppliername'];
	$agentid	=	$agentarray[$i]['pksupplierid'];
	$select		=	"";
	if($agentid == $selected_agent)
	{
		$select = "selected=\"selected\"";
	}
	$agentsel2	.=	"<option value=\"$agentid\" $select>$agentname</option>";
}
$agents			=	$agentsel.$agentsel2."</select>";
// end agents
// countries
if($id!='-1' && $selected_country!=0)
{
	$countrycondition	=	" AND pkcountryid='$selected_country'";
}
$srccountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1 $countrycondition");
$countrysel			=	"<select name=\"country\" id=\"country\" style=\"width:65px;\"><option value=\"\">Select Country</option>";
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
// stores
*/?>