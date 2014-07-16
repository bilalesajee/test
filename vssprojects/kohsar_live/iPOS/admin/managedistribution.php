<?php 	include_once("../includes/security/adminsecurity.php");
		global $AdminDAO,$Component;
		error_reporting(7);
$id			=	$_GET['id'];
$qstring	=	$_SESSION['qstring'];
$param		=		$_REQUEST['param'];
if($id!='-1'){
	$shiplist = $AdminDAO->getrows("shiplist inner join purchase on fkshiplistid=pkshiplistid","pkpurchaseid,pkshiplistid,datetime,shiplist.fkshipmentid,trim(barcode) barcode,boxbarcode,trim(itemdescription) itemdescription,shiplist.quantity,lastpurchaseprice,lastsaleprice,fkcountryid,fkstoreid,shiplist.fkaddressbookid,shiplist.fkcurrencyid,fkcountrylist,shiplist.weight,deadline,fkstatusid,fkbrandid,fkagentid,description,defaultimage,clientinfo",
																																															   " purchase.fkshipmentid='$id' ");//group by rtrim(ltrim(itemdescription)) ,rtrim(ltrim(barcode))
}
$stor			=	$AdminDAO->getrows("store","storecode,pkstoreid,storename as name","storedeleted<>1 AND storestatus=1");
function get_requested($barcode,$item,$fkstoreid){
	global $AdminDAO;
	//$shiplist = $AdminDAO->getrows("shiplist inner join purchase on fkshiplistid=pkshiplistid",								   "sum(shiplist.quantity) qty",								   " shiplist.itemdescription like '".$item."' and shiplist.barcode like '".$barcode."' and fkstoreid='".$fkstoreid."'","",1);			
	$query=" SELECT sum(shiplist.quantity) qty FROM shiplist inner join purchase on fkshiplistid=pkshiplistid  
	WHERE  shiplist.itemdescription like '".$item."' and shiplist.barcode like '".$barcode."' and fkstoreid='".$fkstoreid."'";	
	$shiplist = $AdminDAO->queryresult($query);
	 return $shiplist[0]['qty'];
	//print_r($shiplist);
	//exit;
}
?>
<div id="loaditemscript"></div>
<div id="loading" style="display:none;"></div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;"> <br>
  <form id="distform" name="distform" style="width: 920px;" action="insertshiplist.php?id=-1" class="form"  enctype="multipart/form-data">
    <fieldset>
      <legend>
      <?php
    { echo "Allot Items";}	
    ?>
      </legend>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="addshiplist(-1);"> <img src="../images/tick.png" alt=""/>
        <?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
        <table width="129%">
            <tr>
                <td height="12" valign="top">
                	<div class="topimage2" style="height:6px;"></div>
                    <table  cellpadding="2" cellspacing="0" border="0" width="100%">
                          <tbody>
                        
                                <tr>
	                                <th width="10%">&nbsp;</th>
    	                              <th>Item</th>
        	                          <th>Barcode</th>
            	                      <th>Units Purchased </th>
                	                  <?php foreach($stor as $st){?>
                    	                <th width="20%"><?php echo $st['name'];?></th>
                        	            <?php }?>                                  
                                  </tr>                   
								<?php  $numb=0;
                                
                                if(count($shiplist)>0){	
                                    foreach($shiplist as $slist)
                                    {
                                        $pkpurchaseid 		= 	$slist['pkpurchaseid'];
                                        $pkbarcodeid 		= 	$slist['pkbarcodeid'];
                                        $barcode 			= 	$slist['barcode'];
                                        $itemdescription 	= 	$slist['itemdescription'];
                                        $quantity 			= 	$slist['quantity'];
                                        $weight				=	$slist['weight'];
                                        $selected_brand		=	$slist['fkbrandid'];
                                        $suppliers			=	$AdminDAO->getrows("shiplistsupplier","*","fkshiplistid='$id'");
                                        foreach($suppliers as $supplier){
                                            $selected_suppliers[]	=	$supplier['fksupplierid'];
                                        }
                                        $selected_agent		=	$slist['fkagentid'];
                                        $selected_country	=	$slist['fkcountryid'];
                                        $selected_store		=	$slist['fkstoreid'];
                                        $selected_emp		=	$slist['fkaddressbookid'];
                                        $currencyid			=	$slist['pkcurrencyid'];
                                        $currencysymbol		=	$slist['currencysymbol'];
                                        $lastpurchaseprice 	= 	$slist['lastpurchaseprice'];
                                        $lastsaleprice 		= 	$slist['lastsaleprice'];
                                        $description		=	$slist['description'];
                                        $clientinfo 		= 	$slist['clientinfo'];
                                        $defaultimage	 	= 	$slist['defaultimage'];		
                                        
                                        $deadline			=	implode("-",array_reverse(explode("-",$slist['deadline'])));
                                        ?>        
                                        
                            <tr class="even">        
                                <td valign="top"><input type="checkbox" value="<?php echo $pkpurchaseid; ?>" name="purchaseid[<?php echo $numb;?>]" id="purchaseid"/><?php echo "[".($numb+1)."]";?></td>
                                <td valign="top"><input name="itemdescription[]" id="itemdescription" class="text" value="<?php echo $itemdescription;?>"  style='width:200px;' type="text" readonly="readonly"/></td>
                                <td valign="top"><input name="barcode[]" id="barcode" class="text"  value="<?php echo $barcode; ?>" type="text" readonly="readonly"/></td>
                                <td valign="top"><input name="quantity[]" id="quantity" class="text" size="2" value="<?php echo $quantity; ?>" type="text" onkeypress="return isNumberKey(event);" readonly="readonly" /></td>
                                <?php foreach($stor as $st){?>
                                    
                                <td valign="top">Requested=<?php $req			=	"0"; 
                                    //$dist			=	$AdminDAO->getrows(" distribute "," pkdistributeid, quantity ","fkpurchaseid='$pkpurchaseid' and fkstoreid='".$st['pkstoreid']."'");
									$qury=" SELECT  pkdistributeid, quantity FROM distribute where fkpurchaseid='$pkpurchaseid' and fkstoreid='".$st['pkstoreid']."'";
									$dist			=	$AdminDAO->queryresult($qury);
                                    $ex_qty			=	$dist[0]['quantity'];	
									$req			=	get_requested($barcode,$itemdescription,$st['pkstoreid'])	;								
                                    echo $req		=	(($req>0)?$req:"0");
                                    if($ex_qty>0){
                                        $req		=	$ex_qty;
                                    }                                   
                                    ?>
                                    <input name="store_<?php echo $st['pkstoreid'];?>[]" id="store" size="5" style="width:20px;" value="<?php echo $req;?>" type="text"  onkeypress="return isNumberKey(event);" />
                                </td>
                                <?php  }?>				                 
                            </tr>
                                        
                        <?php $numb++;} 
                        }else{
                            ?><tr>
                                <td colspan="5" align="center"><strong>No Item Found</strong></td>
                            </tr> 		
                        <?php 
                            }	
                        ?>
                                    
                            <tr>
                              <td colspan="12">
                              </td>
                            </tr>
                            <tr>
                              <td colspan="12" align="center"><div class="buttons">
                                  <button type="button" class="positive" onclick="addshiplist(-1);"> <img src="../images/tick.png" alt=""/>
                                  <?php {echo "Save";} //if($id=='-1') else {echo "Update";} ?>
                                  </button>
                                  <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
                            </tr>
                          </tbody>
                    </table>
                </td>
            </tr>
        </table>
      <input type="hidden" name="id" value ="<?php echo $id;?>"/>
      <input type="hidden" name="lastpprice" id="lastpprice" value ="<?php echo $lastpurchaseprice;?>"/>
      <input type="hidden" name="addressbookid" id="addressbookid" value="<?php echo $selected_emp; ?>" />
      <input type="hidden" name="currencyid" id="currencyid" value="<?php echo $currencyid; ?>" />
    </fieldset>
  </form>
  <div id="similaritemsdiv"></div>
