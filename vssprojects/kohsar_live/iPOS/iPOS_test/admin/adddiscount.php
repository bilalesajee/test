<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$discountid = $_GET['id'];
if($discountid!="-1")
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$discounts	=	$AdminDAO->getrows("discount","*"," pkdiscountid = '$discountid'");
		/*echo "<pre>";
		print_r($discounts);*/
		$discounttype	=	$discounts[0]['fkdiscounttypeid'];
		$discountname	=	$discounts[0]['discountname'];
		$discountstatus	=	$discounts[0]['discountstatus'];	
		$startdate		=	date("Y/m/d",$discounts[0]['startdate']);
		$enddate		=	date("Y/m/d",$discounts[0]['enddate']);
		if($discounttype == 1)
		{
			echo "<script>discountdetails(1)</script>";// wanna hide something ;)
			$discountdetails	=	$AdminDAO->getrows("discountdetailsqq","*"," fkdiscountid = '$discountid'");
			$basequantityqq		=	$discountdetails[0]['basequantity'];
			$discountquantityqq	=	$discountdetails[0]['discountquantity'];
			$discountstock		=	$AdminDAO->getrows("discountstock","*"," fkdiscountid = '$discountid'");
			foreach($discountstock as $sel_stock)
			{
				$selected_stock[]	=	$sel_stock['fkstockid'];
			}
			for($i=0;$i<sizeof($discountstock);$i++)
			{
				$stockid			=	$discountstock[$i]['fkstockid'];
				$selbarcodeid		=	$AdminDAO->getrows("stock","*"," pkstockid = '$stockid'");
				$barcodeid			=	$selbarcodeid[0]['fkbarcodeid'];
				$productstock3		=	$AdminDAO->getrows("stock,shipment","date_format(expiry,'%D %b %Y') as expiry,date_format(shipmentdate,'%D %b %Y') as shipmentdate, pkstockid"," fkshipmentid = pkshipmentid AND fkbarcodeid = '$barcodeid'");			
				$sql=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') PRODUCTNAME, b.barcode as bc, pkproductid
				FROM productattribute pa
				RIGHT JOIN (
				product p, attribute a
				) ON ( pa.fkproductid = p.pkproductid
				AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, barcode b
				WHERE pkproductid = pa.fkproductid
				AND pkattributeid = pa.fkattributeid
				AND pkproductattributeid = fkproductattributeid
				AND pkattributeid = ao.fkattributeid
				AND pkattributeoptionid = pi.fkattributeoptionid
				AND b.fkproductid = pkproductid
				AND pi.fkbarcodeid = b.pkbarcodeid
				AND b.pkbarcodeid 	=	'$barcodeid' 
				GROUP BY bc";
				$result			=	$AdminDAO->queryresult($sql);
				$productname	=	$result[0]['PRODUCTNAME'];
			}
		}
		else if($discounttype == 2)
		{
			echo "<script>discountdetails(2)</script>";
			$discountdetails	=	$AdminDAO->getrows("discountdetailsaq","*"," fkdiscountid = '$discountid'");
			$basequantityaq		=	$discountdetails[0]['basequantity'];
			$amountaq			=	$discountdetails[0]['amount'];
			$typeaq				=	$discountdetails[0]['type'];
			$discountstock		=	$AdminDAO->getrows("discountstock","*"," fkdiscountid = '$discountid'");
			foreach($discountstock as $sel_stock)
			{
				$selected_stock[]	=	$sel_stock['fkstockid'];
			}
			for($i=0;$i<sizeof($discountstock);$i++)
			{
				$stockid			=	$discountstock[$i]['fkstockid'];
				$selbarcodeid		=	$AdminDAO->getrows("stock","*"," pkstockid = '$stockid'");
				$barcodeid			=	$selbarcodeid[0]['fkbarcodeid'];
				$productstock4		=	$AdminDAO->getrows("stock,shipment","date_format(expiry,'%D %b %Y') as expiry,date_format(shipmentdate,'%D %b %Y') as shipmentdate, pkstockid"," fkshipmentid = pkshipmentid AND fkbarcodeid = '$barcodeid'");			
				$sql=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') PRODUCTNAME, b.barcode as bc, pkproductid
				FROM productattribute pa
				RIGHT JOIN (
				product p, attribute a
				) ON ( pa.fkproductid = p.pkproductid
				AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, barcode b
				WHERE pkproductid = pa.fkproductid
				AND pkattributeid = pa.fkattributeid
				AND pkproductattributeid = fkproductattributeid
				AND pkattributeid = ao.fkattributeid
				AND pkattributeoptionid = pi.fkattributeoptionid
				AND b.fkproductid = pkproductid
				AND pi.fkbarcodeid = b.pkbarcodeid
				AND b.pkbarcodeid 	=	'$barcodeid' 
				GROUP BY bc";
				$result			=	$AdminDAO->queryresult($sql);
				$productname	=	$result[0]['PRODUCTNAME'];
			}
		}
		else if($discounttype == 3)
		{
			echo "<script>discountdetails(3)</script>";
			$discountdetails	=	$AdminDAO->getrows("discountdetailsaa","*"," fkdiscountid = '$discountid'");
			$baseamount			=	$discountdetails[0]['amount'];
			$discountamount		=	$discountdetails[0]['amountoff'];
			$typeaa		=	$discountdetails[0]['type'];		
		}
		else if($discounttype == 4)
		{
			echo "<script>discountdetails(4)</script>";
			$discountdetails	=	$AdminDAO->getrows("discountdetailspp","*"," fkdiscountid = '$discountid'");
			$basequantitypp		=	$discountdetails[0]['basequantity'];
			$discountquantitypp	=	$discountdetails[0]['discountquantity'];
			$discountstock		=	$AdminDAO->getrows("discountstock","*"," fkdiscountid = '$discountid'");
			for($i=0;$i<sizeof($discountstock);$i++)
			{
				if($discountstock[$i]['type'] == "b")
				{
					$selected_stock1[]	=	$discountstock[$i]['fkstockid'];
					$stockid			=	$discountstock[$i]['fkstockid'];
					$selbarcodeid		=	$AdminDAO->getrows("stock","*"," pkstockid = '$stockid'");
					$barcodeid			=	$selbarcodeid[0]['fkbarcodeid'];
					$productstock1		=	$AdminDAO->getrows("stock,shipment","date_format(expiry,'%D %b %Y') as expiry,date_format(shipmentdate,'%D %b %Y') as shipmentdate, pkstockid"," fkshipmentid = pkshipmentid AND fkbarcodeid = '$barcodeid'");			
					$sql=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') PRODUCTNAME, b.barcode as bc, pkproductid
					FROM productattribute pa
					RIGHT JOIN (
					product p, attribute a
					) ON ( pa.fkproductid = p.pkproductid
					AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, barcode b
					WHERE pkproductid = pa.fkproductid
					AND pkattributeid = pa.fkattributeid
					AND pkproductattributeid = fkproductattributeid
					AND pkattributeid = ao.fkattributeid
					AND pkattributeoptionid = pi.fkattributeoptionid
					AND b.fkproductid = pkproductid
					AND pi.fkbarcodeid = b.pkbarcodeid
					AND b.pkbarcodeid 	=	'$barcodeid' 
					GROUP BY bc";
					$result			=	$AdminDAO->queryresult($sql);
					$productname1	=	$result[0]['PRODUCTNAME'];
				}
				else
				{
					$selected_stock2[]	=	$discountstock[$i]['fkstockid'];
					$stockid			=	$discountstock[$i]['fkstockid'];
					$selbarcodeid		=	$AdminDAO->getrows("stock","*"," pkstockid = '$stockid'");
					$barcodeid			=	$selbarcodeid[0]['fkbarcodeid'];
					$productstock2		=	$AdminDAO->getrows("stock,shipment","date_format(expiry,'%D %b %Y') as expiry,date_format(shipmentdate,'%D %b %Y') as shipmentdate, pkstockid"," fkshipmentid = pkshipmentid AND fkbarcodeid = '$barcodeid'");			
					$sql=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') PRODUCTNAME, b.barcode as bc, pkproductid
					FROM productattribute pa
					RIGHT JOIN (
					product p, attribute a
					) ON ( pa.fkproductid = p.pkproductid
					AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, barcode b
					WHERE pkproductid = pa.fkproductid
					AND pkattributeid = pa.fkattributeid
					AND pkproductattributeid = fkproductattributeid
					AND pkattributeid = ao.fkattributeid
					AND pkattributeoptionid = pi.fkattributeoptionid
					AND b.fkproductid = pkproductid
					AND pi.fkbarcodeid = b.pkbarcodeid
					AND b.pkbarcodeid 	=	'$barcodeid' 
					GROUP BY bc";
					$result			=	$AdminDAO->queryresult($sql);
					$productname2	=	$result[0]['PRODUCTNAME'];
				}
			}
		}
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$discounts			=	$AdminDAO->getrows(" discount left join barcode on pkbarcodeid=fkbarcodeid ","itemdescription,fkstoreid,discountname,startdate,enddate,fkdiscounttypeid,discountstatus,fkbarcodeid,amount,amountoff,amountofftype,quantity,updatetime"," pkdiscountid = '$discountid'");
	$discountdetails	=	$AdminDAO->getrows(" discountdetail","fkdiscountid ,	fkbarcodeid ,	quantity"," fkdiscountid = '$discountid'");
	/*echo "<pre>";
	print_r($discounts);*/
	$storeid		=	$discounts[0]['fkstoreid'];
	$discounttype	=	$discounts[0]['fkdiscounttypeid'];
	$discountname	=	$discounts[0]['discountname'];
	$discountstatus	=	$discounts[0]['discountstatus'];	
	$startdate		=	date("d-m-Y",$discounts[0]['startdate']);
	$enddate		=	date("d-m-Y",$discounts[0]['enddate']);
	$barcode		=	$discounts[0]['fkbarcodeid'];
	$barcodep		=	$AdminDAO->getrows("barcode","barcode","pkbarcodeid='$barcode'");
	$pbarcode		=	$barcodep[0]['barcode'];
	$quantity		=	$discounts[0]['quantity'] ;
	$productname	=	$discounts[0]['itemdescription'] ;
	$productname1	=	$discounts[0]['itemdescription'] ;
	if($discounttype == 1)
	{
		$discountquantityqq	=	$discountdetails[0]['quantity'] ;				
		echo "<script>discountdetails(1)</script>";// wanna hide something ;)
		$basequantityqq		=	$discounts[0]['quantity'] ;		
	}
	else if($discounttype == 2)
	{
		echo "<script>discountdetails(2)</script>";
		//$discountdetails	=	$AdminDAO->getrows("discountdetail","*"," fkdiscountid = '$discountid'");
		$basequantityaq		=	$discounts[0]['quantity'];
		$productname2		=	$discounts[0]['itemdescription'] ;
		$amountaq			=	$discounts[0]['amountoff'];
		$typeaq				=	$discounts[0]['amountofftype'];
	}
	else if($discounttype == 3)
	{
		echo "<script>discountdetails(3)</script>";
		$baseamount			=	$discounts[0]['amount'];
		$discountamount		=	$discounts[0]['amountoff'];
		$typeaa				=	$discounts[0]['amountofftype'];		
	}
	else if($discounttype == 4)
	{		
		echo "<script>discountdetails(4)</script>";				
		$productname4		=	$discounts[0]['itemdescription'] ;
		$basequantitypp		=	$discounts[0]['quantity'] ;		
	}
	}//end edit
}
?>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
<link rel="stylesheet" type="text/css" href="../includes/css/jquery.autocomplete.css" />
<script src="../includes/js/jquery.autocomplete.js" type='text/javascript'></script>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<link href="../includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../includes/autocomplete/ajax_framework.js"></script>
<?php }//end edit?>
<script language="javascript">
/*function loadsuppliers(div,id,url)
{
	$('#'+div).load(url+'?id='+id);
}*/
/**************************autocompleted*******************************************/
   jQuery().ready(function() 
	{
		<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
		$("#startdate").mask("99-99-9999");
		$("#enddate").mask("99-99-9999");
		<?php }//end edit?>		
		function findValueCallback(event, data, formatted)
		{
			document.getElementById('barcode').value=data[1];
			document.getElementById('fieldcheck').value=data[2];			
			getinstance();
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi, '');
		}
			jQuery("#product").autocomplete("productautocomplete.php");
			jQuery("#productaq").autocomplete("productautocomplete.php");
			jQuery("#productpp").autocomplete("productautocomplete.php");
			jQuery("#productdp").autocomplete("productautocomplete.php?id=new");
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
//			document.salefrm.reset(); 
}); 
function getinstance()
{
	var code=document.getElementById('barcode').value;
	if(document.getElementById('fieldcheck').value=='new')
	{
		jQuery('#expdiv4').load('getprodata.php?code='+code+'&id=new');
	}
	else
	{
		jQuery('#expdiv').load('getprodata.php?code='+code);
		jQuery('#expdiv2').load('getprodata.php?code='+code);
		jQuery('#expdiv3').load('getprodata.php?code='+code);	
	}
}
   
   /**********************************************************************************/
