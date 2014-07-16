<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO,$V;
?>

<script language="javascript">
function addform()
{
	loading('Processing, please wait....');
	options	=	{	
					url : 'dbsynchaction.php',
					type: 'POST',
					success: response
				}
	jQuery('#synchdb').ajaxSubmit(options);
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
		jQuery('#maindiv').load('dbsynch.php');
		adminnotice("Database Synchnorized successfully.",0,5000);
	}
}
function hideform()
{
	
	//document.getElementById('storediv').style.display='none';
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="synchdiv">
<form name="synchdb" id="synchdb" onSubmit="addform(); return false;" style="width:920px;" class="form">
<fieldset>
<legend>
	Database Synchnorization
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="addform();">
        <img src="../images/iSync-icon.png" alt=""/> 
   		 Synch
    </button>
     <a href="javascript:void(0);" onclick="hidediv('synchdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div>  
<table width="100%" cellpadding="0" cellspacing="0">
<tr >
<td width="14%">Location Name:</td>
<td width="86%">
	<select name="storeid" id="storeid">
		<option value="">
				Select Location
			</option>
	<?php
		$row 		=	$AdminDAO->getrows('store',"pkstoreid,storename");
		for($s=0;$s<count($row);$s++)
		{
			$pkstoreid				=	$row[$s]['pkstoreid'];
			$storename2 			=	$row[$s]['storename'];
			$storedb				=	$row[$s]['storedb'];
		?>
			<option value="<?php echo $pkstoreid;?>">
				<?php echo $storename2;?>
			</option>
		<?php
		}
	?>
	</select>
</td>
<tr>
<td width="14%">Update Records:</td>
<td>
<select name="numrecs" id="numrecs">
    <option value="10">10</option>
    <option value="50">50</option>
    <option value="100">100</option>
    <option value="a">All</option>
</select>
</td>
</tr>
<tr >
  <td colspan="2" align="center"><!--<input name="save" type="submit" id="save" value="Save" />
    <input name="cancel" type="submit" id="cancel" value="Cancel" onclick="hideform()" />-->
    <div class="buttons">
            <button type="button" class="positive" onclick="addform();">
                <img src="../images/iSync-icon.png" alt=""/> 
                Synch
            </button>
             <a href="javascript:void(0);" onclick="hidediv('synchdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
    </td>
  </tr>
</table>
</fieldset>
</form>
</div>
<script language="javascript">
loading('Loading Form...');
</script>
<br />