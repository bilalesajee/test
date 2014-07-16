<?php
include_once("../includes/security/adminsecurity.php");
$id	=	$_GET['id'];
if($id)
{   
	$emp			=	$AdminDAO->getrows('employee',"*"," pkemployeeid = '$id'");
	$cnic			=	$emp[0]['cnic'];
	$store			=	$emp[0]['fkstoreid'];
	$group			=	$emp[0]['fkgroupid'];
	$addressbookid	=	$emp[0]['fkaddressbookid'];
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$loginallowed	=	$emp[0]['loginallowed']; 		// <!--Added by jafer on 19-12-2011-->
	}//end edit
	$user			=	$AdminDAO->getrows('addressbook',"*"," pkaddressbookid = '$addressbookid'");
	$firstname		=	$user[0]['firstname']; 
	$lastname		=	$user[0]['lastname']; 
	$username		=	$user[0]['username'];
	$password		=	$user[0]['password']; 
	$address1		=	$user[0]['address1'];
	$address2		=	$user[0]['address2'];
	$city			=	$user[0]['fkcityid'];
	$state			=	$user[0]['fkstateid'];
	$zip			=	$user[0]['zip']; 
	$country		=	$user[0]['fkcountryid'];
	$email			=	$user[0]['email'];
	$phone			=	$user[0]['phone']; 
	$mobile			=	$user[0]['mobile'];
	$fax			=	$user[0]['fax']; 
}
$selected_country	=	array($country);
$selected_city		=	array($city);
$selected_state		=	array($state);
$selected_store		=	array($store);
$selected_group		=	array($group);
/********************************COUNTRIES***********************************/
$countries_array	=	$AdminDAO->getrows('countries','*', ' countriesdeleted != 1');
$countries			=	$Component->makeComponent('d','country',$countries_array,'pkcountryid','countryname',1,$selected_country);
$cities_array	=	$AdminDAO->getrows('city','*', ' 1');
$cities			=	$Component->makeComponent('d','city',$cities_array,'pkcityid','cityname',1,$selected_city);
$states_array	=	$AdminDAO->getrows('state','*', ' 1');
$states			=	$Component->makeComponent('d','state',$states_array,'pkstateid','statename',1,$selected_state);
$stores_array	=	$AdminDAO->getrows('store','*', ' 1');
$stores			=	$Component->makeComponent('d','store',$stores_array,'pkstoreid','storename',1,$selected_store);
$groups_array	=	$AdminDAO->getrows('groups','*', ' 1');
$groups			=	$Component->makeComponent('d','group',$groups_array,'pkgroupid','groupname',1,$selected_group);
?>

<script language="javascript">
function adduser(id)
{
	loading('System is saving data....');
	options	=	{	
					url : 'insertuser.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#userform').ajaxSubmit(options);
}
	
function response(text)
{
	//alert(text.length);
	if(text=='')
	{
		//alert(text);
		loading('User Data Saved..');
		jQuery('#maindiv').load('manageusers.php?'+'<?php echo $qstring?>');
		document.getElementById('userdiv').style.display	=	'none';
		
	}
	else
	{
		<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
			document.getElementById('error').innerHTML		=	text;	
			document.getElementById('error').style.display	=	'block';
		<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
			adminnotice(text,0,6000);
		<?php }//end edit?>
	}
}
function hideform3()
{
	document.getElementById('susection').style.display='none';
}
</script>
<div id="userdiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<div id="adduserdiv">
<form enctype="multipart/form-data" name="userform" id="userform" style="width:920px;" onSubmit="adduser(); return false;" class="form">
<fieldset>
<legend>
<?php 
if($id=='-1')
{echo "Add User";}
else
{echo "Edit User $firstname $lastname";}
?>	
</legend>
<div style="float:right">
<?php /*//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
    <span class="buttons">
        <button type="button" class="positive" onclick="adduser();">
            <img src="../images/tick.png" alt=""/> 
            <?php if($id=='-1') {echo "Save";}else {echo "Update";} ?>
        </button>
         <a href="javascript:void(0);" onclick="hidediv('adduserdiv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
      </span>
<?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 17/02/2012*///add comment by ahsan 24/02/2012// 
	   	 buttons('insertuser.php','userform','maindiv','manageusers.php',$place=1,$formtype)
