<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id			=	$_REQUEST['id'];
$fkscreenid	=	$_GET['param'];
if($id!=-1)
{
	$fields			=	$AdminDAO->getrows("field","fieldname,fieldlabel,fkscreenid","pkfieldid='$id'");
	$fieldname		=	$fields[0]['fieldname'];
	$fieldlabel		=	$fields[0]['fieldlabel'];
	$fkscreenid		=	$fields[0]['fkscreenid'];
}
?>
<script language="javascript">
$().ready(function(){
	document.getElementById('fieldname').focus();				 
});
function addfield(id)
{
	options	=	{	
					url : 'insertfield.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#fieldfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Field has been saved.',0,5000);
		jQuery('#sugrid').load('managefields.php?id='+<?php echo $fkscreenid;?>);
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="fieldfrmdiv" style="display: block;"> <br>
  <form id="fieldfrm" style="width: 920px;" class="form">
    <fieldset>
      <legend>
      <?php
    if($id!="-1")
    { echo "Edit Field"." ".$fieldname;}
    else
    { echo "Add Field";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="addfield(-1);"> <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('fieldfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
      <table width="100%">
        <tr>
          <td height="10" valign="top"><div class="topimage2" style="height:6px;">
              <!-- -->
            </div>
            <table cellpadding="2" cellspacing="0" width="100%" >
              <tbody>
                <tr>
                  <th align="left" width="11%">Field Name</th>
                  <th align="left" width="11%">Field Label</th>
                </tr>
                <tr class="even">
                  <td><input name="fieldname" id="fieldname" class="text" value="<?php echo $fieldname; ?>" onKeyDown="javascript:if(event.keycode==13){addfield(); return false;}" type="text" ></td>
                  <td><input type="text" class="text" value="<?php echo $fieldlabel;?>" name="fieldlabel" id="fieldlabel" onkeydown="javascript:if(event.keycode==13){addfield(); return false;}"  /></td>
                </tr>
                <tr>
                  <td colspan="5" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="addfield(-1);"> <img src="../images/tick.png" alt=""/>
                      <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                      </button>
                      <a href="javascript:void(0);" onclick="hidediv('fieldfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
                </tr>
              </tbody>
            </table></td>
        </tr>
      </table>
      <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
      <input type="hidden" name="fkscreenid" id="fkscreenid" value="<?php echo $fkscreenid; ?>" />
    </fieldset>
  </form>
</div>
<script language="javascript">
	focusfield('fieldname');
</script>