<?php if(@$_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
    <link href="../includes/css/style.css" rel="stylesheet" type="text/css" />
    <script src="../includes/js/jquery.js"></script>
    <script src="../includes/js/jquery.form.js"></script>
    <script src="../includes/js/common.js"></script>
    <body style="background-color:#FFF;">
    <?php
    include_once("../includes/security/adminsecurity.php");
    global $AdminDAO;
    $id			=	$_REQUEST['id'];
    $storeid	=	$_SESSION['storeid'];
    //selecting customers
        $customerinfo	=	$AdminDAO->getrows("$dbname_detail.purchaseorder,$dbname_detail.account,$dbname_detail.addressbook LEFT JOIN city ON (fkcityid=pkcityid)","concat(firstname,' ',lastname) as name,concat(address1,' ',address2) as address,cityname","pkpurchaseorderid='$id' AND fkaccountid=id AND account.fkaddressbookid=pkaddressbookid");
        $customername	=	$customerinfo[0]['name'];
        $address		=	$customerinfo[0]['address'];
        $cityname	=	$customerinfo[0]['cityname'];
      
        $customerinfo	=	$AdminDAO->getrows("addressbook LEFT JOIN city ON (fkcityid=pkcityid)","concat(firstname,' ',lastname) as name,concat(address1,' ',address2) as address,cityname","pkaddressbookid='$empid'");
        $username		=	$customerinfo[0]['name'];
        $useraddress	=	$customerinfo[0]['address'];
        $usercity		=	$customerinfo[0]['cityname'];
        $storeaddress	=	$AdminDAO->getrows("store LEFT JOIN city ON (fkcityid=pkcityid)","storename,storephonenumber,storeaddress","pkstoreid='$storeid'");
        $storename		=	$storeaddress[0]['storename'];
        $phonenumber	=	$storeaddress[0]['storephonenumber'];
        $storeaddress	=	$storeaddress[0]['storeaddress'];
    ?>
    <div style="width:6.0in;padding:0px;font-size:17px;" align="center">
        <img src="../images/esajeelogo.jpg" width="150" height="50">
    <br />
    <span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
        <b>Think globally shop locally</b>
        <?php echo "<br>".$storename."<br>Tel: ".$phonenumber."<br> ".$storeaddress;?>
    <br /></span>
        <div style="font:Arial, Helvetica, sans-serif;font-size:11px;" align="left">
            To,<br /><?php echo $customername.",<br/>".$address.",<br/>".$cityname;?>
        <br />Subject:<strong>Quotation</strong>
        </div>
    <table style="width:6.0in; margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif; border-collapse:collapse;" align="left">
    <tr>
        <th style="padding:5px;">Sr. #</th>
        <th style="padding:5px;">Barcode</th>
        <th style="padding:5px;">Item</th>
        <th style="padding:5px;">Quote Price</th>
    </tr>
      <?php 
      $data	=	$AdminDAO->getrows("$dbname_detail.podetail d,barcode b","barcode,itemdescription,d.quoteprice price,addtime","fkpurchaseorderid = '$id' AND fkbarcodeid = b.pkbarcodeid");
      for($i=0;$i<sizeof($data);$i++)
      {
            $barcode 	=	$data[$i]['barcode'];
            $item		=	$data[$i]['itemdescription'];
            $quoteprice	=	$data[$i]['price'];
            $addtime	=	$data[$i]['addtime'];
            if($i%2==0)
            {
                $color	=	"#F8F8F8";
            }
            else
            {
                $color	=	"#ECECFF";
            }
            $pricetotal	+=		 $quoteprice;
      ?>
      <tr>
        <td style="padding:3px;"><?php echo $i+1;?></td>
        <td style="padding:3px;"><?php echo $barcode;?></td>
        <td style="padding:3px;"><?php echo $item;?></td>
        <td style="padding:3px;" align="right"><?php echo number_format($quoteprice,2); ?></td>
      </tr>
      <?php
      }//for
      ?>
    </table>
        <div style="font:Arial, Helvetica, sans-serif;font-size:11px;;" align="left">Best Regards,<br />
            <strong><?php echo $username;?></strong><br /><br /><?php echo date("D, M m, Y");?>
        </div>
    </div>
    </body>
    </html>
    <script language="javascript">
        //window.print();
        //window.close();
    </script>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
	<link rel="stylesheet" type="text/css" href="../includes/css/style.css">
	<?php 	include_once("../includes/security/adminsecurity.php");
			global $AdminDAO,$Component;
			error_reporting(7);
			//print_r($_POST);
	$id			=		$_REQUEST['id'];
	$qstring	=		$_SESSION['qstring'];
	$param		=		$_REQUEST['param'];
	
	
	
	
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
		  <div style="float:right"> 
			<span class="buttons">
			<button type="button" class="positive" onclick="printpage(-1);"> <img src="../images/tick.png" alt=""/>
			<?php {echo "Print";} //if($id=='-1') else {echo "Update";} ?>
			</button>
			<!--<a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> -->
			</span> 
		  </div><br />
	<br />
	
		  <form id="frmpurchase" name="frmpurchase" action="managequote.php" class="form"  enctype="multipart/form-data">             
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
		global $stor,$AdminDAO,$id;
		$aid			=		$_SESSION['addressbookid'];
		$stor			=	$AdminDAO->getrows("store","storecode,pkstoreid,storename as name","storedeleted<>1 AND storestatus=1");
		//$userinfo		=	$AdminDAO->getrows("addressbook","pkaddressbookid, 	firstname, 	lastname, 	email","pkaddressbookid=$aid");
		//$name			=	$userinfo[0]['firstname']." ".$userinfo[0]['lastname'];
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
			$where.=" and fkcountryid='$fkcountryid' ";				
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
		
		
		//echo $where;
		
		
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
		
			$shiplist = $AdminDAO->getrows("quotedetails ",
								   "itemdescription, 	price, 	weight, 	country, 	brand, 	supplier ,	source, 	fkquoteid",
								   " fkquoteid='$id'");
		
		}
		$selectclass=' style="width:100px;"';
	?>	
	
	
	<table width="129%"  class="simple">
		<tr>
			<td height="12" valign="top">
				<div class="topimage2" style="height:6px;"></div>
				<table  cellpadding="2" cellspacing="0" border="1" width="100%">
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
						  
						<?php  
						$curdate=time();
						$curfilepath="quote_$curdate.xls";
						$curfile = "../xl/$curfilepath";
						$data=array("Item","Price","Weight","Country","Brand","Supplier","Source");
						if (!$handle1 = fopen($curfile, 'w+')) {
								 echo "Cannot open file ($curfile)";
								 exit;
						}
						$somecontent=implode("\t",$data)."\n";
						// Write $somecontent to our opened file.
						if (!fwrite($handle1, $somecontent)) {
							echo "Cannot write to file ($curfile)";
							//exit;
						}	
							
							
						$numb	=$tweight	=$tprice	=0;
						
						if(count($shiplist)>0){	
							foreach($shiplist as $slist)
							{
								//itemdescription, 	price, 	weight, 	country, 	brand, 	supplier ,	source,
								$suppliers			=	str_replace("&#039;","'",html_entity_decode($slist['supplier']));
								$itemdescription 	= 	html_entity_decode($slist['itemdescription']);
								$quantity 			= 	html_entity_decode($slist['quantity']);
								$weight				=	html_entity_decode($slist['weight']);							
								$store		 		= 	str_replace("&#039;","'",html_entity_decode($slist['source']));
								$country		 	= 	html_entity_decode($slist['country']);						
								$price		 		= 	html_entity_decode($slist['price']);											
								$brand		 		= 	html_entity_decode($slist['brand']);	
								
								 // $deadline			=	implode("-",array_reverse(explode("-",$slist['deadline'])));
							$data= array("$itemdescription","$price","$weight","$country","$brand","$suppliers","$store");	 
							$somecontent=implode("\t",$data)."\n";
							if (!fwrite($handle1, $somecontent)) {
							echo "Cannot write to file ($curfile)";
							//exit;
						}	
								?>        
								
					<tr class="even">        
						<td valign="top"><?php echo ($numb+1);?>&nbsp;</td>
							<td><?php echo $itemdescription;?>&nbsp;</td>
								<td><?php $tprice+=$price; 	echo $price;?>&nbsp;</td>
								<td><?php $tweight+=$weight; echo $weight;?>&nbsp;</td>
								<td><?php echo $country;?>&nbsp;</td>
								<td><?php echo $brand;?>&nbsp;</td>
								<td><?php echo $suppliers;?>&nbsp;</td>
								<td><?php echo $store;?>&nbsp;</td>
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
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
						 </tr>
					
					
					<?php }
				?>
							
					<tr>
					  <td colspan="12">
					  </td>
					</tr>
					<tr>
					  <td colspan="12" align="center">
						<div class="buttons">
						   <button type="button" class="positive" onclick="printpage(-1);"> <img src="../images/tick.png" alt=""/>
						  <?php {echo "Print";} //if($id=='-1') else {echo "Update";} ?>
						  </button>
						  <button type="button" class="positive" onclick="emailnow();"> <img src="../images/tick.png" alt=""/>
						  <?php {echo "Email";} //if($id=='-1') else {echo "Update";} ?>
						  </button>
						  <a href="<?php echo $curfile;?>" target="_blank"  onclick="javascript:void(0);" >Export Excel</a>
						</div>
					</td>
					</tr>
				  </tbody>
				</table>
			</td>
		</tr>
	</table>	
	
	
	
	<div id="emaildiv" style="display:none">
		<form name="emalfrm" id="emailfrm" method="post" action="">
		  <table width="738"  class="simple">
			  <tr>
				<td height="22" colspan="2"><strong>Email This Report </strong>
				<div style="float:right">
				<span class="buttons">
					<button type="button" class="positive" onclick="sendemail(-1);">
						<img src="../images/email_go.png" alt=""/> 
					   Send
					</button>
					 <a href="javascript:void(0);" onclick="hideclass('emaildiv');" class="negative">
						<img src="../images/cross.png" alt=""/>
						Cancel
					</a>
				</span>
				</div>
				</td>
			</tr>
			  <tr>
				<td width="127">From</td>
				<td width="599" valign="middle"><br />
				<?php
					$sql="select 
								pkaddressbookid,CONCAT(a.firstname,' ',a.lastname) as name,
								 a.email
								from 
									addressbook a
								where pkaddressbookid='$aid'
								
								";
				$addarray	=	$AdminDAO->queryresult($sql);
				//print_r($addarray);
				$ename				=	$addarray[0]['name'];
				$email				=	$addarray[0]['email'];
				?>
				<input name="fromemails" type="text" id="fromemails"  size="58" readonly="readonly" value="<?php print "$ename <$email>";?>"/></td>
			</tr>
			  <tr>
				<td>To</td>
				<td valign="middle">
				<select name="tolist" id="tolist" onchange="toemail('tolist')" style="width:375px;">
					<?php
					$sql="select 
								pkaddressbookid,CONCAT(a.firstname,' ',a.lastname) as name,
								 a.email
								from 
									addressbook a
								";
						$addarray	=	$AdminDAO->queryresult($sql);
					for($a=0;$a<count($addarray);$a++)
					{
						$pkaddressbookid	=	$addarray[$a]['pkaddressbookid'];
						$ename				=	$addarray[$a]['name'];
						$email				=	$addarray[$a]['email'];
					?>
					<option value="<?php echo $email;?>"><?php echo $ename;?></option>
					<?php
					}
					?>
				</select><br />
				<textarea name="toemails" id="toemails" cols="65" rows="1"></textarea></td>
			</tr>
			<tr>
				<td>Cc</td>
				<td><textarea name="ccemails" id="ccemails" cols="65" rows="1"></textarea></td>
			</tr>
			<tr>
				<td>Bcc</td>
				<td><textarea name="bccemails" id="bccemails" cols="65" rows="1"></textarea></td>
			</tr>
			<tr>
				<td>Subject</td>
				<td><input type="text" name="subject" id="subject" size="58" ></td>
			</tr>
			<tr>
				<td>Message</td>
				<td><textarea name="message" id="message" cols="65" rows="8"></textarea></td>
			</tr>
			  <tr>
				<td colspan="2">
							 <div style="float:left">
				<span class="buttons">
					<button type="button" class="positive" onclick="sendemail(-1);">
						<img src="../images/email_go.png" alt=""/> 
					   Send
					</button>
					 <a href="javascript:void(0);" onclick="hideclass('emaildiv');" class="negative">
						<img src="../images/cross.png" alt=""/>
						Cancel
					</a>
				</span>
				</div>
				</td>
			</tr>
		  </table>
		<input type="hidden" value="" name="mailtext" id="mailtext" />
		</form>
	</div>
	
	
	
	
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
	
	<script type="text/javascript">
	
	function emailnow()
	{
		document.getElementById('curfile').value;
		document.getElementById('tolist').focus();
	}
	
	function sendemail()
		{
			var fromemails=document.getElementById('fromemails').value;
			var toemails=document.getElementById('toemails').value;
			if(fromemails=='')
			{
				alert("Please provide email addrtess in From email.");
				 fromemails=document.getElementById('fromemails').focus();
				 return false;
			}
			if(toemails=='')
			{
				alert("Please provide at least One email address in To email.");
				 fromemails=document.getElementById('toemails').focus();
				 return false;
			}
			var maildata=document.getElementById('maildata').innerHTML;
			//alert(maildata);
			document.getElementById('mailtext').value=maildata;
			//mail();
			sendmail();
		}
	function emailnow()
	{
		document.getElementById('emaildiv').style.display='block';
		document.getElementById('tolist').focus();
	}
	function printpage()
	{
		
		document.getElementById('emaildiv').style.display='none';
		window.print();
		//document.getElementById('currencybox2').style.display='none';
	
		//window.close();
	}
	function reload_list(id){
		false;
		options	=	{	
						url : 'managequote.php',
						type: 'POST',
						success: response
					}
		jQuery('#frmpurchase').ajaxSubmit(options);
	}
	function response(text2){
		//if(text==1)	{
			//adminnotice('Distributions has been saved.',0,5000);
			//jQuery('#subsection').slideUp();
			document.getElementById('subsection').style.display="block";
			document.getElementById('subsection').innerHTML=text2;
		//}else{
			//adminnotice(text,0,5000);
		//}
	}
	</script>
<?php }//end edit?>