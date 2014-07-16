<?php
session_start();
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 	=	$_REQUEST['id'];
/*if(strpos($id,"maindiv"))
{
	//somehow the id is coming with maindiv and hafta split it
	$newid	=	explode("maindiv",$id);
	$id		=	$newid[0];
}*/
if(sizeof($_POST)>0)
{
	//post data
/*	echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	exit;*/
	$companyname = filter($_POST['companyname']);
	if($companyname=="")
	{
		echo "<li>Company Name can not be left empty.</li>";
		exit;
	}
	$unique = $AdminDAO->isunique('supplier', 'pksupplierid', $id, 'companyname', $companyname);
	if($unique=='1')
	{
		echo "<li>Company with this name <b><u>$companyname</u></b> already exists. Please choose another name.</li>";	
		exit; 
	}
	$firstname	 			= 	filter($_POST['firstname']);
	$lastname 				= 	filter($_POST['lastname']);
	$username				= 	filter($_POST['username']);
	
	// 
	if ($id){
		$addressbookidres_ 	=	$AdminDAO->getrows('supplier',"fkaddressbookid", " pksupplierid = '$id'");
		$addressbookid_		=	$addressbookidres_[0]['fkaddressbookid'];
	}
	
	$uniqueuname = $AdminDAO->isunique('addressbook', 'pkaddressbookid', $addressbookid_, 'username', $username);
	if($uniqueuname=='1' && $id=="-1")
	{
		echo "<li>User name <b><u>$username</u></b> already exists. Please choose another user name.</li>";	
		exit;
	}
	//
	
	$password 				= 	filter($_POST['password']);
	$companyname 			= 	filter($_POST['companyname']);
	$address1  				= 	filter($_POST['address1']);
	$address2  				= 	filter($_POST['address2']);
	$selected_city			=	$_POST['city'];
	$selected_state			=	$_POST['state'];
	$zipcode  				= 	filter($_POST['zipcode']);	
	$selected_country	 	= 	$_POST['countries'];	
	$email  				= 	filter($_POST['email']);	
	$phone  				= 	filter($_POST['phone']);	
	$mobile  				= 	filter($_POST['mobile']);	
	$fax  					= 	filter($_POST['fax']);	
	$contactperson1 		= 	filter($_POST['contactperson1']);
	$contactperson2 		= 	filter($_POST['contactperson2']);
	$url  					= 	filter($_POST['url']);
	$newcity 				=	filter($_POST['addcity2']);
	$newstate 				=	filter($_POST['addstate2']);
	$newcountry 			=	filter($_POST['addcountry2']);
	$suppliercode 			=	filter($_POST['suppliercode']);
	$typecode				=	filter($_POST['typecode']);
	$suppliertype			=	filter($_POST['suppliertype']);
	if($suppliercode!='')
	{
			$cityrows	=	$AdminDAO->getrows('supplier','pksupplierid'," suppliercode='$suppliercode' AND pksupplierid<>$id");
			if(count($cityrows)>0)
			{
				echo "<li>This Supplier Code  $suppliercode already exists.</li>";	
				exit;
			}
			
	}
	else
	{
		echo "<li>Supplier code should be provided.</li>";	
		exit;
	}
	if($newcity!='')
	{
			if($newcity!='Add New')
			{
				$cityrows	=	$AdminDAO->getrows('city','pkcityid'," cityname='$newcity' ");
				if(count($cityrows)>0)
				{
					echo "<li>This City $newcity already exists.</li>";	
					exit;
				}
			
			}
	}
	if($newstate!='')
	{
			if($newstate!='Add New')
			{
				$staterow	=	$AdminDAO->getrows('state','pkstateid'," statename='$newstate' ");
				if(count($staterow)>0)
				{
					echo "<li>This state $newstate already exists.</li>";	
					exit;
				}
			}
			
	}		
	if($newcountry!='')
	{
			if($newcountry!='Add New')
			{
				$countryrow	=	$AdminDAO->getrows('countries','pkcountryid'," countryname='$newcountry' ");
				if(count($countryrow)>0)
				{
					echo "<li>This country $newstate already exists.</li>";	
					exit;
				}
			}
	}		
	
	
	if($_POST['addcity2']!='' && $_POST['addcity2']!='Add New')
	{
	/*	echo "Adding city";
		exit;*/
		$fields 			= 	array('cityname');
		$newcity 			= 	array($newcity);

		$selected_city		=	$AdminDAO->insertrow('city',$fields,$newcity);
	}
	if($_POST['addstate2']!='' && $_POST['addstate2']!='Add New')
	{
	/*	echo "Adding state";
		exit;*/
		$fields 			= 	array('statename');
		$newstate 			= 	array($newstate);
		
		$selected_state		=	$AdminDAO->insertrow('state',$fields,$newstate);
	}
	if($_POST['addcountry2']!='' && $_POST['addcountry2']!='Add New')
	{
/*		echo "Adding country";
		exit;*/
		$fields 			= 	array('countryname');
		$newcountry 		= 	array($newcountry);
		$selected_country	=	$AdminDAO->insertrow('countries',$fields,$newcountry);
	}
	if($id!="-1")
	{
		$addressbookidres 	=	$AdminDAO->getrows('supplier',"fkaddressbookid", " pksupplierid = '$id'");
		$addressbookid 		=	$addressbookidres[0]['fkaddressbookid'];
		$fields				=	array('firstname','lastname','email','mobile','phone','address1','address2','fkcityid','fkstateid','zip','fkcountryid','fax','username','password');
		$values				=	array($firstname,$lastname,$email,$mobile,$phone,$address1,$address2,$selected_city,$selected_state,$zipcode,$selected_country,$fax,$username,$password);
		$fields1			=	array('contactperson1','contactperson2','companyname','url','fkaddressbookid','suppliercode','fktypecodeid','fksuppliertypeid');
		$values1			=	array($contactperson1,$contactperson2,$companyname,$url,$addressbookid,$suppliercode,$typecode,$suppliertype);
		$AdminDAO->updaterow('supplier',$fields1,$values1, " pksupplierid = '$id'");			
		$AdminDAO->updaterow('addressbook',$fields,$values, " pkaddressbookid = '$addressbookid'");
		
	}
	else
	{
		$fields				=	array('firstname','lastname','email','mobile','phone','address1','address2','fkcityid','fkstateid','zip','fkcountryid','fax','username','password');
		$values				=	array($firstname,$lastname,$email,$mobile,$phone,$address1,$address2,$selected_city,$selected_state,$zipcode,$selected_country,$fax,$username,$password);
		$addressbookid		=	$AdminDAO->insertrow('addressbook',$fields,$values);
		$fields1			=	array('contactperson1','contactperson2','companyname','url','fkaddressbookid','suppliercode','fktypecodeid','fksuppliertypeid');
		$values1			=	array($contactperson1,$contactperson2,$companyname,$url,$addressbookid,$suppliercode,$typecode,$suppliertype);		
		$AdminDAO->insertrow('supplier',$fields1,$values1);
		//echo "success";
	}
}
exit;
?>