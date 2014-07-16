<script language="javascript">
function getbarcodeprice()
{
	var barcode			=	document.getElementById('bcp').value;
 	jQuery('#barcodeprice').load('getbarcodeprice.php?bc='+barcode);
	setTimeout("jQuery('#cancelsalepopup').fadeOut()",5000);
	return false;
}
</script>

<div id="container2">
<div align="center" style="color:#F00">
  <h4>Please Enter barcode to see price</h4>
</div>
<form id="barcodepricefrm" name="barcodepricefrm" method="post" action="">
	    <table width="200" align="center" id="pos">
	      <tr>
	        <td>Barcode</td>
	        <td><input name="bcp" id="bcp" type="text"  autocomplete="off" size="15"  onkeydown="javascript:if(event.keyCode==13) {getbarcodeprice(); return false;}" onfocus="this.select()"/></td>
          </tr>
	      <tr>
	        <td width="50">Price</td>
	        <td width="150">
		 <div id="barcodeprice"></div>
         </td>
         </tr>
	      <tr>
	        <td></td>
            <td align="center">
            <span class="buttons" style="font-size:12px;">
            <button type="button" name="button" id="button" onclick="getbarcodeprice();">
                    <img src="images/tick.png" alt=""/> 
                   ShowPrice                </button>
                		<button type="button" name="button2" id="button2" onclick="javascript:jQuery('#cancelsalepopup').fadeOut();">
                    <img src="images/cross.png" alt=""/> 
                   Cancel                </button>
            </span>       		    </td>
          </tr>
    </table>
		<script language="javascript">
			document.getElementById('bcp').focus();
		</script>
</form>
</div>