//end edit
?>

</div>
<table cellpadding="0" cellspacing="2" width="100%"  >
	<tbody>
	<tr >
		<td width="12%">First  Name: </td>
		<td width="88%"><?php //add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?><div id="error1" class="error" style="display:none; float:right;"><?php //add comment by ahsan 24/02/2012// }//end edit?></div>
		<input name="fname" id="fname" type="text" value="<?php echo $firstname; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}"></td>
	</tr>
	<tr >
	  <td>Last Name:</td>
	  <td><input name="lname" id="lname" type="text" value="<?php echo $lastname; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>User Name: <?php //add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php //add comment by ahsan 24/02/2012// }//end eidt?></td>
	  <td><input name="username" id="username" type="text" value="<?php echo $username; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Password:</td>
	  <td><input name="pass" id="pass" type="password" value="<?php //add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
echo $password; //add comment by ahsan 24/02/2012// }//end edit?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>CNIC Number:</td>
	  <td><input name="cnic" id="cnic" type="text" value="<?php echo $cnic; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Store:</td>
	  <td><?php echo $stores; ?></td>
	  </tr>
	<tr >
	  <td>Group:</td>
	  <td><?php echo $groups; ?></td>
	  </tr>
	<tr >
	  <td>Address1:</td>
	  <td><input name="address1" id="address1" type="text" value="<?php echo $address1; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Address2:</td>
	  <td><input name="address2" id="address2" type="text" value="<?php echo $address2; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>City:</td>
	  <td><?php echo $cities; ?></td>
	  </tr>
	<tr >
	  <td>State:</td>
	  <td><?php echo $states;?></td>
	  </tr>
	<tr >
	  <td>Zip:</td>
	  <td><input name="zip" id="zip" type="text" value="<?php echo $zip; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Country:</td>
	  <td><?php echo $countries; ?></td>
	  </tr>
	<tr >
	  <td>Email:</td>
	  <td><input name="email" id="email" type="text" value="<?php echo $email ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Phone:</td>
	  <td><input name="phone" id="phone" type="text" value="<?php echo $phone; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Mobile:</td>
	  <td><input name="mobile" id="mobile" type="text" value="<?php echo $mobile; ?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<tr >
	  <td>Fax:</td>
	  <td><input name="fax" id="fax" type="text" value="<?php echo $fax;?>" onkeydown="javascript:if(event.keyCode==13) {adduser(); return false;}" /></td>
	  </tr>
	<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
	<tr > <!--Added by jafer on 19-12-2011-->
	  <td>Status:</td>
	  <td>
          <select name="status" id="status">
          <option value="0" <?php if($loginallowed==0){echo "selected='selected'";}?>>Active</option>
          <option value="1" <?php if($loginallowed==1){echo "selected='selected'";}?>>Blocked</option>
          </select>
      </td>
	  </tr>  <!--Added by jafer on 19-12-2011-->     
    <?php }//end edit?>
	<tr >
	  <td colspan="2"  align="center">
	   <!-- <input onclick="adduser();" type="button" value="Save"><input name="button" type="button" value="Cancel" onclick="hideform()" />-->
        <?php /*//add comment by ahsan 24/02/2012// if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
		<div class="buttons">
            <button type="button" class="positive" onclick="adduser();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1') {echo "Save";}else {echo "Update";} ?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('adduserdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
		<?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 17/02/2012*///add comment by ahsan 24/02/2012// 
	   	 buttons('insertuser.php','userform','maindiv','manageusers.php',$place=0,$formtype)
	   //end edit?>
        </td>				
	  </tr>
	</tbody>
</table>
</fieldset>	
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
<input type="hidden" name="passhidden" id="passhidden" value="<?php echo base64_encode($password); ?>" />
<?php }//end edit?>
<input type="hidden" name="id" value ="<?php echo $id;?>" />
<input type="hidden" name="addressbookid" value="<?php echo $addressbookid;?>" />
</form>
</div>
</div>
<script language="javascript" type="text/javascript">
	document.getElementById('fname').focus();
</script>