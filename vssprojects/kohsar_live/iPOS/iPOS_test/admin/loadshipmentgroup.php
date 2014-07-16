<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component,$qs;
$id					=	$_REQUEST['id'];
$type				=	$_REQUEST['type'];
$productname		=	$_REQUEST['productname'];
$barcode			=	$_REQUEST['barcode'];
/****************************PRODUCT DATA*****************************/

if($type=='addproduct')
{
		if($productname=='')
		{
			echo"Product name can not be left Blank.";
			exit;
		}
		if($productname)
		{
				$proid='-1';
				$unique = $AdminDAO->isunique('product', 'pkproductid', $proid, 'productname', $productname);
				if($unique=='1')
				{
						echo"Product with this name <b><u>$newpname</u></b> already exist. Please choose another name.";	
						exit;
				}
				else
				{
					// will add a product on the get_codeinstance.php
					$fields = array('productname');
					$values = array($productname);
					$id = $AdminDAO->insertrow("product",$fields,$values);
					echo 'productid_'.$id.'_barcode_'.$barcode;
					exit;
				}
		}
		
}
if($type=='shipgroup')//Makes the shipment group drop down
{
	$shipment_detail		=	$AdminDAO->getrows('shipment','exchangerate, shipmentcurrency'," pkshipmentid='$id'");
	foreach($shipment_detail as $ship)
	{
		?>
        	<input type="hidden" id="exchangerate" value="<?php echo $ship['exchangerate'];?>" />
			<input type="hidden" id="shipmentcurrency" value="<?php echo $ship['shipmentcurrency'];?>" />
          
        <?php	
	}
	
	$shipment_array			=	$AdminDAO->getrows('shipmentgroups sg, shipmentgroupjunc shj','sg.shipmentgroupname, sg.pkshipmentgroupid'," sg.pkshipmentgroupid=shj.fkshipmentgroupid AND shj.fkshipmentid='$id'");
/*	echo $shipmentgroup		=	$Component->makeComponent('d','shipmentgroup',$shipment_array,'pkshipmentgroupid','shipmentgroupname',1);*/
	$sh1	=	"<select name=brandsupplier><option>Select</option>";
	for($i=0;$i<sizeof($shipment_array); $i++)
	{
		$shipmentgroupid	=	$shipment_array[$i]['pkshipmentgroupid'];
		$shipmentgroupname		=	$shipment_array[$i]['shipmentgroupname'];
		$sh2.=	"<option value=$shipmentgroupid>$shipmentgroupname</option>";
	}
	$sh	=	$sh1.$sh2."</select>";
	echo  $sh;
}
else if($type=='brandsupplier') // makes the brand supplier dropdown
{
	if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
		$supplier_array			=	$AdminDAO->getrows('supplier s,addressbook ab',"CONCAT(s.companyname, ' (', ab.firstname, ab.lastname, ')') as suppliername, s.pksupplierid"," ab.pkaddressbookid=s.fkaddressbookid GROUP BY pksupplierid");
		//print_r($supplier_array);
		$s1	=	"<select name=brandsupplier class = \"eselect\" onchange=\"getinvoices(this.value);\">";
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
		$supplier_array			=	$AdminDAO->getrows('supplier s, brandsupplier bs,addressbook ab',"CONCAT(s.companyname, ' (', ab.firstname, ab.lastname, ')') as suppliername, s.pksupplierid"," s.pksupplierid=bs.fksupplierid AND bs.fkbrandid='$id' AND ab.pkaddressbookid=s.fkaddressbookid GROUP BY pksupplierid");
		//print_r($supplier_array);
		$s1	=	"<select name=brandsupplier class = \"eselect\">";
	}//end edit
	for($i=0;$i<sizeof($supplier_array); $i++)
	{
		$supplierid		=	$supplier_array[$i]['pksupplierid'];
		$suppliername	=	$supplier_array[$i]['suppliername'];
		$s2.=	"<option value=$supplierid>$suppliername</option>";
	}
	$s	=	$s1.$s2."</select>";
	echo  $s;//$suppliers				=	$Component->makeComponent('d','supplier',$supplier_array,'pksupplierid','suppliername',1);
}
?>