</div>
<?php 
function getallbrands($brandid='0'){
	global $AdminDAO,$brandcondition	;

	$sql="SELECT brandname,pkbrandid,fkparentbrandid from brand where branddeleted<>1 $brandcondition and fkparentbrandid='".$brandid."' order by pkbrandid";
	$brands=$AdminDAO->queryresult($sql);
	
	if(count($brands)>0){		
		if($brandid=='0')
			$brands1		=	"<select name='brand' id='brand' style='width:65px;'>
			<option value=''>Brand</option>";
		for($i=0;$i<sizeof($brands);$i++){
			$brandname	=	$brands[$i]['brandname'];
			$brandidin	=	$brands[$i]['pkbrandid'];
			$fkparentbrandid	=	$brands[$i]['fkparentbrandid'];
			$selected=(($brandid == $selected_brand)?' selected=selected ':''); 
			if($fkparentbrandid==0)
				$brands1	.=	"<option value='".$brandidin."' $selected  style='font-weight: bold;'>$brandname</strong>";
			else
				$brands1	.=	"<option value='".$brandidin."' $selected  style='font-weight: italic;'>&nbsp;&nbsp;&nbsp;$brandname</option>";
			$brands1	.=	getallbrands($brandidin);
		}
	}else{		
		return '';			
	}
	return $brands1;
}?>

<script type="text/javascript">
function addshiplist(id){
	false;
	options	=	{	
					url : 'insertdistribution.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#distform').ajaxSubmit(options);
}
function response(text){
	if(text==1)	{
		adminnotice('Distributions has been saved.',0,5000);
		jQuery('#distform').slideUp();
	}else{
		adminnotice(text,0,5000);
	}
}
</script>