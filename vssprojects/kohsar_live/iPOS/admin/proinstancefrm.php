<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$bc	=	$_GET['bc'];
?>
 <form id="addproductfrm" name="addproductfrm">
   	<fieldset>
        <legend>
            Add/Select Product for <?php echo $barcode;?>
        </legend>
            <table cellpadding="0" cellspacing="2" width="100%"  >
                <tbody>
                <tr>
                	<td colspan="3">
                    	<div align='center'><font color=red>Sorry! But No Record Found With This Barcode!</font></div>     </td>
                </tr>
                <tr >
                    <td width="13%">Product Name</td>
                    <td width="6%">
                    	<?php
                        	$product_array	=	$AdminDAO->getrows('product','*');
							echo $products				=	$Component->makeComponent("d","productid",$product_array,"pkproductid","productname",1,$selected_products,"onchange=addproductinstance(this.value,'$bc')");
                    	?>
					</td>
                    <td width="81%">
                    
                    <input name="productname" id="productname" type="text" value="" onkeydown="javascript:if(event.keyCode==13) {addproduct(); return false;}">
                    <input name="barcode" id="barcode" type="hidden" value="<?php echo $bc;?>" >
                    <input name="type" id="type" type="hidden" value="addproduct" >
                    </td>
                </tr>
                <tr >
                  <td colspan="3" id="instance">&nbsp;</td>
                  </tr>
                    
                </tbody>
            </table>
		</fieldset>	
   </form>