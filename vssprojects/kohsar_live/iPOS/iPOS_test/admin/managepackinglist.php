<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$shipmentid		=	$_GET['id'];
echo $packinglistid	=	$_GET['packinglistid'];
if($packinglistid!="")
{
	$packingdata	=	$AdminDAO->getrows("packinglist pl,shiplist,packing","*","pl.pkpackinglistid='$packinglistid' AND pkshiplistid=pl.fkshiplistid AND pkpackingid=pl.fkpackingid");
	$itemidtoedit	=	$packingdata[0]['pkshiplistid'];
	$unitsreserved	=	$packingdata[0]['reserved'];
	$packingtoedit	=	$packingdata[0]['pkpackingid'];
	$maxquantity	=	$packingdata[0]['purchasequantity'];
}
$items	=	$AdminDAO->getrows("shiplist","*","fkshipmentid='$shipmentid'");
$x1	=	"<select name=\"shiplist\" class=eselect onchange=calcremaining(this.value)><option value=\"\">Select Item</option>";
for($i=0;$i<sizeof($items); $i++)
{
	$itemid		=	$items[$i]['pkshiplistid'];
	$itemname	=	$items[$i]['itemdescription'];
	$barcode	=	$items[$i]['barcode'];
	$select		=	"";
	if($itemid == $itemidtoedit)
	{
		$select = "selected=\"selected\"";
	}
	$x2.=	"<option value=\"$itemid\" $select>[$barcode] $itemname</option>";
}
$item	=	$x1.$x2."</select>";
$boxes	=	$AdminDAO->getrows("packing","*","fkshipmentid='$shipmentid' AND fkpackingid<>''");
$q1	=	"<select name=box id=box class=eselect>";
for($i=0;$i<sizeof($boxes); $i++)
{
	$boxid		=	$boxes[$i]['pkpackingid'];
	$boxname	=	$boxes[$i]['packingname'];
	$select		=	"";
	if($boxid == $packingtoedit)
	{
		$select = "selected=\"selected\"";
	}
	$q2.=	"<option value=$boxid $select>$boxname</option>";
}
$box	=	$q1.$q2."</select>";
$oper	=	$_GET['oper'];
$delid	=	$_GET['delid'];
if($delid!='' && $oper=='del')
{
	$ids	=	explode(",",$delid);
	foreach($ids as $val)
	{
		if($val!='')
		{
			$AdminDAO->deleterows("packinglist","pkpackinglistid='$val'","1");
		}
	}
}
?>
<script language="javascript">
jQuery(function($){
	jQuery('#packingboxes').load('packingboxes.php?id='+'<?php echo $shipmentid;?>');
	document.getElementById('packinglistid').value 	=	'<?php echo $packinglistid;?>';
	document.getElementById('maxquantity').value 	=	'<?php echo $maxquantity;?>';
});
function calcremaining(id)
{
	jQuery('#remaining').load('shiplistremitems.php?id='+id);
}
function checkmax(val)
{
	var maxitems	=	parseInt(document.getElementById('maxquantity').value);
	if(maxitems<val)
	{
		alert('Items you entered are more than purchased items, adjusting to a maximum value.');
		document.getElementById('remquantity').value	=	maxitems;
	}
}
function addpackinglist(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertpackinglist.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#translistform').ajaxSubmit(options);
}
function response(text)
{
	if(text==1)
	{
		document.getElementById('error').innerHTML		=	'Please select item and make sure remaining items are mentioned correctly.';	
		document.getElementById('error').style.display	=	'block';
	}
	else(text!="")
	{
		document.getElementById('error').innerHTML		=	'Packing List Updated Successfully.';
		document.getElementById('error').style.display	=	'block';
		document.getElementById('remquantity').value	=	text;
		jQuery('#packingboxes').load('packingboxes.php?id='+'<?php echo $shipmentid;?>');
	}
}
function editselected()
{
	var selectedbrands	=	getselected('packinglist');
	var sb;
	if (selectedbrands.length > 1)
	{
		for (i=1; i < selectedbrands.length; i++)
		{
			 sb	=	selectedbrands[i];
		} 
		var sb1	=	sb.split('packinglist');
		//jQuery('#instdiv').load('managepackinglist.php?id=<?php echo $shipmentid;?>');
		
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
	jQuery('#subsection').load('managepackinglist.php?id=<?php echo $shipmentid;?>&packinglistid='+sb1[0]);
}
function deleteselected()
{
	
	var selecteditem	=	getselected('packinglist');
	if (selecteditem=='')
	{
		alert("Please make sure that you have selected at least one row.");
	}
	else
	{
		if (confirm('Are you sure to DELETE selected record(s)?'))
		{
			jQuery('#subsection').load('managepackinglist.php?oper=del&id=<?php echo $shipmentid;?>&delid='+selecteditem);
		}
	}
}
function updateselected()
{
	var selecteditem	=	getselected('packinglist');
	if (selecteditem=='')
	{
		alert("Please make sure that you have selected at least one row.");
	}
	else
	{
		//loading('System is saving data....');
		options	=	{	
						url : 'updatepackinglist.php?id=<?php echo $shipmentid; ?>',
						type: 'POST',
						success: packinglistupdate
					}
		jQuery('#packinglistitems').ajaxSubmit(options);
	}
}
function packinglistupdate(text)
{
	alert(text);
}
</script>
<div id="printit"></div>
<div id="packingboxes"></div>
<div id="loaditemscript">
</div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;">
<br>
<form id="translistform" style="width: 920px;" action="inserttranslist.php?id=-1" class="form">
<fieldset>
<legend>
   	Transit List
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addpackinglist(-1);">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
<div id="transdata" style="margin-top:25px;">
<table>
  <tr>
    <th>Box</th>
    <th>Item</th>
    <th>Remaining Quantity</th>
    </tr>
	<tr>
    	<td><?php echo $box; ?></td>
        <td><?php echo $item; ?></td>
        <td><div id="remaining"><input type="text" name="remquantity" id="remquantity" value="<?php echo $unitsreserved; ?>" class="text" onfocus="this.select()" /></div></td>
    </tr>
</table>
<input type="hidden" name="maxquantity" id="maxquantity" value="" />
<input type="hidden" name="packinglistid" id="packinglistid" value="" />
<div class="buttons">
  <button type="button" class="positive" onclick="addpackinglist(-1);">
    <img src="../images/tick.png" alt=""/> 
    <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
  <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative">
    <img src="../images/cross.png" alt=""/>
    Cancel
    </a>
</div>
</div>
</fieldset>	
</form>
</div>