var divID=1;
function discountdetails(value)
{
	if(value=='1')
	{
		if(divID!='')
		{
			document.getElementById("discount_details"+divID).style.display="none";
		}
		divID = "1";		
//		alert('Cash is selected');
		document.getElementById("discount_details1").style.display="block";
	}
	if(value=='2')
	{
		if(divID!='')
		{
			document.getElementById("discount_details"+divID).style.display="none";
		}
		divID = "2";		
//		alert('Credit Card is selected');
		document.getElementById("discount_details2").style.display="block";		
	}
	if(value=='3')
	{
		if(divID!='')
		{
			document.getElementById("discount_details"+divID).style.display="none";
		}
		divID = "3";		
//		alert('Foreign Currency is selected');
		document.getElementById("discount_details3").style.display="block";		
	}
	if(value=='4')
	{
		if(divID!='')
		{
			document.getElementById("discount_details"+divID).style.display="none";
		}
		divID = "4";		
//		alert('Cheque is selected');
		document.getElementById("discount_details4").style.display="block";		
	}
}
function savediscount()
{
	loading('System is Saving The Data....');
	options	=	{	
					url : 'insertdiscount.php',
					type: 'POST',
					success: response
				}
	jQuery('#discountform').ajaxSubmit(options);
}
function response(text)
{
	<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
	document.getElementById('error').innerHTML		=	text;	
	document.getElementById('error').style.display	=	'block';	
	<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
	if(text==''){
		adminnotice('Discount saved successfully.',0,6000);
		jQuery('#maindiv').load('managediscounts.php');		
	}
	else
	{
		adminnotice(text,0,5000);
	}
		
	<?php }//end edit?>

	
	/*if(text=='')
	{
		jQuery('#maindiv').load('managediscounts.php?'+'<?php //echo $qs?>');		
	}
	//hideform();*/
}
<?php		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
	function hideform()
	{
		
		document.getElementById('brandiv').style.display='none';
	}
