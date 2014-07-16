<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_REQUEST['id'];
if($id!=-1)
{
	$screens		=	$AdminDAO->getrows("screen","screenname,fkmoduleid,url,visibility,displayorder","pkscreenid='$id'");
	$screenname		=	$screens[0]['screenname'];
	$fkmoduleid		=	$screens[0]['fkmoduleid'];
	$url			=	$screens[0]['url'];
	$visibility		=	$screens[0]['visibility'];
	$displayorder	=	$screens[0]['displayorder'];
}
// selecting modules 
$modulearray		= 	$AdminDAO->getrows("module","*","status=1");
$modulesel		=	"<select name=\"module\" id=\"module\" style=\"width:170px;\" ><option value=''>Select Module</option>";
for($i=0;$i<sizeof($modulearray);$i++)
{
	$modulename		=	$modulearray[$i]['modulename'];
	$moduleid		=	$modulearray[$i]['pkmoduleid'];
	$select		=	"";
	if($moduleid == $fkmoduleid)
	{
		$select = "selected=\"selected\"";
	}
	$modulesel2	.=	"<option value=\"$moduleid\" $select>$modulename</option>";
}
$modules			=	$modulesel.$modulesel2."</select>";
// end modules
?>
<script language="javascript">
$().ready(function(){
	document.getElementById('screenname').focus();				 
});
function addscreen(id)
{
	options	=	{	
					url : 'insertscreen.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#screenfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Screen has been saved.',0,5000);
		jQuery('#maindiv').load('managescreens.php');
		
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="screenfrmdiv" style="display: block;"> <br>
  <form id="screenfrm" style="width: 920px;" action="insertscreen.php?id=-1" class="form">
    <fieldset>
      <legend>
      <?php
    if($id!="-1")
    { echo "Edit Screen"." ".$screenname;}
    else
    { echo "Add Screen";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="addscreen(-1);"> <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('screenfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
      <table width="100%">
        <tr>
          <td height="10" valign="top"><div class="topimage2" style="height:6px;">
              <!-- -->
            </div>
            <table cellpadding="2" cellspacing="0" width="100%" >
              <tbody>
                <tr>
                  <th align="left" width="11%">Screen Name</th>
                  <th align="left" width="11%">File</th>
                  <th align="left" width="11%">Module</th>
                  <th width="11%" align="left">Visibility</th>
				  <th width="11%" align="left">Display Order</th>
			    </tr>
                <tr class="even">
                  <td><input name="screenname" id="screenname" class="text" value="<?php echo $screenname; ?>" onKeyDown="javascript:if(event.keycode==13){addscreen(); return false;}" type="text" ></td>
                  <td><input type="text" class="text" value="<?php echo $url;?>" name="filename" id="filename" onkeydown="javascript:if(event.keycode==13){addscreen(); return false;}"  /></td>
                  <td><?php echo $modules; ?></td>
                  <td><select name="visibility" id="visibility" style="width:170px;"><option value="">Select Visibility</option><option value="2" <?php if($visibility==2) {?> selected="selected" <?php }?>>Both</option><option value="1" <?php if($visibility==1) {?> selected="selected" <?php }?>>Main</option><option value="3" <?php if($visibility==3) {?> selected="selected" <?php }?>>Local</option></select></td>
				  <td><input type="text" class="text" value="<?php echo $displayorder;?>" name="displayorder" id="displayorder" onkeydown="javascript:if(event.keycode==13){addscreen(); return false;}"  /></td>
			    </tr>
                <tr>
                  <td colspan="5" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="addscreen(-1);"> <img src="../images/tick.png" alt=""/>
                      <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                      </button>
                      <a href="javascript:void(0);" onclick="hidediv('screenfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
                </tr>
              </tbody>
            </table></td>
        </tr>
      </table>
      <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
    </fieldset>
  </form>
</div>
<script language="javascript">
	focusfield('screenname');
</script>