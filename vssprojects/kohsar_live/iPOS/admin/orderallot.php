<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO;
$shipmentid			=	$_GET['id'];
//$shipmentid			=	7;
$orders		=	$AdminDAO->getrows("`order`","*","fkshipmentid='$shipmentid'");
$stores		=	$AdminDAO->getrows("`store`","*",'storestatus = 1');
?>
<script language="javascript">
function orderallot()
{
	options	=	{	 
					url : 'orderallotaction.php?shipmentid=<?php echo $shipmentid;?>',
					type: 'POST',
					success: orderallotresponse
				}
	jQuery('#orderallotform').ajaxSubmit(options);
}
function orderallotresponse(text)
{
	hidediv('actionfrmdiv');
	adminnotice(text,0,5000);
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="actionfrmdiv" style="display: block;"> <br>
  <form id="orderallotform" name="orderallotform" style="width: 920px;" class="form">
    <fieldset>
      <legend>
      <?php
      $shipmentname	=	$AdminDAO->getcolumn('shipment',"shipmentname","pkshipmentid = '$shipmentid'");
    	echo "Allocations for Shipment >> $shipmentname";
		?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="orderallot();"> <img src="../images/tick.png" alt=""/>
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
        <button type="button" onclick="hidediv('actionfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </button></span> </div>
        <br /><br />
      <table cellpadding="2" cellspacing="0">
            <tr>
                <th height="10" valign="top">Order ID</th>
                <th height="10" valign="top">Barcode</th>
                <th height="10" valign="top">Description</th>
                <th height="10" valign="top">Client</th>
                <th height="10" valign="top">Needed</th>
          		<th height="10" align="center" valign="top" colspan="<?php echo sizeof($stores);?>">Locations</th>
        </tr>
       <?php
       	for($i=0;$i<sizeof($orders);$i++)
		{
			$orderid	=	$orders[$i][pkorderid];
			//$AdminDAO->dq =1;
			$purchases	=	$AdminDAO->getrows("orderpurchase","*", "fkorderid = '$orderid'");
			//print_r($purchases);
	   ?>
        <tr valign="top">
          <td height="10" ><?php echo $orders[$i]['pkorderid'];?></td>
          <td height="10" ><?php echo $orders[$i]['barcode'];?></td>
          <td height="10" ><?php echo $orders[$i]['itemdescription'];?></td>
          <td height="10" ><?php echo $orders[$i]['clientinfo'];?></td>
          <td height="10" align="right"><?php echo $orders[$i]['quantity'];?></td>
            <td>
            <table cellpadding="2" cellspacing="0">
            <tr>
            	<th>Purchased</th>
              <?php
                for($k=0;$k<sizeof($stores);$k++)
				{
					$storeid=	$stores[$k]['pkstoreid'];
					$storename=	$stores[$k]['storename'];
				?>
					
                	<th><?php echo "$storename";?></th>
              	<?php
				}//for
				?>
            </tr>
            	
		  	<?php
			for($j=0;$j<sizeof($purchases);$j++)
			{
				?>
				<tr>
                <td align="right">
                	<?php 
					echo $purchases[$j]['quantity'];
					?>
                    <input type="hidden" value="<?php echo $purchases[$j]['quantity'];?>" name="purchased[]" />
                </td>
                <?php
				for($k=0;$k<sizeof($stores);$k++)
				{
					$storeid	=	$stores[$k]['pkstoreid'];
					$pid		=	$purchases[$j]['pkorderpurchaseid'];
					$condition	=	" fkorderpurchaseid='$pid' AND fkstoreid = '$storeid' AND fkorderid = '$orderid' AND fkshipmentid= '$shipmentid' ";
					$quantity	=	$AdminDAO->getcolumn("orderallot","quantity",$condition);
					//select * from order where fkstoreid = $storeid and fkorderpurchaseid=$pid and fkorderid = $orderid
				?>
	               	<td><input align="right" size="10" name='<?php echo "box_".$pid."_".$storeid.'_'.$orderid;?>' value="<?php echo "$quantity";?>" /></td>
				<?php
				}//for
				?>
                  </tr>
          		 
                <?php
			}//for
			  ?> </table>
             </td>
             </tr>
            <tr>
             	<td height="10" colspan="<?php echo sizeof($stores) + 6;?>" bgcolor="#0099CC">&nbsp;</td>
         </tr>
		<?php
		}//for
		?>
         
      </table>
    </fieldset>
  </form>
</div>