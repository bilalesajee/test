<?php
include("../includes/security/adminsecurity.php");

global $AdminDAO,$Component,$qs;

$bc		=	$_REQUEST['barcode'];

$np		=	$_REQUEST['np'];

$bcid	=	$_REQUEST['bcid'];
$locl_check=0;
$pcid	=	intval($_REQUEST['pcid']);
 $changetimeu = time();
$fkaddressbookid	=	$_SESSION['addressbookid'];
if($np > 0)

{

	$barcodeid	=	$bcid;

	$newprice	=	$np;

	$pricechangeid	=	$pcid;

	$fields	=	array('price','fkbarcodeid','inserttime','pupdatetime');

	$values	=	array($np,$bcid,$changetimeu,$changetimeu);

	

	if($pcid ==0)

	{

		// taking previous value from stock

		$oldstockprice	=	$AdminDAO->getrows("$dbname_detail.stock","MAX(retailprice) retailprice","fkbarcodeid='$bcid'");

		$changeprice	=	$oldstockprice[0]['retailprice'];

		$pcid			=	$AdminDAO->insertrow("$dbname_detail.pricechange",$fields,$values);
	//	 $url_insert = ('https://kohsar.esajee.com/admin/accounts/local_prc_change.php?fkpricechangeid='.$pcid.'&fkaddressbookid='.$fkaddressbookid.'&oldprice='.$changeprice.'&fkbarcodeid='.$bcid.'&price='.$np.'&countername=1&qtype=2');
           //         $recDataE = ($url_insert);			

    $locl_check=1;
	}

	else

	{

		$pricechanges	=	$AdminDAO->getrows("$dbname_detail.pricechange","pkpricechangeid,countername,price","fkbarcodeid='$bcid'");

		$pricechangeid	=	$pricechanges[0]['pkpricechangeid'];

		$pricecounter	=	$pricechanges[0]['countername'];

		$changeprice	=	$pricechanges[0]['price'];

		// checking priviliged counter

		$priviliges		=	$AdminDAO->getrows("$dbname_detail.counter","previlliged","countername='$pricecounter'");	

		$priviliged		=	$priviliges[0]['previlliged'];

		if(!$priviliged)

		{		$fieldsu	=	array('price','fkbarcodeid','pupdatetime');

	             $valuesu	=	array($np,$bcid,$changetimeu);	

			$AdminDAO->updaterow("$dbname_detail.pricechange",$fieldsu,$valuesu," pkpricechangeid='$pcid' ");
			

		}

	}

	

	$fields	=	array('fkpricechangeid','fkaddressbookid','updatetime','oldprice');

	$values	=	array($pcid,$_SESSION['addressbookid'],time(),$changeprice);

	$pcidc	=	$AdminDAO->insertrow("$dbname_detail.pricechangehistory",$fields,$values);

	$msg	=	"Price changed successfully.";
	 if($locl_check==1){
	 $url_insert = ('https://kohsar.esajee.com/admin/accounts/local_prc_change.php?fkpricechangeid='.$pcid.'&fkaddressbookid='.$fkaddressbookid.'&oldprice='.$changeprice.'&fkbarcodeid='.$bcid.'&price='.$np.'&countername=1&qtype=2');
                    $recDataE = ($url_insert);			
	 }else{
		 
$url_insert = ('https://kohsar.esajee.com/admin/accounts/local_prc_change.php?fkpricechangeid='.$pcid.'&fkaddressbookid='.$fkaddressbookid.'&oldprice='.$changeprice.'&fkbarcodeid='.$bcid.'&price='.$np.'&countername=1&qtype=1');
                     $recDataE = ($url_insert);		
		 
		 }
}



$bc 		=	$AdminDAO->getrows('barcode','*',"barcode = '$bc'");

$barcode	=	$bc[0]['barcode'];

$id			=	$bc[0]['pkbarcodeid'];

$itemdescription		=	$bc[0]['itemdescription'];

$cprices	=	$AdminDAO->getrows("$dbname_detail.pricechange",'price,pkpricechangeid',"fkbarcodeid = '$id'");

$price		=	intval($cprices[0]['price']);

$pkpricechangeid		=	intval($cprices[0]['pkpricechangeid']);


$oldprices_history	=	$AdminDAO->getrows("$dbname_detail.pricechangehistory,$dbname_detail.pricechange",'oldprice',"pkpricechangeid=fkpricechangeid and fkbarcodeid = '$id' order by pkpricechangehistoryid desc limit 1");

$price_history		=	intval($oldprices_history[0]['oldprice']);




if($pkpricechangeid==0)

{

	$sprices	=	$AdminDAO->getrows("$dbname_detail.stock",'MAX(retailprice) price',"fkbarcodeid = '$id'");

	$price		=	$sprices[0]['price'];

	$pkpricechangeid=0;

}

//////////////////////////////////Changed 18-03-2014//////////////////////////////////////////////////////////////
$tp	=	$AdminDAO->getrows("$dbname_detail.stock",'(priceinrs) tp',"fkbarcodeid = '$id' order by pkstockid desc");
$tradeprice		=	$tp[0]['tp'];
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////


?>



<table>

<tr>

	<td>

    	<div id="error3" class="error" style="display:none; float:right;"><?php echo $msg;?></div>

    </td>

</tr>

<tr>

		<td>Item:</td>				

		<td valign="top">

        <div id="item"><?php echo $itemdescription;?></div>

        </td>

		<!--<td valign="top"><input name="nsupplier" id="nsupplier" type="text" value="Add New" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onFocus="if(this.value=='Add New')this.value='';"/></td>-->

	</tr>

    <tr>

   		<td>Trade Price:</td>				

		<td valign="top"><?php echo number_format($tradeprice,2);?></td>

    </tr>

	<tr>

		<td>Old Sale Price:</td>				

		<td valign="top"><?php echo number_format($price_history,2);?></td>

	</tr>
    <tr>

		<td>Current Price:</td>				

		<td valign="top"><?php echo number_format($price,2);?></td>

	</tr>

    <tr>

		<td>New Price: </td>				

		<td valign="top">

        <div id="error3" class="error" style="display:none; float:right;"></div>

       <input name="newprice" id="newprice" type="text" value="<?php echo $price;?>" onkeydown="javascript:if(event.keyCode==13) {addform(1); return false;}">

       <input name="barcodeid" id="barcodeid" type="hidden" value="<?php echo $id;?>">

       <input name="pkpricechangeid" id="pkpricechangeid" type="hidden" value="<?php echo $pkpricechangeid;?>" >

        </td>

		<!--<td valign="top"><input name="nsupplier" id="nsupplier" type="text" value="Add New" onkeydown="javascript:if(event.keyCode==13) {addform(); return false;}" onFocus="if(this.value=='Add New')this.value='';"/></td>-->

	</tr>

	<tr>

		<td colspan="3"  align="left">

		<div class="buttons">

            <button type="button" class="positive" onclick="addform();">

                <img src="../images/tick.png" alt=""/> 

                Save

            </button>

             <a href="javascript:void(0);" onclick="hidediv('brandiv');" class="negative">

                <img src="../images/cross.png" alt=""/>

                Cancel

            </a>

          </div>

        </td>				

</tr>

</table>