<?php }//end edit?>
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
function getitemdetails(bc,itm)
{
		if(bc=='')
		{
			alert("Please enter Barcode.");
			return false;
		}
	bc = trim(bc);
}
function focusnow(id)
{
	count	=	document.getElementById('counter').value;
	for(i=1;i<=count;i++)
	{
		document.getElementById("productnames_"+i+"_pro").innerHTML	=	'';
	}
	document.getElementById('productname_pro').innerHTML	=	'';
	document.getElementById('productname1_pro').innerHTML	=	'';
	document.getElementById('productname4_pro').innerHTML	=	'';
	document.getElementById(id+"_pro").innerHTML	=	'<span id="results" style="width:200px"></span>';
}
function alertdate(val,id)
{
	if(val!="")
	{
		dtval	=	val.split('-');
		dateval	=	dtval[2]+'-'+dtval[1]+'-'+dtval[0];
		if(dateval<"<?php echo date('Y-m-d')?>")
		{
			alert("Discount will not be applied: past date is selected");
		}
		if(!isValidDate(val))
		{
			alert("The date you entered is not valid. Correct format is: (dd-mm-yyyy)");
		}
	}
}
function getitemdetails(bc,itm,it,bcid)
{
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getdiscdata.php?bc='+bc+'&it='+it+'&pro='+itm+'&bcid='+bcid);
}	
<?php }//end edit?>
</script>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<div id="discountdiv">
<form name="discountform" id="discountform" onSubmit="savediscount(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	<?php 
		if($discountid == '-1')
		{
			print"Adding New Discount";
		}
		else
		{
			print"Editing: $discountname";
		}
	?>
