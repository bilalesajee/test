<?php session_start();
include("includes/security/adminsecurity.php");
include("saledetail.php");
global $AdminDAO,$Component,$qs,$DiscountDAO;
$barcode	=	trim(filter($_REQUEST['code']));
$barcodeid	=	trim(filter($_REQUEST['pkbarcodeid']));
$productid	=	filter($_REQUEST['productid']);
$dstockid	=	filter($_REQUEST['stockid']);
$action		=	$_REQUEST['action'];
$empid		=	$_SESSION['employeeid'];
$storeid	=	$_SESSION['storeid'];
$billing	=	$_REQUEST['param'];		
if($_GET['id']!='')
{
 	if($billing!='billing')
	{ 
		$_SESSION['tempsaleid']	=	$_GET['id'];
	}
}
if($barcode!='')
{
	$boxbarcode1	=	$AdminDAO->getrows("barcode","boxbarcode,boxquantity"," pkbarcodeid = '$barcodeid'");
	$boxbarcode		=	$boxbarcode1[0]['boxbarcode'];
	$boxquantity	=	$boxbarcode1[0]['boxquantity'];
	if($boxbarcode!="")
	{
		$box			= 	$boxbarcode;
		$boxbarcode		=	$AdminDAO->getrows("barcode","barcode"," pkbarcodeid = '$boxbarcode'");
		$boxbarcode		=	$boxbarcode[0]['barcode'];
		$productprice	=	$AdminDAO->getrows("$dbname_detail.stock","*","pkstockid='$dstockid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
		$boxprice		=	$productprice[0]['boxprice'];
		$barcode		= 	$boxbarcode;
	}
	$and=" AND `barcode`='$barcode'	";
}
elseif($productid!='')
{
	 $and=" AND `pkproductid`='$productid'	";
}
/****************************PRODUCT DATA*****************************/
if($barcode!='' || $productid!='')
{
	$barcode_array		=	$AdminDAO->getrows('barcode,product','pkproductid,pkbarcodeid,barcode,productdescription,defaultimage',"`fkproductid`=`pkproductid` $and ");
	//$productname 		=	$barcode_array[0]['productname'];
	$productid	 		=	$barcode_array[0]['pkproductid'];
	$pkbarcodeid 		=	$barcode_array[0]['pkbarcodeid'];
	$barcode	 		=	$barcode_array[0]['barcode'];
	$productdescription =	$barcode_array[0]['productdescription'];
	$defaultimage		=	$barcode_array[0]['defaultimage'];
	
$sql="
	SELECT itemdescription,shortdescription
,barcode as bc
FROM 
	barcode b
WHERE
	b.pkbarcodeid				=	'$pkbarcodeid'
	";
	$itemdata	=	$AdminDAO->queryresult($sql);
	$productname	=	$itemdata[0]['shortdescription'];
	if($productname=='')
	{
		$productname	=	$itemdata[0]['itemdescription'];
	}
}
//print"here pkbarcodeid=$pkbarcodeid";
/***********************************Attributes DATA*************************/
if($productid!='')
{
	 /*$query="
		 SELECT  DISTINCT (
					fkbarcodeid
					) AS barcodeid, (
					
					SELECT SUM( quantity )
					FROM stock
					WHERE fkbarcodeid = barcodeid
					) AS quantity, (
					
					SELECT SUM( unitsremaining )
					FROM stock
					WHERE fkbarcodeid = barcodeid
					) AS unitsremaining, (
					
					SELECT min( expiry )
					FROM stock
					WHERE fkbarcodeid = barcodeid
					) AS minexpiry, (
					
					SELECT retailprice
					FROM stock
					WHERE fkbarcodeid = barcodeid
					ORDER BY pkstockid DESC
					LIMIT 0 , 1
					) AS currprice, (select retailprice  from stock where fkbarcodeid=barcodeid order by pkstockid DESC	LIMIT 0,1) as tradeprice,barcode.barcode, product.productname as productattributeoptionname
					FROM stock, barcode, product
					WHERE fkbarcodeid = barcode.pkbarcodeid
					AND barcode.fkproductid = product.pkproductid 
					AND barcode.barcode='$barcode'
				";*/
			//Added By riz 06-1-2010 for trade price mode
			$tpmode		=	$_SESSION['tpmode'];
			$customerid	=	$_SESSION['customerid'];
			//echo "Tpmode=".$tpmode;
			if($tpmode==1)
			{
				$pricefield="costprice";
			}
			else if($tpmode==2 && $customerid!='')
			{//changed $dbname_main to $dbname_detail on line 115, 116, 122 by ahsan 22/02/2012
				 $subqry	=	" ,( SELECT	
						CONCAT(quoteprice,'_',pkpodetailid,'_',taxable,'_',pkpurchaseorderid,'_',quotetitle) 
					FROM
						$dbname_detail.podetail,
						$dbname_detail.purchaseorder
					WHERE
						fkbarcodeid	=	barcodeid AND 
						fkpurchaseorderid=pkpurchaseorderid AND
						fkaccountid='$customerid' AND 
						expired=1 AND 
						$dbname_detail.purchaseorder.status=2
					LIMIT 0,1) as quoteprice ";
				$pricefield="retailprice";
			}
			else
			{
				$pricefield="retailprice";
			
			}
			
			// changed by Yasir - 04-07-11 MAX($pricefield) retailprice (added order by)
			//changed $dbname_main to $dbname_detail on line 138, 146 by ahsan 22/02/2012	
			$query="SELECT  DISTINCT (pkbarcodeid) AS barcodeid, 
						(SELECT 
								$pricefield
							from 
								$dbname_detail.stock ,barcode
							where 
								fkbarcodeid = barcode.pkbarcodeid AND
								barcode.pkbarcodeid='$pkbarcodeid'
						ORDER BY
						        pkstockid DESC LIMIT 0,1 	
							
						) as tradeprice
						,(SELECT $pricefield from $dbname_detail.stock where fkbarcodeid=barcode.pkbarcodeid order by pkstockid DESC limit 0,1) recenttradeprice
						$subqry
					FROM 
						 barcode
					WHERE 
					
						pkbarcodeid='$pkbarcodeid'
				";
	$stockdata			=	$AdminDAO->queryresult($query);
}
	/*$brands_array		=	$AdminDAO->getrows('brand, barcodebrand ',' pkbrandid, brandname '," branddeleted<>'1' AND fkbrandid=pkbrandid AND fkbarcodeid='$pkbarcodeid' ");
	$brands				=	$brands_array[0]['brandname'];*/

