<?php
include("../includes/security/adminsecurity.php");

global $AdminDAO,$userSecurity;
$payeeid		=	$_REQUEST['id'];
if($payeeid!='')
{
	$accheadarray	=	$AdminDAO->getrows("$dbname_detail.account"," * "," id='$payeeid' ");
	$accounttitle	=	$accheadarray[0]['title'];
	$accountlimit	=	$accheadarray[0]['accountlimit'];
	$status			=	$accheadarray[0]['status'];	
}
?>
<script language="javascript" type="text/javascript">
function savecustomer()
{
	
		if(document.getElementById('acctitle').value == '')
		{
			alert('Please add Account Title to continue ');
			document.getElementById('nicno').focus();
			return false;
		}		
		else
		{
			loading('Saving Payee Data ...');
			options	=	{	
					url : 'savepayee.php?new=2',
					type: 'POST',
					success: customerresponse
				}
		//alert('now i am saving new customer form');
		jQuery('#newcustomerfrm').ajaxSubmit(options);
		
		}
		//notice("Customer data has been saved.",0,3000);
}
function customerresponse(text)
{
	if(text=='')
	{
		adminnotice("A Payee account has been saved.",0,5000);
		jQuery('#maindiv').load("managepayee.php");
	}
	else
	{
		adminnotice(text,0,1000);
	}
}
</script>
<div id="newcustomer">
<div id="error" class="notice" style="display:none"></div>
<form name="newcustomerfrm" id="newcustomerfrm" onSubmit="savecustomer(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	<?php 
		if($payeeid == '-1')
		{
			print"Adding New Payee";
		}
		else
		{
			print"Editing: $firstname";
		}
	?>
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="savecustomer();">
        <img src="../images/tick.png" alt=""/> 
        <?php //if($brandid=='-1'){echo 'Save';}else{echo 'Update';}?>Save
    </button>
     <a href="javascript:void(0);" onclick="hidediv('newcustomer');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>
<table>
	<tbody>
    
	<tr>
	  <td>Account Title:</td>
	  <td><input type="text" name="acctitle" id="acctitle" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $accounttitle;?>"/></td>
	  </tr>
    <tr>
        <td>Status:</td>
        <td><select name="status" id="status" style="width:135px;">
                <option value="1" <?php if($status==1){echo "selected=\"selected\"";} ?> >Active</option>
                <option value="0" <?php if($status==0){echo "selected=\"selected\"";} ?>>Inactive</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Account Limit:</td>
        <td><input type="text" name="acclimit" id="acclimit" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $accountlimit;?>"/></td>
    </tr>    

	<tr>
    <td colspan="3">
	    <input type="hidden" name="payeeid" value="<?php echo $payeeid;?>" />       
         <div class="buttons">
            <button type="button" class="positive" onclick="savecustomer();">
                <img src="../images/tick.png" alt=""/> 
                <?php //if($brandid=='-1'){echo 'Save';}else{echo 'Update';}?>Save
            </button>
             <a href="javascript:void(0);" onclick="hidediv('newcustomer');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
        </div>
	</td>
	</tr>
    </tbody>
</table>
</form>
</div>