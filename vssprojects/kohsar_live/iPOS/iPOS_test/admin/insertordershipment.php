<?php
session_start();
error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_REQUEST['id'];
$lock		=	$_POST['lock'];

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
	
	$tshiplistid=count($_POST['shiplistid']);	 
	$t_items=count($_POST['barcode']);
	 //$Query[]=new array();
	$datetime			=	time();
	$msg='';
	//print_r($_POST['deadline']);
	if($t_items>0 && $tshiplistid>0){
		for($i=0;$i<$t_items;$i++){		
			//echo "this is checkbox===>  ".$_POST['productid'][$i];
			$j=$i+1;
			if($_POST['shiplistid'][$i]){			
			//echo "called";
				$barcode			=	filter($_POST['barcode'][$i]);//
				$itemdescription 	=	filter($_POST['itemdescription'][$i]);	//
				$addressbookid		=	$_POST['addressbookid'][$i];
				$quantity			=	$_POST['quantity'][$i];//
				$weight				=	$_POST['weight'][$i];//
				$countryoforigin	=	$_POST['country'][$i];
				$brand				=	$_POST['brand'][$i];//
				$agents				=	$_POST['agents'][$i];
				$suppliers[]		=	$_POST["supplier_$i"];	
				//print_r($_POST["supplier_$i"]);
				//exit;
				
				$description		=	filter($_POST['description'][$i]);
				$clientinfo			=	filter($_POST['clientinfo'][$i]);
				$store				=	$_POST['store'][$i];//
				$lastpprice			=	$_POST['lastpurchaseprice'][$i];
				$lastsprice			=	$_POST['lastsaleprice'][$i];
				$currencyid			=	$_POST['currencyid'][$i];	
				$deadline			=	$_POST['deadline'][$i];	//
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
				//"<br>".$values[]	=	"'".$barcode."','".$itemdescription."','".$quantity."','".$countryoforigin."','".$store."','".$lastpprice."','".$lastsprice."','".$weight."','".$deadline."','".$addressbookid."','".$currencyid."','".$brand."','".$agents."','".$description."','".$defaultimage."','".$clientinfo."','".$datetime."'";
				//"<br>".$fields[] 	= 	"(barcode,itemdescription,quantity,fkcountryid,fkstoreid,lastpurchaseprice,lastsaleprice,weight,deadline,fkaddressbookid,fkcurrencyid,fkbrandid,fkagentid,description,defaultimage,clientinfo,datetime)";
				//$Query[]	=	"INSERT INTO shiplist  (barcode,itemdescription,quantity,fkcountryid,fkstoreid,lastpurchaseprice,lastsaleprice,weight,deadline,fkaddressbookid,fkcurrencyid,fkbrandid,fkagentid,description,defaultimage,clientinfo,datetime)	values ('".$barcode."','".$itemdescription."','".$quantity."','".$countryoforigin."','".$store."','".$lastpprice."','".$lastsprice."','".$weight."','".$deadline."','".$addressbookid."','".$currencyid."','".$brand."','".$agents."','".$description."','".$defaultimage."','".$clientinfo."','".$datetime."')";
			}
		}
		 //$values=	substr($values,0,(strlen($values)-1));	
		//$fields 	= 	"(barcode,itemdescription,quantity,fkcountryid,fkstoreid,lastpurchaseprice,lastsaleprice,weight,deadline,fkaddressbookid,fkcurrencyid,fkbrandid,fkagentid,description,defaultimage,clientinfo,datetime)";
		//$Query[]	=	"INSERT INTO shiplist $fields values $values";
			//echo count($Query);
			
		if($msg)
		{
			echo $msg;
			exit;
		}else{
			//$Query[]	=	"INSERT INTO shiplist  (barcode,itemdescription,quantity,fkcountryid,fkstoreid,lastpurchaseprice,lastsaleprice,weight,deadline,fkaddressbookid,fkcurrencyid,fkbrandid,fkagentid,description,defaultimage,clientinfo,datetime)	values ('".$barcode."','".$itemdescription."','".$quantity."','".$countryoforigin."','".$store."','".$lastpprice."','".$lastsprice."','".$weight."','".$deadline."','".$addressbookid."','".$currencyid."','".$brand."','".$agents."','".$description."','".$defaultimage."','".$clientinfo."','".$datetime."')";
			$fields	=	array('barcode','itemdescription','quantity','fkcountryid','fkstoreid','lastpurchaseprice','lastsaleprice','weight','deadline','fkaddressbookid','fkcurrencyid','fkbrandid','fkagentid','description','defaultimage','clientinfo','datetime');
			for($i=0;$i<$t_items;$i++){
				$j=$i+1;
				if($_POST['shiplistid'][$i]){			
					//echo "called";
					$barcode			=	filter($_POST['barcode'][$i]);//
					$itemdescription 	=	filter($_POST['itemdescription'][$i]);	//
					$addressbookid		=	$_POST['addressbookid'][$i];
					$quantity			=	$_POST['quantity'][$i];//
					$weight				=	$_POST['weight'][$i];//
					$countryoforigin	=	$_POST['country'][$i];
					$brand				=	$_POST['brand'][$i];//
					$agents				=	$_POST['agents'][$i];
					$suppliers[]		=	$_POST["supplier_$i"];	
					//print_r($_POST["supplier_$i"]);
					//exit;
					
					$description		=	filter($_POST['description'][$i]);
					$clientinfo			=	filter($_POST['clientinfo'][$i]);
					$store				=	$_POST['store'][$i];//
					$lastpprice			=	$_POST['lastpurchaseprice'][$i];
					$lastsprice			=	$_POST['lastsaleprice'][$i];
					$currencyid			=	$_POST['currencyid'][$i];	
					$deadline			=	$_POST['deadline'][$i];	//
					$values	=	array($barcode,$itemdescription,$quantity,$countryoforigin,$store,$lastpprice,$lastsprice,$weight,$deadline,$addressbookid,$currencyid,$brand,$agents,$description,$defaultimage,$clientinfo,$datetime);
					$AdminDAO->insertrow("shiplist",$fields,$values);
				}
			}
			//print_r($Query);
			/*for($k=0;$k<count($Query);$k++){
				//echo $Query[$k];
				$shiplistid=$AdminDAO->queryresult($Query[$k]);
				$supplierid=$suppliers[$k];
				
				if(count($supplierid)>0){
					foreach($supplierid as $supid){
						$qry="INSERT INTO shiplistsupplier set fkshiplistid ='$shiplistid',fksupplierid='$supid'";
						//$AdminDAO->queryresult($qry);	 
					}
				}			
			}			*/
			//exit;			
			echo "1";
		}		
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