</legend>
<div style="float:right">
<?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
<span class="buttons">
    <button type="button" class="positive" onclick="savediscount();">
        <img src="../images/tick.png" alt=""/> 
        <?php 
		if($discountid == '-1')
		{
			print"Save";
		}
		else
		{
			print"Update";
		}
		?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('discountdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
 <?php 		}elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 17/02/2012
	   	 buttons('insertdiscount.php','discountform','maindiv','managediscounts.php',$place=1,$formtype)
//end edit	   ?>
</div>          
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
<table>
	<tbody>
	<tr>
	  <td>Discount Type</td>
	  <td>
      	<select name="discounttype" id="discounttype" onchange="discountdetails(this.value); return false;" <?php  if($discountid !="-1") echo "disabled=disabled";?>>
        	<option value="1" <?php if ($discounttype == 1) print "selected=\"selected\""?>>Quantity Against Quantity</option>
            <option value="2" <?php if ($discounttype == 2) print "selected=\"selected\""?>>Amount Against Quantity</option>
            <option value="3" <?php if ($discounttype == 3) print "selected=\"selected\""?>>Amount Against Amount</option>
            <option value="4" <?php if ($discounttype == 4) print "selected=\"selected\""?>>Product Against Product</option>
        </select>
      </td>
	  </tr>
	<tr>
	  <td>Discount Name : </td>
	  <td>
        <div id="error1" class="error" style="display:none; float:right;"></div>
        	<input name="discountname" id="discountname" type="text" value="<?php echo $discountname; ?>" onkeydown="javascript:if(event.keyCode==13) {savediscount(); return false;}" />
        </td>
	  </tr>
	<tr>
	  <td>Start Date</td>
	  <td><input type="text" name="startdate" onclick='scwShow(this,event)' onfocus="scwShow(this,event)" readonly="readonly" value="<?php if($startdate=="") echo date("Y/m/d"); else echo $startdate;?>"/></td>
	  </tr>
	<tr>
	  <td>End Date</td>
	  <td><input type="text" name="enddate" onclick='scwShow(this,event)' onfocus="scwShow(this,event)" readonly="readonly" value="<?php if($enddate=="") echo date("Y/m/d"); else echo $enddate;?>"/></td>
	  </tr>
	<tr>
	  <td>Status</td>
	  <td><input type="radio" name="status" value="a" <?php if($discountstatus == 'a') echo "checked=\"checked\"";?> />
	    Active
	    <input type="radio" name="status" value="i" <?php if($discountstatus != 'a') echo "checked=\"checked\"";?>/>
	    Inactive</td>
	  </tr>
    </table>
    <table id="discount_details1" style="display:block; width:650px;">
    <tr>
	  <td colspan="2"><strong>Quantity Against Quantity Settings</strong></td>
	  </tr>
	<tr>
	  <td>Product</td>
	  <td><label>
	    <input type="text" name="product" id="product" value="<?php echo $productname; ?>" <?php  if($discountid !="-1") echo "readonly=readonly";?> />
	    </label>
      </td>
	  </tr>
	<tr>
	  <td>Stock</td>
	  <td>
      <div id="expdiv">
      <?php if(sizeof($productstock3)>0)
	  {
	  ?>
          <select name="expiry[]" multiple="multiple" size="5">
          <?php
		  for($j=0;$j<sizeof($productstock3);$j++)
		  {
		  ?>
          	<option value="<?php echo $productstock3[$j]['pkstockid'];?>" <?php if(in_array($productstock3[$j]['pkstockid'],$selected_stock)) echo "selected=\"selected\"";?>>
            <?php
			echo "(".$productstock3[$j]['shipmentdate'].")  Expiry - ".$productstock3[$j]['expiry'];
			?>
            </option>
		  <?php 
		  }
          ?>
          </select>
	  <?php 
	  }
	  else
	  {
		  echo "<input type=\"text\" name=\"disabledexpiry\" id=\"expiry\" readonly=\"readonly\"/>";
	  }
	  ?>
      </div>
         </td>
	  </tr>
	<tr>
	  <td>Items to be Purchased</td>
	  <td><label>
	    <input type="text" name="basequantity" id="basequantity" value="<?php echo $basequantityqq; ?>" />
	    </label></td>
	  </tr>
	<tr>
	  <td>Free Items</td>
	  <td><input type="text" name="discountquantity" id="discountquantity" value="<?php echo $discountquantityqq; ?>" /></td>
	  </tr>
    </table>
    <table id="discount_details2" style="display:none;">
	<tr>
	  <td colspan="2"><strong>Amount Against Quantity Settings</strong></td>
	</tr>
	<tr>
	  <td>Product</td>
	  <td><label>
	    <input type="text" name="productaq" id="productaq" value="<?php echo $productname; ?>" <?php  if($discountid !="-1") echo "readonly=readonly";?> />
	    </label></td>
	  </tr>
	<tr>
	  <td>Stock</td>
	  <td>
      <div id="expdiv2">
      <?php if(sizeof($productstock4)>0)
	  { 
	  ?>
          <select name="expiry[]" multiple="multiple" size="5">
          <?php
		  for($j=0;$j<sizeof($productstock4);$j++)
		  {
		  ?>
          	<option value="<?php echo $productstock4[$j]['pkstockid'];?>" <?php if(in_array($productstock4[$j]['pkstockid'],$selected_stock)) echo "selected=\"selected\"";?>>
            <?php
			echo "(".$productstock4[$j]['shipmentdate'].")  Expiry - ".$productstock4[$j]['expiry'];
			?>
            </option>
		  <?php 
		  }
          ?>
          </select>
	  <?php 
	  }
	  else
	  {
		  echo "<input type=\"text\" name=\"disabledexpiry\" id=\"expiry\" readonly=\"readonly\"/>";
	  }
	  ?>
     	  </div>
         </td>
	  </tr>
	<tr>
	  <td>Items to be Purchased</td>
	  <td><label>
	    <input type="text" name="basequantityaq" id="basequantityaq" value="<?php echo $basequantityaq;?>" />
	    </label></td>
	  </tr>
	<tr>
	  <td>Amount Off</td>
	  <td>
      	<input type="text" name="amountaq" id="amountaq" value="<?php echo $amountaq;?>" />
     </td>
	 </tr>
     <tr>
	  <td>Offer Type</td>
	  <td>
		<input type="radio" name="type" value="p" <?php if($typeaq == 'p') echo "checked=\"checked\"";?> />
		Percentage
        <input type="radio" name="type" value="f" <?php if($typeaq == 'f') echo "checked=\"checked\"";?> />
        Flat
     </td>
	 </tr>
     </table>
     <table id="discount_details3" style="display:none;width:650px;">
     <tr>
	  <td colspan="2"><strong>Amount Against Amount Settings</strong></td>
	  </tr>
	<tr>
	  <td>Amount</td>
	  <td><label>
	    <input type="text" name="amountaa" id="amountaa" value="<?php echo $baseamount; ?>" />
	    </label></td>
	  </tr>
	<tr>
	  <td>Amount Off</td>
	  <td>
	    <input type="text" name="amountoff" id="amountoff" value="<?php echo $discountamount;?>" />
	    </td>
	  </tr>
     <tr>
	  <td>Offer Type</td>
	  <td>
		<input type="radio" name="typeaa" value="p" <?php if($typeaa == 'p') echo "checked=\"checked\"";?> />
		Percentage
        <input type="radio" name="typeaa" value="f" <?php if($typeaa == 'f') echo "checked=\"checked\"";?>/>
        Flat</td>
	 </tr>
     </table>
     <table id="discount_details4" style="display:none;width:650px;">     
      <tr>
	  <td colspan="2"><strong>Product Against Product Settings</strong></td>
	  </tr>
	<tr>
	  <td>Purchase Product</td>
	  <td><label>
	    <input type="text" name="productpp" id="productpp" value="<?php echo $productname1; ?>" <?php  if($discountid !="-1") echo "readonly=readonly";?> />
	    </label></td>
	  </tr>
      <tr>
      <td>Stock</td>
      	<td>
	  <div id="expdiv3">
      <?php if(sizeof($productstock1)>0)
	  { 
	  ?>
          <select name="expiry[]" multiple="multiple" size="5">
          <?php
		  for($j=0;$j<sizeof($productstock1);$j++)
		  {
		  ?>
          	<option value="<?php echo $productstock1[$j]['pkstockid'];?>" <?php if(in_array($productstock1[$j]['pkstockid'],$selected_stock1)) echo "selected=\"selected\"";?>>
            <?php
			echo "(".$productstock1[$j]['shipmentdate'].")  Expiry - ".$productstock1[$j]['expiry'];
			?>
            </option>
		  <?php 
		  }
          ?>
          </select>
	  <?php 
	  }
  	  else
	  {
		  echo "<input type=\"text\" name=\"disabledexpiry\" id=\"expiry\" readonly=\"readonly\"/>";
	  }
	  ?>
      	  </div>
        </td>
      </tr>
	<tr>
	  <td>Quantity</td>
	  <td><input type="text" name="basequantitypp" id="basequantitypp" value="<?php echo $basequantitypp; ?>" /></td>
	  </tr>
	<tr>
	  <td>Discounted Product</td>
	  <td><label>
	    <input type="text" name="productdp" id="productdp" value="<?php echo $productname2; ?>" <?php  if($discountid !="-1") echo "readonly=readonly";?> />
	    </label></td>
	  </tr>
	<td>Stock</td>
      	<td>
	  <div id="expdiv4">
      <?php if(sizeof($productstock2)>0)
	  { 
	  ?>
          <select name="expiry2[]" multiple="multiple" size="5">
          <?php
		  for($j=0;$j<sizeof($productstock2);$j++)
		  {
		  ?>
          	<option value="<?php echo $productstock2[$j]['pkstockid'];?>" <?php if(in_array($productstock2[$j]['pkstockid'],$selected_stock2)) echo "selected=\"selected\"";?>>
            <?php
			echo "(".$productstock2[$j]['shipmentdate'].")  Expiry - ".$productstock2[$j]['expiry'];
			?>
            </option>
		  <?php 
		  }
          ?>
          </select>
	  <?php 
	  }
	  else
	  {
		  echo "<input type=\"text\" name=\"disabledexpiry\" id=\"expiry\" readonly=\"readonly\"/>";
	  }
	  ?>
      	  </div>
        </td>
      </tr>
	<tr>
	  <td>Discount Quantity</td>
	  <td>
	    <input type="text" name="discountquantitypp" id="discountquantitypp" value="<?php echo $discountquantitypp;?>" />
	    </td>
	  </tr>
     </table>
     <tr>
	  <td colspan="2"  align="center">
	  <!--  <input type="button" value="Save" onclick="savediscount();"><input name="submit" type="button" value="Cancel" onclick="hideform()" />-->
        <div class="buttons">
            <button type="button" class="positive" onclick="savediscount();">
                <img src="../images/tick.png" alt=""/> 
                 <?php 
					if($discountid == '-1')
					{
						print"Save";
					}
					else
					{
						print"Update";
					}
				?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('discountdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="barcode" id="barcode" value="<?php echo $barcode?>" />
<input type="hidden" name="fieldcheck" id="fieldcheck" value="<?php echo $barcode?>" />
<input type="hidden" name="discountid" id="discountid" value="<?php echo $discountid?>" />
<?php
if($discountid!="-1")
{
?>
	<input type="hidden" name="discounttype" id="discounttype" value="<?php echo $discounttype?>" />
<?php
}
?>
</form>
</div>
</div>
<script language="javascript">

//document.brandform.brand.focus();
//loading('Loading Form...');

</script>
<?php 		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
<table>
	<tbody>
    <tr>
	  <td>Store : </td>
	  	<td>       
        	<?php 
       			$sql				=" SELECT pkstoreid,storename FROM store";
                $selstore	='<select name="storeid" id="storeid" >';
                $result				=	$AdminDAO->queryresult($sql);
                foreach($result as $row){					
                    $selstore	.='<option value="'.$row['pkstoreid'].'" '.(($storeid == $row['pkstoreid'])? "selected=selected":"").'>'.$row['storename'].'</option>';
                }
                echo $selstore	.='</select>';
  				?>
            
        </td>
	  </tr>
    
	<tr>
	  <td>Discount Type : </td>
	  <td>
	  <?php 
	$sql				=" SELECT pkdiscounttypeid, typename, combine, priority FROM discounttype ";
	$seldiscounttype	='<select name="discounttype" id="discounttype" onchange="discountdetails(this.value); return false;" '.(($discountid !="-1")? "disabled=disabled":"").'>';
	$result				=	$AdminDAO->queryresult($sql);
	foreach($result as $row){					
		$seldiscounttype	.='<option value="'.$row['pkdiscounttypeid'].'" '.(($discounttype == $row['pkdiscounttypeid'])? "selected=selected":"").'>'.$row['typename'].'</option>';					
	}
	$seldiscounttype	.='</select>';
	echo $seldiscounttype;
	?>
      </td>
	  </tr>
	<tr>
	  <td>Discount Name : <span class="redstar" title="This field is compulsory">*</span></td>
	  <td>
        <div id="error1" class="error" style="display:''; float:right;"></div>
        	<input name="discountname" id="discountname" type="text" value="<?php echo $discountname; ?>" onkeydown="javascript:if(event.keyCode==13) {savediscount(); return false;}" />
        </td>
	  </tr>
	<tr>
	  <td>Start Date : </td>
	  <td><input type="text" name="startdate" id="startdate" value="<?php if($startdate=="") echo date("d-m-Y"); else echo $startdate;?>"/></td>
	  </tr>
	<tr>
	  <td>End Date : </td>
	  <td><input type="text" name="enddate" id="enddate" value="<?php if($enddate=="") echo date("d-m-Y"); else echo $enddate;?>" onblur="alertdate(this.value,this.id);"/>
      <input name="barcode" id="barcode" type="hidden"  value="<?php echo $barcode; ?>" />
      </td>
	  </tr>
	<tr>
	  <td>Status : </td>
	  <td><input type="radio" name="status" value="a" <?php if($discountstatus == 'a') echo "checked=\"checked\"";?> />
	    Active
	    <input type="radio" name="status" value="i" <?php if($discountstatus != 'a') echo "checked=\"checked\"";?>/>
	    Inactive
        </td>
	  </tr>
    </table>
   	<table id="discount_details1" style="display:block; width:900px;">
    <tr>
	  <td colspan="2"><strong>Quantity Against Quantity Settings</strong></td>
	  </tr>
	<tr>
	  <td>Item : </td>
	  <td><label>
        <input name="productname" id="productname" value="<?php echo $productname; ?>" type="text" onkeyup="suggestnow(event,'productname','barcode','results')" class="text" autocomplete="off" onfocus="this.select();focusnow(this.id)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/><span id="productname_pro"></span>
        </label>
      </td>
	  </tr>
    <tr>
    	<td>Barcode : </td>
        <td><input name="barcode_productname" id="barcode_productname" class="text" size="16" value="<?php echo $pbarcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,'productname','basequantity','barcodes_1'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
    </tr>
	<tr>
	  <td>Items to be Purchased (Numbers): <span class="redstar" title="This field is compulsory">*</span></td>
	  <td><label>
	    <input type="text" name="basequantity" id="basequantity" value="<?php echo $basequantityqq; ?>" />
	    </label>
      </td>
	  </tr>
	<tr>
	  <td>Free Items : <span class="redstar" title="This field is compulsory">*</span></td>
	  <td><input type="text" name="discountquantity" id="discountquantity" value="<?php echo $discountquantityqq; ?>" /></td>
	  </tr>
    </table>
    <table id="discount_details2" style="display:none; width:900px;">
	<tr>
	  <td colspan="2"><strong>Amount Against Quantity Settings</strong></td>
	</tr>
	<tr>
	  <td>Product : </td>
	  <td><label>
		<input name="productname1" id="productname1" value="<?php echo $productname; ?>" type="text" onkeyup="suggestnow(event,'productname1','barcode','results')" class="text" autocomplete="off" onfocus="this.select();focusnow(this.id)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/><span id="productname1_pro"></span>
        </label></td>
	  </tr>
      <tr>
      	<td>Barcode :</td>
        <td><input name="barcode_productname1" id="barcode_productname1" class="text" size="16" value="<?php echo $pbarcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,'productname1','basequantityaq','barcodes_1'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
      </tr>
	<tr>
	  <td>Items to be Purchased : </td>
	  <td><label>
	    <input type="text" name="basequantityaq" id="basequantityaq" value="<?php echo $basequantityaq;?>" />
	    </label></td>
	  </tr>
	<tr>
	  <td>Amount Off : </td>
	  <td>
      	<input type="text" name="amountaq" id="amountaq" value="<?php echo $amountaq;?>" />
     </td>
	 </tr>
     <tr>
	  <td>Offer Type : </td>
	  <td>
		<input type="radio" name="type" value="2" <?php if($typeaq == '2') echo "checked=\"checked\"";?> />
		Percentage
        <input type="radio" name="type" value="1" <?php if($typeaq == '1') echo "checked=\"checked\"";?> />
        Flat
     </td>
	 </tr>
     </table>
    <table id="discount_details3" style="display:none; width:900px;">
     <tr>
	  <td colspan="2"><strong>Amount Against Amount Settings</strong></td>
	  </tr>
	<tr>
	  <td>Amount : </td>
	  <td><label>
	    <input type="text" name="amountaa" id="amountaa" value="<?php echo $baseamount; ?>" />
	    </label></td>
	  </tr>
	<tr>
	  <td>Amount Off : </td>
	  <td>
	    <input type="text" name="amountoff" id="amountoff" value="<?php echo $discountamount;?>" />
	    </td>
	  </tr>
     <tr>
	  <td>Offer Type : </td>
	  <td>
		<input type="radio" name="typeaa" value="2" <?php if($typeaa == '2') echo "checked=\"checked\"";?> />
		Percentage
        <input type="radio" name="typeaa" value="1" <?php if($typeaa == '1') echo "checked=\"checked\"";?>/>
        Flat</td>
	 </tr>
     </table>
    <table id="discount_details4" style="display:none; width:900px;">     
      <tr>
	  <td colspan="3"><strong>Product Against Product Settings</strong></td>
	  </tr>
	<tr>
	  <td>Purchase Product: </td>
	  <td><label>
			<input name="productname4" id="productname4" value="<?php echo $productname4; ?>" type="text" onkeyup="suggestnow(event,'productname4','barcode','results')" class="text" autocomplete="off" onfocus="this.select();focusnow(this.id)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/><span id="productname4_pro"></span></label>
      </td>
      </tr>
      <tr>
      <td>Barcode : </td>
      <td>
      	<input name="barcode_productname4" id="barcode_productname4" class="text" size="16" value="<?php echo $pbarcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,'productname4','basequantitypp','barcodes_1'); return false;}" type="text" autocomplete="off" onfocus="this.select()" >
      </td>
	  </tr>
	<tr>
	  <td>Quantity : </td>
	  <td><input type="text" name="basequantitypp" id="basequantitypp" value="<?php echo $basequantitypp; ?>" /> 
	<script language="javascript">
        function additemdet(){
            var counter, previousInnerHTML; 
            counter = document.getElementById("counter").value;
            if(counter==''){	
				counter=1;
			}							
			counter =parseInt(counter)+1;					
			//previousInnerHTML	=	document.getElementById("additemslist").innerHTML;
			//previousInnerHTML 	= 	previousInnerHTML.concat('<div><tr><td><input name="productnames[]" id="productnames_'+counter+'" type="text" onkeyup="suggestnow(event,\'productnames_'+counter+'\',\'barcodes_'+counter+'\',\'results\')" class="text" autocomplete="off" onfocus="this.select();focusnow(this.id)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/></td><td><input name="barcode_productnames_'+counter+'" id="barcode_productnames_'+counter+'" class="text" size="16" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,\'productnames_'+counter+'\',\'discountquantitypp_'+counter+'\',\'barcodes_'+counter+'\'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td><td><input type="text" name="discountquantitypp[]" id="discountquantitypp_'+counter+'" value="1"  style="width:50px; height:14px;" /><input type="hidden" name="barcodes[]" id="barcodes_'+counter+'" /></td></tr></div><span id="productnames_'+counter+'_pro"></span>');
			var innStr	=	'<div style="margin-left:7px;"><tr><td><input name="productnames[]" id="productnames_'+counter+'" type="text" onkeyup="suggestnow(event,\'productnames_'+counter+'\',\'barcodes_'+counter+'\',\'results\')" class="text" autocomplete="off" onfocus="this.select();focusnow(this.id)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/></td><td><input style="margin-left:3px;" name="barcode_productnames_'+counter+'" id="barcode_productnames_'+counter+'" class="text" size="16" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,\'productnames_'+counter+'\',\'discountquantitypp_'+counter+'\',\'barcodes_'+counter+'\'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td><td><input type="text" name="discountquantitypp[]" id="discountquantitypp_'+counter+'" value="1"  style="width:50px; height:14px; margin-left:4px;" /><input type="hidden" name="barcodes[]" id="barcodes_'+counter+'" /><span id="productnames_'+counter+'_pro"></span></td></tr></div>';
			document.getElementById("additemslist").innerHTML+=innStr;
			document.getElementById("additemslist").style.display='block';
			document.getElementById("additemslist").style.display='table-row';
			//document.getElementById("additemslist").innerHTML=previousInnerHTML;
            document.getElementById("counter").value=counter++;
			return false;
        }        
    </script>
        </td>
	  </tr>
	<tr>
	  <td colspan="2"><div style="float:left;width:68%; text-align:right;"><a href="javascript:void(0);" onclick="javascript:additemdet();" >Add Another Item</a></div></td>
	  </tr>
	<tr>
	  <td valign="top" colspan="2">Discounted Product: </td>
    </tr>
    <tr>
    <td colspan="2" align="left">
    <table>
    <tr>
    <th>Item</th>
    <th>Barcode</th>
    <th>Quantity</th>
    </tr>
         <?php 
			$discountdetails	=	$AdminDAO->getrows(" discountdetail Left join barcode on pkbarcodeid=fkbarcodeid","itemdescription as item,fkdiscountid ,	fkbarcodeid , barcode,	quantity"," fkdiscountid = '$discountid'");
			if($discounttype == 4){	
				$counter=1;
				foreach($discountdetails as $details){						
					if($counter==1){
						$productnames_1		=$details['item'];
						$barcodes_1			=$details['fkbarcodeid'];
						$barcodeids_1		=$details['barcode'];
						$discountquantitypp	=$details['quantity'];
						$counter++;	
					}
					else{												
						break;					
					}
				}
			}
		?> 
     
      	<div>
        	<tr><td><input name="productnames[]" id="productnames_1" type="text" value="<?php echo $productnames_1;?>" onkeyup="suggestnow(event,'productnames_1','barcodes_1','results')" class="text" autocomplete="off" onfocus="this.select();focusnow(this.id)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/></td>
            <td><input name="barcode_productnames_1" id="barcode_productnames_1" value="<?php echo $barcodeids_1;?>" class="text" size="16" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,'productnames_1','discountquantitypp_1','barcodes_1'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
            <td><input type="text" name="discountquantitypp[]" id="discountquantitypp_1" value="<?php echo $discountquantitypp;?>" style="width:50px; height:14px;"/></td></tr>
        </div>         
        <input type="hidden" name="barcodes[]" id="barcodes_1"  value="<?php echo $barcodes_1;?>" />
        <input type="hidden" name="counter" id="counter" value="<?php echo ((count($discountdetails)>1)?count($discountdetails):1); ?>" /><span id="productnames_1_pro"></span>
        <?php //echo count($discountdetails);
			$discountdetails	=	$AdminDAO->getrows("discountdetail Left join barcode on pkbarcodeid=fkbarcodeid","itemdescription as item,fkdiscountid,barcode,fkbarcodeid,quantity","fkdiscountid = '$discountid'");
			if($discounttype == 4)
			{	
				$counter=1;
				foreach($discountdetails as $details1)
				{
					if($counter==1)
					{
						$counter++;
						continue;						
					}
					else
					{
						echo '<div><tr><td><input name="productnames[]" id="productnames_'.$counter.'" value="'.$details1["item"].'"  type="text" onkeyup="suggestnow(event,\'productnames_'.$counter.'\',\'barcodes_'.$counter.'\',\'results\')" class="text" autocomplete="off" onfocus="this.select();focusnow(this.id)" onkeydown="javascript:if(event.keyCode==13) {return false;}"/></td><td><input name="barcode_productnames_'.$counter.'" id="barcode_productnames_'.$counter.'" value="'.$details1["barcode"].'" class="text" size="16" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,\'productnames_'.$counter.'\',\'discountquantitypp_'.$counter.'\',\'barcodes_'.$counter.'\'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td><td><input type="text" name="discountquantitypp[]" id="discountquantitypp_'.$counter.'" value="'.$details1["quantity"].'"  style="width:50px; height:14px;" /><input type="hidden" name="barcodes[]" id="barcodes_'.$counter.'" value="'.$details1["fkbarcodeid"].'" /></td></tr></div><span id="productnames_'.$counter.'_pro"></span>';
						$counter++;	
					}
				}
			}
		?>        
        <tr id="additemslist_<?php echo ((count($discountdetails)>1)?count($discountdetails):1);;?>"></tr>
        </table>
        </td>
	  </tr>
     </table>
    <tr>
    	<td colspan="2">
            <div id="additemslist" margin-left:7px;>
            </div>
        </td>
    </tr> 
    <tr>
	  <td colspan="2"  align="center">
        <?php /*?><div class="buttons">
            <button type="button" class="positive" onclick="savediscount();">
                <img src="../images/tick.png" alt=""/> 
                 <?php 
					if($discountid == '-1')
					{
						print"Save";
					}
					else
					{
						print"Update";
					}
				?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('discountdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div><?php */?>
		   <?php
	   	 buttons('insertdiscount.php','discountform','maindiv','managediscounts.php',$place=0,$formtype)
	   ?>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="__barcode" id="__barcode" value="<?php echo $barcode?>" />
<input type="hidden" name="fieldcheck" id="fieldcheck" value="<?php echo $barcode?>" />
<input type="hidden" name="discountid" id="discountid" value="<?php echo $discountid?>" />
<?php
if($discountid!="-1")
{
?>
	<input type="hidden" name="discounttype" id="discounttype" value="<?php echo $discounttype?>" />
<?php
}
?>
</form>
</div>
</div>
<script language="javascript">
	focusfield('storeid');
</script>
<?php }//end edit?>