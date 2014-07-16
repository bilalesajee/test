<?php
include_once("../includes/security/adminsecurity.php");
if(sizeof($_POST)>0)
{
	    $p=1;
		$field	=	array("attributeposition");
		foreach($_POST['position'] as $pos)
		{
			$value= array($p);
			$AdminDAO->updaterow("attribute",$field,$value," pkattributeid ='$pos'");
			//echo $pos.'<br>';//dump($_POST);
			$p++;
		}
}
?>