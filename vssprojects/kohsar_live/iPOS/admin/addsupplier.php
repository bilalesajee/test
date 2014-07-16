<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 	=	$_REQUEST['id'];
if(strpos($id,"maindiv"))
{
	//somehow the id is coming with maindiv and hafta split it
	$newid	=	explode("maindiv",$id);
	$id		=	$newid[0];
}
if($id)
{
	//edit data
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$row 		=	$AdminDAO->getrows("supplier s,addressbook a","*"," pksupplierid= '$id' AND s.fkaddressbookid=a.pkaddressbookid");
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$row 		=	$AdminDAO->getrows("supplier s LEFT JOIN typecode ON (fktypecodeid=pktypecodeid),addressbook a","*"," pksupplierid= '$id' AND s.fkaddressbookid=a.pkaddressbookid");
	}//end edit
	for($i = 0;$i<sizeof($row);$i++)
	{
		$firstname 				=	$row[0]['firstname'];
		$lastname 				=	$row[0]['lastname'];		
		$address1 				=  	$row[0]['address1'];
		$address2				=  	$row[0]['address2'];		
		$selected_city[]		=	$row[0]['fkcityid'];//this should go to a different table and take ids
		$selected_state[]		=	$row[0]['fkstateid'];//this should go to a different table and take ids
		$zipcode				=	$row[0]['zip'];		
		$selected_country[]		= 	$row[0]['fkcountryid'];//this should go to a different table and take ids
		$phone 					= 	$row[0]['phone'];
		$mobile					= 	$row[0]['mobile'];
		$fax					= 	$row[0]['fax'];		
		$email					=	$row[0]['email'];
		$password 				=	$row[0]['password'];
		$username 				=	$row[0]['username'];
		$companyname			=	$row[0]['companyname'];
		$contactperson1			=	$row[0]['contactperson1'];
		$contactperson2			=	$row[0]['contactperson2'];
		$url					=	$row[0]['url'];		
		if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
			$suppliercode			=	$row[0]['suppliercode'];
			$selected_typecode		=	$row[0]['fktypecodeid'];
			$selected_stypecode		=	$row[0]['fksuppliertypeid'];
		}//end edit
	}
}
/********************************CITIES***********************************/
$city_array	=	$AdminDAO->getrows('city','*','1');
$cities			=	$Component->makeComponent('d','city',$city_array,'pkcityid','cityname',1,$selected_city,"onchange=clearfield(this.value,'addcity2')");
/********************************STATE***********************************/
$store_array	=	$AdminDAO->getrows('state','*','isdeleted!=1');
$states			=	$Component->makeComponent('d','state',$store_array,'pkstateid','statename',1,$selected_state,"onchange=clearfield(this.value,'addstate2')");
/********************************COUNTRIES***********************************/
$countries_array	=	$AdminDAO->getrows('countries','*', ' countriesdeleted != 1');
$countries			=	$Component->makeComponent('d','countries',$countries_array,'pkcountryid','countryname',1,$selected_country,"onchange=clearfield(this.value,'addcountry2')");
/****************************************************************************/
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	/********************************SUPPLIER TYPE CODES***********************************/
	// typecodes
	$types		=	$AdminDAO->getrows("typecode","pktypecodeid,typecodename,description","1");
	$typesel	=	"<select name=\"typecode\" id=\"typecode\" style=\"width:150px;\" ><option value=\"\">Business Type</option>";
	for($i=0;$i<sizeof($types);$i++)
	{
		$typecodename	=	$types[$i]['typecodename'];
		$typecodedesc	=	$types[$i]['description'];
		$pktypecodeid	=	$types[$i]['pktypecodeid'];
		$select		=	"";
		if($pktypecodeid == $selected_typecode)
		{
			$select = "selected=\"selected\"";
		}
		$typesel2	.=	"<option value=\"$pktypecodeid\" $select>$typecodename - $typecodedesc</option>";
	}
	$typecodes			=	$typesel.$typesel2."</select>";
	// end typecodes
	/****************************************************************************/
	/********************************SUPPLIER TYPE CODES***********************************/
	// typecodes
	$types		=	$AdminDAO->getrows("suppliertype","pksuppliertypeid,typename,typecode","1");
	$stypesel	=	"<select name=\"suppliertype\" id=\"suppliertype\" style=\"width:150px;\" ><option value=\"\">Supplier Type</option>";
	for($i=0;$i<sizeof($types);$i++)
	{
		$typename			=	$types[$i]['typename'];
		$typecode			=	$types[$i]['typecode'];
		$pksuppliertypeid	=	$types[$i]['pksuppliertypeid'];
		$select				=	"";
		if($pksuppliertypeid == $selected_stypecode)
		{
			$select = "selected=\"selected\"";
		}
		$stypesel2	.=	"<option value=\"$pksuppliertypeid\" $select>$typecode - $typename</option>";
	}
	$stypecodes			=	$stypesel.$stypesel2."</select>";
	// end typecodes
	/****************************************************************************/
}//end edit
?>

