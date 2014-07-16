<?php
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;
$id		=	$_GET['id'];
$id		=	ltrim($id,",");
$ids	=	explode(",",$id);

if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	//start selection
	$shiplist		=	$AdminDAO->getrows("shiplist","*","pkshiplistid IN ($id)");
	//end selection
	
	// countries
	$srccountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1");
	$countrysel			=	"<select name=\"country\" id=\"country\" style=\"width:100px;\"><option value=\"\">Select Country</option>";
	for($i=0;$i<sizeof($srccountries);$i++)
	{
		$countryname	=	$srccountries[$i]['countryname'];
		$countryid		=	$srccountries[$i]['pkcountryid'];
		$select		=	"";
		if($countryid == $selected_country)
		{
			$select = "selected=\"selected\"";
		}
		$countrysel2	.=	"<option value=\"$countryid\" $select>$countryname</option>";
	}
	$countries			=	$countrysel.$countrysel2."</select>";
	// end countries
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
	//start selection
	$shiplist		=	$AdminDAO->getrows("shiplist sl LEFT JOIN shiplistdetails sd ON (fkshiplistid=pkshiplistid) LEFT JOIN brand on fkbrandid=pkbrandid LEFT JOIN store ON (fkstoreid=pkstoreid)","FROM_UNIXTIME(datetime,'%d-%m-%Y') datetime,barcode,itemdescription,sl.quantity qty,IF(sum(sd.quantity) IS NULL,0,sum(sd.quantity)) mqty,lastpurchaseprice,sl.weight,deadline,brandname,storename","pkshiplistid IN ($id) GROUP BY pkshiplistid");
	//end selection
	
	// shipments
	$srcshipments		=	$AdminDAO->getrows("shipment","*","shipmentdeleted<>1 and fkstatusid=1 ORDER BY pkshipmentid DESC");
	$shipmentsel			=	"<select name=\"shipment\" id=\"shipment\" style=\"width:100px;\"><option value=\"\">Select Shipment</option>";
	for($i=0;$i<sizeof($srcshipments);$i++)
	{
		$shipmentname	=	$srcshipments[$i]['shipmentname'];
		$shipmentid		=	$srcshipments[$i]['pkshipmentid'];
		$select		=	"";
		if($shipmentid == $selected_shipment)
		{
			$select = "selected=\"selected\"";
		}
		$shipmentsel2	.=	"<option value=\"$shipmentid\" $select>$shipmentname</option>";
	}
	$shipments			=	$shipmentsel.$shipmentsel2."</select>";
	// end shipments
}//end edit
?>
<script language="javascript" type="text/javascript">
function movelist()
{
	//loading('System is saving data....');
	options	=	{	
					url : 'moveshiplist.php',
					type: 'POST',
					success: response
				}
	jQuery('#movelistform').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>
		adminnotice('Wish List has been moved.',0,5000);
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
		adminnotice('Order has been moved to selected shipment.',0,5000);
<?php }//end edit?>
		jQuery('#maindiv').load('manageshiplist.php');
	}
	else
	{
		adminnotice(text,0,5000);
		jQuery('#maindiv').load('manageshiplist.php');
	}
}
</script>
<div id="error" class="notice" style="display:none"></div>
<div id="movelistdiv" style="display: block;">
<form id="movelistform" style="width: 920px;" action="moveshiplist.php" class="form">
<fieldset>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>
<legend>
	Move Items
</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
<legend>Shipment Multiple Move</legend>
<div style="float:right">
<span class="buttons">
    <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
    </button>
     <a href="javascript:void(0);" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
    </a>
</span>
<?php }//end edit?>
</div>
<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition?>
    <table width="100%">
    <tr>
     <td>Country</td>
     <td colspan="2"><?php echo $countries;?></td>
    </tr>
    <tr>
        <th>Barcode</th>
        <th>Item</th>
    </tr>
    <?php
    for($i=0;$i<sizeof($shiplist);$i++)
    {
    ?>
    <tr>
        <td><?php echo $shiplist[$i]['barcode'];?></td>
        <td><?php echo $shiplist[$i]['itemdescription'];?></td>
    </tr>
    <?php
    }
    ?>
    <tr>
        <td colspan="2" align="center">
        <div class="buttons">
          <button type="button" class="positive" onclick="movelist();">
            <img src="../images/tick.png" alt=""/> 
            <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
            </button>
          <a href="javascript:void(0);" onclick="hidediv('movelistdiv');" class="negative">
            <img src="../images/cross.png" alt=""/>
            Cancel
            </a>
          </div>
        </td>				
    </tr>
    </table>
    <input type="hidden" name="id" value ="<?php echo $id;?>"/>
    </fieldset>	
    </form>
    <br />
    <br />
    </div>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
<table width="100%">
<tr>
 <td>Shipment</td>
 <td colspan="2"><?php echo $shipments;?></td>
</tr>
<tr>
    <th>Barcode</th>
    <th>Item</th>
    <th>Qty</th>
    <th>Moved Qty</th>
    <th>Move Qty</th>
    <th>Price</th>
    <th>Weight</th>
    <th>Deadline</th>
    <th>Order Date</th>
    <th>Brand</th>
    <th>Source</th>
    
</tr>
<?php
for($i=0;$i<sizeof($shiplist);$i++)
{
	$remqty	=	$shiplist[$i]['qty'] - $shiplist[$i]['mqty'];
?>
<tr>
    <td><?php echo $shiplist[$i]['barcode'];?></td>
    <td><?php echo $shiplist[$i]['itemdescription'];?></td>
    <td><?php echo $shiplist[$i]['qty'];?></td>
    <td><?php echo $shiplist[$i]['mqty'];?></td>
    <td><input name="moveqty[]" type="text" value ="<?php echo $remqty;?>"/></td>
    <td><?php echo $shiplist[$i]['lastpurchaseprice'];?></td>
    <td><?php echo $shiplist[$i]['weight'];?></td>
    <td><?php echo $shiplist[$i]['deadline'];?></td>
	<td><?php echo $shiplist[$i]['datetime'];?></td>
    <td><?php echo $shiplist[$i]['brandname'];?></td>
    <td><?php echo $shiplist[$i]['storename'];?></td>	
</tr>
<?php
}
?>
<tr>
    <td colspan="11" align="center">
    <div class="buttons">
      <button type="button" class="positive" onclick="movelist();">
        <img src="../images/tick.png" alt=""/> 
        <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
        </button>
      <a href="javascript:void(0);" onclick="hidediv('movelistdiv');" class="negative">
        <img src="../images/cross.png" alt=""/>
        Cancel
        </a>
      </div>
    </td>
</tr>
</table>
<input type="hidden" name="id" value ="<?php echo $id;?>"/>
</fieldset>	
</form>
<br />
<br />
</div>
<?php }//end edit ?>
