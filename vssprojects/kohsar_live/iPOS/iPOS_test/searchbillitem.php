<link href="includes/css/autocomplete_srach_bill.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="includes/autocomplete/ajax_framework_search_bill.js"></script>
<script language="javascript">
		/*function lookup(inputString) 
		{
			// post data to our php processing page and if there is a return greater than zero
			// show the suggestions box
			var fd=document.getElementById('fd').value;
			var fm=document.getElementById('fm').value;
			var fy=document.getElementById('fy').value;

			var fdate=fd+'-'+fm+'-'+fy;
			var td=document.getElementById('td').value;
			var tm=document.getElementById('tm').value;
			var ty=document.getElementById('ty').value;

			var tdate=td+'-'+tm+'-'+ty;
			
			$.post("getbillitem.php", {mysearchString: ""+inputString+"",fromDate:""+fdate+"",toDate:""+tdate+""}, function(data){
		
	} //end*/
	
	// if user clicks a suggestion, fill the text box.
function serachiteminbill()
{
	var productnameit	=	document.getElementById('productnameit').value;
	var barcode			=	document.getElementById('bc').value;
 	//productnameit	=	productnameit.split(":",1);
			jQuery('#popupdiv').hide();
			var fd=document.getElementById('fd').value;
			var fm=document.getElementById('fm').value;
			var fy=document.getElementById('fy').value;

			var fdate=fd+'-'+fm+'-'+fy;
			var td=document.getElementById('td').value;
			var tm=document.getElementById('tm').value;
			var ty=document.getElementById('ty').value;

			var tdate=td+'-'+tm+'-'+ty;
	loadsection('main-content',"billing.php?productname="+encodeURIComponent(productnameit)+"&barcode="+barcode+"&fromDate="+fdate+"&toDate="+tdate);
	//selecttab('main-content',"billing.php?saleid="+productnameit);
	return false;
	//alert(productnameit);
	
}

</script>

<div id="container2">
<div align="center" style="color:#F00">
  <h4>Please Enter item name to search bill</h4>
</div>
<form id="serachitemfrm" name="serachitemfrm" method="post" action="">
	    <table width="494" align="center" id="pos">
	      <tr>
	        <td>&nbsp;</td>
	        <td>
			From Date 
			<?php
			$expiry	=	@date('Y-m-d');
			$exp	=	explode('-',$expiry);
			$expy	=	$exp[0];
			$expm	=	$exp[1];
			$expd	=	$exp[2];
		   ?>
		   dd
       	<input type="text" name="fd" id="fd" onkeypress="return isNumberKey(event)" size="1" maxlength="2" value="<?php echo $expd;?>" onfocus="this.select()"/>
       	mm
		<input type="text" name="fm" id="fm" onkeypress="return isNumberKey(event)" size="1" maxlength="2" value="<?php echo $expm;?>" onfocus="this.select()"/>
       	yy
		<input type="text" name="fy" id="fy" onkeypress="return isNumberKey(event)" size="1" maxlength="4" value="<?php echo $expy;?>" onfocus="this.select()"/>
			<?php
			$expiry	=	@date('Y-m-d');
			$exp	=	explode('-',$expiry);
			$expy	=	$exp[0];
			$expm	=	$exp[1];
			$expd	=	$exp[2];
			?>
			To Date 
			   dd
       	<input type="text" name="td" id="td" onkeypress="return isNumberKey(event)" size="1" maxlength="2" value="<?php echo $expd;?>" onfocus="this.select()"/>
       	mm
		<input type="text" name="tm" id="tm" onkeypress="return isNumberKey(event)" size="1" maxlength="2" value="<?php echo $expm;?>" onfocus="this.select()"/>
       	yy
		<input type="text" name="ty" id="ty" onkeypress="return isNumberKey(event)" size="1" maxlength="4" value="<?php echo $expy;?>" onfocus="this.select()"/>			</td>
          </tr>
	      <tr>
	        <td>Barcode</td>
	        <td><input name="bc" id="bc" type="text"  autocomplete="off" size="50"  onkeydown="javascript:if(event.keyCode==13) {serachiteminbill(); return false;}" onfocus="this.select()"/></td>
          </tr>
	      <tr>
	        <td width="27">Item</td>
	        <td width="372">
		 <input name="productnameit" id="productnameit" type="text"  autocomplete="off" size="50"  onkeyup="suggestnow_(event,'results_');" onkeydown="newScrol_(event);" onfocus="this.select()"/> <?php /*?>onkeydown="javascript:if(event.keyCode==13) {serachiteminbill(); return false;}"<?php */?>
         <div id="results_" class="results_"></div>
		 <div class="suggestionsBox" id="suggestions" style="display: none;">
        
        <div class="suggestionList" id="autoSuggestionsList"></div>			</td>
          </tr>
	      <tr>
	        <td></td>
            <td align="center">
            <span class="buttons" style="font-size:12px;">
            <button type="button" name="button" id="button" onclick="serachiteminbill();">
                    <img src="images/tick.png" alt=""/> 
                   Search                </button>
                		<button type="button" name="button2" id="button2" onclick="javascript:jQuery('#popupdiv').fadeOut();">
                    <img src="images/cross.png" alt=""/> 
                   Cancel                </button>
            </span>       		    </td>
          </tr>
    </table>
		<script language="javascript">
			document.getElementById('fd').focus();
		</script>
</form>
</div>