<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$id	=	explode("maindiv",$_REQUEST['id']);
$fkdemanddetailsid	=	$id[0];
//$demandid			=	 $_SESSION['demandid'];
/****************************PRODUCT DATA*****************************/
$demanddetails_array		=	$AdminDAO->getrows('instancedemanddetails','*',"`fkdemanddetailsid`='$fkdemanddetailsid'");
$attributes	=	array();
for($i=0;$i<count($demanddetails_array);$i++)
{
	$fkproductattributeid	=	$demanddetails_array[$i]['fkproductattributeid'];
	if(!in_array($fkproductattributeid,$attributes))
	{
		$attributes_array	=	$AdminDAO->getrows('productattribute,attribute','attributename',"`fkattributeid`=`pkattributeid` AND pkproductattributeid = '$fkproductattributeid'");
		$attributes[]		=	$demanddetails_array[$i]['fkproductattributeid'];
		$attributesnames[]	=	$attributes_array[0]['attributename'];
	}
	//$options3[$fkproductattributeid]	=	$demanddetails_array[$i]['fkattributeoptionid'];
	
	//get options for the attributes... get the option names...
}
?>
<script language="javascript" type="text/javascript">
/*jQuery(function($)
{
	alert('Hello');
	$("#deadline").datepicker({dateFormat: 'yy-mm-dd'});
});*/
/*jQuery(document).ready(function()
{
	
	jQuery('#demanddetails').load('demanddetails.php');
//	$("#deadline").datepicker();
	
});*/
function addform1()
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'insertdemand.php',
					type: 'POST',
					success: response
				}
	jQuery('#frminstance').ajaxSubmit(options);
}
function response(text)
{
	if(text	== "")
	{
		text	=	"Data has been saved successfully.";
		document.getElementById('frminstance').style.display='none';
		jQuery('#demanddetails').load('demanddetails.php?id=<?php echo $demandid?>');
	}
	document.getElementById('error').innerHTML		=	text;
	document.getElementById('error').style.display	=	'block';	
}
</script>
<div id="instancediv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form id="frminstance" onSubmit="addform1(); return false;">
<input type="hidden" name="barcodeid" value="<?php echo $barcodeid."_".$demandname;?>" />
<table border="1" width="100%">
<tr>
	<th colspan="<?php echo sizeof($attributesnames);?>">
    	Item Properties
    </th>
</tr>
<tr>
  	<?php
		for($i=0;$i<sizeof($attributesnames); $i++)
		{
	?>
    <th align="left">
    <?php
			echo $attributesnames[$i];
		?>
        </th>
		<?php
//		$options_array	=	$AdminDAO->getrows('attributeoption,attribute','attributename',"`fkattributeid`=`pkattributeid` AND pkproductattributeid = '$fkproductattributeid'");
        }
		//$options_array	=	$AdminDAO->getrows('productinstance,attributeoption,barcode ','*',"`fkproductattributeid`='$attributeids[$i]' AND `fkattributeoptionid`=`pkattributeoptionid` AND fkbarcodeid=pkbarcodeid AND barcode='$barcode'");
	?>
</tr>
<tr>
  	<?php
		for($i=0;$i<sizeof($attributes); $i++)
		{
			//print"$attributes[$i]<br>";
	?>
    <td>
    <?php
		
			$options_array	=	$AdminDAO->getrows('instancedemanddetails,attributeoption','*',"fkattributeoptionid= pkattributeoptionid AND fkproductattributeid = '$attributes[$i]' AND fkdemanddetailsid = '$fkdemanddetailsid'");
			for ($k=0; $k<sizeof($options_array); $k++)
			{
				$options	.=	$options_array[$k]['attributeoptionname'].",";
			}
			$options	=	rtrim($options,",");
			echo $options;
			$options	=	"";	
		?>
        </td>
		<?php
//		$options_array	=	$AdminDAO->getrows('attributeoption,attribute','attributename',"`fkattributeid`=`pkattributeid` AND pkproductattributeid = '$fkproductattributeid'");
        }
		//$options_array	=	$AdminDAO->getrows('productinstance,attributeoption,barcode ','*',"`fkproductattributeid`='$attributeids[$i]' AND `fkattributeoptionid`=`pkattributeoptionid` AND fkbarcodeid=pkbarcodeid AND barcode='$barcode'");
	?>
</tr>
</table>
</form>
</div>

<script>
//function showdate()
//{

</script>