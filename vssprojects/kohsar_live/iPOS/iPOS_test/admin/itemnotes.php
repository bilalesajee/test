<?php

error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_GET['id'];
/********************************COUNTRIES***********************************/
$selected_barcodes	=	$AdminDAO->getrows('itemnote','*',"fknoteid = '$id'");
foreach($selected_barcodes as $barcodenote)
{
	$selected_barcode[]	=	$barcodenote['fkbarcodeid'];
}
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	$query	=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') productname, pkbarcodeid
	   FROM productattribute pa
	   RIGHT JOIN (
	   product p, attribute a
	   ) ON ( pa.fkproductid = p.pkproductid
	   AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, stock s,barcode b
	   WHERE pkproductid = pa.fkproductid
	   AND pkattributeid = pa.fkattributeid
	   AND pkproductattributeid = fkproductattributeid
	   AND pkattributeid = ao.fkattributeid
	   AND pkattributeoptionid = pi.fkattributeoptionid
	   AND b.fkproductid = pkproductid
	   AND pi.fkbarcodeid = b.pkbarcodeid
	   AND s.fkbarcodeid=b.pkbarcodeid
	   GROUP BY
	   pkbarcodeid
	   ";
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$query	=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') productname, pkbarcodeid
	   FROM productattribute pa
	   RIGHT JOIN (
	   product p, attribute a
	   ) ON ( pa.fkproductid = p.pkproductid
	   AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, barcode b
	   WHERE pkproductid = pa.fkproductid
	   AND pkattributeid = pa.fkattributeid
	   AND pkproductattributeid = fkproductattributeid
	   AND pkattributeid = ao.fkattributeid
	   AND pkattributeoptionid = pi.fkattributeoptionid
	   AND b.fkproductid = pkproductid
	   AND pi.fkbarcodeid = b.pkbarcodeid
	   GROUP BY
	   pkbarcodeid
	   ";
}//end edit
$products_array	=	$AdminDAO->queryresult($query);
$products			=	$Component->makeComponent('d','products[]',$products_array,'pkbarcodeid','productname',10,$selected_barcode);
   
?>
<!--<link rel="stylesheet" type="text/css" href="../includes/css/all.css" />
<script src="../includes/js/jquery.js" type="text/javascript"></script>
<script src="../includes/js/jquery.form.js" type="text/javascript"></script>
<script src="../includes/js/common.js" type="text/javascript"></script>-->
<script language="javascript">
function addproductnote(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertitemnote.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#noteform').ajaxSubmit(options);
}
	
function response(text)
{
	if(text=='')
	{
		loading('Note Saved...');
		jQuery('#maindiv').load('managenotes.php?'+'<?php echo $qstring?>');
		document.getElementById('notefrmdiv').style.display	=	'none';
		
	}
	else
	{
		document.getElementById('error').innerHTML		=	text;	
		document.getElementById('error').style.display	=	'block';
	}
}
function hideform()
{
	document.getElementById('notefrmdiv').style.display	=	'none';	
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="notefrmdiv" style="display: block;">
<br>
<form id="noteform" style="width: 920px;" action="insertnote.php?id=-1" class="form">
<fieldset>
<legend>
    Attach Product
</legend>
<div style="float:right">
    <span class="buttons">
        <button type="button" class="positive" onclick="addproductnote(-1);">
            <img src="../images/tick.png" alt=""/> 
            <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
        </button>
         <a href="javascript:void(0);" onclick="hidediv('notefrmdiv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
        </a>
      </span>    
</div>
<table cellpadding="0" cellspacing="2" width="100%">
	<tbody>
	<tr>
		<td>Select Product(s)</td>
		<td>
<?php echo $products;?>
</td>
	</tr>
	<tr>
	  <td colspan="2" align="center">
        <div class="buttons">
            <button type="button" class="positive" onclick="addproductnote(-1);">
                <img src="../images/tick.png" alt=""/> 
                <?php if($id=='-1'){echo 'Save';}else{echo 'Update';}?>
            </button>
             <a href="javascript:void(0);" onclick="hidediv('notefrmdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
        </td>				
	  </tr>
	</tbody>
</table>
<input type="hidden" name="noteid" value ="<?php echo $id;?>" />
</fieldset>	
</form>
</div>