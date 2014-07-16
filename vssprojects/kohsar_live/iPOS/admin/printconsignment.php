<?php
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	include_once("../includes/security/adminsecurity.php");
	global $AdminDAO,$Component;
	$id			=	$_GET['id'];
	$conres1	=	$AdminDAO->getrows("consignment","consignmentname","pkconsignmentid = '$id'");
	$consignmentname	=	$conres1[0]['consignmentname'];
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />
	<body>
	<table class="simple">
		<tr>
			<td colspan="7" align="center" style="background-color:#CCC;font-size:16px;"><strong>Price Differences Report for <?php echo $consignmentname;?></strong></td>
		</tr>
		<tr>
			<th>Sr. #</th>
			<th>Barcode</th>
			<th>Item</th>
			<th>Cost Price</th>
			<th>Store Cost Price</th>
			<th>Suggested Sale Price</th>
			<th>Store Sale Price</th>
		</tr>
	<?php
	$conres	=	$AdminDAO->getrows("consignment,consignmentdetail,barcode","costprice,retailprice,fkbarcodeid,pkconsignmentdetailid,barcode,itemdescription","pkconsignmentid = '$id' AND pkconsignmentid=fkconsignmentid AND fkbarcodeid=pkbarcodeid");
	for($i=0;$i<sizeof($conres);$i++)
	{
		$count=1;
		$barcode		=	$conres[$i]['barcode'];
		$barcodeid		=	$conres[$i]['fkbarcodeid'];
		$condetid		=	$conres[$i]['pkconsignmentdetailid'];
		$item			=	$conres[$i]['itemdescription'];
		$costprice		=	$conres[$i]['costprice'];
		$retailprice	=	$conres[$i]['retailprice'];
		// fetching data from stocks
		$stockres		=	$AdminDAO->getrows("$dbname_detail.stock","pkstockid,MAX(costprice) costprice,MAX(retailprice) retailprice","fkbarcodeid='$barcodeid' AND fkconsignmentdetailid<>'$condetid'");
		$stockcostprice	=	$stockres[0]['costprice'];
		$stockid		=	$stockres[0]['pkstockid'];
		// fetching data from pricechange
		$priceres			=	$AdminDAO->getrows("$dbname_detail.pricechange","price","fkbarcodeid='$barcodeid'");
		if(sizeof($priceres)>0)
		{
			$storeretailprice	=	$priceres[0]['price'];
		}
		else
		{
			$storeretailprice	=	$stockres[0]['retailprice'];
		}
		//
		
		if($retailprice==$storeretailprice)
		{
			if($stockcostprice==$costprice)
			{
				$color2	=	"";
				continue;
			}
			else
			{
				$color2	=	"red";
				$color	=	"";
			}
		}
		else
		{
			$color	=	"red";
		}
	?>
		<tr>
			<td><?php echo $i+1;?></td>
			<td><?php echo $barcode;?></td>
			<td><?php echo $item;?></td>
			<td align="right" style="color:<?php echo $color2;?>;"><?php echo number_format($costprice,2);?></td>
			<td align="right" style="color:<?php echo $color2;?>;"><?php echo number_format($stockcostprice,2);?></td>
			<td align="right" style="color:<?php echo $color;?>;"><?php echo number_format($retailprice,2);?></td>
			<td align="right" style="color:<?php echo $color;?>;"><?php echo number_format($storeretailprice,2);?></td>
		</tr>
	<?php
	}
	?>
	</table>
	</body>
	</html>
	<script language="javascript">
		//window.print();
	</script>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link href="../includes/css/all.css" rel="stylesheet" type="text/css" />
    <script src="../includes/js/jquery.js"></script>
    <script src="../includes/js/jquery.form.js"></script>
    <script src="../includes/js/common.js"></script>
    <body style="background-color:#FFF;">
    <?php
    include_once("../includes/security/adminsecurity.php");
    global $AdminDAO,$Component;
    $id			=	$_REQUEST['id'];
    $id			=	trim($id,',');
    $ids		=	explode(',',$id);
    $arrcount	=	count($ids);
    $arrcount	=	$arrcount-1;
    $id			=	$ids[$arrcount];
    $source		=	$AdminDAO->getrows("consignment c,store,addressbook","concat(firstname,' ',lastname,'(',pkaddressbookid,')') empname,storename,storeaddress,storedb","fkstoreid = pkstoreid AND pkconsignmentid = '$id' AND pkaddressbookid = c.fkaddressbookid");
    $dest		=	$AdminDAO->getrows("consignment,store","storename,storeaddress","fkdeststoreid = pkstoreid AND pkconsignmentid = '$id'");
    $pkconsignmentdetailids	=	$AdminDAO->getrows("consignmentdetail","pkconsignmentdetailid,consignmentdetailstatus","fkconsignmentid='$id'");
    $source_storename	=	$source[0]['storename'];
    $empname			=	$source[0]['empname'];
    $storedb			=	$source[0]['storedb'];
    $source_storeaddress=	$source[0]['storeaddress'];
    $deststorename		=	$dest[0]['storename'];
    $deststoreaddress	=	$dest[0]['storeaddress'];
    
    ?>
    <table width="900" align="left" style=" margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;">
        <tr>
            <td colspan="4" align="center">
                <div style="padding:0px;font-size:17px;" align="center">
        <img src="../images/esajeelogo.jpg" width="150" height="50">
    <br />
    <span style="font-size:11px;font-family:'Comic Sans MS', cursive;">
    <b>Think globally shop locally</b>
    </span>
    </div>
    <div style="font-family:Verdana, Geneva, sans-serif; font-size:12px;" align="center">
    
    <b>Delivery Note</b><br /><?php echo date("d:m:Y h:i:s a");?>
    </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td>Source</td>
            <td>
            <?php
                echo $source_storename;
                echo $source_storeaddress;
            ?>
            </td>
            <td>Destination</td>
            <td>
            <?php echo $deststorename;?>
            <?php echo $deststoreaddress;?>
            </td>
        </tr>
    </table>
    <table width="900" align="left" style=" margin-left:0px; margin-right:auto;font-size:10px;font-family:Arial, Helvetica, sans-serif;">
      <tr>
        <th width="30">Sr. #</th>
        <th width="102">Barcode</th>
        <th width="127">Item</th>
        <th width="45">Quantity</th>
         <th width="45">Received</th>
          <th width="45">Status</th>
      </tr>
      <?php 
      $shipdata	=	$AdminDAO->getrows("consignmentdetail c,barcode b","barcode,itemdescription,c.quantity quantity","fkconsignmentid = '$id' AND fkbarcodeid = b.pkbarcodeid");
      
      for($i=0;$i<sizeof($shipdata);$i++)
      {
          $cdid 	=	$pkconsignmentdetailids[$i]['pkconsignmentdetailid'];
            $status	=	$pkconsignmentdetailids[$i]['consignmentdetailstatus'];
            $stock_details				=	$AdminDAO->getrows("barcode b,consignmentdetail cd",
                                                           " barcode,itemdescription,cd.*,cd.retailprice rp, cd.costprice cp",
                                                           "fkbarcodeid = pkbarcodeid AND  cd.pkconsignmentdetailid = '$cdid' AND fkconsignmentid = '$id'");
                    
            
            $receivedquantity	=	$stock_details[0]['receivedquantity'];
            $fkdamagetypeid		=	$stock_details[0]['fkdamagetypeid'];
            
            $receivetime		=	$stock_details[0]['receivetime'];
            $receivedby			=	$stock_details[0]['receivedby'];
            $receivedby			=	$AdminDAO->getrows("addressbook","concat(firstname,' ',lastname) receivedby","pkaddressbookid= '$receivedby'");
            $receivedby			=	$receivedby[0]['receivedby'];
    
          if($i%2==0)
          {
            $color	=	"#F8F8F8";
          }
          else
          {
            $color	=	"#ECECFF";
          }
         $qtytotal	+=		 $shipdata[$i]['quantity'];
      ?>
      <tr bgcolor="<?php echo $color; ?>">
        <td><?php echo $i+1;?></td>
        <td><?php echo $shipdata[$i]['barcode'];?></td>
        <td><?php echo $shipdata[$i]['itemdescription'];?></td>
        <td><?php echo $shipdata[$i]['quantity']; ?></td>
          <td><?php  echo $receivedquantity;?></td>
           <td>
           <?php 
                $receivetime	=	date('d/m/y h:i:s a',$receivetime);
                if($status==1)
                {
                    echo "$receivedby Received $receivedquantity on $receivetime";
                }
                else
                {
                    echo "Not Received";
                }
           ?>
           </td>
      </tr>
      <?php
      }//for
      ?>
      <tr>
        <td colspan="13"><hr/></td>
      </tr>
      <tr>
        <td><strong>Total Items: <?php echo $i;?></strong></td>
         
         <td colspan="2" align="center"></td>
         
         <td colspan="4"><?php echo $qtytotal;?></td>
        
      </tr>
      <tr>
        <td align="center" colspan="6">
            <strong><?php echo $empname;?></strong>
            
     ________________ <br /><br /><?php echo date("d:m:Y h:i:s a");?></td>
      </tr>
      
    </table>
    </body>
    </html>
    <script language="javascript">
        window.print();
        //window.close();
    </script>
<?php }//end edit?>