<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$brandid = $_GET['id'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
	$param	=	$_GET['param'];	
	if($param=='suppliers')
	{
		$curdiv="subsection";
	}
	else
	{
		$curdiv="maindiv";
	}
}//end edit
$qs	=	$_SESSION['qstring'];
$selected_suppliers	=	array();
$selected_countries	=	array();
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
	$selected_brands	=	array();
}//end edit
if($brandid=="")
{
	$brandid="-1";
}
else if($brandid !="-1")
{
	$brand = $AdminDAO->getrows('brand','*',"`pkbrandid`='$brandid' AND branddeleted != '1'");
	$brandname = $brand[0]['brandname'];
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
		$selected_brands[0] = $brand[0]['fkparentbrandid'];
	}//end edit
	$selected_countries[0]	=	$brand[0]['fkcountryid'];
	$suppliers		=	$AdminDAO->getrows('brandsupplier','fksupplierid',"`fkbrandid`='$brandid' AND brandsupplierdeleted !='1'");
	foreach($suppliers as $suppliers1)
	{
		$selected_suppliers[]	=	$suppliers1['fksupplierid'];
	}
}

/********************************COUNTRIES***********************************/
$countries_array	=	$AdminDAO->getrows('countries','*', ' countriesdeleted != 1');
if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012
	$countries			=	$Component->makeComponent('d','countries',$countries_array,'pkcountryid','countryname',1,$selected_countries);
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
	$countries			=	$Component->makeComponent('d','countries',$countries_array,'pkcountryid','code3',1,$selected_countries);
	/****************************************************************************************************************/
	$parentbrands_array		=	$AdminDAO->getrows('brand','*', " fkparentbrandid = 0 AND pkbrandid <> $brandid");
	
	$parentbrands			=	$Component->makeComponent('d','parentbrands',$parentbrands_array,'pkbrandid','brandname',1,$selected_brands);
	//dump($parentbrands);
}//end edit
/***********************************************SUPPLIER***********************************/
$suppliers_array	=	$AdminDAO->getrows('supplier','pksupplierid,companyname', ' supplierdeleted <> 1');
//$suppliers			=	$Component->makeComponent('d','suppliers[]',$suppliers_array,'pksupplierid','companyname',10,$selected_suppliers,"onchange=clearfield(this.value,'nsupplier')");
/*if($brandid!="-1" && sizeof($selected_suppliers)<1)
{*/
	$none	=	" selected=\"selected\" ";
/*}*/
$suppliers1			=	"<select name=\"suppliers[]\" id=\"suppliers[]\" multiple=multiple size=5 ><option value='' ".$none.">None</option>";
for($i=0;$i<sizeof($suppliers_array);$i++)
{
	if(in_array($suppliers_array[$i]['pksupplierid'],$selected_suppliers) && $brandid !="-1")
	{
		$selected		=	" selected=\"selected\" ";
		$suppliers2		.=	"<option value = \"".$suppliers_array[$i]['pksupplierid'].$selected."\">".$suppliers_array[$i]['companyname']."</option>";
	}
	else
	{
		$suppliers2		.=	"<option value = \"".$suppliers_array[$i]['pksupplierid']."\">".$suppliers_array[$i]['companyname']."</option>";
	}
}
$suppliers		=	$suppliers1.$suppliers2."</select>";

/****************************************************************************/
?>

<script language="javascript">
/*function loadsuppliers(div,id,url)
{
	$('#'+div).load(url+'?id='+id);
}*/
function addform()
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'insertbrand.php',
					type: 'POST',
					success: response
				}
	jQuery('#brandform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Brand data has been saved.',0,5000);
		jQuery('#maindiv').load('managebrands.php?'+'<?php echo $qs?>');
		hidediv('brandiv');
	}
	else
	{
		adminnotice(text,0,5000);	
	}
	//hideform();
}
function hideform()
{
	
	document.getElementById('brandiv').style.display='none';
}
</script>
<?php


?>
<div id="brandiv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="brandform" id="brandform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	<?php 
		if($brandid == '-1')
		{
			print"Adding New Brand";
		}
		else
		{
			print"Editing: $brandname";
		}
	?>
</legend>
<div style="float:right">
<?php if($_SESSION['siteconfig']!=1){ //edit by ahsan 14/02/2012, if condition added?>
    <span class="buttons">
        <button type="button" class="positive" onclick="addform();">
            <img src="../images/tick.png" alt=""/> 
            <?php if($brandid=='-1'){echo 'Save';}else{echo 'Update';}?>
        </button>
         <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
      </span>
  <?php }?>
  <?php //from main, edit by ahsan 14/02/2012
  		if($_SESSION['siteconfig']!=3)
	   		 buttons('insertbrand.php','brandform',$curdiv,'managebrands.php',$place=1,$formtype)
	//end edit?> 
</div>
<table>
	<tbody>
	<tr>
		<td>Brand Name: <?php if($_SESSION['siteconfig']!=3) {?><span class="redstar" title="This field is compulsory">*</span> <?php } //from main, edit by ahsan 14/02/2012?></td>
		<td colspan="2"><?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?><div id="error1" class="error" style="display:none; float:right;"><?php } ?></div>
		<input name="brand" id="brand" type="text" value="<?php echo $brandname; ?>" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}"></td>
	</tr>
	<?php if($_SESSION['siteconfig']!=3){ //from main, edit by ahsan 14/02/2012?>
    <tr>
		<td>Parent Brand: </td>
		<td colspan="2">
        	<div id="error1" class="error" style="float:right;"><?php echo $parentbrands;?></div>
		</td>
	</tr>
	<?php } //end edit?>    
	<tr>
		<td>Brand Country: </td>
		<td colspan="2"><div id="error2" class="error" style="display:none; float:right;"></div><?php echo $countries ?></td>
	</tr>
	<tr>
		<td>Brand Suppliers: </td>				
		<td valign="top"><div id="error3" class="error" style="display:none; float:right;"></div><div id="suppliers"><?php echo $suppliers ?></div></td>
		<!--<td valign="top"><input name="nsupplier" id="nsupplier" type="text" value="Add New" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onFocus="if(this.value=='Add New')this.value='';"/></td>-->
	</tr>
	<tr>
		<td colspan="3"  align="left">
        <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
		<div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($brandid=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
         <?php }?>
		  <?php
		  if($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   		 buttons('insertbrand.php','brandform',$curdiv,'managebrands.php',$place=0,$formtype)
			//end edit?> 
        </td>				
	</tr>
	</tbody>
</table>
</fieldset>	
<input type=hidden name="brandid" value = <?php echo $brandid?> />	
</form>
</div><br />
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
<script language="javascript">
document.brandform.brand.focus();
loading('Loading Form...');
</script>
<?php }elseif($sitecofnig==1){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">
	focusfield('brand');
</script>
<?php }//end edit?>