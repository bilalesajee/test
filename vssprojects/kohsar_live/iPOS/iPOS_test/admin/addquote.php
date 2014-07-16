<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
$qstring=$_SESSION['qstring'];
if($id!='')
{
	$quotes = $AdminDAO->getrows("$dbname_detail.purchaseorder","quotetitle,FROM_UNIXTIME(deadline,'%d-%m-%Y') as deadline,ponum,fkaccountid,terms,status,expired"," pkpurchaseorderid='$id'");
	$quotetitle		= 	$quotes[0]['quotetitle'];
	$customerid 	= 	$quotes[0]['fkaccountid'];
	$deadline		=	$quotes[0]['deadline'];
	$ponum			=	$quotes[0]['ponum'];
	$terms			=	$quotes[0]['terms'];
	$status		 	= 	$quotes[0]['status'];
	$expired		=	$quotes[0]['expired'];
}
//selecting customers
$customerarr	=	$AdminDAO->getrows("main.customer","concat(firstname,' ',lastname) as name,pkcustomerid id","ctype=1 and location=3");
$d1			=	"<select name=\"customer\" id=\"customer\" style=\"width:150px;\">";
for($i=0;$i<sizeof($customerarr);$i++)
{
	$select		=	"";
	if($customerid==$customerarr[$i]['id'])
	{
		$select	=	"selected=\"selected\"";
	}
	$d2			.=	"<option value = \"".$customerarr[$i]['id']."\" $select>".$customerarr[$i]['name']."</option>";
}
$customers		=	$d1.$d2."</select>";
//end customers
?>

<script language="javascript">
jQuery().ready(function() 
{
	$("#deadline").mask("99-99-9999");
});
function addquote(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertquote.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#quoteform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Quote Saved...');
		jQuery('#maindiv').load('managequotes.php?'+'<?php echo $qstring?>');
		document.getElementById('quotefrmdiv').style.display	=	'none';
		
	}
	else
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
}
function hideform()
{
	document.getElementById('quotefrmdiv').style.display	=	'none';	
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="quotefrmdiv" style="display: block;">
<br>
<form id="quoteform" style="width: 920px;" action="insertquote.php?id=-1" class="form">
<fieldset>
<legend>
	<?php
    if($id!="-1")
    { echo "Edit Quote"." ".$quotename;}
    else
    { echo "Add Quote";}	
    ?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addquote(-1);">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('quotefrmdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
</div>
<table cellpadding="0" cellspacing="2" width="100%">
	<tbody>
	<tr>
		<td>Quotation Title</td>
		<td>

		<input name="quotetitle" id="quotetitle" value="<?php echo $quotetitle; ?>" onkeydown="javascript:if(event.keyCode==13) {addquote(); return false;}" type="text"></td>
	</tr>
	<tr>
		<td>Customer</td>
		<td><?php echo $customers;?></td>
	</tr>
    <tr>
		<td>Deadline</td>
		<td><input type="text" id="deadline" name="deadline" size="10" maxlength="10" value="<?php echo $deadline;?>" /></td>
	</tr>
    <tr>
		<td>PO Number</td>
		<td><input type="text" id="ponum" name="ponum" maxlength="30" value="<?php echo $ponum;?>" /></td>
	</tr>
    <tr>
		<td>Terms</td>
		<td><textarea id="terms" name="terms"><?php echo $terms;?></textarea></td>
	</tr>
	<tr>
	  
	  <td>Status: </td>
	  <td>Quote <input name="status" type="radio" value="1" <?php if($status==1) echo "checked"; ?>> Purchase Order <input name="status" type="radio" value="2" <?php if($status==2) echo "checked"; ?>></td>
	  </tr>
    <tr>
	  
	  <td>Expired</td>
	  <td>Yes<input name="expired" type="radio" value="0" <?php if($expired==0) echo "checked"; ?>> No <input name="expired" type="radio" value="1" <?php if($expired==1) echo "checked"; ?>></td>
	  </tr>
	<tr>
	  <td colspan="2" align="center">
<!--	    <input value="Save" onclick="addnote(-1)" type="button"><input name="btnsubmit" value="Cancel" onclick="hideform()" type="button">-->
        <div class="buttons">
            <button type="button" class="positive" onclick="addquote(-1);">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('quotefrmdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>				
	  </tr>
	</tbody>
</table>
<input type="hidden" name="id" value ="<?php echo $id;?>" />
</fieldset>	
</form>
</div>