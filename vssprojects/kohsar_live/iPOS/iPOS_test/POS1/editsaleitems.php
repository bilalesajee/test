<?php
include("includes/security/adminsecurity.php");
global $AdminDAO;
@session_start();
$tempsaleid	=	$_SESSION['tempsaleid'];
?>
<form id="edititemform" name="edititemform" method="post" action="">
        <table width="300" align="left" class="epos">
        	<tr>
            	<th>Sr.#</th>
            	<th width="50%">Item</th>
            	<th>Quantity</th>
            	<th>Price</th>
            	<th>Total</th>
            </tr>
            <?php //changed $dbname_main to $dbname_detail on line 17 by ahsan 22/02/2012
			$salerows	=	$AdminDAO->getrows("$dbname_detail.saledetail",'pksaledetailid,fkstockid,saleprice, sum( quantity ) AS quantity, sum( saleprice * quantity ) AS subtotal, boxsize'," fksaleid='$tempsaleid' group by fkstockid,saleprice ORDER BY 	timestamp DESC");
			$items=0;
			for($i=0;$i<count($salerows);$i++)
			{
				$pksaledetailid	=	$salerows[$i]['pksaledetailid'];
				$saleprice		=	$salerows[$i]['saleprice'];
				$subtotal 		=	$salerows[$i]['subtotal'];
				$quantity		=	$salerows[$i]['quantity'];
				$boxsize		=	$salerows[$i]['boxsize'];
				$stockid		=	$salerows[$i]['fkstockid'];
				if($stockid!='')
				{//changed $dbname_main to $dbname_detail on line 33 by ahsan 22/02/2012
						$sql="select 
									barcode, pkbarcodeid, itemdescription,shortdescription
								FROM 
									barcode,
									$dbname_detail.stock 
								WHERE 
									pkbarcodeid=fkbarcodeid AND 
									pkstockid='$stockid'";
				
					$productrow		=	$AdminDAO->queryresult($sql);
					$productnameinv	=	$productrow[0]['shortdescription'];
					$productbarcode	=	$productrow[0]['barcode'];
					$productbarcodeid	=	$productrow[0]['pkbarcodeid'];
					if($productnameinv=='')
					{
						$productnameinv	=	$productrow[0]['itemdescription'];
					}
				}
			?>
        	<tr>
            	<td><?php echo $i+1;?></td>
                <td><?php echo $productnameinv;?></td>
                <td><input type="text" size="6" onfocus="this.select()" name="quantity[]" id="quantity_<?php echo $i+1;?>" value="<?php echo $quantity;?>" style="text-align:right" onkeydown="func1(event,this.id)" onchange="calctotal(this.id)" title="<?php echo "Original Quantity = ".$quantity;?>" /></td>
                <td><input type="text" size="6" onfocus="this.select()" name="saleprice[]" id="saleprice_<?php echo $i+1;?>" value="<?php echo $saleprice;?>" style="text-align:right" onkeydown="func1(event,this.id)" onchange="calctotal(this.id)" title="<?php echo "Original Price = ".$saleprice;?>" /><input type="hidden" name="saledetailid[]" value="<?php echo $pksaledetailid; ?>" />
                <input type="hidden" name="originalprice[]" id="originalprice" value="<?php echo $saleprice;?>" /><input type="hidden" name="originalquantity[]" id="originalquantity" value="<?php echo $quantity; ?>" /><input type="hidden" name="fkstockid[]" id="fkstockid" value="<?php echo $stockid; ?>" /><input type="hidden" name="fkbarcodeid[]" id="fkbarcodeid" value="<?php echo $productbarcodeid; ?>" /><input type="hidden" name="fkbarcode[]" id="fkbarcode" value="<?php echo $productbarcode; ?>" /></td>
                <td align="right"><div id="subtotal_<?php echo $i+1;?>"><?php echo $subtotal;?></div></td>
            </tr>
            <?php
			}
			?>
            <tr>
                <td colspan="5" align="center">          	
                    <span class="buttons" style="font-size:12px;">
                    <button type="button" name="button" id="button" onclick="editsaleitem();">
                        <img src="images/tick.png" alt=""/> 
                       Save                </button>
                    <button type="button" name="button2" id="button2x" onclick="javascript:jQuery('#editsaleitems').fadeOut();">
                        <img src="images/cross.png" alt=""/> 
                       Cancel                </button>
                    </span>   
                </td>
			</tr>
            <input type="hidden" name="total" id="total" value="<?php echo $i+1;?>"  />
            <input type="hidden" name="saleid" id="saleid" value="<?php echo $tempsaleid;?>"  />
        </table>
		<script language="javascript">
		function editsaleitem()
		{ 
			options	={	
			url : 'editsaleitemsact.php',
			type: 'POST',
			success: edititemresponse
		}
		jQuery('#edititemform').ajaxSubmit(options,function(){return false});
		}
		function edititemresponse(text)
		{
			
			if(text!='')
			{
				notice(text,'',5000);	
			}
			else
				loadsection('main-content','sale.php');	
		}
		function func1(e,id)
		{
			if(e.keyCode==13) 
			{
				editsaleitem();
				return false;
			}
			if (e.keyCode == 40)
			{
				var newid	=	id.split("_");
				id2			=	parseInt(newid[1])+1;
				//oldbox		=	newid[0]+'_'+newid[1];
				newbox		=	newid[0]+'_'+id2;
				//olddiv		=	'qty_'+newid[1];
				//newdiv		=	'qty_'+id2;
				total		=	document.getElementById('total').value;
				if(id2<total)
				{
					/*document.getElementById(oldbox).type='hidden';
					document.getElementById(olddiv).style.display	=	'block';
					document.getElementById(newdiv).style.display	=	'none';
					document.getElementById(newbox).type='text';*/
					document.getElementById(newbox).focus();
				}
				else
					return false;
			}
			else if(e.keyCode == 38)
			{
				var newid	=	id.split("_");
				id2			=	parseInt(newid[1])-1;
				newbox		=	newid[0]+'_'+id2;
				if(id2>0)
				{
					document.getElementById(newbox).focus();
				}
				else
					return false;
			}
		}
		function calctotal(id)
		{ 
			var newid	=	id.split("_");
			quantity	=	document.getElementById('quantity_'+newid[1]).value;
			price		=	document.getElementById('saleprice_'+newid[1]).value;
			subtotal	=	price*quantity;
			document.getElementById('subtotal_'+newid[1]).innerHTML	=	'';
			document.getElementById('subtotal_'+newid[1]).innerHTML	=	subtotal;
		}
		//alert(document.getElementById('total').value);
		if(document.getElementById('total').value>1)
		{
			document.getElementById('quantity_1').focus();
			//document.getElementById('qty_1').style.display	=	'none';
		}
		else
		{
			document.getElementById('editsaleitems').style.display='none';
		}
		/*var total	=	document.getElementById('total').value;
		for(j=2;j<=total;j++)
		{
			alert('here');
			document.getElementById('quantity_'+j).type	=	'hidden';
		}*/
		</script>
  </form>