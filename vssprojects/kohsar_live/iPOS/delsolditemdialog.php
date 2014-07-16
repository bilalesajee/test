<?php
include("includes/security/adminsecurity.php");
global $AdminDAO;
?>
<div align="center" style="color:#F00"><h4>Please Select item and Provide the Reason to Delete this item from sale. </h4></div>
<form id="delitemform" name="delitemform" method="post" action="">
	    <table width="300" align="center" id="pos">
	      <tr>
	        <td>Item</td>
	        <td>
			<?php
			@session_start();
			$tempsaleid	=	$_SESSION['tempsaleid'];//changed $dbname_main to $dbname_detail on line 14 by ahsan 22/02/2012
			$salerows	=	$AdminDAO->getrows("$dbname_detail.saledetail",'pksaledetailid,fkstockid,saleprice, sum( quantity ) AS quantity, sum( saleprice * quantity ) AS subtotal, boxsize'," fksaleid='$tempsaleid' group by fkstockid,saleprice ORDER BY 	timestamp DESC");
			//print_r($salerows);
			?>
			<select name="delitems" id="delitems" size="4">
			<?php
			
 
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
	{
		/* $sql=" 
		 SELECT CONCAT(productname, ' (', IFNULL(GROUP_CONCAT(attributeoptionname ORDER BY attributeposition),'') ,') ', brn.brandname) PRODUCTNAME
			FROM productattribute pa
			RIGHT JOIN (
			product p, attribute a
			) ON ( pa.fkproductid = p.pkproductid
			AND pa.fkattributeid = a.pkattributeid ) , attributeoption ao, productinstance pi, stock s,barcode b, brand brn, barcodebrand bb
			WHERE pkproductid = pa.fkproductid
			AND pkattributeid = pa.fkattributeid
			AND pkproductattributeid = fkproductattributeid
			AND pkattributeid = ao.fkattributeid
			AND pkattributeoptionid = pi.fkattributeoptionid
			AND b.fkproductid = pkproductid
			AND pi.fkbarcodeid = b.pkbarcodeid
			AND s.fkbarcodeid=b.pkbarcodeid
			AND brn.pkbrandid	=	bb.fkbrandid
			AND bb.fkbarcodeid=b.pkbarcodeid
			AND s.pkstockid='$stockid' 
			";*///changed $dbname_main to $dbname_detail on line 56 by ahsan 22/02/2012
			$sql="select 
						itemdescription as PRODUCTNAME 
					FROM 
						barcode,
						$dbname_detail.stock 
					WHERE 
						pkbarcodeid=fkbarcodeid AND 
						pkstockid='$stockid'";
	
		$productrow		=	$AdminDAO->queryresult($sql);
		$productnameinv	=	$productrow[0]['PRODUCTNAME'];
	}
 	if($quantity>0)
	{
	//fkstockid='$dstockid' AND fksaleid='$saleid' AND boxsize='$boxsize' AND saleprice='$price'
	$delitems	=	"stockid=$stockid&saledetailid=$pksaledetailid&boxsize=$boxsize&quantity=$quantity&price=$saleprice";
  ?>
	
			<option value="<?php echo $delitems;?>"><?php echo $productnameinv;?> ( <?php echo $saleprice;?>) X <?php if($boxsize>0){echo $boxsize;}else{echo $quantity;}?> = <?php if($boxsize>0){echo $boxsize*$saleprice;}else{echo $quantity*$saleprice;}?>)</option>
		
	<?php	
	}//end of quantity
  }//end of stockid

	?>	
	</select>	
			</td>
          </tr>
	      <tr>
	        <td width="120">Reason</td>
	        <td width="180">
            <select name="delreason" id="delreason" onkeydown="javascript:if(event.keyCode==13) {delsaleitem();}">
           
                <!--<option value="0">Select Reason</option>-->
                <?php
                $sql="SELECT 
                            * 
                        FROM 
                            saleitemdeletereason
                        WHERE
                            reasonsatus='a' 
                    ORDER BY 
                        reasontitle ASC";
                $discountreasonarray		=	$AdminDAO->queryresult($sql);
                for($dis=0;$dis<count($discountreasonarray);$dis++)
                {
                ?>
                <option value="<?php echo $discountreasonarray[$dis]['pkreasonid'];?>"><?php echo $discountreasonarray[$dis]['reasontitle'];?></option>
              <?php
                }
               ?>
              </select>            </td>
          </tr>
	      <tr>
	        <td></td>
            <td align="center">
            	<input type="hidden" name="quantity" id="quantity" />
                <input type="hidden" name="boxsize2" id="boxsize2" />
                <input type="hidden" name="saleid" id="saleid" />
                <input type="hidden" name="stockid" id="stockid" />
                  <input type="hidden" name="price" id="price" />
                <input type="hidden" name="action" id="action" value="del"/>
               <span class="buttons" style="font-size:12px;">
                <button type="button" name="button" id="button" onclick="delsaleitem();">
                    <img src="images/tick.png" alt=""/> 
                   Save                </button>
                <button type="button" name="button2" id="button2" onclick="javascript:jQuery('#deleteitemreason').fadeOut();">
                    <img src="images/cross.png" alt=""/> 
                   Cancel                </button>
               </span>          
                             </td>
          </tr>
        </table>
		<script language="javascript">
			document.getElementById('delitems').focus();
		</script>
  </form>