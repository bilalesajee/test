<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs,$Key;
$brandid = $_GET['id'];
$qs	=	$_SESSION['qstring'];
$selected_suppliers	=	array();
$selected_countries	=	array();

/*if($brandid=="")
{
	$brandid="-1";
}
else if($brandid !="-1")
{
	$brand = $AdminDAO->getrows('brand','*',"`pkbrandid`='$brandid' AND branddeleted != '1'");
	$brandname = $brand[0]['brandname'];
	$selected_countries[0]	=	$brand[0]['fkcountryid'];
	$suppliers		=	$AdminDAO->getrows('brandsupplier','fksupplierid',"`fkbrandid`='$brandid' AND brandsupplierdeleted !='1'");
	foreach($suppliers as $suppliers1)
	{
		$selected_suppliers[]	=	$suppliers1['fksupplierid'];
	}
}*/

/********************************COUNTRIES***********************************/
$countries_array	=	$AdminDAO->getrows('countries','*', ' countriesdeleted != 1');
//$countries			=	$Component->makeComponent('d','countries',$countries_array,'pkcountryid','countryname',1,$selected_countries);
/****************************************************************************/
?>
<script language="javascript">
function addform()
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'insertnewinvoice.php',
					type: 'POST',
					success: response
				}
	jQuery('#invoicefrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Invoice data has been saved.',0,5000);
		jQuery('#sugrid').load('manageinvoices.php');		
	}
	else
	{
		adminnotice(text,0,5000);	
	}
	//hideform();
}
function hideform()
{
	
	document.getElementById('addinvoicediv').style.display='none';
}

</script>
<?php


?>
<div id="addinvoicediv">
<br />

<form name="invoicefrm" id="invoicefrm" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	<?php 
		if($brandid == '-1')
		{
			print"Adding New Invoice";
		}
		else
		{
			print"Editing: $invoicename";
		}
	?>
</legend>
<div style="float:right">
<span class="buttons">
<button type="button" class="positive" onclick="addform();">
    <img src="../images/tick.png" alt=""/> 
    Save
</button>
 <a href="javascript:void(0);" onclick="hidediv('addinvoicediv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
</a>
</span>
</div>
<table>
	<tbody>
	<tr>
		<td>Invoice Name: </td>
		<td colspan="2" id="invname">
		<input name="invoicename" id="invoicename" type="text" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" readonly="readonly"></td>
	</tr>
	<tr>
		<td>Country: </td>
		<td colspan="2">
		<select name="country" id="country" onchange="makeinvoicename(this.value)">
		<option value="">Please Select Country</option>
		<?php 
        for($c=0;$c<count($countries_array);$c++)
        {
  		?>
            <option value="<?php echo $countries_array[$c]['pkcountryid'];?>">
            	<?php echo $countries_array[$c]['countryname'];?>
            </option>
		<?php
			}
		?>
        </select>
        </td>
	</tr>
	<tr>
		<td colspan="3"  align="left">
         <div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                Save
            </button>
             <a href="javascript:void(0);" onclick="hidediv('addinvoicediv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
		</td>				
	</tr>
	</tbody>
</table>
</fieldset>	
</form>
</div>
<script language="javascript">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
document.invoicefrm.country.focus();
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
document.invoicefrm.invoicename.focus();
<?php } //end edit?>
loading('Loading Form...');
function makeinvoicename(val)
{
	jQuery('#invname').load('makeinvoicename.php?cid='+val);
}
</script>