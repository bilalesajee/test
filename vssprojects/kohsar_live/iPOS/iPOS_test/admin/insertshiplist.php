<?php

error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_REQUEST['id'];
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	$lock		=	$_POST['lock'];
	
	
	 $defaultimage= uploadimage();
}//end edit
/*echo "<pre>";
print_r($_POST);
echo "</pre>";
exit;*/
/*if($id!="-1")
{
	// this is the edit section
	$packs = $AdminDAO->getrows("shiplist","*"," pkpackingid='$id'");
	foreach($packs as $pack)
	{
		$packingname = $pack['packingname'];
	}
}*/
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
	if(sizeof($_POST)>0)
	{
		$barcode			=	filter($_POST['barcode']);
		if($barcode=='')
		{
			$msg.="<li>Please enter barcode.</li>";
		}
		$itemdescription 	=	filter($_POST['itemdescription']);
		$addressbookid		=	$_POST['addressbookid'];
		$quantity			=	$_POST['quantity'];
		$weight				=	$_POST['weight'];
		$countryoforigin	=	$_POST['country'];
		$brand				=	$_POST['brand'];
		if($brand=='')
		{
			$msg.="<li>Please select a brand.</li>";
		}
		$suppliers			=	$_POST['supplier'];
		if(sizeof($suppliers)<1)
		{
			$msg.="<li>Please select a supplier.</li>";
		}
		if($msg)
		{
			echo $msg;
			exit;
		}
		$store				=	$_POST['store'];
		$lastpprice			=	$_POST['lastpprice'];
		$currencyid			=	$_POST['currencyid'];
		$deadline			=	implode("-",array_reverse(explode("-",$_POST['deadline'])));
		$fields = array('barcode','itemdescription','quantity','fkcountryid','fkstoreid','lastpurchaseprice','weight','deadline','fkaddressbookid','fkcurrencyid','fkbrandid');
		$values = array($barcode,$itemdescription,$quantity,$countryoforigin,$store,$lastpprice,$weight,$deadline,$addressbookid,$currencyid,$brand);
		if($id!='-1')//updates records 
		{
			$AdminDAO->updaterow("shiplist",$fields,$values," pkshiplistid='$id' ");
			//removing previous suppliers
			$AdminDAO->deleterows("shiplistsupplier","fkshiplistid='$id'",1);
			$sfields	=	array('fkshiplistid','fksupplierid');
			foreach($suppliers as $supplierid)
			{
				$sdata		=	array($id,$supplierid);
				$AdminDAO->insertrow("shiplistsupplier",$sfields,$sdata);
			}
		}
		else
		{
			// this is the add section	
			$id = $AdminDAO->insertrow("shiplist",$fields,$values);
			$sfields	=	array('fkshiplistid','fksupplierid');
			foreach($suppliers as $supplierid)
			{
				$sdata		=	array($id,$supplierid);
				$AdminDAO->insertrow("shiplistsupplier",$sfields,$sdata);
			}
		}//end of else
	exit;
	}// end post
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	if(sizeof($_POST)>0)
	{
		$barcode			=	filter($_POST['barcode']);
		$itemdescription 	=	filter($_POST['itemdescription']);
		if($itemdescription =='' && $barcode == '')
		{
			$msg	.=	"<li>Please enter the barcode or description</li>";
		}
		$deadline			=	implode("-",array_reverse(explode("-",$_POST['deadline'])));
		if($deadline!='')
		{
			if($deadline<date('Y-m-d'))
			{
				$msg	.=	"<li>Please enter valid date</li>";
			}
		}
		$addressbookid		=	$_POST['addressbookid'];
		$quantity			=	$_POST['quantity'];
		$weight				=	$_POST['weight'];
		$countryoforigin	=	$_POST['country'];
		$brand				=	$_POST['brand'];
		$agents				=	$_POST['agents'];
		if($quantity=='' || $quantity<=0)
		{
			$msg.="<li>Please enter valid quantity</li>";
		}
		
		$suppliers			=	$_POST['supplier'];
		
		$description		=	$_POST['description'];
		$clientinfo			=	$_POST['clientinfo'];
		
		/*if($quantity=='')
		{
			$msg.="<li>Please enter quantity.</li>";
		}*/
		/*if(sizeof($suppliers)<1)
		{
			$msg.="<li>Please select a supplier.</li>";
		}*/
		if($msg)
		{
			echo $msg;
			exit;
		}
		$store				=	$_POST['store'];
		$lastpprice			=	$_POST['lastpurchaseprice'];
		$lastsprice			=	$_POST['lastsaleprice'];
		$currencyid			=	$_POST['currencyid'];
		$datetime			=	time();
		if($defaultimage!=''){
			$fields = array('barcode','itemdescription','quantity','fkcountryid','fkstoreid','lastpurchaseprice','lastsaleprice','weight','deadline','fkaddressbookid','fkcurrencyid','fkbrandid','fkagentid','description','defaultimage','clientinfo','datetime');
			$values = array($barcode,$itemdescription,$quantity,$countryoforigin,$store,$lastpprice,$lastsprice,$weight,$deadline,$addressbookid,$currencyid,$brand,$agents,$description,$defaultimage,$clientinfo,$datetime);
		}else{
			$fields = array('barcode','itemdescription','quantity','fkcountryid','fkstoreid','lastpurchaseprice','lastsaleprice','weight','deadline','fkaddressbookid','fkcurrencyid','fkbrandid','fkagentid','description','clientinfo','datetime');
			$values = array($barcode,$itemdescription,$quantity,$countryoforigin,$store,$lastpprice,$lastsprice,$weight,$deadline,$addressbookid,$currencyid,$brand,$agents,$description,$clientinfo,$datetime);	
		}
		if($id!='-1')//updates records 
		{
			$AdminDAO->updaterow("shiplist",$fields,$values," pkshiplistid='$id' ");
			//removing previous suppliers
			$AdminDAO->deleterows("shiplistsupplier","fkshiplistid='$id'",1);
			$sfields	=	array('fkshiplistid','fksupplierid');
			foreach($suppliers as $supplierid)
			{
				$sdata		=	array($id,$supplierid);
				$AdminDAO->insertrow("shiplistsupplier",$sfields,$sdata);
			}
		}
		else
		{
			// this is the add section	
			$id = $AdminDAO->insertrow("shiplist",$fields,$values);
			$sfields	=	array('fkshiplistid','fksupplierid');
			foreach($suppliers as $supplierid)
			{
				$sdata		=	array($id,$supplierid);
				$AdminDAO->insertrow("shiplistsupplier",$sfields,$sdata);
			}
		}//end of else
		echo $lock;
	exit;
	}// end post
	?>
	
	<?php
	
	 define ("MAX_SIZE","100"); 	
	//This function reads the extension of the file. It is used to determine if the file  is an image by checking the extension.
	 function getExtension($str) {
			 $i = strrpos($str,".");
			 if (!$i) { return ""; }
			 $l = strlen($str) - $i;
			 $ext = substr($str,$i+1,$l);
			 return $ext;
	 }
	
	function uploadimage(){
		//define a maxim size for the uploaded images in Kb
		 define ("MAX_SIZE","100"); 	
		 $errors=0;
			$image=$_FILES['defaultimage']['name'];
			
			//echo $image."---------------";
			if($image==''){
				return "";
			}
			//if it is not empty
			if ($image){
				//get the original name of the file from the clients machine
					$filename = stripslashes($_FILES['defaultimage']['name']);
				//get the extension of the file in a lower case format
					$extension = getExtension($filename);
					$extension = strtolower($extension);
				//if it is not a known extension, we will suppose it is an error and will not  upload the file,  //otherwise we will do more tests
				if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
					$msg= '<h1>Unknown extension!</h1>';
					$errors=1;
					
				}else{
					
					 $size=filesize($_FILES['defaultimage']['tmp_name']);			
					if ($size > MAX_SIZE*1024){
					//	$msg= '<h1>You have exceeded the size limit!</h1>';
					//	$errors=1;
					}	
					$image_name=time().'.'.$extension;
					$newname="../images/".$image_name;
					/*
					$copied = copy($_FILES['defaultimage']['tmp_name'], $newname);
					if(!$copied){
						$msg= '<h1>Copy unsuccessfull!</h1>';
						$errors=1;
					}
					*/	
					
					if($extension=="jpg" || $extension=="jpeg" ){
						$uploadedfile = $_FILES['defaultimage']['tmp_name'];
						$src = imagecreatefromjpeg($uploadedfile);
					}else if($extension=="png"){
						$uploadedfile = $_FILES['defaultimage']['tmp_name'];
						$src = imagecreatefrompng($uploadedfile);
					}else {
						$src = imagecreatefromgif($uploadedfile);
					}
		
					list($width,$height)=getimagesize($_FILES['defaultimage']['tmp_name']);
					$newwidth=200;
					$newheight=($height/$width)*$newwidth;
					$tmp=imagecreatetruecolor($newwidth,$newheight);
					imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
					$copied=imagejpeg($tmp,$newname,100);
					if(!$copied){
						$msg= '<h1>Copy unsuccessfull!</h1>';
						$errors=1;
					}
				}
			}		
			 if(!$errors){
				return $newname;
			 }else{
				return false;
			 }
	}
}//end edit
?>