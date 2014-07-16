<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$add				=	$_REQUEST['add'];
$barcode			=	$_REQUEST['barcode'];
$demandname			=	$_REQUEST['demandname'];
$demandarray		=	explode("-",$demandname);
$demandid			=	$demandarray[2];
/****************************PRODUCT DATA*****************************/
$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` AND `barcode`='$barcode'");
$productname 		=	$barcode_array[0]['productname'];
$productid	 		=	$barcode_array[0]['pkproductid'];
$barcodeid	 		=	$barcode_array[0]['pkbarcodeid'];
$productdescription =	$barcode_array[0]['productdescription'];
/************************************BRANDS DATA***************************/
$brands_array		=	$AdminDAO->getrows('barcode,barcodebrand,brand','*',"`fkbrandid`=`pkbrandid` AND fkbarcodeid=pkbarcodeid AND`barcode`='$barcode'");
/***********************************Attributes DATA*************************/
$attributes_array	=	$AdminDAO->getrows('productattribute,attribute','*',"`pkattributeid`=`fkattributeid` AND `fkproductid`='$productid'");
if (sizeof($attributes_array)==0)
{
	echo "<div align='center'><font color=red>Sorry! But No Record Found!</font></div>";
	exit;
}
?>
<!--<script src="../includes/js/jquery.form.js" type="text/javascript"></script>-->
<script src="../includes/js/datepicker/ui.datepicker.js" type="text/javascript" charset="utf-8"></script>
<script language="javascript" type="text/javascript">
jQuery(function($)
{
	$("#deadline").datepicker({dateFormat: 'yy-mm-dd'});
});

function addform1()
{
	loading('Syetem is Saving The Data....');
	options	=	{	
					url : 'insertdemand.php',
					type: 'POST',
					success: response3
				}
	jQuery('#frminstance').ajaxSubmit(options);
}
function response3(text)
{
	if(text	== "")
	{
		text	=	"Data has been saved successfully.";
		document.getElementById('frminstance').style.display='none';
		//jQuery('#subsection').load('getinstance.php?id=<?php //echo $productid?>');
		
		jQuery('#maindiv').load('managedemands.php');
		jQuery('#subsection').load('demanddetails.php?id=<?php echo $demandid?>');
		adminnotice(text,"0",5000);
		hidediv("demanddiv");
	}
	else
	{
		adminnotice(text,"0",5000);	
	}
}
</script>
<div id="instancediv">
<br />
<div id="error" class="notice" style="display:none"></div>
<form id="frminstance" onSubmit="addform1(); return false;">
<input type="hidden" name="barcodeid" value="<?php echo $barcodeid."_".$demandname;?>" />
<input type="hidden" name="add2" value="<?php print"$add";?>" />
<table width="471" border="0">
   <?php /*?> <tr>
        <td width="165">Product Name</td>
                <td width="290"><?php
			echo $productname;
		?></td>
	  </tr>
    <?php /*?><tr>
      <td>BarCode</td>
      <td><input name="barcode" type="hidden" value="<?php print"$barcode";?>" size="12" readonly="readonly"/></td>
      </tr>
    <tr><?php ?>
    <td>Brands</td>
     
    <td>
      <select name="brandname" id="brandname">
        <?php
                for($b=0;$b<sizeof($brands_array);$b++)
                {
				?>
        <option value="<?php echo $brands_array[$b]['pkbrandid'];?>">
          
          <?php
                
					echo $brands_array[$b]['brandname'];
				?>
          </option>
        <?php
			 }
            ?>
        </select>
    </td>
    </tr>
<?php
for($i=0; $i<sizeof($attributes_array); $i++)
{
?>
<tr>
  <td>
  	<?php
        
				//var_dump($attributes_array[$i]);
				$attributename[] 	=	$attributes_array[$i]['attributename'];
				$attributetype[]	=	$attributes_array[$i]['attributetype'];
				$attributeids[]		=	$attributes_array[$i]['pkproductattributeid'];
				$productattributeid	=	 $attributes_array[$i]['pkproductattributeid'];
				?>
				
				 	<?php echo $attributes_array[$i]['attributename'];?>
                
			
  </td>
  <td>
    <?php
	$options_array	=	$AdminDAO->getrows('productinstance,attributeoption,barcode ','*',"`fkproductattributeid`='$productattributeid' AND `fkattributeoptionid`=`pkattributeoptionid` AND fkbarcodeid=pkbarcodeid AND barcode='$barcode'");
	//dump($options_array);
	?>
    
    <select name="<?php  
							if ($attributetype[$i]=='m')
							{
								echo "attribute".'_'."$attributeids[$i]"."[]";
							}
							else
							{
								echo "attribute".'_'."$attributeids[$i]";
							}
					?>"
					<?php 
						if ($attributetype[$i]=='m'){ print"Multiple";}
					?>
                   >
      <?php
			//$attributeoptionname	=	array();
			for ($j=0; $j<count($options_array); $j++)
			{
				$attributeoptionname	=	$options_array[$j]['attributeoptionname'];	
				?>
      <option selected="selected" value="<?php echo $options_array[$j]['pkattributeoptionid'];?>"><?php print"$attributeoptionname";?></option>
      <?php
				//$attributeoptionid[]	=	$options_array[$j]['attributeoptionid'];
			}
			
			//echo rtrim($attributeoptionname,",");
			//echo $attributeids[$i].$attributetype[$i];
		//}
	?>
      </select>
  </td>
  </tr>
 <?php
}//for
?><?php */?>
<tr>
  <td>Units</td>
  <td><input name="units" id="units"  onkeypress="return isNumberKey(event)" size="10"/></td>
  </tr>
<tr>
  <td>Dead Line</td>
  <td><input name="deadline" type="text" id="deadline" size="10" readonly="readonly"/></td>
  </tr>
<tr>
  <td>Comments</td>
  <td><textarea name="comments" cols="40" rows="5" id="comments"></textarea></td>
  </tr>
<tr>
  <td>&nbsp;</td>
  <td>
  <input name="barcode" type="hidden" value="<?php print"$barcode";?>" size="12" readonly="readonly"/>
            <div class="buttons">
            <button type="button" class="positive" onclick="addform1();" name="btnsave">
                <img src="../images/tick.png" alt=""/> 
                Save
            </button>
             <a href="javascript:void(0);" onclick="hidediv('demandform');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </div>
    </td>
  </tr>

</table>
</form>
</div>