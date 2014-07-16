<?php

include("includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs,$DiscountDAO;
$barcode	=	filter($_REQUEST['code']);
$productid	=	filter($_REQUEST['productid']);
$dstockid	=	filter($_REQUEST['stockid']);
$action		=	$_REQUEST['action'];
$empid		=	$_SESSION['employeeid'];
$storeid	=	$_SESSION['storeid'];
if($_GET['id']!='')
{
 	$_SESSION['tempsaleid']	=	$_GET['id'];
}

if($action=='del')
{
	$saleid	=	$_REQUEST['saleid'];
	$AdminDAO->deleterows("saledetail","  fkstockid='$dstockid' AND fksaleid='$saleid' ","1");
	echo "The Item has been deleted from the sale.";
	exit;
}
if($barcode!='')
{
	 $and=" AND `barcode`='$barcode'	";
}
elseif($productid!='')
{
	 $and=" AND `pkproductid`='$productid'	";
}

/****************************PRODUCT DATA*****************************/
if($barcode!='' || $productid!='')
{
	$barcode_array		=	$AdminDAO->getrows('barcode,product','*',"`fkproductid`=`pkproductid` $and ");
	$productname 		=	$barcode_array[0]['productname'];
	$productid	 		=	$barcode_array[0]['pkproductid'];
	$pkbarcodeid 		=	$barcode_array[0]['pkbarcodeid'];
	$barcode	 		=	$barcode_array[0]['barcode'];
	$productdescription =	$barcode_array[0]['productdescription'];
	$defaultimage		=	$barcode_array[0]['defaultimage'];
}
//print"here pkbarcodeid=$pkbarcodeid";
/***********************************Attributes DATA*************************/
if($productid!='')
{
	 $query="
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
					) AS currprice, (select priceinrs  from stock where fkbarcodeid=barcodeid order by pkstockid DESC	LIMIT 0,1) as tradeprice,barcode.barcode, product.productname as productattributeoptionname
					FROM stock, barcode, product
					WHERE fkbarcodeid = barcode.pkbarcodeid
					AND barcode.fkproductid = product.pkproductid 
					AND barcode.barcode='$barcode'
				";
	$attributes_array	=	$AdminDAO->getrows('productattribute,attribute,productinstance ','*',"`pkattributeid`=`fkattributeid` AND `fkproductid`='$productid' AND fkproductattributeid=pkproductattributeid AND fkbarcodeid='$pkbarcodeid' ");
	$proinstance_array	=	$AdminDAO->getrows('productinstance','*',"`fkbarcodeid`='$pkbarcodeid' ");
	$stockdata			=	$AdminDAO->queryresult($query);
	
}
	$brands_array		=	$AdminDAO->getrows('brand, barcodebrand ',' pkbrandid, brandname '," branddeleted<>'1' AND fkbrandid=pkbrandid AND fkbarcodeid='$pkbarcodeid' ");
	$brands				=	$brands_array[0]['brandname'];

//print_r($proinstance_array);
?>

