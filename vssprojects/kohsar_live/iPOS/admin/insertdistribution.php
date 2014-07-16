<?php session_start(); error_reporting(0);
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id 		= 	$_REQUEST['id'];
$lock		=	$_POST['lock'];

//echo "<pre>";
//	print_r($_POST);
//	echo "---------------------------------";
	//print_r($_SESSION);
//echo "</pre>";

$stor			=	$AdminDAO->getrows("store","storecode,pkstoreid,storename as name","storedeleted<>1 AND storestatus=1");
$addressbookid	=	$_SESSION['addressbookid'];
if(sizeof($_POST)>0){
	$t_items=count($_POST['purchaseid']);
	$t_items=count($_POST['barcode']);
	 
	$datetime			=	time();
	$msg='';

	if($t_items>0){
		for($i=0;$i<$t_items;$i++){		
			$j=$i+1;
			if($_POST['purchaseid'][$i]){			
				//$quantity			=	$_POST['quantity'][$i];
				$weight				=	$_POST['weight'][$i];				
				$purchaseid			=	$_POST['purchaseid'][$i];				
				$storess			=	$stor ;
				foreach($storess as $st){					
					$quantity		=	$_POST['store_'.$st['pkstoreid']][$i];
					$store			=	$st['pkstoreid'];
					
					$dist			=	$AdminDAO->getrows(" distribute "," pkdistributeid ","fkpurchaseid='$purchaseid' and fkstoreid='$store'");
					$distid			=	$dist[0]['pkdistributeid'];
					
					$fields 		= 	array( datetime,fkpurchaseid,fkstoreid,	quantity,fkaddressbookid) ;
					$values			=	array($datetime,$purchaseid,$store,$quantity,$addressbookid);
						
					if($distid>0){						
						$AdminDAO->updaterow("distribute",$fields,$values," pkdistributeid='$distid' ");						
					}else{										
						$AdminDAO->insertrow("distribute",$fields,$values);						
					}
					//echo $Query	=	" INSERT INTO distribute ( $fields )	values ( $values )";	echo " <br>";
					//$AdminDAO->queryresult($Query); 
				}		
				
			}
		}//pkdistributeid 
		if($msg){
			echo $msg;
			exit;
		}else{			
			echo "1";
		}
		exit;
	}else{
		echo "<li>Please select a record .</li>";
		exit;
	}
}// end post
?>

