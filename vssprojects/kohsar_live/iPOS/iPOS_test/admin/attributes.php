<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$productid			=	$_REQUEST['id'];
$barcodeid			=	$_REQUEST['barcode'];
//echo $pkattributeid."is attribute id <br>";
if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
	$options_array			=	$AdminDAO->getrows("attributeoption ao, productinstance pi","ao.pkattributeoptionid,ao.fkattributeid, ao.attributeoptionname", " pi.fkattributeoptionid=ao.pkattributeoptionid AND pi.fkbarcodeid='$barcodeid' ORDER BY attributeoptionname ASC");
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
	$options_array			=	$AdminDAO->getrows("attributeoption ao, attribute a, productinstance pi","ao.pkattributeoptionid,ao.fkattributeid, ao.attributeoptionname,a.attributename", " pi.fkattributeoptionid=ao.pkattributeoptionid AND pi.fkbarcodeid='$barcodeid' AND a.pkattributeid=ao.fkattributeid ORDER BY attributeoptionname ASC");
}//end edit
for($o=0;$o<count($options_array);$o++)
{
	$fkattributeid			=	$options_array[$o]['fkattributeid'];
	$pkattributeoptionid	=	$options_array[$o]['pkattributeoptionid'];
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$attributename			=	$options_array[$o]['attributename'];
	}//end edit
	$attributeoptionname	=	$options_array[$o]['attributeoptionname'];
	$val=$fkattributeid.'_'.$pkattributeoptionid;
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$selectedoptions.="<option value='".$pkattributeoptionid."' selected>$attributeoptionname</option>";		
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$selectedoptions.="<option value='".$fkattributeid."_".$pkattributeoptionid."' selected>$attributeoptionname ($attributename)</option>";		
	}//end edit
}
?>
				<tr>
					<td width="45%">
						Attribute <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?> <span class="redstar" title="This field is compulsory">*</span><?php }//end edit?>

					</td>
					<td width="55%">
					
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition?>
					<select name="attributeid" id="attributeid" onchange="jQuery('#attribuiteoptions').load('productattributeoptions.php?pkattributeid='+this.value+'&barcodeid=<?php echo $barcodeid;?>');">
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012?>
					<select name="attributeid" id="attributeid" onchange="jQuery('#attribuiteoptions').load('productattributeoptions.php?pkattributeid='+this.value+'&productid=<?php echo $productid;?>&barcodeid=<?php echo $barcodeid;?>');">
<?php }//end edit?>
						<option value="">Select an Attribute</option>
						<?php
						$attributes_array	=	$AdminDAO->getrows("attribute a, productattribute pa","a.attributename, a.pkattributeid, pa.pkproductattributeid", " a.attributedeleted != 1 AND a.pkattributeid=pa.fkattributeid AND pa.fkproductid='$productid' AND pa.attributetype<>'n' GROUP BY a.attributename ORDER BY attributename ASC");
						foreach($attributes_array as $attrib)
						{
							$pkattributeid	=	$attrib['pkattributeid'];
							$productattributes	=	$AdminDAO->getrows("productattribute","fkproductid,fkattributeid,pkproductattributeid,attributetype","fkproductid='$productid'");
							
							foreach($productattributes as $pattributes)
							{
								if($pattributes['fkattributeid']==$pkattributeid)
								{
									
									?>
										<option value="<?php echo $attrib['pkattributeid'];?>"><?php echo $attrib['attributename'];?></option>
									<?php
							
								}//end of if
							}//end of foreach
						}//end of each attributes_array
						?>
					</select>
					</td>
				</tr>
				
			<tr>
				
				<td colspan="2">
			<?php	
			
			//getting selected option for edit
				
				
				?>
					<table width="207">
						<tr>
						  <td width="68" valign="top">&nbsp;</td>
							<td width="68" valign="top" id="attribuiteoptions">
						 	<select  name="attributeoptions1" multiple="multiple" size="7" id="attributeoptions1" style="max-width:130px;">
							
								
							
							</select>
						  	</td>
							<td>
									<input type="button" id="add" value="&gt;&gt;"><br /> 
									<input type="button" id="remove" value="&lt;&lt;"> 	
							</td>
							<td width="49" valign="top">
							<!-- style="margin-top:-117px; margin-left:250px"-->
								<select  multiple="multiple" size="7"  name="attributeoptions[]" id="attributeoptions" style="max-width:130px;">
							  		<?php echo $selectedoptions;?>
								</select>
						  </td>
						</tr>
				  </table>					
			  </td>
				</tr>
				<?php
				//echo $productattributes[0]['attributetype']."<b> I M HERE</b>";
			//}//end if
		//}//end foreach inner
//}//end foreach outer
?>
<script language="javascript">
$().ready(function() {
 $('#add').click(function() 
 {
  return !$('#attributeoptions1 option:selected').remove().appendTo('#attributeoptions');
 });
 $('#remove').click(function() 
 {
  return !$('#attributeoptions option:selected').remove().appendTo('#attributeoptions1');
 });
});
</script>