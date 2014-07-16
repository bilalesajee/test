<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
$param	=	$_REQUEST['param'];
if($param=='undefined' || $param=='')
{
	$id		=	$_REQUEST['id'];
	$param	=	$id;
}
else
{
	$id			=	$_REQUEST['param'];
	$detailid	=	$_REQUEST['id'];
}
$qstring			=	$_SESSION['qstring'];
$query	=	"SELECT
				pkconsignmentid,
				fkstoreid,
				fkdeststoreid,
				consignmentname,
				storename,
				fkstatusid,
				(select storename from store where pkstoreid=fkdeststoreid) as deststorename
			FROM
				consignment,store,statuses
			WHERE
				fkstoreid	=	pkstoreid AND
				fkstatusid	=	pkstatusid AND
				consignmentdeleted<>1 AND
				 pkconsignmentid='$id'";
$res	=	$AdminDAO->queryresult($query);
$consignmentname	=	$res[0]['consignmentname'];
$storename			=	$res[0]['storename'];
$deststorename		=	$res[0]['deststorename'];
$fkstoreid			=	$res[0]['fkstoreid'];
$fkstatusid			=	$res[0]['fkstatusid'];

/**** grid values */
//selecting source store info
$sourcestore		=	$AdminDAO->getrows("store","storedb","pkstoreid='$fkstoreid'");
$storedb			=	$sourcestore[0]['storedb'];
$dest 	= 	'addconsignmentitems.php';
$div	=	'subsection';
$form 	= 	"frmconsignmentitems";	
define(IMGPATH,'../images/');
if($detailid)
{
	$res				=	$AdminDAO->getrows("consignmentdetail,barcode","barcode,itemdescription,quantity,priceinrs,fkstockid","fkbarcodeid=pkbarcodeid AND pkconsignmentdetailid='$detailid'");
	$barcode			=	$res[0]['barcode'];
	$itemdescription	=	$res[0]['itemdescription'];
	$quantity			=	$res[0]['quantity'];
	$price				=	$res[0]['priceinrs'];
	$stockid			=	$res[0]['fkstockid'];
}

if($stockid=='')
{
	$stockid='undefined';
}
//echo $stockid."is the stockid";
//echo "the result is $quantity and $barcode and $itemdescription and $price";
$query 	= 	"SELECT
				pkconsignmentdetailid,
				barcode,
				itemdescription,
				cd.quantity,
				(select CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=cd.fkaddressbookid) as addedby
			FROM
				consignmentdetail cd,barcode
			WHERE
				cd.fkbarcodeid=pkbarcodeid AND
				fkconsignmentid	=	'$id'
			";
$navbtn	=	"<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addconsignmentitems.php','subsection','subsection','$param') \" title=\"Add Consignment\"><span class=\"editrecord\">&nbsp;</span></a>";
$labels	=	array('ID','Barcode','Item','Quantity','Added By');
$fields	=	array('pkconsignmentdetailid','barcode','itemdescription','quantity','addedby');
/**** end grid section */
//$consignmentname	=	$res['consignmentname'];
// selecting suppliers
if($detailid)
{
echo "<script language=\"javascript\">getitemdetails('$barcode','0','$fkstoreid','$quantity','$id','$stockid');</script>";
}
?>
<script language="javascript">
jQuery().ready(function() 
	{
		$("#expiry").mask("99-99-9999");
		$("#deadline").mask("99-99-9999");
		document.getElementById('barcode').focus();
		function findValueCallback(event, data, formatted) 
		{
			var barcode=document.getElementById('barcode').value=data[1];
			getitemdetails(document.getElementById('barcode').value,1,'<?php echo $fkstoreid; ?>');
			//getinstance('instancediv',barcode);
			jQuery("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
		}
		function formatItem(row) 
		{
			return row[0] + " (<strong>id: " + row[0] + "</strong>)";
		}
		function formatResult(row) 
		{
			return row[0].replace(/(<.+?>)/gi,'');
		}
			jQuery("#itemdescription").autocomplete("productautocomplete.php") ;
			jQuery(":text, textarea").result(findValueCallback).next().click(function() 
			{
				$(this).prev().search();
			});
			jQuery("#clear").click(function() 
			{
				jQuery(":input").unautocomplete();
			});
			//document.adstockfrm.reset(); 
			
});
function getitemdetails(bc,itm,store,quantity,id,stockid)
{
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getconsignmentitem.php?bc='+bc+'&item='+itm+'&store='+store+'&qty='+quantity+'&detailid='+id+'&stockid='+stockid);
}
function insertconsignment(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertconsignmentitem.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#consignmentitemfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Consignment has been updated.',0,5000);
		jQuery('#subsection').load('addconsignmentitems.php?param=undefined&id='+'<?php echo $id; ?>');
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
</script>
<div id="loaditemscript"> </div>
<div id="error" class="notice" style="display:none"></div>
<div id="shipfrmdiv" style="display: block;"> <br>
  <form id="consignmentitemfrm" style="width: 920px;" action="insertconsignmentitem.php?id=-1" class="form">
    <fieldset>
      <legend>
      <?php
   
     echo "Add Items for >> $consignmentname";	
    ?>
      </legend>
      <?php 
	  		print"<b>From:</b> $storename <b>To:</b> $deststorename";
		?>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="insertconsignment(-1);"> 
        <img src="../images/tick.png" alt=""/>
        <?php echo "Save"; ?>
        </button>
        <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </span> </div>
      <table width="100%">
        <tr>
          <td height="10" valign="top"><div class="topimage2" style="height:6px;">
              <!-- -->
            </div>
            <table cellpadding="2" cellspacing="0" width="100%" >
              <tbody>
              <tr>
              	<th>Barcode</th>
                <th>Item</th>
              </tr>
                <tr class="even">
                  <td align="center"><input name="barcode" id="barcode" class="text" size="20" value="<?php echo $barcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0,'<?php echo $fkstoreid; ?>','<?php echo $quantity; ?>','<?php echo $id; ?>','<?php echo $stockid; ?>'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
                  <td align="center"><input name="itemdescription" id="itemdescription" class="text" size="50" value="<?php echo $itemdescription; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" ></td>
				</tr>
				<tr>
					<div id="stockdetails">
					</div>
				</tr>
                <tr>
                  <td colspan="2" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="insertconsignment(-1);"> <img src="../images/tick.png" alt=""/>
                      <?php if($id=='-1') {echo "Save";} else {echo "Update";} ?>
                      </button>
                      <a href="javascript:void(0);" onclick="hidediv('shipfrmdiv');" class="negative"> <img src="../images/cross.png" alt=""/> Cancel </a> </div></td>
                </tr>
              </tbody>
            </table></td>
        </tr>
      </table>
	  <div id="stockdetails">
	  </div>
	  <input type="hidden" name="id" id="id" value ="<?php echo $id;?>"/>
	  <input type="hidden" name="store" id="store" value="<?php echo $fkstoreid; ?>" />
      <input type="hidden" name="detailid" id="detailid" value ="<?php if($detailid) {echo $detailid;} else {echo "-1";}?>"/>
	  <input type="hidden" name="existingunits" id="existingunits" value="<?php echo $quantity; ?>" />
	  <input type="hidden" name="constatus" id="constatus" value="<?php echo $fkstatusid;?>" />
    </fieldset>
  </form>
  <script language="javascript">
	focusfield('barcode');
</script>
</div>
<div id="<?php echo $div;?>">
	<div class="breadcrumbs" id="breadcrumbs">Consignment Items</div>
	<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
</div>