//print_r($proinstance_array);
?>

<div id="leftpanel">
  <table width="100%" class="pos">
    <tr>
      <th width="50%">Items</th>
      <th width="10%">Qty</th>
      <th width="20%">Unit Price</th>
      <th width="20%">Sub Total</th>
    </tr>
  <?php
	$adjust	=$_SESSION['adjustment'];
	if($salecompleted==1)
	{
	 	$_SESSION['tempsaleid']='';
	}
	$tempsaleid	=	$_SESSION['tempsaleid'];	
	if($tempsaleid=='')
	{
		$tempsaleid	=	$_GET['id'];	
	}
	
	// added by Yasir -- 06-07-11	
	if (isset($_GET['id']) && $_GET['id'] != ''){
		$tempsaleid	=	$_GET['id'];
	}
	//

if($tempsaleid)
{//changed $dbname_main to $dbname_detail on line 190 by ahsan 22/02/2012
  $salerows	=	$AdminDAO->getrows("$dbname_detail.saledetail",'remainingstock,pksaledetailid,fkdiscountid,fkstockid,saleprice, sum( quantity ) AS quantity, sum( saleprice * quantity ) AS subtotal, boxsize'," fksaleid='$tempsaleid' group by fkstockid,saleprice,fkdiscountid ORDER BY timestamp DESC");
}
  $items=0;
  for($i=0;$i<count($salerows);$i++)
  {
  	//$pksaledetailid	=	$salerows[$i]['pksaledetailid'];
	$saleprice		=	$salerows[$i]['saleprice'];
	$subtotal 		=	$salerows[$i]['subtotal'];
	$quantity		=	$salerows[$i]['quantity'];
	$boxsize		=	$salerows[$i]['boxsize'];
	$stockid		=	$salerows[$i]['fkstockid'];
	$discountid		=	$salerows[$i]['fkdiscountid'];
	$remstock		=	$salerows[$i]['remainingstock'];
	if($stockid!='')
	{//changed $dbname_main to $dbname_detail on line 207 by ahsan 22/02/2012
		$sql=" 
		 SELECT itemdescription,shortdescription,costprice,barcode
		 	FROM
				$dbname_detail.stock s,barcode b
			WHERE 
				s.fkbarcodeid=b.pkbarcodeid
				AND s.pkstockid='$stockid' 
			";
	
		$productrow		=	$AdminDAO->queryresult($sql);
		$productnameinv	=	$productrow[0]['shortdescription'];
		$itembarcode	=	$productrow[0]['barcode'];
		$costprice		=	$productrow[0]['costprice'];
		if($productnameinv=='')
		{
			$productnameinv	=	$productrow[0]['itemdescription'];
		}
	}
 	if($quantity>0)
	{
		if($saleprice==0)
		{
			$class	=	"class=\"rowcolor\"";
		}
		else if($saleprice<$costprice)
		{
			$class	=	"class=\"rowcolor2\"";
		}
		else if($discountid!=0)
		{
			$class	=	"class=\"rowcolor3\"";
		}else if($remstock<0){
		
			$class	=	"style=\"background-color:#FF0; color:#000;\"";
		}else{
			
			$class	=	"";
		}
  ?>
    <tr <?php echo $class;?>>
      <td>
   		 <?php echo $productnameinv." [".$itembarcode."]";?>
      </td>
      <td id="qty"><?php if($boxsize>0) {echo $quantity."X".$boxsize;} else {echo $quantity;}?></td>
      <td id="aright">
	  		<?php  
		  		echo numbers($saleprice);	
			?>
      </td>
      <td id="aright"><?php  
		  		echo numbers($subtotal);
			?></td>
      <!--<td align="center">-->
      <?php
	 
	 /*	if($billing!='billOing' && $adjust =='')
		{
	  ?>
        <a href="javascript: void(0)" onclick="javascript: delsaleitem('<?php echo $stockid;?>','<?php echo $tempsaleid;?>','<?php echo $boxsize;?>','<?php echo $quantity;?>','<?php echo $saleprice;?>')"><img src="includes/images/hr.gif" border="0" title="Delete this Item from Stock"/></a>
      <?php
		}//end of if billing*/
	  ?>
      <!--</td>-->
    </tr>
    
  <?php
	  
	$totalquantity+=$quantity;
	// change for discounts, the amount is not added if the product is discounted
	if($discountid==0)
	{
		$grandtotal	+=$subtotal;	
	}
	//}//end of qty check
  }//echo $quantity;
	if($quantity>0)
	{
		$totalitems			=	count($salerows);
		$adjustmentarray[]	=	$productnameinv;
		$adjustmentarray[]	=	$quantity;	
		$adjustmentarray[]	=	$saleprice;	
		$adjustmentarray[]	=	$subtotal;	
		$adjustmentarray[]	=	$stockid;	
			
		$items++;
		$totalitems		=	$i+1;
		//$adjustment		=$adjustmentarray;
		$totaldisplayitems	=	$totalitems." ($totalquantity)";
	} 
}//end of for
  ?>
	 <tr>
      <td><strong>Total Items</strong></td>
      <td id="qty2"><strong><?php echo $totaldisplayitems;?></strong></td>
      <td id="aright"><strong>Total Price</strong></td>
      <td id="aright"><strong><?php echo numbers($grandtotal);?></strong></td>
    </tr>
	 <tr>
	   <td colspan="4">&nbsp;</td>
    </tr>
	 <tr>
	   <th colspan="4">Returns &amp; Adjustments</th>
    </tr>
	<?php
	if($tempsaleid)
	{//changed $dbname_main to $dbname_detail on line 308 by ahsan 22/02/2012
		$salerows	=	$AdminDAO->getrows("$dbname_detail.saledetail",'pksaledetailid,fkstockid,saleprice, sum(quantity) AS qty,quantity, sum( saleprice * quantity ) AS subtotal, boxsize'," fksaleid='$tempsaleid' AND quantity<0 group by fkstockid,saleprice");
	}
 // $totalitems	=	count($salerows);
  $items=0;
  for($i=0;$i<count($salerows);$i++)
  {
  	//$pksaledetailid	=	$salerows[$i]['pksaledetailid'];
	$saleprice		=	$salerows[$i]['saleprice'];
	$subtotal 		=	$salerows[$i]['subtotal'];
	$quantity		=	$salerows[$i]['qty'];
	$boxsize		=	$salerows[$i]['boxsize'];
	$stockid		=	$salerows[$i]['fkstockid'];
	if($stockid!='')
	{//changed $dbname_main to $dbname_detail on line 325 by ahsan 22/02/2012
		 $sql=" 
		 SELECT itemdescription,shortdescription
		 	FROM
				$dbname_detail.stock s,barcode b
			WHERE 
				s.fkbarcodeid=b.pkbarcodeid
				AND s.pkstockid='$stockid' 
			";
	
		$productrow		=	$AdminDAO->queryresult($sql);
		$productnameinv	=	$productrow[0]['shortdescription'];
		if($productnameinv=='')
		{
			$productnameinv	=	$productrow[0]['itemdescription'];
		}
	}
	?>
     <tr>
	   <td><?php echo $productnameinv;?></td>
	   <td id="qty5"><?php if($boxsize>0) {echo $quantity."X".$boxsize;} else {echo $quantity;}?></td>
	   <td id="aright"><?php echo numbers($saleprice);?></td>
	   <td id="aright"><?php echo numbers($subtotal);?></td>
    </tr>
  <?php
  	$adjutedprice+= $subtotal;
  }
  ?>
 	 <tr>
      <td><strong>Total Items </strong></td>
      <td id="qty2"><strong><div id="idforesc"><?php echo $i;?></div></strong></td>
      <td id="aright"><strong>Total Price</strong></td>
      <td id="aright"><strong><?php $adjutedprice2=$adjutedprice; ;echo $adjutedprice = numbers($adjutedprice);?></strong></td>
    </tr>
  </table>
  <?php
  if($totalitems>0)
  {
  	if($billing!='billing')
	{
  ?>
     <span class="buttons">
      <button type="button" name="btn" id="button" onclick="javascript:loadsection('main-content','payment.php');" title="F2">
            <img src="images/tick.png" alt=""/> 
           Collect Payment
        </button>
     </span>    
     <?php
	}//end of if billing
	 ?>
	<?php
	if($_SESSION['tpmode']==2)
	{
					$dccustomerid	=	$_SESSION['customerid'];
					
		/* //commented 13-08-2010
		?>
        <button type="button" name="btn3" id="button" onclick="javascript:printcustomerinvoice('<?php echo $tempsaleid;?>','<?php echo $dccustomerid;?>')" title="CTRL+P">
            <img src="images/printer.png" alt=""/> 
           Print Sale Invoice
        </button>
        <?php
		*/
	}
	else
	{
	/*
	?>
      <button type="button" name="btn3" id="button" onclick="javascript:printaleinvice('<?php echo $tempsaleid;?>');" title="CTRL+P">
            <img src="images/printer.png" alt=""/> 
           Print Sale Invoice
        </button>
 	<?php

*/	}
  		$tpmode	=	$_SESSION['tpmode'];
		if($tpmode==2 && $_SESSION['invmode']!=1)
		{
		?>
        <span class="buttons">
		<button type="button" name="btn2" id="button" onclick="javascript:loadsection('main-content','sale.php?salecompleted=4');" title="Ctrl+3">
            <img src="images/tick.png" alt=""/> 
           Delivery Chalan
        </button>
        </span>
	<?php
		}//end of delivery Chalan tpmode
  }
	if($billing!='billing' && $adjust !='adjustment' && $tempsaleid!='' && $i <= 0) //  && $i <= 0 added by Yasir -- 04-07-11 
	{
  ?>
  <span class="buttons">
  		<button type="button" name="btn2" id="button" onclick="javascript:cancel_sale();" title="">
            <img src="images/cross.png" alt=""/> 
           Cancel Sale
        </button>
  </span>
	<?php
		
	}//end of if billing
	//echo $adjust;
	if($adjust=='adjustment')
	{
  ?>
  <span class="buttons">
  		<button type="button" name="btn2" id="button" onclick="javascript:loadsection('main-content','sale.php?salecompleted=adjustment');" title="CTRL+SPACE">
            <img src="images/cross.png" alt=""/> 
           Cancel Adjustment
        </button>
  </span>
	<?php
	}//end of if billing
