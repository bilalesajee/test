<?php 	include_once("../includes/security/adminsecurity.php");
		global $AdminDAO,$Component;
		error_reporting(7);

//print_r($_REQUEST);
				
$id			=	$_GET['id'];
$qstring	=	$_SESSION['qstring'];
$param		=	$_REQUEST['param'];
//if($id!='-1'){
//	$shiplist = $AdminDAO->getrows("shiplist inner join purchase on fkshiplistid=pkshiplistid","pkpurchaseid,pkshiplistid,datetime,shiplist.fkshipmentid,trim(barcode) barcode,boxbarcode,trim(itemdescription) itemdescription,shiplist.quantity,lastpurchaseprice,lastsaleprice,fkcountryid,fkstoreid,shiplist.fkaddressbookid,shiplist.fkcurrencyid,fkcountrylist,shiplist.weight,deadline,fkstatusid,fkbrandid,fkagentid,description,defaultimage,clientinfo"," purchase.fkshipmentid='$id' group by rtrim(ltrim(itemdescription)) ,rtrim(ltrim(barcode))");
//}
$stor			=	$AdminDAO->getrows("store","storecode,pkstoreid,storename as name","storedeleted<>1 AND storestatus=1");

$detaultstore='<select name="fkstoreid" id="fkstoreid">';
foreach($stor as $store){
	$detaultstore.='<option value="'.$store['pkstoreid'].'">'.$store['name'].'</option>';	
}
$detaultstore.='</select>';

$prod			=	$AdminDAO->getrows("product","pkproductid, productname as name","productdeleted<>1");

$detaultproduct='<select name="fkproductid" id="fkproductid">';
foreach($prod as $product){
	$detaultproduct.='<option value="'.$product['pkproductid'].'">'.$product['name'].'</option>';	
}
$detaultproduct.='</select>';
?>



<div id="loaditemscript"></div>
<div id="loading" style="display:none;"></div>
<div id="error" class="notice" style="display:none"></div>
<div id="testdiv" style="display:none;">before </div>
<?php if(count($_FILES)>0)

{ ?>

  
    <fieldset>
      <legend>
      <?php
    { echo "Import Item List from Excel";}	
    ?>
      </legend>
        <div style="float:right"> 
            <span class="buttons">
                <button type="button" class="positive" onclick="addshiplist(-1);"> <img src="../images/tick.png" alt=""/><?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>          </button>
                <a href="javascript:void(0);" onclick="hidediv('subsection');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> 
            </span> 
        </div>        
        <table width="100%">
        	<tr>
            	<td><?php 
				$t1 = time(); 
				echo  populate_file();
				$t2 = time();
				
				echo '<br/>'.floor((($t2-$t1)/60)).' minutes'; 
				
				?>
                </td>
            </tr>
        </table>        
       
  </fieldset>
 
<?php exit;}//else{?>


 <br>
<form id="distform" name="distform" style="width: 920px;" action="manageimportitems.php?id=-1" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      <?php
    { echo "Import Item List from Excel";}	
    ?>
      </legend>
        <div style="float:right"> 
            <span class="buttons">
                <button type="button" class="positive" onclick="getXL(-1);"> <img src="../images/tick.png" alt=""/><?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>          </button>
                <a href="javascript:void(0);" onclick="hidediv('subsection');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> 
            </span> 
        </div>       
        <table >
            <tr>
                <td height="0" valign="top">
                    <table  cellpadding="2" cellspacing="0" border="0" width="100%">
                        <tr>
                            <td width="30%" align="center"><input type='file' name='filename' id="filename"  /></td>
                        </tr> 		                                                       
                        <tr>
                            <td align="center">
                                <div class="buttons">
                                    <button type="button" class="positive" onclick="getXL(-1);"> <img src="../images/tick.png" alt=""/>
                                    <?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>
                                    </button>
                                    <a href="javascript:void(0);" onclick="hidediv('subsection');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> 
                                </div>
                            </td>
                        </tr>                        
                    </table>
                </td>
            </tr>
        </table>
      <input type="hidden" name="id" value ="<?php echo $id;?>" />     
    </fieldset>
  </form>
