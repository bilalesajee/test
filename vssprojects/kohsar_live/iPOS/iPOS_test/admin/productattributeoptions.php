<?php
//productattributeoptions.php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$pkattributeid		=	$_GET['pkattributeid'];
$barcodeid			=	$_GET['barcodeid'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$productid			=	$_GET['productid'];
	$attribtype			=	$AdminDAO->getrows("productattribute","attributetype","fkproductid='$productid'");
	$attributetype		=	$attribtype[0]['attributetype'];
}//end edit
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$options_array		=	$AdminDAO->getrows("attributeoption ao, attribute a,productattribute pa","DISTINCT(ao.pkattributeoptionid) as pkattributeoptionid, ao.attributeoptionname,pa.attributetype", " ao.fkattributeid=a.pkattributeid AND a.pkattributeid='$pkattributeid' and pa.fkproductid=a.pkattributeid ORDER BY attributeoptionname ASC");
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$options_array		=	$AdminDAO->getrows("attributeoption ao, attribute a,productattribute pa","DISTINCT(ao.pkattributeoptionid) as pkattributeoptionid, ao.attributeoptionname,pa.attributetype,a.attributename", " ao.fkattributeid=a.pkattributeid AND a.pkattributeid='$pkattributeid' and pa.fkattributeid=a.pkattributeid and fkproductid='$productid' ORDER BY attributeoptionname ASC");
}//end edit
if($barcodeid)
{
	$selected_options	=	$AdminDAO->getrows("productinstance","*"," fkbarcodeid = '$barcodeid'");
	foreach($selected_options as $attoptions)
	{
		$selected_option[]	=	$attoptions['fkattributeoptionid'];
	}
}
//print_r($options_array);
//print"<br><br>";
//echo $options_array[0]['attributetype'];
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	if($options_array[0]['attributetype']=='s')
	{
		//this is the section for single attributes
		?>
		
	
		<select name="attributeoptions[]" size="7" style="max-width:130px;" id='attributeoptions1'>
		<option value="">None</option>
		<?php 
		foreach($options_array as $options)
		{
			if(@!in_array($options['pkattributeoptionid'],$selected_option)) 
			{
		?>
			<option value="<?php echo $pkattributeid."_".$options['pkattributeoptionid'];?>" ><?php echo $options['attributeoptionname']; ?></option>
		<?php
			}//end of if
		}//end foreach
		?>
		</select>
	
		
		<!--<select  size="7"  name="attributeoptions[]" id="attributeoptions" style="margin-top:-117px; margin-left:250px">
		</select>-->
		
		<?php
	}//end if inner $productattributes[0]['attributetype']
	else if($options_array[0]['attributetype'] =='m')
	{
		//this is the section for multiple attributes
		?>
		<select name="attributeoptions1" multiple="multiple" size="7" id='attributeoptions1' style="max-width:130px;">
		<?php 
		foreach($options_array as $options)
		{
			if(@!in_array($options['pkattributeoptionid'],$selected_option)) 
			{
		?>
		<option value="<?php echo $pkattributeid."_".$options['pkattributeoptionid'];?>" >
		<?php
		
			echo $options['attributeoptionname'];?>
		</option>
		<?php
			}//end of if
	   }//end foreach
		?>
		</select>
		<?php
	}//end else
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	if($attributetype=='s')
	{
		//this is the section for single attributes
		?>
		<select name="attributeoptions[]" size="7" style="max-width:130px;" id='attributeoptions1'>
		<option value="">None</option>
		<?php 
		foreach($options_array as $options)
		{
			if(@!in_array($options['pkattributeoptionid'],$selected_option)) 
			{
				$attributeoptionname	=	$options['attributeoptionname'];
				$attributename			=	$options['attributename'];
		?>
			<option value="<?php echo $pkattributeid."_".$options['pkattributeoptionid'];?>" >
			<?php echo "$attributeoptionname ($attributename)";?></option>
			<?php
			}//end of if
		}//end foreach
		?>
		</select>
		<!--<select  size="7"  name="attributeoptions[]" id="attributeoptions" style="margin-top:-117px; margin-left:250px">
		</select>-->
		<?php
	}//end if inner $productattributes[0]['attributetype']
	else if($attributetype =='m')
	{
		//this is the section for multiple attributes
		?>
		<select name="attributeoptions1" multiple="multiple" size="7" id='attributeoptions1' style="max-width:130px;">
		<?php 
		foreach($options_array as $options)
		{
			if(@!in_array($options['pkattributeoptionid'],$selected_option)) 
			{
				$attributeoptionname	=	$options['attributeoptionname'];
				$attributename			=	$options['attributename'];
		?>
		<option value="<?php echo $pkattributeid."_".$options['pkattributeoptionid'];?>" >
		<?php
			echo "$attributeoptionname ($attributename)";
			?>
		</option>
		<?php
			}//end of if
	   }//end foreach
		?>
		</select>
		<?php
	}//end else
}//end edit
?>