// calculating price changes
if($billing=='billing' && $adjust =='')
{//changed $dbname_main to $dbname_detail on line 436 by ahsan 22/02/2012
	$pchanges	=	$AdminDAO->getrows("$dbname_detail.sale,$dbname_detail.saledetail sd,$dbname_detail.stock st,barcode bc,discountreason","pksaleid,round(sd.saleprice,2) as sprice,round(sd.originalprice,2) as originalprice,from_unixtime(timestamp,'%d-%m-%y %h:%i:%s') as dtime,barcode,reasontitle","sd.fksaleid='$tempsaleid' AND sd.originalprice<>sd.saleprice AND sd.fkstockid=st.pkstockid AND st.fkbarcodeid=bc.pkbarcodeid AND fkreasonid=pkreasonid GROUP by pksaledetailid");
?>
<br />
<br />
<table   class="pos">
<tr>
<th colspan="6">Price Changes</th>
</tr>
<tr>
<td width="50">Date & Time</td>
<td width="50">ID</td>
<td width="50">Barcode</td>
<td width="50">Original</td>
<td width="50">Changed</td>
<td width="50">Reason</td>
</tr>
<?php
$original	=	0;
$pchange	=	0;
for($i=0;$i<sizeof($pchanges);$i++)
{
	$pchange+=$pchanges[$i][sprice];
	$original+=$pchanges[$i][originalprice];
?>
<tr>
<td><?php echo $pchanges[$i][dtime];?></td>
<td><?php echo $pchanges[$i][pksaleid];?></td>
<td><?php echo $pchanges[$i][barcode];?></td>
<td align="right"><?php echo $pchanges[$i][originalprice];?></td>
<td align="right"><?php echo $pchanges[$i][sprice];?></td>
<td align="right"><?php echo $pchanges[$i][reasontitle];?></td>
</tr>
<?php
}
?>
<tr>
<td colspan="4" align="right">Total</td>
<td align="right"><?php echo numbers($original);?></td>
<td align="right"><?php echo numbers($pchange);?></td>
</tr>
</table>

<?php
}
?>

