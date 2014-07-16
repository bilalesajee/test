<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
$qs		=	$_SESSION['qstring'];
$id 	=	$_REQUEST['id'];
//echo $id;
if($id!=-1)
{
	//edit data
	$row 		=	$AdminDAO->getrows('store',"*"," pkstoreid= '$id'");
	for($i = 0;$i<sizeof($row);$i++)
	{
		$storename2 			=	$row[0]['storename'];
		$address 				=  	$row[0]['storeaddress'];
		$selected_city[]		=	$row[0]['fkcityid'];
		$selected_state[]		=	$row[0]['fkstateid'];
		$zip					=	$row[0]['zipcode'];		
		$selected_country[]		= 	$row[0]['fkcountryid'];
		$phone 					= 	$row[0]['storephonenumber'];		
		$email					=	$row[0]['email'];
	}
}
/********************************CITIES***********************************/
$city_array	=	$AdminDAO->getrows('city','*','1');
$cities			=	$Component->makeComponent('d','city',$city_array,'pkcityid','cityname',1,$selected_city,"onchange=clearfield(this.value,'addcity2')");
/********************************STATE***********************************/
$state_array	=	$AdminDAO->getrows('state','*','isdeleted!=1');
$states			=	$Component->makeComponent('d','state',$state_array,'pkstateid','statename',1,$selected_state,"onchange=clearfield(this.value,'addstate2')");
/********************************COUNTRIES***********************************/
$countries_array	=	$AdminDAO->getrows('countries','*', ' countriesdeleted != 1');
$countries			=	$Component->makeComponent('d','countries',$countries_array,'pkcountryid','countryname',1,$selected_country,"onchange=clearfield(this.value,'addcountry2')");

//echo "<div id='newcity' style='display:none'>$cities</div>";
/****************************************************************************/
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
					url : 'insertstore.php',
					type: 'POST',
					success: response
				}
	jQuery('#addstore').ajaxSubmit(options);
}
function response(text)
{
	if(text!='')
	{
		//document.getElementById('error').style.display		=	'block';
		adminnotice(text,0,5000);
	}
	else
	{
		jQuery('#maindiv').load('managestores.php?'+'<?php echo $qs?>');
		adminnotice("Store data saved successfully.",0,5000);
	}
}
/*function hideform()
{
	
	//document.getElementById('storediv').style.display='none';
}*/
function cleartext(id)
{
	if(document.getElementById(id).value=='Add New')
	{
		document.getElementById(id).value='';
	}
}
function originaltext(id)
{
	if(document.getElementById(id).value=='')
	{
		document.getElementById(id).value='Add New';
	}
}
/*function addnewcountry()
{
	document.getElementById('addcountry2').style.display	=	'block';
	//document.getElementById('addcountry');
}
function addnewcity()
{
	document.getElementById('addcity2').style.display	=	'block';
	//document.getElementById('addcountry');
}
function addnewstate()
{
	document.getElementById('addstate2').style.display	=	'block';
	//document.getElementById('addcountry');
}
function addnewcities()
{
	loading('System is saving data....');
	options	=	{	
					url : 'addstore.php',
					type: 'POST',
					success: response
				}
	jQuery('#addstore').ajaxSubmit(options);
}
function addnewstates()
{
	loading('System is saving data....');
	options	=	{	
					url : 'addstore.php',
					type: 'POST',
					success: response
				}
	jQuery('#addstore').ajaxSubmit(options);
}
function addnewcountries()
{
	loading('System is saving data....');
	options	=	{	
					url : 'addstore.php',
					type: 'POST',
					success: response
				}
	jQuery('#addstore').ajaxSubmit(options);
}*/
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="storediv">
<form name="addstore" id="addstore" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
<?php 
if($id=='-1')
{echo "Add Store";}
else
{echo "Edit Store: $storename";}
?>
</legend>
<div style="float:right">
<?php /*?><span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        <?php 
        if($id=='-1')
        {echo "Save";}
        else
        {echo "Update";}
        ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('storediv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span><?php */?>
 	 <?php
	   	 buttons('insertstore.php','addstore','maindiv','managestores.php',$place=1,$formtype)
	   ?>
</div>  
<table width="100%" cellpadding="0" cellspacing="0">
<tr >
<td>Store Name: <span class="redstar" title="This field is compulsory">*</span> </td>
<td><input type="text" name="storename" value="<?php echo $storename2; ?>" id="storename" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
</tr>
<tr >
<td>Address: <span class="redstar" title="This field is compulsory">*</span> </td>
<td><textarea name="address" id="address" cols="45" rows="5"><?php echo stripslashes($address); ?></textarea></td>
</tr>
<tr >
<td>City:</td><td><?php echo $cities; ?>&nbsp;<input type="text" name="addcity2" id="addcity2" value="Add New" onfocus="cleartext(this.id)" onblur="originaltext(this.id)" /></td>
</tr>
<tr >
<td>State:</td><td><?php echo $states; ?>&nbsp;<input type="text" name="addstate2" id="addstate2" value="Add New" onfocus="cleartext(this.id)" onblur="originaltext(this.id)" /></td>
</tr>
<tr >
<td>Zip:</td><td><input type="text" name="zipcode" id="zipcode" value="<?php echo $zip; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
</tr>
<tr >
<td>Country:</td><td><?php echo $countries; ?>&nbsp;<input type="text" name="addcountry2" id="addcountry2" value="Add New" onfocus="cleartext(this.id)" onblur="originaltext(this.id)" /></td>
</tr>
<tr >
<td>Phone:</td><td><input type="text" name="phone" id="phone" value="<?php echo $phone; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
</tr>
<tr >
  <td>Email:</td>
  <td><input type="text" name="email" id="email" value="<?php echo $email; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" /></td>
</tr>
<tr >
  <td colspan="2" align="center"><!--<input name="save" type="submit" id="save" value="Save" />
    <input name="cancel" type="submit" id="cancel" value="Cancel" onclick="hideform()" />-->
    <!--<div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                <?php /*
				if($id=='-1')
				{echo "Save";}
				else
				{echo "Update";}
				*///commented by ahsan 14/02/2012?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('storediv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>-->
		<?php
	   	 buttons('insertstore.php','addstore','maindiv','managestores.php',$place=0,$formtype)
	   ?>
    </td>
  </tr>
</table>
</fieldset>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />
</form>
</div>
<script language="javascript">
//document.addstore.storename.focus();
loading('Loading Form...');
</script>
<script language="javascript">
	focusfield('storename');
</script>
<br />