<?php
include("../includes/security/adminsecurity.php");

global $AdminDAO,$userSecurity;
$customerid		=	$_REQUEST['id'];
$customerarray	=	$AdminDAO->getrows("customer"," * "," pkcustomerid='$customerid' ");
$ctype			=	$customerarray[0]['ctype'];
$stype			=	$customerarray[0]['location'];
$firstname		=	$customerarray[0]['firstname'];
$lastname		=	$customerarray[0]['lastname'];
$phone			=	$customerarray[0]['phone'];
$nic			=	$customerarray[0]['nic'];
$address1		=	$customerarray[0]['address1'];
$address2		=	$customerarray[0]['address2'];
$email			=	$customerarray[0]['email'];
$fax			=	$customerarray[0]['fax'];
$companyname	=	$customerarray[0]['companyname'];
$taxnumber		=	$customerarray[0]['taxnumber'];
$ntn			=	$customerarray[0]['ntn'];
$brandid=$customerid;
?>
<script language="javascript" type="text/javascript">
function savecustomer()
{
	
		if(document.getElementById('newfname').value == '')
		{
			alert('Please add First Name to continue ');
			document.getElementById('newfname').focus();
			return false;
		}
		else if(document.getElementById('newphone').value == '')
		{
			alert('Please add Phone Number to continue ');
			document.getElementById('newphone').focus();
			return false;
		}
		else if(document.getElementById('nicno').value == '')
		{
			alert('Please add NIC Number to continue ');
			document.getElementById('nicno').focus();
			return false;
		}
		else
		{
			loading('Saving Cutomer Data ...');
			options	=	{	
					url : 'savecustomer.php?new=2',
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
		adminnotice("Customer data has been saved.",0,5000);
		jQuery('#maindiv').load("managecustomers.php");
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
		if($customerid == '-1')
		{
			print"Adding New Customer";
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
        <?php if($brandid=='-1'){echo 'Save';}else{echo 'Update';}?>
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
    <td><span class="compulsory">Customer Type:</span></td>
    <td>
    	<select name="ctype" id="ctype" style="width:135px;">
        	<option value="1" <?php if($ctype==1){echo "selected=\"selected\"";} ?> >Hotel</option>
            <option value="2" <?php if($ctype==2){echo "selected=\"selected\"";} ?>>Creditor</option>
    	</select>
    </td>
	</tr>
      <tr>
    <td><span class="compulsory">Select Location:</span></td>
    <td>
    	<select name="loc" id="loc" style="width:135px;">
        	<option value="3" <?php if($stype==3){echo "selected=\"selected\"";} ?> >Kohsar</option>
           <?php /*?> <option value="1" <?php if($stype==1){echo "selected=\"selected\"";} ?>>DHA</option>
            <option value="2" <?php if($stype==2){echo "selected=\"selected\"";} ?> >Gulberg</option>
            <option value="4" <?php if($stype==4){echo "selected=\"selected\"";} ?>>warehouse</option>
            <option value="5" <?php if($stype==5){echo "selected=\"selected\"";} ?>>Pharma</option>
   <?php */?> 	</select>
    </td>
	</tr>
	<tr>
    <td><span class="compulsory">First Name:</span></td>
    <td><input type="text" name="newfname" id="newfname" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $firstname;?>"/></td>
	</tr>
	<tr>
    <td>Last Name:</td>
    <td><input type="text" name="newlname" id="newlname" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $lastname;?>"/></td>
    </tr>
    <tr>
    <td>Email:</td>
    <td><input type="text" name="email" id="email" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $email;?>"/></td>
    </tr>
    <tr>
    <td>Company:</td>
    <td><input type="text" name="company" id="company" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $companyname;?>"/></td>
    </tr>
    <tr>
    <td>Address Line 1:</td>
    <td><input type="text" name="address1" id="address1" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $address1;?>"/></td>
    </tr>
    <tr>
    <td>Address Line 2:</td>
    <td><input type="text" name="address2" id="address2" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $address2;?>"/></td>
    </tr>
	<tr>
    <td><span class="compulsory">Phone Number:</span></td>
    <td><input type="text" name="newphone" id="newphone" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $phone;?>"/></td>
    </tr>
    <tr>
    <td>Fax:</td>
    <td><input type="text" name="fax" id="fax" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $fax;?>"/></td>
    </tr>
	<tr>
    <td><span class="compulsory">NIC #:</span></td>
    <td><input type="text" name="nicno" id="nicno" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $nic;?>"/></td>
    </tr>
    <tr>
    <td>Sales Tax #:</td>
    <td><input type="text" name="taxnumber" id="taxnumber" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $taxnumber;?>"/></td>
    </tr>
    <tr>
    <td>NTN:</td>
    <td><input type="text" name="ntn" id="ntn" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $ntn;?>"/></td>
    </tr>
    <tr>
	<tr>
    <td colspan="3">
    <input type="hidden" name="customerid" value="<?php echo $customerid;?>" />
    <input type="hidden" name="loc" value="3" />
    <input type="hidden" name="employeeid" value="<?php echo $_SESSION['employeeid'];?>" />
       <div class="buttons">
            <button type="button" class="positive" onclick="savecustomer();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($brandid=='-1'){echo 'Save';}else{echo 'Update';}?>
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