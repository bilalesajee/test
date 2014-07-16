<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$productid			=	$_REQUEST['id'];
$barcodeid			=	$_REQUEST['barcode'];
$attributes_array	=	$AdminDAO->getrows("attribute a, productattribute pa","a.attributename, a.pkattributeid, pa.pkproductattributeid", " a.attributedeleted != 1 AND a.pkattributeid=pa.fkattributeid AND pa.fkproductid='$productid' AND pa.attributetype<>'n' GROUP BY a.attributename ORDER BY attributename ASC");
$o1d			=	"<select name=\"oldattribute\" id=\"oldattribute\" style=\"width:80px;\">";
for($j=0;$j<sizeof($attributes_array);$j++)
{
	$o1d2			.=	"<option value = \"".$attributes_array[$j]['pkattributeid']."\">".$attributes_array[$j]['attributename']."</option>";
}
$oldattributes		=	$o1d.$o1d2."</select>";
?>
<form id="frmexoptions" class="form" style="width:460;" onSubmit="oldoptions(); return false;">
<fieldset>
<legend>
    Options
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="oldoptions();">
        <img src="../images/tick.png" alt=""/> 
        Save
    </button>
     <a href="javascript:void(0);" onclick="hidediv('existingoptions');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
  </span>
</div><br /><br />
  <table cellpadding="0" cellspacing="2" width="100%">
    <tr>
        <td><strong>Attribute Name</strong></td>
        <td align="left"><strong>Option Name</strong><?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012 ?><span class="redstar" title="This field is compulsory">*</span><?php }//end edit?></td>
    </tr>
    <tr>
        <td>
            <?php echo $oldattributes; ?>
        </td>
        <td><input type="text" name="option[]" value="" /><br /><input type="text" name="option[]" value="" /><br /><input type="text" name="option[]" value="" /></td>                                    <input type="hidden" name="productid" value="<?php echo $productid; ?>" />
        <input type="hidden" name="barcode" value="<?php echo $barcodeid;?>" />
        <input type="hidden" name="newattribute" value="n3" />
    </tr>
    <tr>
        <td colspan="2"><div class="buttons">
        <button type="button" class="positive" onclick="oldoptions();">
            <img src="../images/tick.png" alt=""/> 
            Save
        </button>
         <a href="javascript:void(0);" onclick="hidediv('existingoptions');" class="negative">
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