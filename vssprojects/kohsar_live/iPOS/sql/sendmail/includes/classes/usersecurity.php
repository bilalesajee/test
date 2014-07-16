<?php
session_start();
/* *********************************** */
/* User Security and User rights class */
/***************************************/
class userSecurity
{
	private	 $screen	=	"";
	public function getRights($screen)
	{
		$AdminDAO	=	new AdminDAO;
		$employeeid	=	$_SESSION['employeeid'];
		//getting screens
		$fields	=	$AdminDAO->getrows("employee e, groups g, groupfield gf, field f","*"," f.fkscreenid = '$screen' AND g.pkgroupid = gf.fkgroupid AND f.pkfieldid = gf.fkfieldid AND e.fkgroupid = g.pkgroupid AND e.pkemployeeid = '$employeeid' ORDER BY f.pkfieldid");
		for($i=0;$i<sizeof($fields);$i++)
		{
			$label[]	=	$fields[$i]['fieldlabel'];
			$field[]	=	$fields[$i]['fieldname'];
		}
		$actions	=	$AdminDAO->getrows("employee e, groups g, groupaction ga, action a","*"," a.fkscreenid = '$screen' AND ga.fkgroupid = g.pkgroupid AND a.pkactionid = ga.fkactionid AND e.fkgroupid = g.pkgroupid AND e.pkemployeeid = '$employeeid'");
		for($i=0;$i<sizeof($actions);$i++)
		{
			$action[]	=	$actions[$i]['fkactionid'];
		}
		$fieldslabels['fields']		=	array_unique($field);
		$fieldslabels['labels']		=	array_unique($label);
		$fieldslabels['actions']	=	array_unique($action);
		return $fieldslabels;
	}
}//end class
/*
$rights 	=	new userSecurity;
$fieldlables	=	$rights->getRights(1);
echo "<pre>";
print_r($fieldlables);
echo "</pre>";
*/
?>