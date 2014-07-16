<?php
include_once("../includes/security/adminsecurity.php");
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
*/
if(sizeof($_POST)>0)
{
	$fname			=	filter($_POST['fname']); 
    $lastname		=	filter($_POST['lname']); 
    $username		=	trim(filter($_POST['username'])," ");
	$password		=	trim(filter($_POST['pass'])," "); 
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		if($pass && $pass!=$password)
		{
			$password	=	$pass;
		}
	}//end edit
    $cnic			=	filter($_POST['cnic']);
	$store			=	filter($_POST['store']);
    $group			=	filter($_POST['group']); 
	$address1		=	filter($_POST['address1']);
    $address2		=	filter($_POST['address2']);
    $city			=	filter($_POST['city']);
	$state			=	filter($_POST['state']);
    $zip			=	filter($_POST['zip']); 
 	$country		=	filter($_POST['country']);
    $email			=	filter($_POST['email']);
    $phone			=	filter($_POST['phone']); 
    $mobile			=	filter($_POST['mobile']);
    $fax			=	filter($_POST['fax']); 
    $id				=	filter($_POST['id']);
	$addressbookid	=	filter($_POST['addressbookid']);
	if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$status			=	filter($_POST['status']);		// <!--Added by jafer on 19-12-2011-->
	}//end edit
	if($username)
	{
			$unique = $AdminDAO->isunique('addressbook', 'pkaddressbookid', $addressbookid, 'username', $username);
			if($unique=='1')
			{
					echo "The User Name <b><u>$username</u></b> already exists. Please choose another name.";	
					exit;
			}
	}
	else
	{
		echo "User Name can not be left blank. Please enter username to continue";
		exit;
	}
	$fields			=	array('firstname','lastname','email','mobile','phone','address1','address2','fkcityid','fkstateid','zip','fkcountryid','fax','username','password');
	$data			=	array($fname,$lastname,$email,$mobile,$phone,$address1,$address2,$city,$state,$zip,$country,$fax,$username,md5($password));
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		$fields2		=	array('cnic','fkstoreid','fkgroupid','fkaddressbookid');
		$data2			=	array($cnic,$store,$group,$addressbookid);
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$fields2		=	array('cnic','fkstoreid','fkgroupid','fkaddressbookid','loginallowed'); // <!--Added by jafer on 19-12-2011-->
		$data2			=	array($cnic,$store,$group,$addressbookid,$status); // <!--Added by jafer on 19-12-2011-->
	}//end edit
	if($id!="-1")
	{
		$AdminDAO->updaterow('employee',$fields2,$data2, " pkemployeeid = '$id'");
		$AdminDAO->updaterow('addressbook',$fields,$data," pkaddressbookid = '$addressbookid'");
	}
	else
	{
		$addressbookid2	=	$AdminDAO->insertrow('addressbook',$fields,$data);
		if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
			$data3			=	array($cnic,$store,$group,$addressbookid2);
		}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
			$data3			=	array($cnic,$store,$group,$addressbookid2,$status); // <!--Added by jafer on 19-12-2011-->
		}//end edit
		$AdminDAO->insertrow('employee',$fields2,$data3);
	}
}
?>