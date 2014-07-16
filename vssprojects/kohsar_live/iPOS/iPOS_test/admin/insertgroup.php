<?php

include_once("../includes/security/adminsecurity.php");

global $AdminDAO;

$groupname		=	filter($_POST['groupname']);

$postedscreens	=	$_POST['screens'];

$groupid		=	$_GET['groupid'];

$name=$_SESSION['name'];

$dat=time();

if($groupname	==	'')

{

	print"Group name must be provided.";

	exit;	

}

if($groupid=='-1')

{

	$table		=	"groups";

	/*$field		=	array('groupname');

	$value		=	array($groupname);
*/
    $field		=	array('groupname','created_by','creation_date');
	$value		=	array($groupname,$name,$dat);


	$groupid	=	$AdminDAO->insertrow($table,$field,$value);

}

else

{

	$table		=	"groups";

	/*$field		=	array('groupname');

	$value		=	array($groupname);*/
	
	$field		=	array('groupname','modified_by','modified_date');
	$value		=	array($groupname,$name,$dat);

	$AdminDAO->updaterow($table,$field,$value,"pkgroupid = '$groupid'");

}

foreach($_POST as $key=>$value)

{

	list($x,$screenid,$fieldid)	=	explode('_',$key);

	if($x=='fields')

	{

		$postedfields[]	=	$fieldid;

	}

	elseif($x=='actions')

	{

		$postedactions[]	=	$fieldid;

	}

}



$groupfieldz		=	$_SESSION['groupfieldz'];

$groupactionz		=	$_SESSION['groupactionz'];

$groupscreenz		=	$_SESSION['groupscreenz'];



if($postedscreens == '')

{

	$postedscreens =	array();

}

if($postedfields == '')

{

	$postedfields =	array();

}

if($postedactions == '')

{

	$postedactions =	array();

}



//starting to delete old data

if($groupid!='-1')

{

	//old data to be deleted

	$oldfields	=	array_diff($groupfieldz,$postedfields);

	$oldactions	=	array_diff($groupactionz,$postedactions);

	$oldscreens	=	array_diff($groupscreenz,$postedscreens);



	foreach($oldfields as $dfields)

	{

		$AdminDAO->deleterows('groupfield'," fkgroupid='$groupid' AND fkfieldid='$dfields' ",'1');

	}

	foreach($oldactions as $dactions)

	{

		$AdminDAO->deleterows('groupaction'," fkgroupid='$groupid' AND fkactionid='$dactions' ",'1');

	}

	foreach($oldscreens as $dscreens)

	{

		$AdminDAO->deleterows('groupscreen'," fkgroupid='$groupid' AND fkscreenid='$dscreens' ",'1');

	}

}

//end deletion



//new data to be inserted

$newfields	=	array_diff($postedfields,$groupfieldz);

$newactions	=	array_diff($postedactions,$groupactionz);

$newscreens	=	array_diff($postedscreens,$groupscreenz);



$gfields	=	array('fkgroupid','fkfieldid');

$gactions	=	array('fkgroupid','fkactionid');

$gscreens	=	array('fkgroupid','fkscreenid');



//starting to insert new data

foreach($newfields as $fieldz)

{

	$data	=	array($groupid,$fieldz);

	$AdminDAO->insertrow("groupfield",$gfields,$data);

}

foreach($newactions as $actionz)

{

	$data	=	array($groupid,$actionz);

	$AdminDAO->insertrow("groupaction",$gactions,$data);

}

foreach($newscreens as $screenz)

{

	$data	=	array($groupid,$screenz);

	$AdminDAO->insertrow("groupscreen",$gscreens,$data);

}

//end insertion

//print"Group data has been saved.";//add comment by ahsan 24/02/2012// 

?>