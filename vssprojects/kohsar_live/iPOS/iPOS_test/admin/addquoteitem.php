<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component;
$param	=	$_REQUEST['param'];
if($param=='undefined')
{
	$id		=	$_REQUEST['id'];
	$param	=	$id;
}
else
{
	$id			=	$_REQUEST['param'];
	$detailid	=	$_REQUEST['id'];
}
$oper	=	$_GET['oper'];
if($oper	==	'del' && $id!='')
{
	//deleting items
	$detailids	=	ltrim($detailid,",");
	$detids		=	explode(",",$detailids);
	for($i=0;$i<sizeof($detids);$i++)
	{
		$detid		=	$detids[$i];
		//$delquery	=	"DELETE FROM $dbname_detail.podetail WHERE fkpurchaseorderid='$id' AND pkpodetailid='$detid'";
		//$AdminDAO->queryresult($delquery);
		$AdminDAO->deleterows("$dbname_detail.podetail","fkpurchaseorderid='$id' AND pkpodetailid='$detid'",1);
	}
}
$qstring			=	$_SESSION['qstring'];
$query	=	"SELECT
				pkpurchaseorderid,
				quotetitle,
				status
			FROM
				$dbname_detail.purchaseorder,statuses
			WHERE
				status	=	pkstatusid AND
				pkpurchaseorderid='$id'";
$res	=	$AdminDAO->queryresult($query);
$quotename	=	$res[0]['quotetitle'];
$fkstatusid			=	$res[0]['status'];

/**** grid values */
$dest 	= 	'addquoteitem.php';
$div	=	'subsection';
$form 	= 	"quoteitemfrm";	
define(IMGPATH,'../images/');
if($detailid)
{
	$res				=	$AdminDAO->getrows("$dbname_detail.podetail,barcode","barcode,itemdescription,customdescription,quoteprice,saleprice,fkstockid,taxable","fkbarcodeid=pkbarcodeid AND pkpodetailid='$detailid'");
	$barcode			=	$res[0]['barcode'];
	$itemdescription	=	$res[0]['itemdescription'];
	$customdescription	=	$res[0]['customdescription'];
	$quoteprice			=	$res[0]['quoteprice'];
	$exempt				=	$res[0]['taxable'];
	$price				=	$res[0]['saleprice'];
	$stockid			=	$res[0]['fkstockid'];
}

if($stockid=='')
{
	$stockid='undefined';
}
//echo $stockid."is the stockid";
//echo "the result is $quantity and $barcode and $itemdescription and $price";
$query 	= 	"SELECT
				pkpodetailid,
				barcode,
				po.customdescription,
				itemdescription,
				po.quoteprice,
				(select CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=po.fkaddressbookid) as addedby
			FROM
				$dbname_detail.podetail po,barcode
			WHERE
				po.fkbarcodeid=pkbarcodeid AND
				fkpurchaseorderid	=	'$id'
			";
$navbtn	=	"<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addquoteitem.php','subsection','subsection','$param') \" title=\"Add Consignment\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','$param') title=\"Delete Items\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
$labels	=	array('ID','Barcode','Item','Custom Description','Quote Price','Added By');
$fields	=	array('pkpodetailid','barcode','itemdescription','customdescription','quoteprice','addedby');
/**** end grid section */
//$consignmentname	=	$res['consignmentname'];
// selecting suppliers
if($detailid && $oper=='')
{
echo "<script language=\"javascript\">getitemdetails('$barcode','0','$fkstoreid','$quoteprice','$id','$stockid');</script>";
}
?>
<script language="javascript">
jQuery().ready(function() 
	{
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
function getitemdetails(bc,itm,store,quoteprice,id,stockid)
{
	if(bc=='')
	{
		alert("Please enter Barcode.");
		return false;
	}
	bc = trim(bc);
	jQuery('#loaditemscript').load('getquoteitem.php?bc='+bc+'&item='+itm+'&store='+store+'&qprice='+quoteprice+'&detailid='+id+'&stockid='+stockid);
}
function insertquoteitem(id)
{
	//loading('System is saving data....');
	options	=	{	
					url : 'insertquoteitem.php?id='+id,
					type: 'POST',
					success: response
				}
	jQuery('#quoteitemfrm').ajaxSubmit(options);
}
function response(text)
{
	if(text=='')
	{
		adminnotice('Item has been updated.',0,5000);
		jQuery('#subsection').load('addquoteitem.php?param=undefined&id='+'<?php echo $id; ?>');
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
  <form id="quoteitemfrm" style="width: 920px;" action="insertquoteitem.php?id=-1" class="form">
    <fieldset>
      <legend>
      <?php
   
     echo "Add Items for >> $quotename";	
    ?>
      </legend>
      <?php 
	  		print"<b>From:</b> $storename <b>To:</b> $deststorename";
		?>
      <div style="float:right"> <span class="buttons">
        <button type="button" class="positive" onclick="insertquoteitem(-1);"> 
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
                <th>Custom Description</th>
                <th>Exempt Tax</th>
              </tr>
                <tr class="even">
                  <td align="center"><input name="barcode" id="barcode" class="text" size="20" value="<?php echo $barcode; ?>" onkeydown="javascript:if(event.keyCode==13) {getitemdetails(this.value,0,'<?php echo $fkstoreid; ?>','<?php echo $quoteprice; ?>','<?php echo $id; ?>','<?php echo $stockid; ?>'); return false;}" type="text" autocomplete="off" onfocus="this.select()" ></td>
                  <td align="center"><input name="itemdescription" id="itemdescription" class="text" size="50" value="<?php echo $itemdescription; ?>" onKeyDown="javascript:if(event.keycode==13){addshiplist(); return false;}" type="text" ></td>
                  <td align="center"><input name="customdescription" id="customdescription" class="text" size="50" value="<?php echo $customdescription;?>" onKeyDown="javascript:if(event.keycode==13){return false;}" type="text" ></td>
                  <td><input type="checkbox" value="1" name="exempt" id="exempt" <?php if($exempt==1){ echo "checked=\"checked\"";}?> /></td>
				</tr>
				<tr>
                	<td colspan="4">
					<div id="stockdetails">
					</div>
                    </td>
				</tr>
                <tr>
                  <td colspan="4" align="center"><div class="buttons">
                      <button type="button" class="positive" onclick="insertquoteitem(-1);"> <img src="../images/tick.png" alt=""/>
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
</div>
<div id="<?php echo $div;?>">
	<div class="breadcrumbs" id="breadcrumbs">Quote Items</div>
	<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
</div>