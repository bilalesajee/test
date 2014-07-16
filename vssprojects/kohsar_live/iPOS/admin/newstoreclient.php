<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
// client/store countries
$clientcountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1");
$clientcountrysel		=	"<select name=\"clientcountries\" id=\"clientcountries\" onchange=\"getcity3(this.value)\"><option value=\"\">Select Country</option>";
for($i=0;$i<sizeof($clientcountries);$i++)
{
	$countryname		=	$clientcountries[$i]['countryname'];
	$countryid			=	$clientcountries[$i]['pkcountryid'];
	$clientcountrysel2	.=	"<option value=\"$countryid\">$countryname</option>";
}
$clientcountries		=	$clientcountrysel.$clientcountrysel2."</select>";
// end client/store countries
// client/store states
$states			=	$AdminDAO->getrows("state","*","isdeleted<>1");
$statesel		=	"<select name=\"state\" id=\"state\" ><option value=\"\">Select State </option>";
for($i=0;$i<sizeof($states);$i++)
{
	$statename			=	$states[$i]['statename'];
	$stateid			=	$states[$i]['pkstateid'];
	$statesel2	.=	"<option value=\"$stateid\">$statename</option>";
}
$state		=	$statesel.$statesel2."</select>";
// end client/store states
$type	=	$_GET['type'];
if($type==1)
{
?>
<form id="newstorefrm">
<table style="background-color:#FFC;border:1px solid #F96;padding:5px;">
<tr><td colspan="4" align="center"><b>Add New Store</b></td></tr>
<tr>
    <td>Store Name:</td>
    <td><input type="text" name="newstorename" id="newstorename" value="<?php echo $newstorename;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Email:</td>
    <td><input type="text" name="email" id="email" value="<?php echo $email;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
    <td>Address:</td>
    <td><input type="text" name="address" id="address" value="<?php echo $address;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Country:</td>
    <td><?php echo $clientcountries; ?></td>
</tr>
<tr>
    <td>City:</td>
    <td><span id="clientcities"></span>&nbsp;<input type="text" name="newclientcities" id="newclientcities" onkeydown="javascript:if(event.keyCode==13) {return false;}"  /></td>
    <td>State:</td>
    <td><?php echo $state;?></td>
</tr>
<tr>
    <td>Zip:</td>
    <td><input type="text" name="zip" id="zip" value="<?php echo $zip;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Phone:</td>
    <td><input type="text" name="phone" id="phone" value="<?php echo $phone;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
    <td>Fax:</td>
    <td colspan="3"><input type="text" name="fax" id="fax" value="<?php echo $fax;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
	<td colspan="4">
    <div class="buttons">
                <button type="button" class="positive" onclick="addstore();"> <img src="../images/tick.png" alt=""/>
                Save
                </button>
                <a href="javascript:void(0);" onclick="document.getElementById('newstoreclient').style.display='none';" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div>
    </td>
</tr>
</table>
</form>
<?php
}
else
{
?>
<form id="newclientfrm">
<table style="background-color:#FFC;border:1px solid #F96;padding:5px;">
<tr><td colspan="4" align="center"><b>Add New Client</b></td></tr>
<tr>
    <td>First Name:</td>
    <td><input type="text" name="fname" id="fname" value="<?php echo $fname;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Last Name:</td>
    <td><input type="text" name="lname" id="lname" value="<?php echo $lname;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
    <td>Company Name:</td>
    <td><input type="text" name="company" id="company" value="<?php echo $company;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Email:</td>
    <td><input type="text" name="email" id="email" value="<?php echo $email;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
    <td>Mobile:</td>
    <td><input type="text" name="mobile" id="mobile" value="<?php echo $mobile;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Address1:</td>
    <td><input type="text" name="address1" id="address1" value="<?php echo $address1;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
    <td>Address2:</td>
    <td><input type="text" name="address2" id="address2" value="<?php echo $address2;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Country:</td>
    <td><?php echo $clientcountries; ?></td>
</tr>
<tr>
    <td>City:</td>
    <td><span id="clientcities"></span>&nbsp;<input type="text" name="newclientcities" id="newclientcities" onkeydown="javascript:if(event.keyCode==13) {return false;}"  /></td>
    <td>State:</td>
    <td><?php echo $state;?></td>
</tr>
<tr>
    <td>Zip:</td>
    <td><input type="text" name="zip" id="zip" value="<?php echo $zip;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Phone:</td>
    <td><input type="text" name="phone" id="phone" value="<?php echo $phone;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
    <td>Fax:</td>
    <td><input type="text" name="fax" id="fax" value="<?php echo $fax;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>Username:</td>
    <td><input type="text" name="username" id="username" value="<?php echo $username;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
    <td>Password:</td>
    <td><input type="password" name="password" id="password" value="<?php echo $password;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
    <td>NIC #:</td>
    <td><input type="text" name="nic" id="nic" value="<?php echo $password;?>" onkeydown="javascript:if(event.keyCode==13) {return false;}" /></td>
</tr>
<tr>
	<td colspan="4">
    <div class="buttons">
                <button type="button" class="positive" onclick="addclient();"> <img src="../images/tick.png" alt=""/>
                Save
                </button>
                <a href="javascript:void(0);" onclick="document.getElementById('newstoreclient').style.display='none';" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div>
    </td>
</tr>
</table>
</form>
<?php
}
?>