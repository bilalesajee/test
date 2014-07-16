<?php

include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id	=	$_REQUEST['id'];
if(sizeof($_POST)>0)
{
	//post data
	$storename 				= 	filter($_POST['storename']);	
	$address 				= 	filter($_POST['address']);
	$selected_city[] 		= 	$_POST['city'];
	$selected_state[] 		= 	$_POST['state'];
	$zip 					= 	filter($_POST['zipcode']);
	$selected_country[]  	= 	$_POST['countries'];
	$phone					=	filter($_POST['phone']);
	$email					=	filter($_POST['email']);
	if($storename=='')
	{
		$msg.="<li>Store Name can not be left Blank.</li>";	
	}
	if($address =='')
	{
		$msg.="<li>Store Address can not be left Blank.</li>";	
	}
	if($selected_city[0]=='')
	{
		if($_POST['addcity2'])
		{
			$newcity 		=	filter($_POST['addcity2']);
			if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
				$fields 		= 	array('cityname');
				$newcity 		= 	array($newcity);
				$selected_city[0]	=	$AdminDAO->insertrow('city',$fields,$newcity);
			}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
				if($newcity!='Add New')
				{
					$fields 		= 	array('cityname');
					$newcity 		= 	array($newcity);
					$selected_city[0]	=	$AdminDAO->insertrow('city',$fields,$newcity);
				}//if
			}//end edit
		}
	}
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		if($selected_city[0]=='')
		{
			$msg.="<li>City Name can not be left Blank</li>";	
		}
		else if($_POST['addstate2'])
		{
			$newstate 		=	filter($_POST['addstate2']);
			$fields 		= 	array('statename');
			$newstate 		= 	array($newstate);
			$selected_state[]	=	$AdminDAO->insertrow('state',$fields,$newstate);
		}
		else if($_POST['addcountry2'])
		{
			$newcountry 	=	filter($_POST['addcountry2']);
			$fields 		= 	array('countryname');
			$newcountry 	= 	array($newcountry);
			$selected_country[]	=	$AdminDAO->insertrow('countries',$fields,$newcountry);
		}
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		if($_POST['addstate2'])
		{
			$newstate 		=	filter($_POST['addstate2']);
			if($newstate!='Add New')
			{
				$fields 		= 	array('statename');
				$newstate 		= 	array($newstate);
				$selected_state[]	=	$AdminDAO->insertrow('state',$fields,$newstate);
			}//if
			
			
		}
		else if($_POST['addcountry2'])
		{
			$newcountry 	=	filter($_POST['addcountry2']);
			if($newcountry!='Add New')
			{
			
				$fields 		= 	array('countryname');
				$newcountry 	= 	array($newcountry);
				$selected_country[]	=	$AdminDAO->insertrow('countries',$fields,$newcountry);
			}//if
			
		}
		}//end edit
	if($msg!='')
	{
		echo $msg;
		exit;
	}
	else
	{
		$fields		=	array('storename','storeaddress','fkcityid','fkstateid','zipcode','fkcountryid','storephonenumber','email');
		$values		=	array($storename,$address,$selected_city[0],$selected_state[0],$zip,$selected_country[0],$phone,$email);
		if($id!="-1")
		{
			$AdminDAO->updaterow('store',$fields,$values, " pkstoreid = '$id'");
			exit;
		}
		else
		{
			$AdminDAO->insertrow('store',$fields,$values);
			exit;
		}
	}
}
?>