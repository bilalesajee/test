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
		if(strstr($_SERVER['REQUEST_URI'],'/admin/')){//edit by ahsan 20/02/2012
			//start code from store_usersecuity.php, add by ahsan 15/02/2012
			$fieldslabels['fields']		=	$_SESSION['screens'][$screen]['fields'];
			$fieldslabels['labels']		=	$_SESSION['screens'][$screen]['labels'];
			$fieldslabels['actions']	=	$_SESSION['screens'][$screen]['actions'];
			return $fieldslabels;
			//end add code here
		}else{//end if request_uri, edit by ahsan
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