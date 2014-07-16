<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$attributeid	=	$_GET['id'];
/********************************COUNTRIES***********************************/
$options_array		=	$AdminDAO->getrows("attributeoption ao, attribute a","ao.pkattributeoptionid, ao.attributeoptionname", " ao.fkattributeid=a.pkattributeid AND a.pkattributeid=$attributeid");
foreach($options_array as $options)
{
	echo $options['attributeoptionname'];
	?>
    	<input type="checkbox" name="attributeoptions[]" value="<?php echo $options['pkattributeoptionid']?>" />
    <?php
}
?>