<?php
session_start();
include_once("includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$barcode			=	filter($_REQUEST['code']);
$salecompleted		=	$_GET['salecompleted'];
$tpmode				=	$_GET['tpmode'];//tradeprice mode /hotel mode
$quatationmode		=	$_GET['quatationmode'];//quatationmode mode 
$param				=	$_REQUEST['param'];
$customerid			=	$_GET['customerid'];
$creditcustomername	=	$_GET['customername'];
$breakmode			=	$_GET['breakmode'];
$discountsmode		=	$_GET['discountsmode'];
if($_GET['invmode']!='')
{
	$_SESSION['invmode']=	$_GET['invmode'	]; 
}
if($param=='delievrychalan')
{
	$tempsaleid	=	$_SESSION['tempsaleid']=$_GET['id'];
	$_SESSION['tpmode']=2;//this means Are sales will be procceded with Hotels Mode
	$sqldc="select 	
				s.fkaccountid as fkcustomerid,
				s.fkclosingid,
				c.title,
				a.firstname,
				a.lastname 
			from 
				$dbname_detail.sale s,$dbname_detail.account c,$dbname_detail.addressbook a
			where 
				s.fkaccountid=c.id and 
				a.pkaddressbookid=c.fkaddressbookid and
				s.pksaleid='$tempsaleid'";
			$dcarr	=	$AdminDAO->queryresult($sqldc);
			$customerid			=	$dcarr[0]['fkcustomerid'];
			if($_SESSION['customerid']=='')
			{
				$_SESSION['customerid']	=	$customerid;
			}
			$companyname		=	$dcarr[0]['companyname'];
			$firstname			=	$dcarr[0]['firstname'];
			$lastname			=	$dcarr[0]['lastname'];
			$dcclosingid		=	$dcarr[0]['fkclosingid'];
			$dcclosingid		=	$dcarr[0]['fkclosingid'];
			
			if($companyname!='')
			{
				$creditcustomername	=	$companyname;
			}
			else
			{
				$creditcustomername	=	$firstname.' '.$lastname;
			}
			//$_SESSION['creditcustomername']=$creditcustomername;
			//$_SESSION['customerid']=$customerid;
	/*print"<script language=javascript>jQuery('#content').load(\"includes/menu/usercontrols.php?customerid=$customerid&creditcustomername=$creditcustomername\");</script>";*/
}
if($_SESSION['tempsaleid']!='' && $_GET['tpmode']==2 && $_SESSION['customerid']=='')//check if there are active sales then restrict switching to Hotel Mode
{
	?>
		<script language="javascript">
			alert('There are active sales in the session please clear those sales first.');
			//adminnotice('There are active sales in the session please clear those sales first.','',9000);
			//jQuery('#hotelmsg').html('There are active sales in the session please clear those sales first.');
			//jQuery('#hotelmsg').fadeOut(9000);	
			//jQuery('').fadeOut(9000);
			//return false;
		</script>
   <?php
	$tpmode='';
	//exit;
}
if($discountsmode==1)
{
	$discountmodesession	=	$_SESSION['discountsmode'];
	if($discountmodesession==1)
	{
		$_SESSION['discountsmode']='2';
	}
	else
	{
		$_SESSION['discountsmode']=$discountsmode;
	}
}
if($breakmode!='')
{
	$breakmodesession	=	$_SESSION['breakmode'];	
	if($breakmodesession==1)
	{
		$_SESSION['breakmode']=2;	//break mode off
		//currentred
	}
	else
	{
		$_SESSION['breakmode']=1;//Break Mode on
	}
}
$discmode	=	$_SESSION['discountsmode'];
if($discmode==1)
{
	?>
    <script language="javascript">
		document.getElementById('discountsmodediv').style.display='block';
	</script>
    <?php	
}
else if($discmode==2)
{
	?>
	<script language="javascript">
		document.getElementById('discountsmodediv').style.display='none';
	</script>
    <?php
}
$breakmode	=	$_SESSION['breakmode'];	
if($breakmode==1)
{
	?>
    <script language="javascript">
		document.getElementById('breakmodediv').style.display='block';
	</script>
    <?php	
}
else if($breakmode==2)
{
	?>
	<script language="javascript">
		window.location="signout.php?msg=Please Enter your user name and password to turn off break mode.";
	</script>
    <?php
}
if($customerid!='')
{	
	$_SESSION['customerid']=$customerid;
	$_SESSION['creditcustomername']=$creditcustomername;	
}
else
{
	 $customerid			=	$_SESSION['customerid'];
	 $creditcustomername	=	$_SESSION['creditcustomername'];
	 
	 
}
if($_SESSION['tpmodecustomer']=='')
{
	$_SESSION['tpmodecustomer']=$_GET['customerid'];
}
$tpmodecustomer=$_SESSION['tpmodecustomer'];
if($tpmode=='1')
{
	$tpmodesession	=	$_SESSION['tpmode'];
	if($tpmodesession==1)
	{
		$_SESSION['tpmode']=0;	//tpmode is inactive
		
		//currentred
	}
	else
	{
		$_SESSION['tpmode']=1;//this means all sales will be procceded with trade price
			
	}
}
else if($tpmode==2)
{
	//exit;
	$tpmodesession	=	$_SESSION['tpmode'];
	if($tpmodesession==2)
	{
			if($_SESSION['customerid']!='')
			{
				//checking sales of previouse selected active customer.
				$activecustomerid			=	$_SESSION['customerid'];
				if($_SESSION['invmode']==1)
				{
					$status	=	"status='1'";
				}
				else
				{
					$status	=	"status='0'";
				}
				$sql="select 
							count(pksaleid) as foundsale 
						from 
							$dbname_detail.sale 
						where 
							fkaccountid='$activecustomerid' AND 
							$status AND  
							fkclosingid='$closingsession'
							";
				$arrsale	=	$AdminDAO->queryresult($sql);			
				$foundsale	=	 $arrsale[0]['foundsale'];
				//exit;
			}
		if($foundsale<1)//if sales found then stop user to change the mode or switch to anhother customer
		{
			$_SESSION['tpmode']=0;	//customer mode is inactive
			$_SESSION['customerid']=0;
			$_SESSION['creditcustomername']=0;
		}
		else
		{
			?>
            	<script language="javascript">
				alert('There are active sales for the customer please clear those sales first.');
				//notice('There are active sales for the customer please clear those sales first.',5000);
		//return false;
				</script>
            <?php	
		}
		//currentred
	}
	else
	{
		$_SESSION['tpmode']=2;//this means Are sales will be procceded with Hotels Mode
		$_SESSION['tpmodeinvoice']=2;
	}
}
$tpmode	=	$_SESSION['tpmode'];
if($tpmode==0)
{
	?>
	<script language='javascript'>
				document.getElementById('tpmode').className='';
				if(document.getElementById('Hotel_tab'))
				{
					document.getElementById('Hotel_tab').className='';
				}
				document.getElementById('closingfrmdiv').style.display='none';	
				document.getElementById('closingfrmdiv').innerHTML='';
				document.getElementById('creditcutomerdiv').style.display='block';	
				document.getElementById('creditcutomerdiv').innerHTML='';
				document.getElementById('quotetitlediv').style.display='block';	
				document.getElementById('quotetitlediv').innerHTML='';
	</script>
    <?php
}
else if($tpmode==1)
{
	print"<script language='javascript'>
				document.getElementById('tpmode').className='currentred';
			</script>
	";
}
else if($tpmode==2)
{
	print"<script language='javascript'>
				document.getElementById('Hotel_tab').className='currentblue';
			</script>
	";
	if($customerid=='')
	{
		?>
        	<script language="javascript">
				//alert("Select customer.");
				//selecttab('Customers_tab','customers.php');
				jQuery("#closingfrmdiv").load("customerfrm.php");
				//selecttab('Customers_tab','customerfrm.php')
				document.getElementById('closingfrmdiv').style.display='block';
            </script>
        <?php	
	}
	else
	{?>
		
		<script language="javascript">
			//alert('hello');
			document.getElementById('creditcutomerdiv').style.display='block';
			if(document.getElementById('creditcutomerdiv'))
			{
				document.getElementById('creditcutomerdiv').innerHTML="<div style=\"position:absolute;background-color:#000;padding:3px;-moz-border-radius:3px;-webkit-border-radius:4px;font-weight:bold;color:#fff;float:right;margin-left:600px;\" ><?php echo $creditcustomername;?></div>";
			}
		//	document.getElementById('closingfrmdiv').style.display='none';	
        </script>
       <?php
	}
}
//echo $tpmode;
/*************************************************************************/
if($salecompleted=='adjustment')//cancells the activated Adjustment
{
	$_SESSION['tempsaleid']='';
	$tempsaleid='';
	$_SESSION['adjustment']='';
	$adjust	="";
	// added by yasir 21-09-11
	$_SESSION['purchaseorderid']=	'';
	$_SESSION['quotetitle']		=	'';
	//	
}
if($param=='adjustment')
{
	$_SESSION['tempsaleid']=$_GET['id'];
	$adjust	=$_SESSION['adjustment']='adjustment';
		//$_SESSION['adjustment'];
}
if($_SESSION['adjustment']=='adjustment' && $salecompleted==3)
{
	print"<script language='javascript'>
	notice('This Sale is active for adjustment You can not hold this sale.','',5000);	
	</script>
	";
}
if($salecompleted==3 && $_SESSION['adjustment']!='adjustment')
{
	//echo $salecompleted;
	$time	=	time();
	$tempsaleid	=	$_SESSION['tempsaleid'];
	$field=array("status","updatetime");
	$data=array($salecompleted,$time);
	$AdminDAO->updaterow("$dbname_detail.sale",$field,$data," pksaleid='$tempsaleid' ");
	$_SESSION['tempsaleid']='';
	// added by yasir 21-09-11
	$_SESSION['purchaseorderid']=	'';
	$_SESSION['quotetitle']		=	'';
	//
	print"<script language='javascript'>loadsection('navigation','includes/menu/nav.php');</script>";
}
elseif($salecompleted==2 || $salecompleted==1 || $salecompleted==4)
{
	    //1 is for completed
		//0 is current sale
		//2 is cancelled
		//3 Holding
		$tempsaleid	=	$_SESSION['tempsaleid'];
		if($tempsaleid!='')
		{
				$dccustomerid	=	$_SESSION['customerid'];
				
				if($_SESSION['customerid']!='' && $_SESSION['creditcustomername']!='')
				{
					$_SESSION['customerid']='';
					$_SESSION['creditcustomername']='';
					$_SESSION['tpmode']=0;
				?>
				<script language='javascript'>
					document.getElementById('tpmode').className='';
					if(document.getElementById('Hotel_tab'))
					{
						document.getElementById('Hotel_tab').className='';
					}
					document.getElementById('closingfrmdiv').style.display='none';	
					document.getElementById('closingfrmdiv').innerHTML='';
					document.getElementById('creditcutomerdiv').style.display='block';	
					document.getElementById('creditcutomerdiv').innerHTML='';
					document.getElementById('quotetitlediv').style.display='block';	
					document.getElementById('quotetitlediv').innerHTML='';
				</script>
				<?php
				}//if
			if($salecompleted==1 || $salecompleted==4)
			{
				$field=array("status","updatetime");
				$data=array($salecompleted,time());
				
				print"<script language='javascript'>
				loadsection('content','includes/menu/usercontrols.php');
				loadsection('navigation','includes/menu/nav.php');
				</script>
				";
				if($salecompleted==4)
				{
					$field	=	array("status","updatetime","dcclosingid","fkclosingid");
					$data	=	array($salecompleted,time(),$dcclosingid,$closingid);
//					$_SESSION['deliverychalan']=4;
					print"<script language=javascript>printcustomerinvoice($tempsaleid,$dccustomerid)</script>";
				}
				elseif($salecompleted==1 && $_SESSION['tpmodeinvoice']==2 && $tpmodecustomer!='')//insert the serial number in the sale and also make new entery in the creditinvoice table
				{
					// select stockid
					$salequery		=	"SELECT 1 FROM $dbname_detail.sale,$dbname_detail.saledetail,$dbname_detail.stock,barcode WHERE fkbarcodeid=pkbarcodeid and fkstockid=pkstockid and barcode='st' and fksaleid=pksaleid and pksaleid='$tempsaleid'";
					$saledatares	=	$AdminDAO->queryresult($salequery);
					/*echo "<pre>";
					print_r($saledatares);
					echo "</pre>";*/
					if(sizeof($saledatares)==0)
					{
						$invtime=time();
						$sqlmx			=	"select (max(serialno)+1) as maxserial from $dbname_detail.creditinvoices";
						$mxarr			=	$AdminDAO->queryresult($sqlmx);
						$maxserial		=	$mxarr[0]['maxserial'];
						$empid			=	$_SESSION['addressbookid'];
						$gst			=	$AdminDAO->getrows("$dbname_detail.gst","amount","1 ORDER BY pkgstid DESC LIMIT 0,1");
						$taxpercentage	=	$gst[0]['amount'];
						$fieldinv		=	array("serialno","datetime","invoicedate","fkaddressbookid","invoicestatus","fkaccountid","taxpercentage");
						$datainv		=	array($maxserial,$invtime,$invtime,$empid,2,$tpmodecustomer,$taxpercentage);
						$creditinvoiceno=	$AdminDAO->insertrow("$dbname_detail.creditinvoices",$fieldinv,$datainv);
						$time			=	time();
						$sqlupdatesale="update 
											$dbname_detail.sale 
										set 
											creditinvoiceno='$creditinvoiceno' 
										where 
											pksaleid='$tempsaleid'";
						$AdminDAO->queryresult($sqlupdatesale);
						$_SESSION['tpmodecustomer']='';
						$_SESSION['tpmodeinvoice']='';
						$_SESSION['invmode']='';
					}
					//session_unset();
				}//end of credit invoice insertion condition
				
			}
			else
			{
				$time	=	time();
				$field=array("status","updatetime");
				$data=array($salecompleted,$time);
			}
			if($salecompleted==2)
			{
				$f=array("unitsremaining");
				$sql="Select 
							fkstockid,
							SUM(quantity) as ret 
						from 
							$dbname_detail.saledetail 
						where 
							fksaleid='$tempsaleid' 
								group by fkstockid";
				$returnarray	=	$AdminDAO->queryresult($sql);
				for($i=0;$i<count($returnarray);$i++)
				{
					$fkstockid		= $returnarray[$i]['fkstockid'];	
					$retqty			= $returnarray[$i]['ret'];	

					 $sql="UPDATE $dbname_detail.stock set unitsremaining=(unitsremaining+$retqty) where pkstockid='$fkstockid'";
					 $AdminDAO->queryresult($sql);
				}
			}
			$AdminDAO->updaterow("$dbname_detail.sale",$field,$data," pksaleid='$tempsaleid' ");
			$_SESSION['tempsaleid']='';
			$_SESSION['adjustment']='';
			// added by yasir 21-09-11
			$_SESSION['purchaseorderid']=	'';
			$_SESSION['quotetitle']		=	'';
			//
			print"<script language='javascript'>loadsection('navigation','includes/menu/nav.php');tempsaleid='';</script>";
		}
}
if($_GET['tempsaleid']!='')
{
	$_SESSION['tempsaleid']=$_GET['tempsaleid'];	
}
//$tempsaleid	=	$_REQUEST['tempsaleid'];
//$counter	=	gethostbyaddr($_SERVER['REMOTE_ADDR']);
//$pendingsalerows	=	$AdminDAO->getrows(" $dbname_main.sale "," pksaleid,datetime "," countername='$counter' and status='0' order by pksaleid DESC");
?>
<?php /*?><div align="left">
Holding Sales<select name="holdingsales" id="holdingsales" onchange="activatesale(this.value)">
	<option value="">Select Sale</option>
	<?php
	for($h=0;$h<count($pendingsalerows);$h++)
	{
	?>
    <option value="<?php echo $pendingsalerows[$h]['pksaleid'];?>" <?php if($_SESSION['tempsaleid']==$pendingsalerows[$h]['pksaleid']){print"selected";}?>><?php echo $pendingsalerows[$h]['datetime'];?></option>
	<?php
	}
	?>
</select>
</div><?php */?>

<link href="includes/css/autocomplete.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="includes/autocomplete/ajax_framework.js"></script>
<script language="javascript">

function setnextfocus()
{
	//var expirystatus	=	document.getElementById('expiry').disabled;
	var expirystatus	='';
	if(expirystatus)
	{
		//tab('quantity');
	}
	else
	{
		//tab('expiry');
	}
}

function tab(to)
{
	document.getElementById(to).focus();
}
function getinstance(div,inputdata)
{ 
	//setnextfocus();
	//alert(inputdata);
	//return false;
	document.getElementById(div).style.display='block';
	if(inputdata=='')
	{
		if(inputdata=='')
		{
			alert("Please enter Barcode.");
			return false;
		}
		inputdata=jQuery.trim( document.getElementById('barcode1').value);
	}
	inputdata=trim(inputdata);
	jQuery('#expdiv').load('getprodata.php?code='+inputdata);
}

function getqty(val)
{
	var quantity	=	qty[val];	
	var arr	=	val.split('_');
	boxval	=	document.getElementById('isbox').value;
	
	if(boxval==1)
	{
		jQuery('#price').load('boxitem.php?stockid='+arr[1]);
	}
	else
	{
		jQuery('#price').load('boxitem.php?type=price&stockid='+arr[1]);
	}
	document.getElementById('remqty').value=quantity;
}
function shortcutsale()
{
	<?php /*?>var tpmodestatus	=	'<?php echo $tpmode;?>';
	if(tpmodestatus==1)
	{
		if(document.getElementById('maxtradeprice').disabled==true && document.getElementById('newtradeprice').disabled==true)
		{
			notice('Please select price','',5000);
			return false;
		}
	}<?php */?>
	qval	=	document.getElementById('quantity').value;
	if(parseInt(qval)>3000)
	{
		notice('You can not add more than 3000 units at once','',5000);
		return false;
		/*if(confirm('Are you sure you want to enter this quantity '+qval))
		{
			submitformsale();
			return false;
		}*/
	}
	else
	{
			submitformsale();
			return false;
	}
}
function submitformsale()
{
	var newprice	=	document.getElementById('newprice').value;
	if(newprice!='')
	{
		var reason	=	document.getElementById('reason').value;
		if(reason=='0' || reason=='')
		{
			alert("You have chnaged the sale price to "+newprice+" (Please select the Reason).");
			document.getElementById('reason').focus();
			return false;
		}
	}
options	={	
			url : 'insertsaletemp.php',
			type: 'POST',
			success: response
		}
		jQuery('#salefrm').ajaxSubmit(options,function(){return false});
}
function response(text)
{
	loadsection('main-content','sale.php');
	jQuery('#expdiv').html("<input type='text' name='exp' class='text' id='exp' readonly='readonly'/>");
	document.getElementById('barcode1').focus();
	if(text==1)
	{
		notice('Please enter opening amount','',5000);
		$('#openingfrmdiv').load('openingfrm.php');
		document.getElementById('openingfrmdiv').style.display='block';
	}
	else if(text!='')
	{
		notice(text,'',5000);
	}
	return false;
}
jQuery('#instancediv').load('codeitem.php');
function cancelsale()
{
	// check added by Yasir -- 04-07-11	 
 	if(parseInt(document.getElementById('idforesc').innerHTML) < 1){
	
		if(confirm("Do you really want to cancel this Sale?"))
		{
			loadsection('main-content','sale.php?salecompleted=2');
			return false;
		}
 	} //
}
function newdiscountreason(id)
{
  var reshtm="<input name='reason' id='reason' type='text' class='text'><input name='newreason' id='newreason' type='hidden' value='1'>";
	document.getElementById(id).innerHTML=reshtm;
	document.getElementById('reason').focus();
}
/*
function toggleclass(c)
{
	if(c==1)
	{
		document.getElementById('maxtradeprice').className	=	'pricebgsel';
		document.getElementById('newtradeprice').className	=	'pricebg';
	}
	else
	{
		document.getElementById('newtradeprice').className	=	'pricebgsel';
		document.getElementById('maxtradeprice').className	=	'pricebg';
	}
}
shortcut.add("Ctrl+1",function() 
{
	document.getElementById('maxtradeprice').disabled	=	false;
	document.getElementById('newtradeprice').disabled	=	true;
	toggleclass(1);
	return false;
});
shortcut.add("Ctrl+2",function() 
{
	document.getElementById('newtradeprice').disabled	=	false;
	document.getElementById('maxtradeprice').disabled	=	true;
	toggleclass(2);
	return false;
});*/
/*document.onkeydown = checkKeycode;
function checkKeycode(e) {
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
alert("keycode: " + keycode);
}
function displaykey(e) {
	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	var character = String.fromCharCode(code);
	//alert('Character was ' + character);
	//alert(code);
	//if(code
	if(code!=37 && code!=38 && code!=39 && code!=40)
	{
		if(code==35 || code==8)
		{
			document.getElementById('suggestdiv').innerHTML	=	document.getElementById('productname').value;
		}
		else
		{
			document.getElementById('suggestdiv').innerHTML	=	document.getElementById('suggestdiv').innerHTML+character;
		}
	}
	
}

*/
</script>
<?php /*?>Added tab indexes by Yasir 07-07-11<?php */?>
<div id="barcode">
     <form  name="salefrm" id="salefrm" method="post">
    <fieldset>
    <div class="Table">
    <div class="Row">
        <div class="Column">
        
        <label>Barcode </label>
        <input name="barcode" id="barcode1" type="text" class="text" value="<?php echo $barcode;?>" onkeydown="javascript:if(event.keyCode==13) {getinstance('instancediv',this.value); return false;}" autocomplete="off" onfocus="this.select()" tabindex="1" >
         </div>
        <div class="Column"> 
        <label>Product</label>
          <!--<input type="text" name="productname" class="text" id="productname" autocomplete="off"/>-->
        	<!--<input type="hidden" name="productid" id="productid" />-->
                    <input name="productname" id="productname" type="text" onkeyup="suggestnow(event,'results');" onkeydown="newScrol(event);" class="text" autocomplete="off" onfocus="this.select()" tabindex="2" />
                    <div id="suggestdiv"></div>
        <input name="barcodeid" id="barcodeid" type="hidden"/>
         <input name="pkpodetailid" id="pkpodetailid" type="hidden"/>
         <input name="pkpurchaseorderid" id="pkpurchaseorderid" type="hidden"/>
         <input name="quotetitle" id="quotetitle" type="hidden"/>
		 <input name="taxable" id="taxable" type="hidden"/>
		 <div id="results" class="results"></div>
        </div>
        <div class="Column">
        <label>Stock</label>
            <div id="expdiv">
              <input type="text" name="exp" class="text" id="exp" readonly="readonly"/>
            </div>
         </div>
      </div>
    
   <div class="Row">
        <div class="Column">
        	<label>Qty </label>
          	<input type="text" name="quantity" class="text" id="quantity" value="1" onkeypress="return isNumberKeyQty(event)" onkeydown="javascript:if(event.keyCode==13) {shortcutsale(); return false;}" onfocus="this.select()"  tabindex="3" /></li></td>
        </div>
        <?php /*?>else if(event.keyCode==9) {tab('newprice'); return false;}<?php */?>
        <div class="Column">
        	<label>Price</label>
          	<span id="stockpricespan"><input type="text" name="price" class="text" id="price" readonly="readonly"/></span>
            <span id="tradepricespan" style="display:none;"><input type="text" name="maxtradeprice" id="maxtradeprice" disabled="disabled" size="8" class="pricebg" title="Max Price" />&nbsp;&nbsp;&nbsp;<input type="text" name="newtradeprice" id="newtradeprice" disabled="disabled" size="8" class="pricebg" title="Recent Price" /></span>
        </div>
        <div class="Column">
        <label>Changed Price</label>
        <input type="text" name="newprice" class="text" id="newprice" onkeyup="Javascript: changepriceincodeitem(this.value)" onkeypress="return isNumberKey(event)" onkeydown="javascript:if(event.keyCode==13 ) {shortcutsale(); return false;}"  tabindex="4" />
        </div>
   </div>
    
   <div class="Row">
      <div class="Column">
      	<div id="boxeditemdiv" style="display:none;">
      <label id="boxeditem">Box</label>
      <input type="text" name="boxsize" id="boxsize" value="" />
      <input type="hidden" name="isbox" id="isbox" value="" />
      </div>
      </div>
      <div class="Column" >
      	<label>Reason</label>
        <div id="newdiscountreason">
          <select name="reason" id="reason" class="selectbox" onkeydown="javascript:if(event.keyCode==13 ) {shortcutsale(); return false;}" tabindex="5">
            <option value="0">Select Reason</option>
              
            <?php
			 $sql="SELECT 
						* 
					FROM 
						discountreason
					WHERE
						reasonsatus='a' 
				ORDER BY 
					reasontitle ASC";
			$discountreasonarray		=	$AdminDAO->queryresult($sql);
			// hotels mode, setting price change to default on Purhchase Order Price
			for($dis=0;$dis<count($discountreasonarray);$dis++)
			{
				if($tpmode==2 && $discountreasonarray[$dis]['pkreasonid']==12)
				{
					$select =	"selected=\"selected\"";
				}
				else
				{
					$select	=	"";
				}

			?>
		    <option value="<?php echo $discountreasonarray[$dis]['pkreasonid'];?>" <?php echo $select;?>><?php echo $discountreasonarray[$dis]['reasontitle'];?></option>
          <?php
			}
		   ?>
          </select>
         <!-- <a href="javascript:void(0)" onclick="newdiscountreason('newdiscountreason')">New</a>-->
          	</div>
          </div>
             <div class="Column">
      <label>&nbsp;</label>
      <span class="buttons">
        <button type="button" name="button" id="button" onclick="submitformsale();" title="ENTER">
            <img src="images/disk.png" alt=""/> 
           Save
        </button>
        <button type="button" name="button2" id="button2" onclick="loadsection('main-content','sale.php');" title="F5">
            <img src="images/cross.png" alt=""/> 
           Cancel
        </button>
      </span>
      </div>
    </div>
  </div>  
    </fieldset>
    </form>
    </div>
     
<div id="content">
</div>
<div id="instancediv"></div> 
<script language="javascript" type="text/javascript">
	var tempsaleid	=	'<?php echo $_SESSION['tempsaleid']; ?>';
		<?php
		if($closingsession=='')
		{
		?>
		document.getElementById('amount').focus();
		<?php
		}
		else
		{
		?>
		document.getElementById('barcode1').focus();
		<?php
		}
		if($_SESSION['tpmode']==1 && $tpmode==1)
		{
			?>
			document.getElementById('tpval').innerHTML	=	'1';
			<?php
		}
		else
		{
			?>
			document.getElementById('tpval').innerHTML	=	'2';
			<?php
		}
		?>
</script>
<?php
//include_once("discount.inc.php");
?>