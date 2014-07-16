<?php
include("includes/security/adminsecurity.php");
global $AdminDAO;
@session_start();
$tempsaleid	=	$_SESSION['tempsaleid'];

?>
<table>
<tr>
    <th colspan="6" width="920"><span style="padding-left: 390px; border:none; font-size:16px; width:80%;"><b>Profit Check</b></span></th>
      <th  width="10" colspan="6"><span style=" border:none; font-size:16px;"><a href="javascript:void(0);" onClick="javascript:jQuery('#profit_check').fadeOut();"><img src="images/cross.png" alt="CLOSE" border="0"/></a></span></th>
  </tr>

   <tr>
    <td colspan="1" style="text-align:right; font-size:16px;" width="361"><strong>Net Profit</strong></td>
    <td id="qty2" width="219" style="font-size:16px;" ><div id="fad_div2" style="display:block;"></div></td>
    <td id="aright" colspan="2" width="219" style="font-size:16px;"><strong>Profit Percentage</strong></td>
    <td id="aright" width="117" style="text-align:left; font-size:16px;" colspan="3" > <div id="fad_div1" style="display:block;"></div>
    </td>
   
  </tr>
</table>

<table width="300" align="left" class="epos">
  <tr>
    <th>Sr.#</th>
    <th width="30%">Item</th>
    <th>Quantity</th>
    <th>Retail Price</th>
    <th>Cost Price</th>
    <th>Retail Total</th>
    <th>Cost Total</th>
  </tr>
  <?php
			$salerows	=	$AdminDAO->getrows("$dbname_detail.saledetail",'pksaledetailid,fkstockid,saleprice, sum( quantity ) AS quantity, sum( saleprice * quantity ) AS subtotal, boxsize, taxable'," fksaleid='$tempsaleid' group by fkstockid,saleprice ORDER BY 	timestamp DESC");
			$items=0;
			$tf=0;
			$tf_p1=0;
			$tf_p2=0;
			$t_item=count($salerows);
			for($i=0;$i<$t_item;$i++)
			{
				$pksaledetailid	=	$salerows[$i]['pksaledetailid'];
				$saleprice		=	$salerows[$i]['saleprice'];
				$subtotal 		=	$salerows[$i]['subtotal'];
				$quantity		=	$salerows[$i]['quantity'];
				$boxsize		=	$salerows[$i]['boxsize'];
				$taxable		=	$salerows[$i]['taxable'];
				$stockid		=	$salerows[$i]['fkstockid'];
				if($stockid!='')
				{
						$sql="select 
									itemdescription,shortdescription,pkbarcodeid
								FROM 
									barcode,
									$dbname_detail.stock 
								WHERE 
									pkbarcodeid=fkbarcodeid AND 
									pkstockid='$stockid'";
				
					$productrow		=	$AdminDAO->queryresult($sql);
					$productnameinv	=	$productrow[0]['shortdescription'];
					if($productnameinv=='')
					{
						$productnameinv	=	$productrow[0]['itemdescription'];
					}
				}
			?>
  <tr>
    <td><?php echo $i+1;?></td>
    <td><?php echo $productnameinv;?></td>
    <td><?php  echo $quantity;?></td>
    <td><?php echo $saleprice;?></td>
    <td><?php 

			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
 $pkbarcodeid 		=	$productrow[0]['pkbarcodeid'];



 $sql_=" SELECT CONCAT( firstname,' ', lastname,' (',nic,')') as customername,id as pkcustomerid
			FROM $dbname_detail.account, addressbook, $dbname_detail.sale
			WHERE fkaddressbookid = pkaddressbookid AND pksaleid='$tempsaleid' AND fkaccountid=id
		";
		$customer_array_	=	$AdminDAO->queryresult($sql_);
		$customerid_		=	$customer_array_[0]['pkcustomerid'];
	
	
	$subqry_customer	=	" ,( SELECT	
						CONCAT(quoteprice,'_',pkpodetailid,'_',taxable,'_',pkpurchaseorderid,'_',quotetitle) 
					FROM
						$dbname_detail.podetail,
						$dbname_detail.purchaseorder
					WHERE
						fkbarcodeid	=	barcodeid AND 
						fkpurchaseorderid=pkpurchaseorderid AND
						fkaccountid='$customerid_' AND 
						expired=1 AND 
						$dbname_detail.purchaseorder.status=2
					LIMIT 0,1) as quoteprice ";
			
	
	$query_Cus="SELECT  DISTINCT (pkbarcodeid) AS barcodeid, 
						(SELECT 
								costprice
							from 
								$dbname_detail.stock ,barcode
							where 
								fkbarcodeid = barcode.pkbarcodeid AND
								barcode.pkbarcodeid='$pkbarcodeid'
						ORDER BY
						        pkstockid DESC LIMIT 0,1 	
							
						) as tradeprice
						,(SELECT costprice from $dbname_detail.stock where fkbarcodeid=barcode.pkbarcodeid order by pkstockid DESC limit 0,1) recenttradeprice
						$subqry_customer
					FROM 
						 barcode
					WHERE 
					
						pkbarcodeid='$pkbarcodeid'
				";
	$stockdata_Cus=$AdminDAO->queryresult($query_Cus);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				echo $stockdata_Cus[0]['tradeprice'];
				$subtotal_t=$quantity*$stockdata_Cus[0]['tradeprice'];	
			   $tf=$tf+$quantity;
			   $tf_p1=$tf_p1+$subtotal;
			   $tf_p2=$tf_p2+$subtotal_t;
			?></td>
    <td align="right"><div id="subtotal_<?php echo $i+1;?>"><?php echo $subtotal;?></div></td>
    <td align="right"><div id="subtotal_<?php echo $i+1;?>"><?php echo $subtotal_t;?></div></td>
  </tr>
  <?php
			}
			
			?>
  <tr>
    <td colspan="2" style="text-align:right;"><strong>Total Items</strong></td>
    <td id="qty2"><strong>
      <?php  echo $t_item.'('.$tf.')';?>
      </strong></td>
    <td id="aright" colspan="2"><strong>Total Price</strong></td>
    <td id="aright"><strong><?php echo $tf_p1;?></strong></td>
    <td id="aright"><strong><?php echo $tf_p2;?></strong></td>
  </tr>
  
	 <tr>
	   <th colspan="8" style="padding-left: 50px;" ><b>Profit</b></th>
    </tr>

  
   <tr>
    <td colspan="2" style="text-align:left;padding-left: 50px;"><strong>Net Profit</strong></td>
    <td id="qty2"><strong><?php echo $tf_p1-$tf_p2;?></strong></td>
   
  </tr>

  
  
   <tr>
    <td colspan="2" style="text-align:left;padding-left: 50px;"><strong>Profit Percentage</strong></td>
    <td id="qty2"><strong><?php 
	$rtp=round((($tf_p1-$tf_p2)*100)/$tf_p1,2);
	echo $rtp. '%'?></strong>
    <script>
    document.getElementById('fad_div1').innerHTML='<?php echo $rtp. '%'?>';
	document.getElementById('fad_div2').innerHTML='<?php echo $tf_p1-$tf_p2?>';
    </script>
    </td>
    
   
  </tr>
</table>
<table>
  <tr>
    <td colspan="5" align="center" style="padding-left: 410px; padding-top: 27px; border:none;"><span class="buttons" style="font-size:12px;">
      <button type="button" name="button2" id="button2x" onclick="javascript:jQuery('#profit_check').fadeOut();"> <img src="images/cross.png" alt=""/> Close </button>
      </span></td>
  </tr>
</table>
