<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
//echo $id			=	$_REQUEST['id'];
$id			=	$_REQUEST['id']; //line added by Ahsan - 06/02/2012 
$fkscreenid	=	$_GET['param'];
if($id!=-1)
{
	$actions		=	$AdminDAO->getrows("action","actionlabel,actioncode,fkscreenid","pkactionid='$id'");
	$actionlabel	=	$actions[0]['actionlabel'];
	$actioncode		=	$actions[0]['actioncode'];
	$fkscreenid		=	$actions[0]['fkscreenid'];
}
?>
<script language="javascript">
$().ready(function(){
	document.getElementById('actionlabel').focus();				 
});
function addaction(id)
{
	options	=	{	
					url : 'insertaction.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#actionfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Action has been saved.',0,5000);
		jQuery('#sugrid').load('manageactions.php?id='+<?php echo $fkscreenid;?>);
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="actionfrmdiv" style="display: block;"> <br>
  <form id="actionfrm" style="width: 920px;" class="form">
    <fieldset>
      <legend>
      <?php
    if($id!="-1")
    { echo "Edit Action"." ".$fieldname;}
    else
    { echo "Add Action";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="addaction(-1);"> <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('actionfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
      <table width="100%">
        <tr>
          <td height="10" valign="top"><div class="topimage2" style="height:6px;">
              <!-- -->
            </div>
            <table cellpadding="2" cellspacing="0" width="100%" >
              <tbody>
                <tr>
                  <th align="left" width="11%">Action Label</th>
                  <th align="left" width="11%">Action Code</th>
                </tr>
                <tr class="even">
                  <td><input name="actionlabel" id="actionlabel" class="text" value="<?php echo $actionlabel; ?>" onKeyDown="javascript:if(event.keycode==13){addaction(); return false;}" type="text" ></td>
                  <td><input type="text" class="text" value="<?php echo $actioncode;?>" name="actioncode" id="actioncode" onkeydown="javascript:if(event.keycode==13){addaction(); return false;}" maxlength="1"  /></td>
                </tr>
                <tr>
                  <td colspan="5" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="addaction(-1);"> <img src="../images/tick.png" alt=""/>
                      <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                      </button>
                      <a href="javascript:void(0);" onclick="hidediv('actionfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
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
	focusfield('actioncode');
</script>