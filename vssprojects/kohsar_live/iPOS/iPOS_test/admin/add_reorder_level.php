<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
 $id = $_GET['id'];

$qs	=	$_SESSION['qstring'];
	if($id =="-1")
{
	$sql			=	"SELECT max(fkbarcodeid) as barcodeid  from $dbname_detail.re_order_level ";
   $result2			=	$AdminDAO->queryresult($sql);
   
  
 
}
else 
{
	//$query = $AdminDAO->getrows('$dbname_detail.re_order_level','*',"`barcodeid`='$id'");
	
		$sql2			=	"SELECT r.*,b.itemdescription from $dbname_detail.re_order_level r 
		left join barcode b on b.pkbarcodeid=r.fkbarcodeid 
		where r.fkbarcodeid='$id'";
   $result		=	$AdminDAO->queryresult($sql2);
	
	
 $fkbarcodeid = $result[0]['fkbarcodeid'];
   $barcode = $result[0]['barcode'];
	$reorderlevel = $result[0]['reorderlevel'];
	$itemdescription = $result[0]['itemdescription'];
}
?>
<script language="javascript">


		
function loaditeminfo(val)
{

	$('#iteminfo').load('loaditem.php?bc='+val);
}
$().ready(function() 
{
	document.getElementById('barcode').focus();
	function findValueCallback(event, data, formatted) 
	{
		document.getElementById('barcode').value=data[1];
		document.getElementById('itemdescription').value=data[0];
		document.getElementById('barcodeid').value=data[2];
		document.getElementById('btn2').focus();			
		$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}
	function formatItem(row) 
	{
		return row[0] + " (<strong>id: " + row[0] + "</strong>)";
	}
	function formatResult(row) 
	{
		return row[0].replace(/(<.+?>)/gi,'');
	}
	$(":text, textarea").result(findValueCallback).next().click(function() 
	{
		$(this).prev().search();
	});
	$("#clear").click(function() 
	{
		$(":input").unautocomplete();
	});
	$("#itemdescription").autocomplete("itemautocomplete.php") ;
});
//////////////////////////////////////////////
function addform()
{
	if(document.getElementById('barcode').value == '')
		{
			alert('Please Enter Barcode ');
			document.getElementById('barcode').focus();
			return false;
		}
		else if(document.getElementById('reorderlevel').value == '')
		{
			alert('Please Enter Reorder Level ');
			document.getElementById('reorderlevel').focus();
			return false;
		}
	loading('System is Saving The Data....');
	options	=	{	
					url : 'inser_reorderlevel.php',
					type: 'POST',
					success: response
				}
	jQuery('#curform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice(' data has been saved.',0,5000);
		jQuery('#maindiv').load('manage_reorder_level.php');		
	}
	else
	{
		adminnotice(text,0,5000);	
	}
}
function hideform()
{
	
	document.getElementById('curdiv').style.display='none';
}
</script>
<div id="curdiv">
<div id="iteminfo" style="display:none;"></div>
<br />
<div id="error" class="notice" style="display:none"></div>
<form name="curform" id="curform" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	 
		Add/Edit

</legend>
<div style="float:right">
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, adeed if condition?>
<span class="buttons">
    <button type="button"  class="positive" onclick="addform();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
 <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	
		  buttons('inser_reorderlevel.php','curform','maindiv','manage_reorder_level.php',$place=0,$formtype)
	 ?>    
</div>          
<table>
	<tbody>
    <?php

	?>
    <tr>
		<td width="131">Barcode:</td>
		<td width="260" colspan="2"><input type="text" class="text" id="barcode" name="barcode" value="<?php echo $barcode;?>"  onkeydown="javascript:if(event.keyCode==13) {loaditeminfo(this.value); return false;}" onfocus="this.select();" /></td>
	</tr>
	<tr>
		<td width="131">Description:</td>
		<td width="260" colspan="2"><input name="itemdescription" type="text" class="text" value="<?php echo $itemdescription; ?>" id="itemdescription" onfocus="this.select();" size="37" autocomplete="off" /></td>
	</tr>

	<tr>
		<td>Re Order Level: </td>				
		<td valign="top"><input name="reorderlevel" type="text" class="text" id="reorderlevel" onfocus="this.select();"  onkeydown="javascript:if(event.keyCode==13) {loaditeminfo(this.value); return false;}" value="<?php echo  $reorderlevel;?>" size="10" /></td>
	</tr>
	<tr>
		<td colspan="3"  align="left">
        <?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
			<div class="buttons">
         
            <button type="button"  class="positive"  onclick="addform();">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}
				else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('curdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>		
		  <?php }elseif($_SESSION['siteconfig']!=3)//from main, edit by ahsan 14/02/2012
	   		 buttons('inser_reorderlevel.php','curform','maindiv','manage_reorder_level.php',$place=0,$formtype)
		 ?>   	
          
        </td>	<input type="hidden" name="fkbarcodeid" id="fkbarcodeid" value="<?php echo $fkbarcodeid;?>" />	
        <input type="hidden" name="barcodeid" id="barcodeid" value="" />			
	</tr>
	</tbody>
</table>
</fieldset>	
<input type="hidden" name="id" value = <?php echo $id?> />	
</form>
</div><br />
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
<script language="javascript">
//document.curfrm.brand.focus();
loading('Loading Form...');
</script>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
<script language="javascript">

loading('Loading Form...');
</script>
<script language="javascript">
	focusfield('amount');
</script>
<?php }//end edit?>