<?php //}?>

  <div id="shipfrmdiv" style="display: block;"></div> 
  <div id="similaritemsdiv"></div>
</div>

<?php 


function populate_file(){
	global $AdminDAO,$dbname_detail;
	//$table="test";	
	//print_r($_POST);	
	$filename=$_FILES['filename']['tmp_name'];
	$uploadpath="../xl/".time()."_".$_FILES["filename"]["name"];
	//move_uploaded_file($_FILES["filename"]["tmp_name"], "xl/".time()."_".$_FILES["filename"]["name"]);			
	
	if(move_uploaded_file($_FILES["filename"]["tmp_name"], $uploadpath)){
		$filename=	$uploadpath;
	}
	
	if (($handle = fopen($filename, "r")) !== FALSE) {		
		
		$rows = 0;
		$process = '';
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if ($rows > 0) { // skip first row of header
						
			  $num = count($data);
			 			  
			  for ($c=0; $c < $num; $c++) {			
				  $process[$rows][$c] = $data[$c];
			  }			  
			}
		 	$rows++;
		}		
		fclose($handle);
	}
	
	$curdate=time();
	$curfilepath="Errors$curdate.csv";
	$curfile = "../xl/".$curfilepath;
	
	if (!$handle1 = fopen($curfile, 'w+')) {
		 echo "Cannot open file ($curfile)";
		 exit;
	}	
	
	$success = 0;
	$failure = 0;			
	foreach($process as $val){
		
		$errormsg	=	'';
		$somecontent = "";
				
		// check for errors
		if ($val[0] == ''){		
			$failure++;
			$errormsg	=	'Barcode not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;		
		}
		
		if ($val[1] == ''){
			$failure++;
			$errormsg	=	'Item not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		if ($val[3] == ''){
			$failure++;
			$errormsg	=	'Weight not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		if ($val[4] == ''){
			$failure++;
			$errormsg	=	'Brand not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		if ($val[5] == ''){
			$failure++;
			$errormsg	=	'Supplier not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		if ($val[6] == ''){
			$failure++;
			$errormsg	=	'Country not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		if ($val[9] == ''){
			$failure++;
			$errormsg	=	'Quantity not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		if ($val[10] == ''){
			$failure++;
			$errormsg	=	'Expiry date not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		if ($val[11] == ''){
			$failure++;
			$errormsg	=	'Location not exists.';
			$somecontent=implode(",",$val).",".$errormsg."\n";
			// Write $somecontent to our opened file.
			if (!fwrite($handle1, $somecontent)) {
				echo "Cannot write to file ($curfile)";
				exit;
			}
			continue;
		}
		
		//	
		
				
		
		  
		$product	=	$AdminDAO->getrows("product","pkproductid"," LOWER(productname)='".strtolower($val[1])."'");
		
		if (($product[0]['pkproductid'])>0 && $product[0]['pkproductid']!=''){
		  $fkproductid	=	$product[0]['pkproductid'];
		} else {				
		  $productqry		=	"INSERT INTO product set productname ='$val[1]', productdescription='$val[2]',product_status='active'";
		  //$fkproductid	=	$AdminDAO->queryresult($productqry);	

				$tblj		 = 	"product";
				$field		 =	array('productname','productdescription','product_status');
				$value		 =	array(
										$val[1],																		
										$val[2],																		
										'active'
									 );
				$fkproductid = $AdminDAO->insertrow($tblj,$field,$value);		  
		}		  
		
		
		$country	=	$AdminDAO->getrows("countries","pkcountryid"," LOWER(code3)='".strtolower($val[6])."'");
		
		if(($country[0]['pkcountryid'])>0 && $country[0]['pkcountryid']!=''){
		  $fkcountryid	=	$country[0]['pkcountryid'];
		} else {
		  $countryqry		=	"INSERT INTO countries set code3 ='$val[6]'";
		  //$fkcountryid	=	$AdminDAO->queryresult($countryqry);
		  
				$tblj		 = 	"countries";
				$field		 =	array('code3');
				$value		 =	array(
										$val[6]
									 );
				$fkcountryid = $AdminDAO->insertrow($tblj,$field,$value);		  
		  
		  
		}	  
		
		
		$brand	=	$AdminDAO->getrows("brand","pkbrandid"," LOWER(brandname)='".strtolower($val[4])."' and fkcountryid='$fkcountryid'");
		
		if(($brand[0]['pkbrandid'])>0 && $brand[0]['pkbrandid']!=''){
		  $fkbrandid		=	$brand[0]['pkbrandid'];
		} else {
		  $brandqry		=	"INSERT INTO brand set brandname ='$val[4]', fkcountryid='$fkcountryid', displaytab='yes', brand_status='active'";
		 // $fkbrandid		=	$AdminDAO->queryresult($brandqry);
	  
				$tblj		 = 	"brand";
				$field		 =	array('brandname','fkcountryid','displaytab','brand_status');
				$value		 =	array(
										$val[4],
										$fkcountryid,
										'yes',
										'active'
									 );
				$fkbrandid   = $AdminDAO->insertrow($tblj,$field,$value);			  
		  
		}
		
		$barcode	=	$val[0];
	  
		$bar	=	$AdminDAO->getrows("barcode"," pkbarcodeid"," barcode='".$barcode."'");
				
		if(($bar[0]['pkbarcodeid'])>0 && $bar[0]['pkbarcodeid']!=''){
		  $fkbarcodeid	=	$bar[0]['pkbarcodeid'];
		}else{						
		  $barcodeqry="INSERT INTO barcode set barcode ='$val[0]',itemdescription='$val[1]',fkproductid='$fkproductid', barcode_status='active'";
		  //$fkbarcodeid=$AdminDAO->queryresult($barcodeqry);
		  
				$tblj		 = 	"barcode";
				$field		 =	array('barcode','itemdescription','fkproductid','barcode_status');
				$value		 =	array(
										$val[0],
										$val[1],
										$fkproductid,
										'active'
									 );
				$fkbarcodeid   = $AdminDAO->insertrow($tblj,$field,$value);			  
		}
		
		$brandbarcodeqry="INSERT INTO barcodebrand set fkbrandid ='$fkbrandid',fkbarcodeid='$fkbarcodeid'";
		//$pkbarcodebrandid=$AdminDAO->queryresult($brandbarcodeqry);
		
				$tblj		 = 	"barcodebrand";
				$field		 =	array('fkbrandid','fkbarcodeid');
				$value		 =	array(
										$fkbrandid,
										$fkbarcodeid,
									 );
				$pkbarcodebrandid   = $AdminDAO->insertrow($tblj,$field,$value);				
					
		
		$attribute	=	$AdminDAO->getrows("attribute","pkattributeid"," attributename='Weight'");
		
		if(($attribute[0]['pkattributeid'])>0 && $attribute[0]['pkattributeid']!=''){
		  $fkattributeid		=	$attribute[0]['pkattributeid'];
		} else {
		  $attributeqry		=	"INSERT INTO attribute set attributename ='Weight'";
		  //$fkattributeid	=	$AdminDAO->queryresult($attributeqry); 

				$tblj		 = 	"attribute";
				$field		 =	array('attributename');
				$value		 =	array(
										'Weight'
									 );
				$fkattributeid   = $AdminDAO->insertrow($tblj,$field,$value);
		  
		}
		
		$attributeoption	=	$AdminDAO->getrows("attributeoption","pkattributeoptionid"," LOWER(attributeoptionname)='$val[3]'");
		
		if(($attributeoption[0]['pkattributeid'])>0 && $attributeoption[0]['pkattributeid']!=''){
		  $pkattributeoptionid		=	$attributeoption[0]['pkattributeoptionid'];
		} else {
		  $attributeoptionqry="INSERT INTO attributeoption set attributeoptionname ='$val[3]',fkattributeid='$fkattributeid'";
		  //$pkattributeoptionid=$AdminDAO->queryresult($attributeoptionqry); 
		  
				$tblj		 = 	"attributeoption";
				$field		 =	array('attributeoptionname','fkattributeid');
				$value		 =	array(
										$val[3],
										$fkattributeid,
									 );
				$pkattributeoptionid   = $AdminDAO->insertrow($tblj,$field,$value);			  
		  
		}
		
		$productattributeqry="INSERT INTO productattribute set fkproductid ='$fkproductid',fkattributeid='$fkattributeid'";
		//$pkproductattributeid=$AdminDAO->queryresult($productattributeqry);

				$tblj		 = 	"productattribute";
				$field		 =	array('fkproductid','fkattributeid');
				$value		 =	array(
										$fkproductid,
										$fkattributeid,
									 );
				$pkproductattributeid   = $AdminDAO->insertrow($tblj,$field,$value);	
		
		
		$productinstanceqry="INSERT INTO productinstance set fkproductattributeid ='$pkproductattributeid',fkattributeoptionid='$pkattributeoptionid', fkbarcodeid= '$fkbarcodeid'";
		//$pkproductinstanceid=$AdminDAO->queryresult($productinstanceqry);
		
				$tblj		 = 	"productinstance";
				$field		 =	array('fkproductattributeid','fkattributeoptionid','fkbarcodeid');
				$value		 =	array(
										$pkproductattributeid,
										$pkattributeoptionid,
										$fkbarcodeid
									 );
				$pkproductinstanceid   = $AdminDAO->insertrow($tblj,$field,$value);		
		
		
		
		$addressbook	=	$AdminDAO->getrows("addressbook","pkaddressbookid"," LOWER(CONCAT( firstname,lastname ))='".strtolower(str_replace(' ','',$val[5]))."'");
		
		if(($addressbook[0]['pkaddressbookid'])>0 && $addressbook[0]['pkaddressbookid']!=''){
		  $fkaddressbookid		=	$addressbook[0]['pkaddressbookid'];
		} else {
		  $name = explode(' ',$val[5]);
		  
		  if (count($name) > 1){
			  $firstname	=	$name[0];
			  $lastname	=	$name[1];
		  } else {
			  $firstname	=	$val[5];
			  $lastname	=	'';
		  }
			
		  $addressbookqry		=	"INSERT INTO addressbook set firstname ='$firstname', lastname ='$lastname'";
		  //$fkaddressbookid	=	$AdminDAO->queryresult($addressbookqry);
		  
				$tblj		 = 	"addressbook";
				$field		 =	array('firstname','lastname');
				$value		 =	array(
										$firstname,
										$lastname
									 );
				$fkaddressbookid   = $AdminDAO->insertrow($tblj,$field,$value);			  
		  
		}
		
		$supplier	=	$AdminDAO->getrows("supplier","pksupplierid"," LOWER(companyname)='".strtolower($val[5])."'");
		
		if(($supplier[0]['pksupplierid'])>0 && $supplier[0]['pksupplierid']!=''){
		  $fksupplierid		=	$supplier[0]['pksupplierid'];
		} else {		  
		  $supplierqry		=	"INSERT INTO supplier set companyname ='$val[5]', fkaddressbookid ='$fkaddressbookid'";
		  //$fksupplierid		=	$AdminDAO->queryresult($supplierqry);
		  
				$tblj		 = 	"supplier";
				$field		 =	array('companyname','fkaddressbookid');
				$value		 =	array(
										$val[5],
										$fkaddressbookid
									 );
				$fksupplierid   = $AdminDAO->insertrow($tblj,$field,$value);		  
		}
		
		$brandsupplier	=	$AdminDAO->getrows("brandsupplier","pkbrandsupplierid"," fkbrandid='$fkbrandid' AND fksupplierid ='$fksupplierid'");
		
		if(($brandsupplier[0]['pkbrandsupplierid'])>0 && $brandsupplier[0]['pkbrandsupplierid']!=''){
		  $fkbrandsupplierid		=	$brandsupplier[0]['pkbrandsupplierid'];
		} else {		  
		  $brandsupplierqry	=	"INSERT INTO brandsupplier set fkbrandid ='$fkbrandid', fksupplierid ='$fksupplierid'";
		  //$fkbrandsupplierid	=	$AdminDAO->queryresult($brandsupplierqry);
		  
				$tblj		 = 	"brandsupplier";
				$field		 =	array('fkbrandid','fksupplierid');
				$value		 =	array(
										$fkbrandid,
										$fksupplierid
									 );
				$fkbrandsupplierid   = $AdminDAO->insertrow($tblj,$field,$value);			  
		}
		
		$date		=	explode('/',$val[10]);
		$expiry	=	mktime(0,0,0,$date[1],$date[0],$date[2]);
		
		$updatetime	=	time();
		
	    $stockqry	=	"INSERT INTO $dbname_detail.stock set quantity ='$val[9]', unitsremaining = '$val[9]', expiry ='$expiry', purchaseprice ='$val[7]', costprice='$val[7]', retailprice='$val[8]', priceinrs='$val[7]', fkbarcodeid='$fkbarcodeid', fksupplierid='$fksupplierid', fkstoreid='$val[11]', fkemployeeid='{$_SESSION['addressbookid']}', fkbrandid='$fkbrandid', updatetime='$updatetime' ";
		//$fkstockid	=	$AdminDAO->queryresult($stockqry);
		
				$tblj		 = 	$dbname_detail."stock";
				$field		 =	array('quantity','unitsremaining','expiry','purchaseprice','costprice','retailprice','priceinrs','fkbarcodeid','fksupplierid','fkstoreid','fkemployeeid','fkbrandid','updatetime','addtime');
				$value		 =	array(
										$val[9],
										$val[9],
										$expiry,
										$val[7],
										$val[7],
										$val[8],
										$val[7],
										$fkbarcodeid,
										$fksupplierid,
										$val[11],
										$_SESSION['addressbookid'],
										$fkbrandid,
										$updatetime,
										time()
									 );
				$fkstockid   = $AdminDAO->insertrow($tblj,$field,$value);			
		
		$success++;
	}
	
	fclose($handle1);
	
	$output	=	'Total Records Inserted: '.$success;
	$output	.=	'<br/>Errors in Records: '.$failure;		
	
	return $output;
}
?>
<script type="text/javascript">
function getXL(id){
	//false;
	options	=	{	
					url : 'manageimportitems.php',
					type: 'POST',
					success: response1
				}
	jQuery('#distform').ajaxSubmit(options);
}
function response1(text1){
	document.getElementById('shipfrmdiv').style.display="block";
	document.getElementById('shipfrmdiv').innerHTML=text1;	
	if(text1==1)	{
		//adminnotice('Distributions has been saved.',0,5000);
		//jQuery('#distform').slideUp();
	}else{
		//adminnotice(text,0,5000);
	}
}

function addshiplist(id){
	//false;
	options	=	{	
					url : 'manageimportitems.php',
					type: 'POST',
					success: response
				}
	jQuery('#frmimp').ajaxSubmit(options);
}
function response(text2){
	//if(text==1)	{
	//	adminnotice('Distributions has been saved.',0,5000);
		//jQuery('#distform').slideUp();
	document.getElementById('shipfrmdiv').style.display="none";
	document.getElementById('similaritemsdiv').style.display="block";
	document.getElementById('similaritemsdiv').innerHTML=text2;
	
	
	//}else{
		adminnotice(text,0,5000);
	//}
}
</script>