</div>
	<div id="deleteitemreason" class="delreasonbox" >
	</div>
    <div id="editsaleitems" class="edititemsbox" >
    </div>
    <div id="movetocounter" class="edititemsbox" >
    </div>
</div>
<div id="rightpanel">

<?php
if($tempsaleid)
{
//$discounts	=	$DiscountDAO->calculatestock($tempsaleid);
}
for($d=0;$d<count($discounts);$d++)
{
	$discountstockid 	=	$discounts[$d]['fkstockid'];
	$discountquantity 	=	$discounts[$d]['quantity'];
	$discountsaleprice 	=	$discounts[$d]['saleprice'];
	$discountstockid 	=	$discounts[$d]['fkstockid'];
}
//print_r($discounts);
if($barcode!='')
{
?>
  <table width="100%" class="pos">
    <tr>
      <th>Name</th>
      <td>
      <?php
      	echo $productname;
		if($productname!='')
		{
			// this gets the price from saledetail requested by hasnain
			//added by Riz & Co
			//23-12-2009
			/*$pricequery	=	"SELECT 
								sd.saleprice
							FROM 
								$dbname_main.saledetail sd,
								$dbname_main.stock st,
								barcode b 
							WHERE
								st.fkbarcodeid		=	b.pkbarcodeid AND
								sd.fkstockid		=	pkstockid AND
								st.priceinrs		<>	sd.saleprice AND
								b.pkbarcodeid		=	'$pkbarcodeid' 
							ORDER BY 
								timestamp desc 
							LIMIT 0,1";*/
			$quoteprice		=	$stockdata[0]['quoteprice'];
			$quotepricearr	=	explode('_',$quoteprice);		
			$quoteprice		= $quotepricearr[0];
			$pkpodetailid	= $quotepricearr[1];
			$taxable		= $quotepricearr[2];
			// added by Yasir 21-09-11
			$purchaseorderid= $quotepricearr[3];
			$quotetitle		= $quotepricearr[4];	
			
			//
			
			
			if($quoteprice=='')
			{//changed $dbname_main to $dbname_detail on line 550 by ahsan 22/02/2012
				$pricequery		=	"SELECT	
										price
									FROM
										$dbname_detail.pricechange
									WHERE
										fkbarcodeid='$pkbarcodeid'
									";
				$priceresult	=	$AdminDAO->queryresult($pricequery);
				$priceinrs		=	$priceresult[0]['price'];
				$tradeprice		=	$stockdata[0]['tradeprice'];
				
				
				$productnewprice	=	$AdminDAO->getrows("$dbname_detail.stock","MAX(retailprice) as newprice ","pkstockid='$dstockid'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
				$newprice 	=		$productnewprice['newprice'];
			
				if($newprice>$tradeprice)
				{
					$highprice=1;
				}
				if(!$priceinrs)
				{
					$priceinrs	=	$tradeprice;
				}
			}
			else
			{
				$priceinrs	=$quoteprice;	
			}
			//if tradepricemode is active then show tradeprice
			if($tpmode==1)
			{
				//$priceinrs			=	$tradeprice;
				$recenttradeprice	=	$stockdata[0]['recenttradeprice'];
				$priceinrs			=	$recenttradeprice;
			}
			//echo $stockid;
			//print_r($productnewprice);
		?>
				<script language="javascript">				
				<?php
				if($pkpodetailid!='')
				{
				?>
						document.getElementById('pkpodetailid').value='<?php  echo $pkpodetailid;?>';
						document.getElementById('taxable').value='<?php  echo $taxable;?>';
						document.getElementById('pkpurchaseorderid').value='<?php  echo $purchaseorderid;?>';
						document.getElementById('quotetitle').value='<?php  echo $quotetitle;?>';
							
				<?php
				}
				if($tpmode==1)
				{
				?>
					<?php /*?>document.getElementById('recenttradeprice').innerHTML='<?php if($recenttradeprice!='') { echo $recenttradeprice;} else { echo "0";}?>';<?php */?>
					/*document.getElementById('stockpricespan').style.display	=	'none';
					document.getElementById('tradepricespan').style.display	=	'block';
					document.getElementById('maxtradeprice').value	=	'<?php //echo $priceinrs;?>';
					document.getElementById('newtradeprice').value	=	'<?php //echo $recenttradeprice;?>';*/
					document.getElementById('price').value	=	'<?php echo $recenttradeprice;?>';
				<?php
				}//if tpmode
				?>
					document.getElementById('price').value='<?php if($boxprice=='') { echo $priceinrs;} else { echo $boxprice;}?>';
					document.getElementById('isbox').value='<?php if($boxprice=='') { echo '0';} else { echo '1';}?>';
					document.getElementById('boxsize').value='<?php if($boxprice!='') { echo $boxquantity;}?>';
					document.getElementById('productname').value='<?php echo $productname;?>';
					document.getElementById('barcode1').value='<?php echo $barcode;?>';
					<?php 
					if($highprice=='1')
					{
					?>
						document.getElementById('newprice').value='<?php if($boxprice=='') { echo $newprice;} else { echo $boxprice;}?>';
						document.getElementById('reason').selected='true';
					<?php
					}//end of high price
					if($boxprice!='')
					{
					?>
					document.getElementById('quantity').value='<?php if($boxprice!='') {echo '1';} else { echo '0';}?>';
					document.getElementById('boxeditemdiv').style.display='block';
					<?php 
					}
					else
					{
						?>
						document.getElementById('boxeditemdiv').style.display='none';
						<?php
					}
					?>
				</script>
		<?php	
		}
	  ?>
      </td>
    </tr>

    <?php
	if($defaultimage!='')
	{
	?>
    <tr>
      <th>Picture</th>
      <td><img src="productimage/<?php echo $defaultimage;?>" width="100" height="100"/></td>
    </tr>
    <?php
	}
	?>
    <tr>
      <th>Sale Price</th>
      <td>
	  		<div id="saleprice" class="totalprice">
			<?php 
			if($boxprice!='')
			{
				echo numbers($boxprice);
			}
			else
			{
				echo number_format($priceinrs,2);
			}
			?>
        	</div>
            <?php
			if($_GET['id']=='')
			{
				if($stockdata[0]['tradeprice']!='')
				{
					?>
        			<?php /*?><script language="javascript">
						document.getElementById('price').value='<?php echo $stockdata[0]['tradeprice'];?>';
						document.getElementById('productname').value='<?php echo $productname;?>';
						document.getElementById('barcode1').value='<?php echo $barcode;?>';
                    </script><?php */?>
                    <?php	
				}
			}

			?>
      </td>
    </tr>
  </table>
<?php
		}///end of if product
if($_GET['id']!='')
{
	$discountarray	=	$AdminDAO->getrows("$dbname_detail.sale",'globaldiscount'," `pksaleid`= '$tempsaleid' ");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
	$globaldiscout		=	$discountarray[0]['globaldiscount'];
}
?>
  
  <table width="100%" class="price">
    <tr>
      <th width="35%">Items</th>
      <td><?php echo $totalitems;?></td>
    </tr>
   <?php
   if($globaldiscout!='')
   {
   ?>
    <tr>
      <th>Global Discount</th>
      <td><?php echo numbers($globaldiscout);?></td>
    </tr>
    <?php
   }
	?>
    <tr>
      <th>Price</th>
      <td><span class="totalprice"><?php 
	  // change by Yasir -- 06-07-11. Previous was $grandtotal-$globaldiscout+$adjutedprice2 
		 echo numbers($grandtotal+$adjutedprice2);
	 ?>
      </span></td>
    </tr>
    <?php
	if($tempsaleid)
	{//changed $dbname_main to $dbname_detail on line 725 by ahsan 22/02/2012
 	  $sql=" SELECT CONCAT( firstname,' ', lastname,' (',nic,')') as customername,id as pkcustomerid
			FROM $dbname_detail.account, $dbname_detail.addressbook, $dbname_detail.sale
			WHERE fkaddressbookid = pkaddressbookid AND pksaleid='$tempsaleid' AND fkaccountid=id
		";
		$customer_array	=	$AdminDAO->queryresult($sql);		
		$customername	=	$customer_array[0]['customername'];
		$customerid		=	$customer_array[0]['pkcustomerid'];
		if($customername!='')
		{
?>
	   <tr>
		  <th>Customer</th>
		  <td style="font-size:14px;"><?php echo $customername;?></td>
		</tr>
<?php
		}
	}//if
   ?>
  </table>
<?php
//$discountytpearray	=	$DiscountDAO->getstockdiscounttype($dstockid,1);
//for($da=0;$da<count($discountytpearray);$da++)
//{
	//echo $DiscountDAO->searchdiscount($pkbarcodeid,$discountytpearray[$da]['pkdiscounttypeid'],$dstockid);//gets the discounts
	//echo $DiscountDAO->searchdiscount($pkbarcodeid,"4",$dstockid);//4=discounttype finds the Product gainst Product discount 
	//echo $DiscountDAO->searchdiscount($pkbarcodeid,"2",$dstockid);//1=discounttype finds the Quantity gainst amount  
	//echo $DiscountDAO->searchdiscount($pkbarcodeid,"1",$dstockid);//1=discounttype finds the quantity gainst  quantity 
//}
//echo $DiscountDAO->searchdiscount('',"3",$dstockid='');//3=discounttype finds the amount gainst amount discount 
if($_GET['id']!='')
{

// added paymenttype <> 'c' by Yasir -- 05-07-11 	
//changed $dbname_main to $dbname_detail on line 768 by ahsan 22/02/2012	
$query		=	"SELECT 
					pkpaymentid as id,
					paymentmethod as type, 
					amount, 
					paytime, 
					(IF(p.rate IS NULL,0,p.rate)) as rate,
					currencyname as currency,  
					(IF(charges IS NULL,0,charges)) as charges,
					tendered,
					(IF(returned IS NULL,0,returned)) as returned
				FROM $dbname_detail.payments p
				LEFT JOIN currency
					   ON fkcurrencyid = pkcurrencyid
				WHERE fksaleid = '$tempsaleid'
					  AND paymenttype <> 'c'					  
				 ORDER BY paytime DESC";				

if($tempsaleid)
{
	$payments	=	$AdminDAO->queryresult($query);
}
for($j=0;$j<sizeof($payments);$j++)
{
	 $ptype			=	$payments[$j]['type'];//c = cash cc =creditcard fc=foreigncurrency ch = cheque cr=credit
	$amount			=	$payments[$j]['amount'];
	$currency		=	$payments[$j]['currency'];
	$rate			=	$payments[$j]['rate'];
	$pcharges		=	$payments[$j]['charges'];
	$tenderedamount	+=	$payments[$j]['tendered'];
	$returned		+=	$payments[$j]['returned'];	
	if($ptype == 'fc')	
	{
		$fcamountinrs +=	$amount;
	}
	elseif($ptype == 'c')
	{
		$cashamount		+=$amount;
	}
	elseif($ptype == 'cc')
	{
		$ccamount		+=$amount;
	}
	elseif($ptype == 'ch')
	{
		$chequeamount		+=$amount;
	}
	elseif($ptype == 5)
	{
		$creditamount		+=$amount;
	}
	
}
include_once("includes/classes/pricing.php");
$totalamount		=	getpaidamount($tempsaleid);
$totalamount		=	explode("_",$totalamount);
$remainingprice		=	$totalamount[0];
   ?>
   <table width="100%" class="price">
    <tr>
      <th colspan="2">Payments</th>
     </tr>
    <tr>
      <th width="35%">Cash</th>
      <td id="aright"><?php echo numbers($cashamount);?></td>
    </tr>
    <tr>
      <th> Credit Card</th>
      <td id="aright"><?php echo numbers($ccamount);?></td>
    </tr>
    <?php
	$creditcardarray	=	ccpaymentbytype($tempsaleid);
	//print_r($creditcardarray);
	for($x=0;$x<sizeof($creditcardarray);$x++)
 	 {
	  $ccname	=	$creditcardarray[$x]['typename'];
	  $ccamount	=	$creditcardarray[$x]['amount'];
	  $ccno		=	$creditcardarray[$x]['ccno'];
	?>
    <tr>
      <th align="right"><?php echo $ccname;?></th>
      <td id="aright"><span style="font-size:14px; font-weight:normal; float:left"><?php if ($ccno!=''){ echo "#: $ccno";}?></span> <?php echo numbers($ccamount);?></td>
    </tr>
    <?php
	 }
	?>
    <tr>
      <th>Foreign Currency</th>
      <td id="aright"><?php $fcamountinrs	=	floor($fcamountinrs); echo numbers($fcamountinrs);?></td>
    </tr>
     <?php
	$fcarray	=	fcpaymentbycurrency($tempsaleid);
	for($y=0;$y<sizeof($fcarray);$y++)
 	 {
	  $currencyname	=	$fcarray[$y]['currencyname'];
	  $symbol		=	$fcarray[$y]['currencysymbol'];
	  $fcamount		=	$fcarray[$y]['amount'];
	?>
    <tr>
      <th><span style="float:right;"><?php echo $currencyname." ".$symbol;?></span></th>
      <td id="aright"><?php echo numbers($fcamount);?></td>
    </tr>
    <?php
	 }
	?>
    <tr>
      <th>Cheque Total</th>
      <td id="aright"><?php echo numbers($chequeamount);?></td>
    </tr>
     <?php
	$chequearray	=	chequepaymentbybank($tempsaleid);
	for($z=0;$z<sizeof($chequearray);$z++)
 	 {
	  $bankname		=	$chequearray[$z]['bankname'];
	  $chamount		=	$chequearray[$z]['amount'];
	  $chequeno		=	$chequearray[$z]['chequeno'];
	?>
    <tr>
      <th><span style="float:right;"><?php echo $bankname;?></span></th>
      <td id="aright"><span style="font-size:14px; font-weight:normal; float:left"><?php if($chequeno!=''){ echo "#: $chequeno";}?></span> <?php echo numbers($chamount);?></td>
    </tr>
    <?php
	 }
	if($remainingprice<0)
	{
		$payable		=	-($remainingprice);
		$payable		=	floor($payable);
		$remainingprice	=	0;
	}
	?>
    <tr>
      <th>Credit</th>
      <td id="aright"><?php echo numbers($remainingprice-$globaldiscout); /* Changed by Yasir -- 06-07-11 Previous was $remainingprice	   
	   If set remaining price in getpaidamount function in pricing.php by uncommenting line 88, but effects payment.
	  */
	    ?></td>
    </tr>
    <tr>
      <th>Return</th>
      <td id="aright"><?php echo numbers($payable);?></td>
    </tr>
  </table>
<?php 
}
?>
</div>
<script language="javascript">
function changepriceincodeitem(val)
{
	if(document.getElementById('saleprice'))
	{
		var sprice	=	document.getElementById('saleprice').innerHTML=val;
	}
	
}
<?php
if($barcode != '')
{
?>
	//submitformsale();
<?php
}
?>
</script>
<?php
$tpmode	=	$_SESSION['tpmode'];
if($tpmode==1)
{
	print"<script language='javascript'>
				document.getElementById('tpmode').className='currentred';
			</script>
	";
}

// added by Yasir 21-09-11
if ($_SESSION['quotetitle'] != ''){
	echo "<script language=javascript>
  document.getElementById('quotetitlediv').innerHTML	=	'<div   style=position:absolute;background-color:#000;padding:6px;-moz-border-radius:3px;-webkit-border-radius:4px;font-weight:bold;color:#fff;float:left; >{$_SESSION['quotetitle']}</div>';
  </script>";
}
//
?>