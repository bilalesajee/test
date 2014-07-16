<?php
session_start();
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$shipmentid = $_REQUEST['shipmentid'];
session_start();
$file=	"../invoiceimports/".$_SESSION['orderinvoicefile'];
$_SESSION['orderinvoicefile']	=	'';
$blankrecords=0;
$row = 1;
?>
<fieldset>
<legend>Import Inovice Records</legend>
<form id="orderimportactionform" name="orderimportactionform">
<table>
	<tr><td>Select Product:</td><td><?php $AdminDAO->dropdown("productid","product","pkproductid","productname");?></td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
</table>
<table width="100%">
<tr>
<?php
$requiredfields	=	array('barcode','item','quantity','weight','brandname','suppliercompanyname','countryname','purchaseprice','batch','expiry','locationstorename','deadline','boxno');
if (($handle = fopen($file, "r")) !== FALSE)
{
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
	{
		$num = count($data);
		if($row==1)
		{
			foreach($data as $col)
			{
				$lowercol	=	strtolower($col);
				$collabel	=	$lowercol;
				$cols[]		=	$lowercol;
			?>
			<th title="<?php echo ucfirst($collabel);?>">
			<?php
            if(in_array($lowercol,$requiredfields))
			{
				echo "<font style='color:red'>*</font>";
			}
				//echo $collabel;			
				echo substr(ucfirst($collabel),0,7);?></th>
			<?php
			}//foreach
			?>
			</tr>
			<?php
		}
		else
		{
		?>
        	<tr>
        <?php
			$smallfields	=	array('weight','purchaseprice','batch','quantity','pricelimit','agreedprice','boxno');
			for($d=0;$d<sizeof($data); $d++)
			{
				
				if(in_array($cols[$d],$smallfields))
				{
					$inputsize	=	"3";
				}
				else
				{
					$inputsize	=	"8";
				}
				if($data[$d]=="" &&  (in_array($cols[$d],$requiredfields)))
				{
					$blankrecords	=	1;
					$bgcolor	='style="background-color:#FF6D6F"';
				}
				else
				{
					$bgcolor	='';
				}
				//if barcode already exists in the system, then fetch its description and brand
				if(($cols[$d]=='barcode') && (trim($data[$d])!=''))
				{
					$bcdetails1	=	$AdminDAO->getrows("barcode","itemdescription", " barcode= '$data[$d]'");
					$bcdetails2	=	$AdminDAO->getrows("barcode,barcodebrand","fkbrandid", " barcode= '$data[$d]' AND pkbarcodeid = fkbarcodeid ");
					$itemdescription	=	$bcdetails1[0]['itemdescription'];
					$fkbrandid			=	$bcdetails2[0]['fkbrandid'];
					$data[$d+1]			=	$itemdescription;
					$branddata			=	$AdminDAO->getrows("brand","brandname", "pkbrandid= '$fkbrandid'");
					$brandname			=	$branddata[0]['brandname'];					
					$data[$d+5]			=	$brandname;
				}
				?>
				<td>
                <input type="text" <?php echo $bgcolor;?>  size="<?php echo $inputsize;?>" id="<?php echo $cols[$d].'_'.$row;?>" name="<?php echo $cols[$d].'[]';?>"  title="<?php echo $data[$d];?>" value="<?php echo $data[$d];?>" readonly="readonly" />
                </td>
			<?php
			}//foreach
			?>
			</tr>
           <?php
		}//else
		$row++;
	}//while
	fclose($handle);
}
if($blankrecords==0)
{
?>

<tr>
	<td colspan="20">&nbsp;</td>
</tr>
<tr>
<td colspan="20">
<div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="importform();"> <img src="../images/tick.png" alt=""/>
        Import Invoice
        </button>
        <a href="javascript:void(0);" onclick="hidediv('actionfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span>
      </div></td>
</tr>
<?php
}
?>
</table>
<input type="hidden" value="<?php echo "$shipmentid";?>" name="shipmentid" id="shipmentid" />
</form>
</fieldset>