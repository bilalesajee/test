<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_REQUEST['id'];
$lock		=	$_POST['lock'];

/*echo "<pre>";
	print_r($_POST);
echo "</pre>";
exit;*/
 //$defaultimage= uploadimage();

//exit;
/*if($id!="-1")
{
	// this is the edit section
	$packs = $AdminDAO->getrows("shiplist","*"," pkpackingid='$id'");
	foreach($packs as $pack)
	{
		$packingname = $pack['packingname'];
	}
}*/

if(sizeof($_POST)>0){
	
	/*if($barcode=='')
	{
		$msg.="<li>Please enter barcode.</li>";
	}*/	
	/*if($brand=='')
	{
		$msg.="<li>Please select a brand</li>";
	}*/
	
	/*if($quantity=='')
	{
		$msg.="<li>Please enter quantity.</li>";
	}*/
	/*if(sizeof($suppliers)<1)
	{
		$msg.="<li>Please select a supplier.</li>";
	}*/
	//echo "<pre>";
	//print_r($_REQUEST);
	
	$t_items=count($_POST['productid']);
	 
	 $t_items=count($_POST['barcode']);
	 
	$datetime			=	time();
	$msg='';
	//print_r($_POST['deadline']);
	if($t_items>0){
		for($i=0;$i<$t_items;$i++){		
			//echo "this is checkbox===>  ".$_POST['productid'][$i];
			$j=$i+1;
			if($_POST['productid'][$i]){			
				$barcode			=	filter($_POST['barcode'][$i]);
				$itemdescription 	=	filter($_POST['itemdescription'][$i]);	
				$addressbookid		=	$_POST['addressbookid'][$i];
				$quantity			=	$_POST['quantity'][$i];
				$weight				=	$_POST['weight'][$i];
				$countryoforigin	=	$_POST['country'][$i];
				
				if($_POST['fkbrandid'])
					$brand			=	$_POST['fkbrandid'];
				else
					$brand			=	$_POST['brand'][$i];
					
				$agents				=	$_POST['agents'][$i];
				$suppliers			=	$_POST['supplier'][$i];	
				$description		=	$_POST['description'][$i];
				$clientinfo			=	$_POST['clientinfo'][$i];
				$store				=	$_POST['store'][$i];
				$lastpprice			=	$_POST['lastpurchaseprice'][$i];
				$lastsprice			=	$_POST['lastsaleprice'][$i];
				$currencyid			=	$_POST['currencyid'][$i];	
				$deadline			=	$_POST['deadline'][$i];	
				if($deadline=='')
					$deadline			=	$_POST['gdeadline'];
				
				if($itemdescription =='' && $barcode == ''){
					$msg	.=	"<li>Please enter the barcode or description</li>";
				}
				$deadline			=	implode("-",array_reverse(explode("-",$deadline)));
				if($deadline!=''){
					if($deadline<date('Y-m-d')){
						$msg	.=	"<li>Please enter valid date [DD-MM-yyyy] [$i]</li>";
					}
				}	
					
				if($barcode==''){
					$msg.="<li>Please enter barcode [$j].</li>";
				}	
				if($brand==''){
					//$msg.="<li>Please select a brand</li>";
				}		
				if($quantity=='' || $quantity<=0){
					$msg.="<li>Please enter quantity [$j].</li>";
				}
				if($weight==''){
					//$msg.="<li>Please enter weight [$j].</li>";
				}
				if($store==''){
					$msg.="<li>Please enter store [$j].</li>";
				}
				if(sizeof($suppliers)<1){
					//$msg.="<li>Please select a supplier.</li>";
				}
				//echo  $deadline			;		
				//$values.="('".$barcode."','".$itemdescription."','".$quantity."','".$countryoforigin."','".$store."','".$lastpprice."','".$lastsprice."','".$weight."','".$deadline."','".$addressbookid."','".$currencyid."','".$brand."','".$agents."','".$description."','".$defaultimage."','".$clientinfo."','".$datetime."'),";
			}
		}
		 //$values=	substr($values,0,(strlen($values)-1));	
		//$fields = 	"(barcode,itemdescription,quantity,fkcountryid,fkstoreid,lastpurchaseprice,lastsaleprice,weight,deadline,fkaddressbookid,fkcurrencyid,fkbrandid,fkagentid,description,defaultimage,clientinfo,datetime)";
		//$Query	=	"INSERT INTO shiplist $fields values $values";
		//	exit;
		if($msg)
		{
			echo $msg;
			exit;
		}else{
			for($i=0;$i<$t_items;$i++){		
			//echo "this is checkbox===>  ".$_POST['productid'][$i];
			$j=$i+1;
			$fields	=	array('barcode','itemdescription','quantity','fkcountryid','fkstoreid','lastpurchaseprice','lastsaleprice','weight','deadline','fkaddressbookid','fkcurrencyid','fkbrandid','fkagentid','description','defaultimage','clientinfo','datetime');
			if($_POST['productid'][$i]){			
					$barcode			=	filter($_POST['barcode'][$i]);
					$itemdescription 	=	filter($_POST['itemdescription'][$i]);	
					$addressbookid		=	$_POST['addressbookid'][$i];
					$quantity			=	$_POST['quantity'][$i];
					$weight				=	$_POST['weight'][$i];
					$countryoforigin	=	$_POST['country'][$i];
					
					if($_POST['fkbrandid'])
						$brand			=	$_POST['fkbrandid'];
					else
						$brand			=	$_POST['brand'][$i];
					$agents				=	$_POST['agents'][$i];
					$suppliers			=	$_POST['supplier'][$i];	
					$description		=	$_POST['description'][$i];
					$clientinfo			=	$_POST['clientinfo'][$i];
					$store				=	$_POST['store'][$i];
					$lastpprice			=	$_POST['lastpurchaseprice'][$i];
					$lastsprice			=	$_POST['lastsaleprice'][$i];
					$currencyid			=	$_POST['currencyid'][$i];	
					$deadline			=	$_POST['deadline'][$i];
					$values	=	array($barcode,$itemdescription,$quantity,$countryoforigin,$store,$lastpprice,$lastsprice,$weight,$deadline,$addressbookid,$currencyid,$brand,$agents,$description,$defaultimage,$clientinfo,$datetime);
					$AdminDAO->queryresult($Query);
					echo "1";
				}
			}
		}
		exit;
	}else{
		echo "<li>Please select a record .</li>";
		exit;
	}

	

	
	
	//$fields = array('barcode','itemdescription','quantity','fkcountryid','fkstoreid','lastpurchaseprice','lastsaleprice','weight','deadline','fkaddressbookid','fkcurrencyid','fkbrandid','fkagentid','description','defaultimage','clientinfo','datetime');
	//$values = array($barcode,$itemdescription,$quantity,$countryoforigin,$store,$lastpprice,$lastsprice,$weight,$deadline,$addressbookid,$currencyid,$brand,$agents,$description,$defaultimage,$clientinfo,$datetime);
	// this is the add section	
	//$id = $AdminDAO->insertrow("shiplist",$fields,$values);
	//$sfields	=	array('fkshiplistid','fksupplierid');
	
	
	/*foreach($suppliers as $supplierid)
	{
		$sdata		=	array($id,$supplierid);
		$AdminDAO->insertrow("shiplistsupplier",$sfields,$sdata);
	}
	*/
//	echo $lock;

}// end post
?>