<script language="javascript">
jQuery(function($)
{
	if('<?php echo $id;?>'!='-1')
	{
		document.getElementById('addcity2').value='';
				document.getElementById('addstate2').value='';
						document.getElementById('addcountry2').value='';
	}
});
function addform()
{
	loading('System is saving The Data....');
	options	=	{	
					url : 'insertsupplier.php',
					type: 'POST',
					success: response
				}
	jQuery('#supplierform').ajaxSubmit(options);
}
function response(text)
{
	
	  if(text=="")
	  {
		//document.getElementById('error').style.display		=	'none';
		adminnotice('Supplier data has been saved.',0,5000);
		jQuery('#maindiv').load('managesuppliers.php?'+'<?php echo $qs?>');		
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function cleartext(id)
{
	if(document.getElementById(id).value=='Add New')
	{
		document.getElementById(id).value='';
	}
	<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
		else if(document.getElementById(id).value=='')
		{
			document.getElementById(id).value='Add New';
		}
	<?php }//end edit?>
}
</script>
<div id="supplierdiv">
<form enctype="multipart/form-data" name="supplierform" id="supplierform" style="width:920px;" onSubmit="addform(); return false;" class="form">
<fieldset>
<legend>
    Add Supplier	</legend>
<div style="float:right">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
    <span class="buttons">
        <button type="button" class="positive" onclick="addform();" >
            <img src="../images/tick.png" alt=""/> 
            <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
        </button>
         <a href="javascript:void(0);" onclick="hidediv('supplierdiv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
      </span>    
<?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 17/02/2012
	   	 buttons('insertsupplier.php','supplierform','maindiv','managesuppliers.php',$place=1,$formtype)
	  //end edit ?>
</div>          
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
  <tr >
    <td>Supplier Code : </td>
    <td><div id="error1" class="error" style="display:none; float:right;"></div>
        <input name="suppliercode" type="text" id="suppliercode" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" value="<?php echo $suppliercode; ?>" maxlength="10" /></td>
  </tr>
  <tr>
		<td width="14%">Supplier Type: </td>
   		<td width="86%"><?php echo $stypecodes;?></td>
  </tr>
  <tr>
		<td width="14%">Business Type: </td>
   		<td width="86%"><?php echo $typecodes;?></td>
  </tr>
  <?php }//end edit?>    
	<tr >
		<td width="14%">First  Name: </td>
		<td width="86%"><div id="error1" class="error" style="display:none; float:right;"></div>
		<input name="firstname" id="firstname" type="text" value="<?php echo $firstname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<tr >
	  <td>Last Name:</td>
	  <td><input name="lastname" id="lastname" type="text" value="<?php echo $lastname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>User Name: </td>
	  <td><input name="username" id="username" type="text" value="<?php echo $username; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Password:</td>
	  <td><input name="password" id="password" type="password" value="<?php echo $password; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Company Name:<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php }//end edit?></td>
	  <td><input name="companyname" id="companyname" type="text" value="<?php echo $companyname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Address1:</td>
	  <td><input name="address1" id="address1" type="text" value="<?php echo $address1; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Address2:</td>
	  <td><input name="address2" id="address2" type="text" value="<?php echo $address2; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>City:</td>
	  <td><?php echo $cities; ?>
         <input type="text" name="addcity2" id="addcity2" value="Add New" onfocus="cleartext(this.id)" <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?> onblur="cleartext(this.id)" <?php }//end edit?> /></td>
	  </tr>
	<tr >
	  <td>State:</td>
	  <td><?php echo $states; ?>
      <input type="text" name="addstate2" id="addstate2" value="Add New" onfocus="cleartext(this.id)" <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?> onblur="cleartext(this.id)" <?php }//end edit?> /></td>
	  </tr>
	<tr >
	  <td>Zip:</td>
	  <td><input name="zipcode" id="zipcode" type="text" value="<?php echo $zipcode; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Country:</td>
	  <td><?php echo $countries; ?>
      <input type="text" name="addcountry2" id="addcountry2" value="Add New" onfocus="cleartext(this.id)" <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?> onblur="cleartext(this.id)" <?php }//end edit?> /></td>
	  </tr>
	<tr >
	  <td>Email:</td>
	  <td><input name="email" id="email" type="text" value="<?php echo $email; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Phone:</td>
	  <td><input name="phone" id="phone" type="text" value="<?php echo $phone; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Mobile:</td>
	  <td><input name="mobile" id="mobile" type="text" value="<?php echo $mobile; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Fax:</td>
	  <td><input name="fax" id="fax" type="text" value="<?php echo $fax; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Contact Person 1: </td>
	  <td><div id="error2" class="error" style="display:none; float:right;"></div>
	    <input name="contactperson1" id="contactperson1" type="text" value="<?php echo $contactperson1; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
		<td>Contact Person 2: </td>				
		<td><div id="error3" class="error" style="display:none; float:right;"></div><div id="suppliers">
		  <input name="contactperson2" id="contactperson2" type="text" value="<?php echo $contactperson2; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" />
		</div></td>
	</tr>
	<tr >
	  <td>URL:</td>
	  <td><input name="url" id="url" type="text" value="<?php echo $url; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td colspan="2"  align="left">
        <?php 		if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
        <div class="buttons">
            <button type="button" class="positive" onclick="addform();" >
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('supplierdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 17/02/2012
	   	 buttons('insertsupplier.php','supplierform','maindiv','managesuppliers.php',$place=0,$formtype)
	  //end edit ?>

        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value ="<?php echo $id; ?>" />	
</form>
</div>
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
<script language="javascript">
	focusfield('suppliercode');
</script>
<?php }//end edit?>