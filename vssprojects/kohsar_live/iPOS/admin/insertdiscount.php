<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO;
/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/
if(sizeof($_POST)>0)
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, added if condition
		$discountid			=	$_POST['discountid'];
		$discounttype		=	$_POST['discounttype'];
		$discountname		=	trim(filter($_POST['discountname'])," ");
		if($discountname)
		{
			$unique = $AdminDAO->isunique('discount', 'pkdiscountid', $discountid, 'discountname', $discountname);
			if($unique=='1')
			{
				echo"Discount with this name <b><u>$discountname</u></b> already exists. Please choose another name.";	
				exit;
			}
		}
		$startdate			=	$_POST['startdate'];
		$startdate			=	strtotime($startdate);
		$enddate			=	$_POST['enddate'];
		$enddate			=	strtotime($enddate);
		if($discountname=="")
		{
			echo "Discount name can not be left empty";
			exit;
		}
		if($discountid=="-1")
		{
			if($startdate < strtotime(date("Y-m-d")) || $enddate < strtotime(date("Y-m-d")))
			{
				echo "Start Date and/or End Date can not be less than today";
				exit;
			}
		}
		if($enddate<$startdate)
		{
			echo "End Date can not be less than Start Date";
			exit;
		}
		$status				=	$_POST['status'];
		$basequantity		=	$_POST['basequantity'];
		$basequantityaq		=	$_POST['basequantityaq'];
		$basequantitypp		=	$_POST['basequantitypp'];	
		$amountaq			=	$_POST['amountaq'];
		$discountquantity	=	$_POST['discountquantity'];
		$discountquantitypp	=	$_POST['discountquantitypp'];
		if($discounttype!=3)
		{
			$stockid		=	$_POST['expiry'];
			if($stockid	== "")
			{
				echo "Please select stock level";
				exit;
			}
			if($discounttype	== 4)
			{
				$stockid2			=	$_POST['expiry2'];
				if($stockid2=="")
				{
					echo "Please select stock level for discounted product";
					exit;
				}
			}
		}
		$amount				=	$_POST['amount'];
		$amountaa			=	$_POST['amountaa'];
		$amountoff			=	$_POST['amountoff'];	
		$type				=	$_POST['type'];
		$typeaa				=	$_POST['typeaa'];	
		$fields1			=	array('discountname','startdate','enddate','fkdiscounttypeid','discountstatus');
		$data1				=	array($discountname,$startdate,$enddate,$discounttype,$status);
		if($discountid!="-1")
		{
			$AdminDAO->updaterow("discount",$fields1,$data1," pkdiscountid = '$discountid'");
			$insertid		=	$discountid;
		}
		else
		{
			$insertid		=	$AdminDAO->insertrow("discount",$fields1,$data1);
		}
		/************* section for stock management when quantity is attached with discount ****************/
		
		$existingdiscountstock	=	$AdminDAO->getrows("discountstock","fkstockid,type"," fkdiscountid = '$discountid'");
		foreach($existingdiscountstock as $existing)
		{
			if($existing['type']=='b')
			{
				$existingstock1[]	=	$existing['fkstockid'];
			}
			else
			{
				$existingstock2[]	=	$existing['fkstockid'];
			}
		}
		$soldstock				=	$AdminDAO->getrows("saledetail","fkstockid"," 1");
		foreach($soldstock as $soldstocks)
		{
			$stocks[]	=	$soldstocks['fkstockid'];
		}
		$intersection1	=	@array_intersect($existingstock1,$stocks);
		$intersection2	=	@array_intersect($existingstock2,$stocks);
		if(sizeof($intersection1)>0)
		{			  
			$intersect1		=	@array_intersect($stockid,$intersection1);
			if(sizeof($intersect1)>0)
			{
				$flag1size 	=	0;
				for($f1=0;$f1<sizeof($intersect1);$f1++)
				{
					if(@in_array($intersect1[$f1],$stockid))
					{
						$flag1size	+=1;
					}
				}
				if(sizeof($flag1size)==sizeof(intersect1))
				{
					$flag	=	1;
				}
				else
				{
					$flag	=	0;
				}
			}
			else
			{
				$flag	=	0;
			}
		}
		else
		{
			$flag	=	1;
		}
		if(sizeof($intersection2)>0)
		{			  
			$intersect2		=	@array_intersect($stockid2,$intersection2);
			if(sizeof($intersect2)>0)
			{
				$flag2size 	=	0;
				for($f2=0;$f2<sizeof($intersect2);$f2++)
				{
					if(@in_array($intersect2[$f2],$stockid2))
					{
						$flag2size	+=1;
					}
				}
				if(sizeof($flag2size)==sizeof(intersect2))
				{
					$flag2	=	1;
				}
				else
				{
					$flag2	=	0;
				}
			}
			else
			{
				$flag2	=	0;
			}
		}
		else
		{
			$flag2	=	1;
		}
		/*echo "<pre>";
		echo "the existing stock 1 is ";
		print_r($existingstock1);
		echo "the existing stock 2 is ";
		print_r($existingstock2);
		echo "the intersection with stock 1 is ";
		print_r($intersection1);
		echo "the intersection with stock 2 is ";
		print_r($intersection2);
		echo "posted stock1 is ";
		print_r($stockid);
		echo "posted stock2 is ";
		print_r($stockid2);	
		echo "the stocks are ";
		print_r($stocks);
		echo "the intersection with posted stock1 is";
		print_r($intersect1);
		echo "the intersection with posted stock2 is";
		print_r($intersect2);	
		echo "</pre>";
		echo "1- $flag ,2- $flag2";
		exit;*/
		/***************************************************************************************************/
		if($discounttype == 1)
		{
			if($discountid!="-1")//when editing
			{
				if($flag==1)
				{
					$AdminDAO->deleterows("discountstock"," fkdiscountid = '$discountid'",1);
				}
				else
				{
					echo "Discounted stock can't be removed from the system.";
					exit;
				}
			}
			for($i=0;$i<sizeof($stockid);$i++)
			{
				$sfields	=	array('fkdiscountid','fkstockid','type');//for discount stock
				$sdata		=	array($insertid,$stockid[$i],'b'); // b = base product ; d = discounted product
				$AdminDAO->insertrow("discountstock",$sfields,$sdata);			
			}
			$fieldsqq		=	array('basequantity','discountquantity','fkdiscountid');
			$dataqq			=	array($basequantity,$discountquantity,$insertid);
			if($discountid!="-1")
			{
				$AdminDAO->updaterow("discountdetailsqq",$fieldsqq,$dataqq,"fkdiscountid='$discountid'");
			}
			else
			{
				$AdminDAO->insertrow("discountdetailsqq",$fieldsqq,$dataqq);
			}
		}
		else if($discounttype == 2)
		{
			if($discountid!="-1")//when editing
			{
				if($flag==1)
				{
					$AdminDAO->deleterows("discountstock"," fkdiscountid = '$discountid'",1);
				}
				else
				{
					echo "Discounted stock can't be removed from the system.";
					exit;
				}
			}
			for($i=0;$i<sizeof($stockid);$i++)
			{
				$sfields	=	array('fkdiscountid','fkstockid','type');//for discount stock
				$sdata		=	array($insertid,$stockid[$i],'b');
				$AdminDAO->insertrow("discountstock",$sfields,$sdata);			
			}
			$fieldsaq		=	array('basequantity','amount','type','fkdiscountid');
			$dataaq			=	array($basequantityaq,$amountaq,$type,$insertid);
			if($discountid!="-1")
			{
				$AdminDAO->updaterow("discountdetailsaq",$fieldsaq,$dataaq,"fkdiscountid = '$discountid'");
			}
			else
			{
				$AdminDAO->insertrow("discountdetailsaq",$fieldsaq,$dataaq);
			}
		}
		else if($discounttype == 3)
		{
			$fieldsaa		=	array('amount','amountoff','type','fkdiscountid');
			$dataaa			=	array($amountaa,$amountoff,$typeaa,$insertid);
			if($discountid!="-1")
			{
				$AdminDAO->updaterow("discountdetailsaa",$fieldsaa,$dataaa,"fkdiscountid = '$discountid'");
			}
			else
			{
				$AdminDAO->insertrow("discountdetailsaa",$fieldsaa,$dataaa);
			}
		}
		else if($discounttype == 4)
		{
			if($discountid!="-1")//when editing
			{
				if($flag=="1" && $flag2== "1")
				{
					$AdminDAO->deleterows("discountstock"," fkdiscountid = '$discountid'",1);
				}
				else
				{
					echo "Discounted stock can't be removed from the system.";
					exit;
				}
			}
			for($i=0;$i<sizeof($stockid);$i++)
			{
				$sfields	=	array('fkdiscountid','fkstockid','type');//for discount stock
				$sdata		=	array($insertid,$stockid[$i],'b');
				$AdminDAO->insertrow("discountstock",$sfields,$sdata);			
			}
			for($j=0;$j<sizeof($stockid2);$j++)
			{
				$sfields2	=	array('fkdiscountid','fkstockid','type');//for discount stock
				$sdata2		=	array($insertid,$stockid2[$j],'d');
				$AdminDAO->insertrow("discountstock",$sfields2,$sdata2);			
			}
			$fieldspp		=	array('basequantity','discountquantity','fkdiscountid');
			$datapp			=	array($basequantitypp,$discountquantitypp,$insertid);
			if($discountid!="-1")
			{
				$AdminDAO->updaterow("discountdetailspp",$fieldspp,$datapp,"fkdiscountid = '$discountid'");
			}
			else
			{
				$AdminDAO->insertrow("discountdetailspp",$fieldspp,$datapp);
			}
		}
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$storeid			=	$_POST['storeid'];	
		$discountname		=	trim(filter($_POST['discountname'])," ");
		$discounttype		=	$_POST['discounttype'];
		$startdate			=	$_POST['startdate'];
		$startdate			=	strtotime($startdate);
		$enddate			=	$_POST['enddate'];
		$enddate			=	explode("-",$enddate);
		$enddate			=	mktime(23,59,59,$enddate[1],$enddate[0],$enddate[2]);
		$status				=	$_POST['status'];	
		$addressbookid		=	$_SESSION['addressbookid'];
		$time				=	time();	
		$discountid			=	$_POST['discountid'];	
		$productnames		=	$_POST['productnames'];	
		if($discountname)
		{
			$unique = $AdminDAO->isunique('discount', 'pkdiscountid', $discountid, 'discountname', $discountname);
			if($unique=='1')
			{
				echo"Discount with this name <b><u>$discountname</u></b> already exists. Please choose another name.";	
				exit;
			}
		}
		if($discountname=="")
		{
			echo "Discount name can not be left empty";
			exit;
		}
		if($discountid=="-1")
		{
			if($startdate < strtotime(date("Y-m-d")) || $enddate < strtotime(date("Y-m-d")))
			{
				echo "Start Date and/or End Date can not be less than today";
				exit;
			}
		}
		if(strtotime($_POST['enddate'])<$startdate)
		{
			echo "End Date can not be less than Start Date";
			exit;
		}
		if($discounttype	== 1){
			$barcodeid	=	$_POST['barcode_productname'] ; // replaced barcode_productname from barcode by yasir 18-08-11
			
			// added by yasir 18-08-11
			$sql		=	"SELECT pkbarcodeid FROM barcode WHERE barcode = '$barcodeid'";
			$result		=	$AdminDAO->queryresult($sql);
			$barcodeid	=	$result[0]['pkbarcodeid'];
			//
			$quantity			=$_POST['basequantity'] ;
			$discountquantity	=$_POST['discountquantity'] ;
			$quantity			=$_POST['basequantity'] ;
			if($quantity=='' || $quantity<1){
				echo "Please enter items to be Purchased";
				exit;
			}if($discountquantity=='' || $discountquantity<1){
				echo "Please enter free items";
				exit;
			}		
		}if($discounttype	== 2){
			$barcodeid	=	$_POST['barcode_productname1'] ; // replaced barcode_productname1 from barcode by yasir 18-08-11
			
			// added by yasir 18-08-11
			$sql		=	"SELECT pkbarcodeid FROM barcode WHERE barcode = '$barcodeid'";
			$result		=	$AdminDAO->queryresult($sql);
			$barcodeid	=	$result[0]['pkbarcodeid'];
			//
			$quantity			=$_POST['basequantityaq'];
			$amountoff			=$_POST['amountaq'];
			$amountofftype		=$_POST['type'];	
			if($quantity=='' || $quantity<1){
				echo "Please enter items to be Purchased";
				exit;
			}if($amountoff=='' || $amountoff<1){
				echo "Please enter amount off";
				exit;
			}if($amountofftype==''){
				echo "Please select offer type";
				exit;
			}				
	
		}if($discounttype	== 3){		
			 $amount			=$_POST['amountaa'];
			 $amountoff			=$_POST['amountoff'];
			 $amountofftype		=$_POST['typeaa'];	
			 
	
			if($amount=='' || $amount<1){
				echo "Please enter amount";
				exit;
			}if($amountoff=='' || $amountoff<1){
				echo "Please enter amount off";
				exit;
			}if($amountofftype==''){
				echo "Please select offer type";
				exit;
			}		
		}if($discounttype	== 4){
			$barcodeid	=	$_POST['barcode_productname4'] ; // replaced barcode_productname4 from barcode by yasir 17-08-11
			
			// added by yasir 17-08-11
			$sql		=	"SELECT pkbarcodeid FROM barcode WHERE barcode = '$barcodeid'";
			$result		=	$AdminDAO->queryresult($sql);
			$barcodeid	=	$result[0]['pkbarcodeid'];
			//
			
			$quantity			=$_POST['basequantitypp'];		
			$barcodes			=$_POST['barcodes'];
			$discountquantity	=$_POST['discountquantitypp'];
			//print_r($discountquantity);
			//exit;
			if($quantity=='' || $quantity<1){
				echo "Please enter quantity";
				exit;
			}if($discountquantity=='' || $discountquantity<1){
				echo "Please enter discount quantity ";
				exit;
			}if($barcodes=='' || count($barcodes)==0){
				echo "Please enter discounted product ";
				exit;
			}		
		}
		
		$fields1=array('fkstoreid'	,'discountname'	,'startdate','enddate'	,'fkdiscounttypeid'	,'discountstatus'	,'fkbarcodeid'	,'amount'	,'amountoff','amountofftype','quantity'	,'fkaddressbookid','updatetime');
		$data1	=array($storeid		,$discountname	,$startdate	,$enddate	,$discounttype		,$status			,$barcodeid,	$amount		,$amountoff	,$amountofftype	,$quantity	,$addressbookid		,$time		);
	
		if($discountid!="-1")
		{
	
			$insertid		=	$discountid;
			$AdminDAO->updaterow("discount",$fields1,$data1," pkdiscountid = '$discountid'");		
			
			$fields1=array('fkbarcodeid' 	,'quantity');		
			if($discounttype== 4){			
				$AdminDAO->deleterows('discountdetail',"fkdiscountid='$discountid'");
			if(count($barcodes)>0){
				//print_r(($barcodes));
					//foreach($barcodes as $fkbarcodeid){
					for($bar=0;$bar<count($barcodes);$bar++){
						
						$fkbarcodeid=$barcodes[$bar];
						 $prod=$productnames[$bar];
						 $discqty=$discountquantity[$bar];
						//echo "$fkbarcodeid called -- $prod <br>";
						if($prod=='' || $discqty==0){
							continue;	
						}else{
							$fields1	=	array('fkdiscountid' 	,'fkbarcodeid' 	,'quantity');
							$data1		=	array($discountid		,$fkbarcodeid	,$discqty );
							$insertid	=	$AdminDAO->insertrow("discountdetail",$fields1,$data1);
						}
					}
				}
			}elseif($discounttype== 1 ){//|| $discounttype== 2 || $discounttype== 3){
				$data1		=	array($barcodeid	,$discountquantity);
				$insertid	=	$AdminDAO->updaterow("discountdetail",$fields1,$data1," fkdiscountid = '$discountid' ");
			}		
		}	
		else	
		{
			$fkdiscountid		=	$AdminDAO->insertrow("discount",$fields1,$data1);
			$fields1=array('fkdiscountid' 	,'fkbarcodeid' 	,'quantity');
			if($discounttype== 1  || $discounttype== 4){	
				if($discounttype== 4){		
					if(count($barcodes)>0){
						//foreach($barcodes as $fkbarcodeid){
						for($bar=0;$bar<count($barcodes);$bar++){					
							$fkbarcodeid=$barcodes[$bar];
							$prod=$productnames[$bar];
							$discqty=$discountquantity[$bar];						
							//$data1		=array($fkdiscountid	,$fkbarcodeid	,$discountquantity	);
							$data1		=	array($fkdiscountid	,$fkbarcodeid	,$discqty	);
							$insertid	=	$AdminDAO->insertrow("discountdetail",$fields1,$data1);
						}
					}
				}else{			
					$data1		=array($fkdiscountid	,$barcodeid	,$discountquantity	);
					$insertid	=	$AdminDAO->insertrow("discountdetail",$fields1,$data1);			
				}
			}/*elseif($discounttype== 3  || $discounttype== 2){
				//echo $fkbarcodeid;//=$barcodeid;
				$data1		=array($fkdiscountid	,$barcodeid	,$discountquantity	);
				$insertid	=	$AdminDAO->insertrow("discountdetail",$fields1,$data1);				
				//echo "Invalid data";
				//exit;
			}*/
		}	
	}//end edit
}
else
{
	echo "Invalid data";
	exit;
}
?>