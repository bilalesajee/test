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
	jQuery('#mainpanel').load("customers.php");
	
	//notice("Customer data has been saved.",0,3000);
}
function customerresponse(text)
{
	if(text=='')
	{
		notice("Customer data has been saved.",0,3000);
	}
	else
	{
		notice(text,0,10000);
	}
}
</script>
<?php
include("includes/security/adminsecurity.php");

global $AdminDAO,$userSecurity;
$customerid	=	$_REQUEST['id'];
$customerarray	=	$AdminDAO->getrows("$dbname_detail.account"," * "," id='$customerid' ");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
$addressbookid	=	$customerarray[0]['fkaddressbookid'];
$ctype			=	$customerarray[0]['ctype'];
$addbookarray	=	$AdminDAO->getrows("$dbname_detail.addressbook"," * "," pkaddressbookid='$addressbookid' ");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
//print_r($addbookarray);
$firstname		=	$addbookarray[0]['firstname'];
$lastname		=	$addbookarray[0]['lastname'];
$phone			=	$addbookarray[0]['phone'];
$nic			=	$addbookarray[0]['nic'];
$address1		=	$addbookarray[0]['address1'];
$address2		=	$addbookarray[0]['address2'];
$email			=	$addbookarray[0]['email'];
$fax			=	$addbookarray[0]['fax'];
$companyname	=	$customerarray[0]['title'];
$taxnumber		=	$customerarray[0]['taxnumber'];
$ntn			=	$customerarray[0]['ntn'];
?>
<div id="newcustomer">
<form id="newcustomerfrm">
<table class="price">
	<tr>
    <th width="20%"><span class="compulsory">Type</span></th>
    <td width="30%">
    	<select name="ctype" id="ctype" style="width:155px;">
        	<option value="1" <?php if($ctype==1){echo "selected=\"selected\"";} ?> >Hotel</option>
            <option value="2" <?php if($ctype==2){echo "selected=\"selected\"";} ?>>Creditor</option>
    	</select>
    </td>
	</tr>
	<tr>
    <th width="20%"><span class="compulsory">First Name</span></th>
    <td width="30%"><input type="text" name="newfname" id="newfname" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $firstname;?>"/></td>
	</tr>
	<tr>
    <th>Last Name</th>
    <td><input type="text" name="newlname" id="newlname" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $lastname;?>"/></td>
    </tr>
    <tr>
    <th>Email</th>
    <td><input type="text" name="email" id="email" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $email;?>"/></td>
    </tr>
    <tr>
    <th>Company</th>
    <td><input type="text" name="company" id="company" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $companyname;?>"/></td>
    </tr>
    <tr>
    <th>Address Line 1</th>
    <td><input type="text" name="address1" id="address1" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $address1;?>"/></td>
    </tr>
    <tr>
    <th>Address Line 2</th>
    <td><input type="text" name="address2" id="address2" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $address2;?>"/></td>
    </tr>
	<tr>
    <th><span class="compulsory">Phone Number</span></th>
    <td><input type="text" name="newphone" id="newphone" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $phone;?>"/></td>
    </tr>
    <tr>
    <th>Fax</th>
    <td><input type="text" name="fax" id="fax" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $fax;?>"/></td>
    </tr>
	<tr>
    <th><span class="compulsory">NIC #</span></th>
    <td><input type="text" name="nicno" id="nicno" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $nic;?>"/></td>
    </tr>
    <tr>
    <th>Sales Tax #</th>
    <td><input type="text" name="taxnumber" id="taxnumber" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $taxnumber;?>"/></td>
    </tr>
    <th>NTN</th>
    <td><input type="text" name="ntn" id="ntn" class="text" onkeydown="javascript:if(event.keyCode==13) {savecustomer(1); return false;}" value="<?php echo $ntn;?>"/></td>
    </tr>
    <tr>
	<tr>
    <td colspan="3">
    <input type="hidden" name="customerid" value="<?php echo $customerid;?>" />
    <input type="hidden" name="addressbookid" value="<?php echo $addressbookid;?>" />
    <span class="buttons">
    <!--<input type="button" name="savenewcust" id="savenewcust" value="Save" onclick="savecustomer(1)" />-->
	 <button type="button"name="savenewcust" id="savenewcust" value="Save" onclick="savecustomer(1)" title="Save" style="font-size:12px;">
            <img src="images/disk.png" alt=""/> 
           Save
        </button>
	 <button type="button" name="button2" id="button2" onclick="hidediv('childdiv');" title="Cancel" style="font-size:12px;">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
    </span>
	</td>
	</tr>
</table>
</form>
</div>