<div id="leftpanel">
  <table width="100%" class="pos">
    <tr>
      <th width="26%">Product</th>
      <th width="7%">Qty</th>
      <th width="17%">Unit Price</th>
      <th width="44%">Sub Total</th>
      <th width="6%">&nbsp;</th>
    </tr>
  <?php
	$salecompleted;
	if($salecompleted==1)
	{
	 $_SESSION['tempsaleid']='';		
	}
	 $tempsaleid	=	$_SESSION['tempsaleid'];	
	$salerows	=	$AdminDAO->getrows('saledetail','pksaledetailid,fkstockid,saleprice, sum( quantity ) AS quantity, sum( saleprice * quantity ) AS subtotal '," fksaleid='$tempsaleid' group by fkstockid,saleprice");
  	$totalitems	=	count($salerows);
  	$items=0;
  for($i=0;$i<count($salerows);$i++)
  {
  	//$pksaledetailid	=	$salerows[$i]['pksaledetailid'];
	$saleprice		=	$salerows[$i]['saleprice'];
	$subtotal 		=	$salerows[$i]['subtotal'];
	$quantity		=	$salerows[$i]['quantity'];
	$stockid		=	$salerows[$i]['fkstockid'];
	if($stockid!='')
	{
		 $sql=" SELECT CONCAT( productname, ' (', GROUP_CONCAT( attributeoptionname ) ,')') PRODUCTNAME
			FROM productattribute pa
			RIGHT JOIN (
			product p, attribute a
			) ON ( pa.fkproductid = p.pkproductid
			AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, stock s,barcode b
			WHERE pkproductid = pa.fkproductid
			AND pkattributeid = pa.fkattributeid
			AND pkproductattributeid = fkproductattributeid
			AND pkattributeid = ao.fkattributeid
			AND pkattributeoptionid = pi.fkattributeoptionid
			AND b.fkproductid = pkproductid
			AND pi.fkbarcodeid = b.pkbarcodeid
			AND s.fkbarcodeid=b.pkbarcodeid
			AND s.pkstockid='$stockid' 
			";
		$productrow		=	$AdminDAO->queryresult($sql);
		$productnameinv	=	$productrow[0]['PRODUCTNAME'];
		
	}
  	
 	if($quantity>0)
	{
  ?>
    <tr>
      <td>
   		 <?php echo $productnameinv;?>
      </td>
      <td id="qty"><?php echo $quantity;?></td>
      <td>
	  		<?php  
		  		echo $saleprice;	
			?>
       </td>
      <td><?php  
		  		echo $subtotal;
			?></td>
      <td align="center">
      	<a href="javascript: void(0)" onclick="javascript: delsaleitem('<?php echo $stockid;?>','<?php echo $tempsaleid;?>')"><img src="../images/hr.gif" border="0" title="Delete this Item from Stock"/></a>
      </td>
    </tr>
    
  <?php
  	
  	$grandtotal	+=$subtotal;	
	}//end of qty check
  	if($quantity<0)
	{
		$adjustmentarray[]	=	$productnameinv;
		$adjustmentarray[]	=	$quantity;	
		$adjustmentarray[]	=	$saleprice;	
		$adjustmentarray[]	=	$subtotal;	
		$adjustmentarray[]	=	$stockid;	
			
		$items++;
		$totalitems	=	$totalitems-1;
		$adjustment[]=$adjustmentarray;
	}
  
  }//edn of loop
 
  ?>
	 <tr>
      <td><strong>Total Items: <?php echo $totalitems;?></strong></td>
      <td id="qty2">&nbsp;</td>
      <td>&nbsp;</td>
      <td><strong>Total Price: <?php echo $grandtotal;?></strong></td>
      <td>&nbsp;</td>
    </tr>
	 <tr>
	   <td colspan="5">&nbsp;</td>
    </tr>
	 <tr>
	   <th colspan="5">Returns &amp; Adjustments</th>
    </tr>
	<?php
	for($j=0;$j<count($adjustment);$j++)
	{
	?>
     <tr>
	   <td><?php echo $adjustment[$j][0];?></td>
	   <td id="qty5"><?php echo $adjustment[$j][1];?></td>
	   <td><?php echo $adjustment[$j][2];?></td>
	   <td>&nbsp;</td>
	   <td><?php echo $adjustment[$j][3];?></td>
    </tr>
  <?php
  	$adjutedprice+= $adjustment[$j][3];
  }
  ?>
 	 <tr>
      <td><strong>Total Items: <?php echo $j;?></strong></td>
      <td id="qty2">&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><strong>Total Price: <?php echo $adjutedprice;?></strong></td>
    </tr>
  </table>
  <input type="button" name="btn" value="Collect Payment" onclick="javascript:loadsection('main-content','payment.php')" />
</div>
<div id="rightpanel">

<?php

$discounts	=	$DiscountDAO->calculatestock($tempsaleid);
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
      	echo $productname. " $brands";
	  ?>
      </td>
    </tr>
    <tr>
      <th>Attributes</th>
      <td>
      	<?php
		$attributes_array	=	array_unique($attributes_array);
        for($i=0;$i<sizeof($attributes_array); $i++)
		{
			//echo $v++;
			$pkproductattributeid	=	 $attributes_array[$i][pkproductattributeid];
			$options_array	=	$AdminDAO->getrows('productinstance,attributeoption,barcode ','*',"`fkproductattributeid`='$pkproductattributeid' AND `fkattributeoptionid`=`pkattributeoptionid` AND fkbarcodeid=pkbarcodeid AND barcode='$barcode'");
			
			for($j=0; $j<count($options_array); $j++)
			{
				$attributeoptionname	=	$options_array[$j]['attributeoptionname'];	
				$att.=' '.$attributeoptionname;
			}
		
		}
			echo $att;
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
	  		<div id="saleprice">
			<?php 
				echo $stockdata[0]['tradeprice'];
			?>
        	</div>
            <?php
			if($_GET['id']=='')
			{
				if($stockdata[0]['tradeprice']!='')
				{
					?>
                    <script language="javascript">
						document.getElementById('price').value='<?php echo $stockdata[0]['tradeprice'];?>';
						document.getElementById('productname').value='<?php echo $productname; echo "$att $brands";?>';
						document.getElementById('barcode1').value='<?php echo $barcode;?>';
                    </script>
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
	$discountarray	=	$AdminDAO->getrows('sale','globaldiscount'," `pksaleid`= '$tempsaleid' ");
	$globaldiscout		=	$discountarray[0]['globaldiscount'];
}
?>
  
  <table width="100%" class="price">
    <tr>
      <th width="35%">Total Items</th>
      <td><?php echo $totalitems;?></td>
    </tr>
   <?php
   if($globaldiscout!='')
   {
   ?>
    <tr>
      <th>Global Discount</th>
      <td><?php echo $globaldiscout;?></td>
    </tr>
    <?php
   }
	?>
    <tr>
      <th>Price</th>
      <td><?php echo $grandtotal-$globaldiscout;?></td>
    </tr>
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
$query		=	"SELECT 
	pkcashpaymentid as id,
	1 as type, 
	amount, 
	paytime, 
	NULL as rate, 
	NULL as currency, 
	NULL as charges,
	tendered,
	returned
FROM cashpayment c 
	WHERE c.fksaleid = '$tempsaleid' 
UNION 
SELECT 
	pkccpaymentid as id,
	2 as type, 
	amount, 
	paytime, 
	NULL as rate, 
	NULL as currency, 
	charges,
	NULL as tendered,
	NULL as returned 
FROM ccpayment cc 
	WHERE cc.fksaleid = '$tempsaleid' 
UNION 
	SELECT pkfcpaymentid as id,
	3 as type, 
	amount, 
	paytime, 
	fc.rate as rate, 
	currencyname as currency,
	charges,
	tendered,
	returned
FROM fcpayment fc, 
	currency cr WHERE fc.fkcurrencyid = cr.pkcurrencyid AND fc.fksaleid = '$tempsaleid' 
UNION 
	SELECT pkchequepaymentid as id,
	4 as type,  
	amount, 
	paytime, 
	NULL as rate, 
	NULL as currency, 
	NULL as charges,
	NULL as tendered,
	NULL as returned
FROM chequepayment ch WHERE ch.fksaleid = '$tempsaleid'
ORDER BY paytime DESC";

$payments	=	$AdminDAO->queryresult($query);
for($j=0;$j<sizeof($payments);$j++)
{
	 $ptype			=	$payments[$j]['type'];//1 = cash 2 =cc 3=fc 4 = cheque 5=credit
	$amount			=	$payments[$j]['amount'];
	$currency		=	$payments[$j]['currency'];
	$rate			=	$payments[$j]['rate'];
	$pcharges		=	$payments[$j]['charges'];
	$tenderedamount	+=	$payments[$j]['tendered'];
	$returned		+=	$payments[$j]['returned'];	
	if($ptype == 3)	
	{
		$fcamountinrs +=	$amount*$rate;
	}
	elseif($ptype == 1)
	{
		$cashamount		+=$amount;
	}
	elseif($ptype == 2)
	{
		$ccamount		+=$amount;
	}
	elseif($ptype == 4)
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
      <th colspan="2">Paymets</th>
     </tr>
    <tr>
      <th width="35%">Cash</th>
      <td><?php echo $cashamount;?></td>
    </tr>
    <tr>
      <th> Credit Card</th>
      <td><?php echo $ccamount;?></td>
    </tr>
    <tr>
      <th>Foreign Currency</th>
      <td><?php echo $fcamountinrs;?></td>
    </tr>
    <tr>
      <th>Cheque</th>
      <td><?php echo $chequeamount;?></td>
    </tr>
    <tr>
      <th>Credit</th>
      <td><?php echo $remainingprice;?></td>
    </tr>
  </table>
<?php 
}
?>
</div>