<?php

error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id = $_REQUEST['id'];
$qs	=	$_SESSION['qstring'];
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
if($id!="-1")
{
	// this is the edit section
	$notes = $AdminDAO->getrows("note","*"," pknoteid='$id'");
	foreach($notes as $note)
	{
		$notename = $note['title'];
		$description = $note['description'];
		$status	 = $note['status'];
	}
}
if(sizeof($_POST)>0)
{
		$newnotename = filter($_POST['notename']);
		if($newnotename=='')
		{
			echo"Note Title can not be left Blank.";
			exit;
		}
		if($newnotename)
		{
				$unique = $AdminDAO->isunique('note', 'pknoteid', $id, 'title', $newnotename);
				if($unique=='1')
				{
						echo"Note with this Title <b><u>$newnotename</u></b> already exists. Please choose another name.";	
						exit;
				}
		}
		$description 	=	filter($_POST['description']);
		$status		 	=	$_POST['status'];
		$fields = array('title','description','status');
		$values = array($newnotename, $description, $status);

	if($id!='-1')//updates records 
	{
		$AdminDAO->updaterow("note",$fields,$values," pknoteid='$id' ");
	}
	else
	{
		// this is the add section	
		$id = $AdminDAO->insertrow("note",$fields,$values);
	}//end of else
exit;
}// end post
?>