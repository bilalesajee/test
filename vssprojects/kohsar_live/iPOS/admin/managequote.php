<?php 	include_once("../includes/security/adminsecurity.php");
		global $AdminDAO,$Component;
		error_reporting(7);
		//print_r($_POST);
$fkshipmentid	=$id			=	$_REQUEST['id'];
$qstring	=	$_SESSION['qstring'];
$param		=		$_REQUEST['param'];
$action		=		$_REQUEST['action'];
?>
<div id="loaditemscript"></div>
<div id="loading" style="display:none;"></div>
<div id="error" class="notice" style="display:none"></div>
  
    <fieldset>
      <legend>
      <?php
    { echo "Generate Quote";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="reload_list(-1);"> <img src="../images/tick.png" alt=""/>
        <?php {echo "Calculate";} //if($id=='-1') else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
      <form id="frmpurchase" name="frmpurchase" action="managequote.php" class="form"  enctype="multipart/form-data">
       <table width="100%">
            <tr>
                <td height="12" valign="top">
                	<!--<div class="topimage2" style="height:6px;"></div>-->
                    <table  cellpadding="2" cellspacing="0" border="0" width="100%">
                          <tbody>                        
                            <tr>
                                <td>Add Percentage</td>
                                <td><input type="text" value="<?php echo $_REQUEST['add_percent'];?>" id="add_percent" name="add_percent" ></td>
                                <td>Deduce Percentage</td>
                                <td><input type="text" value="<?php echo $_REQUEST['deduce_percent'];?>" id="deduce_percent" name="deduce_percent"></td>
                              </tr>
                              <tr>
                                <td>Add Flat</td>
                                <td><input type="text" value="<?php echo $_REQUEST['add_flat'];?>" id="add_flat" name="add_flat"></td>
                                <td>Deduce Flat</td>
                                <td><input type="text" value="<?php echo $_REQUEST['deduce_flat'];?>" id="deduce_flat" name="deduce_flat"></td>
                              </tr>
                            <tr>                            
                              <td  colspan="5" align="center"><div class="buttons">
                                  <button type="button" class="positive" onclick="reload_list(-1);"> <img src="../images/tick.png" alt=""/>
                                  <?php {echo "Calculate";} //if($id=='-1') else {echo "Update";} ?>
                                  </button>
                                  <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
                            </tr>
                          </tbody>
                    </table>                   
                </td>
            </tr>
        </table>
         
        <div id="load_result">
        
        	<?php load_result()?>
        
        
        </div>
        <input type="hidden" name="id" value ="<?php echo $id;?>"/>
        </form>
      
   </fieldset>
 
  <div id="similaritemsdiv"></div>
</div>
<?php 

function load_result(){
	global $stor,$AdminDAO,$id,$action;
	$stor			=	$AdminDAO->getrows("store","storecode,pkstoreid,storename as name","storedeleted<>1 AND storestatus=1");
	
	if($id!='-1'){
	//$shiplist = $AdminDAO->getrows("shiplist inner join purchase on fkshiplistid=pkshiplistid",   "pkpurchaseid,pkshiplistid,datetime,shiplist.fkshipmentid,trim(barcode) barcode,boxbarcode,trim(itemdescription) itemdescription,shiplist.quantity,lastpurchaseprice,lastsaleprice,fkcountryid,fkstoreid,shiplist.fkaddressbookid,shiplist.fkcurrencyid,fkcountrylist,shiplist.weight,deadline,fkstatusid,fkbrandid,fkagentid,description,defaultimage,clientinfo",							   " purchase.fkshipmentid='$id' ");	//group by rtrim(ltrim(itemdescription)) ,rtrim(ltrim(barcode))
	
	$where="";
	if($_POST['fkbarcodeid']){
		$fkbarcodeid=$_POST['fkbarcodeid'];		
		$where.=" and fkbarcodeid='$fkbarcodeid' ";			
	}	
	if($_POST['fksupplierid']){
		$fksupplierid=$_POST['fksupplierid'];		
		$where.=" and fksupplierid='$fksupplierid' ";				
	}
	if($_POST['fkcountryid']){
		$fkcountryid=$_POST['fkcountryid'];		
		//$where.=" and fkcountryid='$fkcountryid' ";				
	}
	if($_POST['fkbrandid'])	{
		$fkbrandid=$_POST['fkbrandid'];		
		$where.=" and fkbrandid='$fkbrandid' ";				
	}
	if($_POST['fkproductid']){
		$fkproductid=$_POST['fkproductid'];		
		$where.=" and fkproductid='$fkproductid' ";				
	}
	if($_POST['fkcurrencyid']){
		$fkcurrencyid=$_POST['fkcurrencyid'];		
		//$where.=" and fkcurrencyid='$fkcurrencyid' ";				
	}
	
	if($_POST['add_percent']){
		$add_percent=$_POST['add_percent'];		
		//$where.=" and fkcurrencyid='$fkcurrencyid' ";				
	}
	if($_POST['deduce_percent']){
		$deduce_percent=$_POST['deduce_percent'];		
		//$where.=" and fkcurrencyid='$fkcurrencyid' ";				
	}
	if($_POST['add_flat']){
		$add_flat=$_POST['add_flat'];		
		//$where.=" and fkcurrencyid='$fkcurrencyid' ";				
	}
	if($_POST['deduce_percent']){
		$deduce_flat=$_POST['deduce_flat'];		
		//$where.=" and fkcurrencyid='$fkcurrencyid' ";				
	}
	if($_POST['fkweightid']){
		$fkweightid=$_POST['fkweightid'];			
	}
	
	
	
	if($_POST['fkweightid']){
		//1gm  2 Kg 3 lbs 4 Metric Ton 5 Ounce 6 Stones
		switch($_POST['fkweightid']){
			case "1": $weight_unit= "Gram"; 
			break; 				
			case "2": $weight_unit= "Kg"; 
			break; 				
			case "3": $weight_unit= "Lbs"; 
			break; 				
			case "4": $weight_unit= "Metric Ton"; 
			break; 				
			case "5": $weight_unit= "Ounce"; 
			break; 				
			case "6": $weight_unit= "Stone"; 
			break;
		}
	}	
	echo $where;	
	
	$shiplist = $AdminDAO->getrows("purchase as p 
								   inner join shiplist on fkshiplistid=pkshiplistid
								   left join barcode as b on fkbarcodeid=pkbarcodeid
								   left join supplier as s on fksupplierid=pksupplierid
								   left join countries as c on fkcountryid=pkcountryid
								   left join currency as cu on p.fkcurrencyid=pkcurrencyid
								   left join brand as bd on fkbrandid=pkbrandid		
								   left join store as st on fkstoreid=pkstoreid	
								   left join product as pd on fkproductid=pkproductid	
								   ",
							   "storename,code3,fkbrandid,brandname,companyname,b.itemdescription,pkpurchaseid, 
							   fkshiplistid, fkshiplistdetailsid, fkbarcodeid,
							   purchasetime, p.quantity, purchaseprice, p.fkcurrencyid ,	
							   currencyrate, p.weight, fksupplierid, batch, expiry, 	
							   p.fkshipmentid, p.fkaddressbookid",
							   " p.fkshipmentid='$id' $where");
	}
	$datetime=time();
	if($action=='save'){
		$qry=" INSERT INTO quote set 
		datetime='$datetime', 	weight_unit ='$weight_unit',	fkcurrencyid ='$fkcurrencyid',	
		fkshipmentid ='$id',	fkbrandid ='$fkbrandid',	fkproductid ='$fkproductid',	
		fksupplierid ='$fksupplierid',	fkcountryid ='$fkcountryid',	
		addpercent ='$add_percent',	deducepercent ='$deduce_percent',	addflat ='$addflat',deduceflat='$deduce_flat'";		
		//$fkquoteid=$AdminDAO->queryresult($qry);
		
				$tblj		 = 	"quote";
				$field		 =	array('datetime','weight_unit','fkcurrencyid','fkshipmentid','fkbrandid','fkproductid','fksupplierid','fkcountryid','addpercent','deducepercent','addflat','deduceflat');
				$value		 =	array(
										$datetime,
										$weight_unit,
										$fkcurrencyid,
										$id,
										$fkbrandid,
										$fkproductid,
										$fksupplierid,
										$fkcountryid,
										$add_percent,
										$deduce_percent,
										$addflat,
										$deduce_flat
									 );
				$fkquoteid   = $AdminDAO->insertrow($tblj,$field,$value);			
	}
	$selectclass=' style="width:100px;"';
?>	

<p>
  	<?php echo getitem();?>&nbsp; | &nbsp;<?php echo getproduct();?> &nbsp;|&nbsp;<?php echo getallbrands(0)." </select>";?> &nbsp;|&nbsp;<?php echo getsupplier();?> &nbsp;|&nbsp; <?php echo getcountry();?> 	
</p>

<br />

<p>
Weight in 
    <select id="fkweightid" name="fkweightid"  <?php echo $selectclass;?>>
      <option value="1" <?php if($fkweightid=='1') echo " selected=selected ";?>>Grams</option>
      <option value="2" <?php if($fkweightid=='2') echo " selected=selected ";?>>Kg</option>      
      <option value="3" <?php if($fkweightid=='3') echo " selected=selected ";?>>lbs</option>
      <option value="4" <?php if($fkweightid=='4') echo " selected=selected ";?>>Metric Ton</option>
      <option value="5" <?php if($fkweightid=='5') echo " selected=selected ";?>>Ounce</option>
      <option value="6" <?php if($fkweightid=='6') echo " selected=selected ";?>>Stones</option>
    </select> 
    
    
    
  Currency 
  <?php echo getcurrency(); ?>
 <div class="buttons">
 <button type="button" class="positive" onclick="reload_list(-1);"> <img src="../images/tick.png" alt=""/>
 	<?php {echo "Convert";} //if($id=='-1') else {echo "Update";} ?>
 </button>
</div>
</p>

<table width="129%">
    <tr>
        <td height="12" valign="top">
            <div class="topimage2" style="height:6px;"></div>
            <table  cellpadding="2" cellspacing="0" border="0" width="100%">
              <tbody>
            
                    <tr>
                        <th width="10%">S #</th>
                        <th><strong>Item</strong></th>
                        <th><strong>Price</strong></th>
                        <th><strong>Weight</strong></th>
                        <th><strong>Country</strong></th>
                        <th><strong>Brand</strong></th>
                        <th><strong>Supplier</strong></th>
                        <th><strong>Source</strong></th>   
                      </tr> 
                      
                    <?php  $numb	=$tweight	=$tprice	=0;
                    
                    if(count($shiplist)>0){	
                        foreach($shiplist as $slist)
                        {
                            $pkbarcodeid 		= 	$slist['pkbarcodeid'];
                            $barcode 			= 	$slist['barcode'];                            
                            $selected_brand		=	$slist['fkbrandid'];  
                            $selected_country	=	$slist['fkcountryid'];
                            $selected_store		=	$slist['fkstoreid'];
                            $selected_emp		=	$slist['fkaddressbookid'];
                            $currencyid			=	$slist['pkcurrencyid'];
                            $currencysymbol		=	$slist['currencysymbol'];
                            $lastpurchaseprice 	= 	$slist['lastpurchaseprice'];
                            $lastsaleprice 		= 	$slist['lastsaleprice'];
							
							$suppliers			=	$slist['companyname'];
                            $pkpurchaseid 		= 	$slist['pkpurchaseid'];							
							$itemdescription 	= 	$slist['itemdescription'];
                            $quantity 			= 	$slist['quantity'];
                            $weight				=	$slist['weight'];							

							//  1gm  2 Kg 3 lbs 4 Metric Ton 5 Ounce 6 Stones
							if($_REQUEST['fkweightid']!=''){
								$fkweightid	=	$_REQUEST['fkweightid'];
								if($fkweightid==1)
									$weight	=($weight	/1000);
								elseif($fkweightid==2)
									$weight	=$weight	;
								elseif($fkweightid==3)
									$weight	=($weight	/2.20462);
								elseif($fkweightid==4)
									$weight	=($weight	* 1000);
								elseif($fkweightid==5)
									$weight	=($weight	/35.27396);
								elseif($fkweightid==6)
									$weight	=($weight	/.15747);		
									
							}						
							$weight=round($weight,2);
                            $price		 		= 	$slist['purchaseprice'];
							
							if($add_percent){
								$price		 	= 	$price + (($price/100)*$add_percent);
							}
							if($deduce_percent){
								$price		 	= 	$price - (($price/100)*$deduce_percent);
							}
							if($add_flat){
								$price		 	= 	($price + $add_flat);
							}
							if($deduce_flat){
								$price		 	= 	($price - $deduce_flat);
							}
							
							$brand		 		= 	$slist['brandname'];								
							$country	 		= 	$slist['code3'];
							$store		 		= 	$slist['storename'];                // $deadline			=	implode("-",array_reverse(explode("-",$slist['deadline'])));
							if($_REQUEST['fkcurrencyid']){
								$sql			=	"SELECT pkcurrencyid, currencyname,currencysymbol,	rate,fkcountryid from currency ";
								$rates			=	$AdminDAO->queryresult($sql);
								$rate			=	$rates[0]['rate'];
								$currencysymbol	=	$rates[0]['currencysymbol'];
								$price			=	$currencysymbol.round(($price/$rate),2);
							}
							if(($fkquoteid)>0){
								$qry=" INSERT INTO quotedetails set itemdescription='$itemdescription',	price='$price', 
								weight='$weight', country='$country',brand='$brand', supplier='$suppliers', 
								source='$store', fkquoteid='$fkquoteid' ";		
								//$AdminDAO->queryresult($qry);
								
				$tblj		 = 	"quotedetails";
				$field		 =	array('itemdescription','price','weight','country','brand','supplier','source','fkquoteid');
				$value		 =	array(
										$itemdescription,
										$price,
										$weight,
										$country,
										$brand,
										$suppliers,
										$store,
										$fkquoteid
									 );
				$AdminDAO->insertrow($tblj,$field,$value);			
								
								
							}
							
                            ?>        
                            
                <tr class="even">        
                    <td valign="top"><!--<input type="checkbox" value="<?php echo $pkpurchaseid; ?>" name="purchaseid[<?php echo $numb;?>]" id="purchaseid"/>--><?php echo ($numb+1);?></td>
                        <td><?php echo $itemdescription;?></td>
                            <td><?php $tprice+=$price; 	echo $price;?></td>
                            <td><?php $tweight+=$weight; echo $weight;?></td>
                            <td><?php echo $country;?></td>
                            <td><?php echo $brand;?></td>
                            <td><?php echo $suppliers;?></td>
                            <td><?php echo $store;?></td>
               		 </tr>
                            
            <?php $numb++;} 
            }else{
                ?><tr>
                    <td colspan="5" align="center"><strong>No Item Found</strong></td>
                </tr> 		
            <?php 
                }	
				
				if(count($shiplist)>0){?>
				
				<tr class="even">        
                    <td valign="top">&nbsp;</td>
                        <td><strong>Total </strong></td>
                            <td><strong><?php echo $tprice;?></strong></td>
                            <td><strong><?php echo $tweight;?></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
               		 </tr>
				
				
				<?php }?>
                        
                <tr>
                  <td colspan="12">
                  </td>
                </tr>
                <tr>
                  <td colspan="12" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="save_list(-1);"> <img src="../images/tick.png" alt=""/>
                      <?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>
                      </button>
                      <?php if($_GET['action']=='save'){?>
                          <button type="button" class="positive" onclick="print_list(<?php echo $fkquoteid;?>);"> <img src="../images/tick.png" alt=""/>
                          <?php {echo "Print";} //if($id=='-1') else {echo "Update";} ?>
                          </button>
                         <!-- <button type="button" class="positive" onclick="reload_list(-1);"> <img src="../images/tick.png" alt=""/>
                          <?php {echo "Email";} //if($id=='-1') else {echo "Update";} ?>
                          </button>
                          <button type="button" class="positive" onclick="reload_list(-1);"> <img src="../images/tick.png" alt=""/>
                          <?php {echo "Export Excel";} //if($id=='-1') else {echo "Update";} ?>
                          </button>-->
                      <?php }?>
                      <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div>
                      
                      
                      </td>
                </tr>
              </tbody>
            </table>
        </td>
    </tr>
</table>	

<script type="text/javascript">
function reload_list(id){
	false;
	options	=	{	
					url : 'managequote.php',
					type: 'POST',
					success: response
				}
	jQuery('#frmpurchase').ajaxSubmit(options);
}
function save_list(id){
	false;
	options	=	{	
					url : 'managequote.php?action=save',
					type: 'POST',
					success: response
				}
	jQuery('#frmpurchase').ajaxSubmit(options);
}

function response(text2){
	//if(text==1)	{
		adminnotice('Data has been saved.',0,5000);
		//jQuery('#subsection').slideUp();
		document.getElementById('subsection').style.display="block";
		document.getElementById('subsection').innerHTML=text2;
	//}else{
		//adminnotice(text,0,5000);
	//}
}
function print_list1(id){
	false;
	options	=	{	
					url 	: 'http:\\esajee\admin\printquote.php',
					type	: 'POST',
					success	: response
				}
	jQuery('#frmpurchase').ajaxSubmit(options);
}

function print_list(id) {
	win = window.open('printquote.php?id='+id,'frmpurchase1');		
	document.frmpurchase1.method='POST';
	document.frmpurchase1.target='frmpurchase1';	
	document.frmpurchase1.submit();	
}

</script>


<?php 	
}

function getallbrands($brandid='0'){
	global $AdminDAO,$brandcondition	;

	$sql="SELECT brandname,pkbrandid,fkparentbrandid from brand where branddeleted<>1 $brandcondition and fkparentbrandid='".$brandid."' order by pkbrandid  limit 10";
	$brands=$AdminDAO->queryresult($sql);
	
	if(count($brands)>0){		
		if($brandid=='0'){
			$brands1		=	" <select name='fkbrandid' id='fkbrandid' style='width:65px;'>
			<option value=''>Brand</option>";
		}
		for($i=0;$i<sizeof($brands);$i++){
			$brandname	=	$brands[$i]['brandname'];
			$brandidin	=	$brands[$i]['pkbrandid'];
			$selected_brand	=	$brands[$i]['fkparentbrandid'];
			$selected_brand	=	$_REQUEST['fkbrandid'];
			$selected=(($brandidin == $selected_brand)?' selected=selected ':''); 
			if($fkparentbrandid==0)
				$brands1	.=	"<option value='".$brandidin."' $selected  style='font-weight: bold;'>$brandname</strong>";
			else
				$brands1	.=	"<option value='".$brandidin."' $selected  style='font-weight: italic;'>&nbsp;&nbsp;&nbsp;$brandname</option>";
			$brands1	.=	getallbrands($brandidin);
		}		
		//$brands1		.=	" </select>";
	}else{		
		return '';			
	}
	return $brands1;
}
function getitem(){
	global $AdminDAO;
	$sql="SELECT itemdescription,pkbarcodeid from barcode order by pkbarcodeid  limit 10";
	$brands=$AdminDAO->queryresult($sql);	
	if(count($brands)>0){
		$brands1		=	"<select name='fkbarcodeid' id='fkbarcodeid' style='width:65px;'>
		<option value=''>Item</option>";
		for($i=0;$i<sizeof($brands);$i++){
			$brandname	=	$brands[$i]['itemdescription'];
			$brandid	=	$brands[$i]['pkbarcodeid'];
			$selected_brand	=	$_REQUEST['fkbarcodeid'];
			$selected=(($brandid == $selected_brand)?' selected=selected ':''); 
			$brands1	.=	"<option value='$brandid' $selected  '>$brandname</option>";			
		}
				$brands1		.=	" </select>";
	}else{		
		return '';			
	}
	return $brands1;
}
function getsupplier(){
	global $AdminDAO;
	$sql="SELECT companyname,pksupplierid from supplier  limit 10";
	$brands=$AdminDAO->queryresult($sql);	
	if(count($brands)>0){
		$brands1		=	"<select name='fksupplierid' id='fksupplierid' style='width:65px;'>
		<option value=''>Supplier</option>";
		for($i=0;$i<sizeof($brands);$i++){
			$brandname	=	$brands[$i]['companyname'];
			$brandid	=	$brands[$i]['pksupplierid'];
			$selected_brand	=	$_REQUEST['fksupplierid'];
			$selected=(($brandid == $selected_brand)?' selected=selected ':''); 
			$brands1	.=	"<option value='".$brandid."' $selected  '>$brandname</option>";			
		}
				$brands1		.=	" </select>";		
	}else{		
		return '';			
	}
	return $brands1;
}
function getproduct(){
	global $AdminDAO;
	$sql="SELECT pkproductid,	productname from product limit 10";
	$brands=$AdminDAO->queryresult($sql);	
	if(count($brands)>0){
		$brands1		=	"<select name='fkproductid' id='fkproductid' style='width:65px;'>
		<option value=''>Product</option>";
		for($i=0;$i<sizeof($brands);$i++){
			$brandname	=	$brands[$i]["productname"];
			$brandid	=	$brands[$i]["pkproductid"];
			$selected_brand	=	$_REQUEST['fkproductid'];
			$selected=(($brandid == $selected_brand)?' selected=selected ':''); 
			$brands1	.=	"<option value='".$brandid."' $selected  '>$brandname</option>";			
		}
				$brands1		.=	" </select>";		
	}else{		
		return '';			
	}
	return $brands1;
}

function getcountry(){
	global $AdminDAO;
	$sql="SELECT pkcountryid,	code3 from countries  limit 10";
	$brands=$AdminDAO->queryresult($sql);	
	if(count($brands)>0){
		$brands1		=	"<select name='fkcountryid' id='fkcountryid' style='width:65px;'>
		<option value=''>Product</option>";
		for($i=0;$i<sizeof($brands);$i++){
			$brandname	=	$brands[$i]["code3"];
			$brandid	=	$brands[$i]["pkcountryid"];
			$selected_brand	=	$_REQUEST['fkcountryid'];
			$selected=(($brandid == $selected_brand)?' selected=selected ':''); 
			$brands1	.=	"<option value='".$brandid."' $selected  '>$brandname</option>";			
		}
				$brands1		.=	" </select>";		
	}else{		
		return '';			
	}
	return $brands1;
}
function getcurrency(){
	global $AdminDAO;
	$sql="SELECT pkcurrencyid, currencyname,currencysymbol,	rate,fkcountryid from currency ";
	$brands=$AdminDAO->queryresult($sql);	
	if(count($brands)>0){
		$brands1		=	"<select name='fkcurrencyid' id='fkcurrencyid' style='width:65px;'>
		<option value=''>Currency</option>";
		for($i=0;$i<sizeof($brands);$i++){
			$brandname		=	$brands[$i]["currencyname"];
			$brandid		=	$brands[$i]["pkcurrencyid"];
			$selected_brand	=	$_REQUEST['fkcurrencyid'];
			$selected=(($brandid == $selected_brand)?' selected=selected ':''); 
			$brands1	.=	"<option value='".$brandid."' $selected  '>$brandname</option>";			
		}
				$brands1		.=	" </select>";		
	}else{		
		return '';			
	}
	return $brands1;
}
?>






