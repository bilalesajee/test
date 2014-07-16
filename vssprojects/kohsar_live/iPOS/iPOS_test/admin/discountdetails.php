<?php 

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$discountid	=	$_GET['id'];
$query="SELECT * from discount where pkdiscountid='$discountid'";
$discountarray	=	$AdminDAO->queryresult($query);
$discounttype	=	$discountarray[0]['fkdiscounttypeid'];
if($discounttype!=3)
{
	$query=" select d.*,
					CONCAT(sh.shipmentdate,' EXP-  (',s.expiry,')') as expiry 	 
			FROM 
					discountstock d,stock s,shipment sh
			WHERE
					d.fkdiscountid='$discountid' AND
					d.fkstockid=s.pkstockid AND
					sh.pkshipmentid=s.fkshipmentid AND 
					d.type='b'";
	$basediscountstock	=	$AdminDAO->queryresult($query);
	$stockid		=	$basediscountstock[0]['fkstockid'];
	$query=" select d.*,
					CONCAT(sh.shipmentdate,' EXP-  (',s.expiry,')') as expiry 	 
			FROM 
					discountstock d,stock s,shipment sh
			WHERE
					d.fkdiscountid='$discountid' AND
					d.fkstockid=s.pkstockid AND
					sh.pkshipmentid=s.fkshipmentid AND 
					d.type='d'";
	$discountstock	=	$AdminDAO->queryresult($query);
	
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
				AND s.fkbarcodeid=b.pkbarcodeid ";
}
	if($discounttype=='1')//quanty on quantity=qq : (discountdetailsqq)
	{
		$query="select * from discountdetailsqq where fkdiscountid='$discountid'";
		$discountdetailarray	=	$AdminDAO->queryresult($query);
			$basequantity	=	$discountdetailarray[0]['basequantity'];
			$discountquantity=	$discountdetailarray[0]['discountquantity'];
			$pkddqqid		=	$discountdetailarray[0]['pkddqqid'];
			 $sql.=" 
				AND s.pkstockid='$stockid' 
			
			";
			$productarray	=	$AdminDAO->queryresult($sql);
	
?>
    	 <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<table width="558" border="0">
              <tr>
                <td colspan="2" class="bold">Quantity on Quantity</td>
              </tr>
              <tr>
                <td width="212">Product Name</td>
                <td width="106"><?php echo $productarray[0][PRODUCTNAME];?></td>
              </tr>
	
           
              <tr>
                <td>Base Quantity</td>
                <td><?php echo $basequantity;?></td>
              </tr>
              <tr>
                <td>Discount Quantity</td>
                <td><?php echo $discountquantity;?></td>
              </tr>
              <?php
			  for($s=0;$s<count($basediscountstock);$s++)
			  {
			  ?>
              <tr>
                <td>Base Stock</td>
                <td><?php echo $basediscountstock[$s][expiry];?></td>
              </tr>
           <?php
			  }
		  ?>
          <tr>
                <td colspan="2">&nbsp;</td>
          </tr>
          <?php
			  for($s=0;$s<count($discountstock);$s++)
			  {
			  ?>
              
              <tr>
                <td>Discount Stock</td>
                <td><?php echo $discountstock[$s][expiry];?></td>
              </tr>
           <?php
			  }
		   ?>
			
</table>
	<?php	

}
elseif($discounttype=='2')//amount on quantity=aq 
{
		$query="select * from discountdetailsaq where fkdiscountid='$discountid'";
		$discountdetailarray	=	$AdminDAO->queryresult($query);
		$type 				=	$discountdetailarray[0]['type'];
		$basequantity 		=	$discountdetailarray[0]['basequantity'];
		$amount 			=	$discountdetailarray[0]['amount'];
		 $sql.=" 
			AND s.pkstockid='$stockid' 
			
			";
			$productarray	=	$AdminDAO->queryresult($sql);
	?>
    	 <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<table width="558" border="0">
              <tr>
                <td colspan="2" class="bold">Amount on Quantity</td>
              </tr>
              <tr>
                <td width="212">Product Name</td>
                <td width="106"><?php echo $productarray[0][PRODUCTNAME];?></td>
              </tr>
	
           
              <tr>
                <td>Base Quantity</td>
                <td><?php echo $basequantity;?></td>
              </tr>
              <tr>
                <td>Discount Amount</td>
                <td><?php echo $amount; if($type=='p'){echo"%";}else{echo "Rs.";}?></td>
              </tr>
             <?php
              for($s=0;$s<count($basediscountstock);$s++)
			  {
			  ?>
              <tr>
                <td>Base Stock</td>
                <td><?php echo $basediscountstock[$s][expiry];?></td>
              </tr>
           <?php
			  }
		  ?>
           
			
</table>
<?php
}
elseif($discounttype=='4')// product on product=pp
{
	$query="select * from discountdetailspp where fkdiscountid='$discountid'";
	$discountdetailarray	=	$AdminDAO->queryresult($query);
	$discountquantity	=	$discountdetailarray[0]['discountquantity'];
	$basequantity 		=	$discountdetailarray[0]['basequantity'];
	$pkddppid			=	$discountdetailarray[0]['pkddppid'];
	
		 $sql.=" 
			AND s.pkstockid='$stockid' 
			";
			$productarray	=	$AdminDAO->queryresult($sql);
		
	?>
    	 <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<table width="558" border="0">
              <tr>
                <td colspan="2" class="bold">Product on Product</td>
              </tr>
              <tr>
                <td width="212">Product Name</td>
                <td width="106"><?php echo $productarray[0][PRODUCTNAME];?></td>
              </tr>
	
           
              <tr>
                <td>Base Quantity</td>
                <td><?php echo $basequantity;?></td>
              </tr>
               <?php
			   	$stockid	=	$discountstock[0]['fkstockid'];
				
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
					$discountproductarray	=	$AdminDAO->queryresult($sql);
			  
			  for($s=0;$s<count($basediscountstock);$s++)
			  {
			  ?>
              <tr>
                <td>Base Stock</td>
                <td><?php echo $basediscountstock[$s][expiry];?></td>
              </tr>
			   <?php
                  }
              ?>
               
               <tr>
                <td>Discounted Product Name</td>
                <td><?php echo $discountproductarray[0][PRODUCTNAME];?></td>
              </tr>
              <tr>
                <td>Discount Quantity</td>
                <td><?php echo $discountquantity;?></td>
              </tr>
              
             <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <?php
               for($s=0;$s<count($discountstock);$s++)
			  {
			  ?>
              
              <tr>
                <td>Discount Stock</td>
                <td><?php echo $discountstock[$s][expiry];?></td>
              </tr>
			   <?php
                  }
              ?>
           
			
</table>
<?php
}
elseif($discounttype=='3')//amount on amount=aa
{
	$query="select * from discountdetailsaa where fkdiscountid='$discountid'";
	$discountdetailarray	=	$AdminDAO->queryresult($query);
	$amount					=	$discountdetailarray[0]['amount'];
	$type					=	$discountdetailarray[0]['type'];
	$amountoff				=	$discountdetailarray[0]['amountoff'];
?>
    	 <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
<table width="558" border="0">
              <tr>
                <td colspan="2" class="bold">Amount on Amount</td>
              </tr>
              
	
           
              <tr>
                <td>Target Amount</td>
                <td><?php echo $amount;?></td>
              </tr>
              
              <tr>
                <td>Discount Amount</td>
                <td><?php echo $amountoff;if($type=='p'){echo"%";}else{echo "Rs.";}?></td>
              </tr>
           
			
</table>
	<?php	
}
?>
