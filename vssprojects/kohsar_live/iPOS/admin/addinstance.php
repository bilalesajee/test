<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
/*******************************************************************/
if($_REQUEST['param']!='undefined')
{
	$productid	=	$_REQUEST['param'];
}
else
{
	$productid		=	$_REQUEST['productid'];
}
if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
// checking brand
$flag	=	$_REQUEST['flag'];
if($flag)
{
	$brand		=	$_REQUEST['brand'];
	$brandid	=	$_REQUEST['brandid'];
	if($brand)
	{
		$productid=$brand;
	}
}
}//end eidt
if($productid=='')
{
	$barcodeid		=	$_REQUEST['id'];
	$barcode_array	=	$AdminDAO->getrows('barcode','fkproductid,barcode',"pkbarcodeid= '$barcodeid'");
	$productid		=	$barcode_array[0]['fkproductid'];	
	$barcode		=	$barcode_array[0]['barcode'];
}
//echo $productid;
$product_array		=	$AdminDAO->getrows("product","productname", " pkproductid='$productid'");
$productname		=	$product_array[0]['productname'];		
if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012
$brands_array		=	$AdminDAO->getrows('brand, countries ',"pkbrandid,CONCAT(brandname,' ',countryname) as brandname", ' branddeleted != 1 and fkcountryid=pkcountryid ORDER BY brandname');
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
$brands_array		=	$AdminDAO->getrows('brand LEFT JOIN countries ON fkcountryid=pkcountryid',"pkbrandid,brandname,fkparentbrandid", ' branddeleted != 1  ORDER BY brandname');
}//end edit
$attributes_array	=	$AdminDAO->getrows("attribute a, productattribute pa","a.attributename, a.pkattributeid, pa.pkproductattributeid", " attributedeleted<>1 AND a.pkattributeid=pa.fkattributeid AND pa.fkproductid='$productid' AND pa.attributetype<>'n' GROUP BY a.attributename ORDER BY attributename ASC");
//attributes_array
//print_r($attributes_array);
foreach($attributes_array as $attrib)
{
	$pkattributeid			=	$attrib['pkattributeid'];
	$productattributes[]	=	"'$pkattributeid'";
	$options_array			=	$AdminDAO->getrows("attributeoption ao, attribute a","ao.pkattributeoptionid, ao.attributeoptionname,ao.fkattributeid", " ao.fkattributeid=a.pkattributeid AND a.pkattributeid='$pkattributeid'");
}
//print_r($productattributes);
if($productattributes)
{
	$productattributes	=	implode(",",$productattributes);
	$remainingattributes	=	$AdminDAO->getrows("attribute","*","pkattributeid NOT IN ($productattributes) ORDER BY attributename ASC");
	$a1			=	"<select name=\"newattributes\" id=\"newattributes\" style=\"width:80px;\">";
	for($i=0;$i<sizeof($remainingattributes);$i++)
	{
		$a2			.=	"<option value = \"".$remainingattributes[$i]['pkattributeid']."\">".$remainingattributes[$i]['attributename']."</option>";
	}
	$newattributes		=	$a1.$a2."</select>";
}
$o1d			=	"<select name=\"oldattribute\" id=\"oldattribute\" style=\"width:80px;\">";
for($j=0;$j<sizeof($attributes_array);$j++)
{
	$o1d2			.=	"<option value = \"".$attributes_array[$j]['pkattributeid']."\">".$attributes_array[$j]['attributename']."</option>";
}
$oldattributes		=	$o1d.$o1d2."</select>";
if($barcodeid!='')
{
	$duplicate			=	"AND pkbarcodeid<>'$barcodeid'";
}
$query	=	"SELECT 
							b.itemdescription PRODUCTNAME, pkbarcodeid
						FROM 
							barcode b
							
						WHERE
							
							fkproductid	=	'$productid'
							$duplicate
						GROUP BY
							b.pkbarcodeid"
							;	
$itemresult	=	$AdminDAO->queryresult($query);
?>
<script language="javascript" type="text/javascript">
jQuery(function($)
 {
	 jQuery('#xattributes').load('attributes.php?id='+'<?php echo $productid;?>'+'&barcode='+'<?php echo $barcodeid;?>');
	 jQuery('#existingoptions').load('itemattributes.php?id='+'<?php echo $productid;?>');
 });
function loadinstanceitems(showhide)
{
	if(showhide=='1')
	{
		document.getElementById('viewinstance').style.display='block';
		jQuery('#viewinstance').load('viewinstances.php?id=<?php echo $productid;?>&barcode=<?php echo $barcodeid;?>');
		document.getElementById('showhideinstance').value='0';
	}
	else
	{
		document.getElementById('showhideinstance').value='1';
		document.getElementById('viewinstance').style.display='none';
	}
}
function editselected()
{
	var selectedbrands	=	getselected('viewinstances');
	var sb;
	if (selectedbrands.length > 1)
	{
		for (i=1; i < selectedbrands.length; i++)
		{
			 sb	=	selectedbrands[i];
		} 
		var sb1	=	sb.split('viewinstances');
		jQuery('#instdiv').load('addinstance.php?id=<?php echo $productid;?>&barcode='+sb1[0]);
		
	}
	else
	{
		alert("Please make sure that you have selected at least one row.");
	}//else
	jQuery('#viewinstance').load('viewinstances.php?id=<?php echo $productid;?>&barcode='+sb1[0]);	
}
function deleteselected()
{
	
	var selecteditem	=	getselected('viewinstances');
	if (selecteditem=='')
	{
		alert("Please make sure that you have selected at least one row.");
	}
	else
	{
		if (confirm('Are you sure to DELETE selected record(s)?'))
		{
			jQuery('#viewinstance').load('viewinstances.php?oper=del&id=<?php echo $productid;?>&delid='+selecteditem);
		}
	}
}
function getattributeoptions(id,div)
{
	/*if(id!='')
	{
		jQuery('#'+div).load('getinstanceattributes.php'+'?id='+id);
	}*/
}
function submitinstance()
{
	options	=	{	
					url : 'insertinstance.php',
					type: 'POST',
					success: response
				}
	jQuery('#instanceform').ajaxSubmit(options);
}
function response(text)
{
	<?php if($_SESSION['siteconfig']!=1){//edit by ahsan 14/02/2012, if condition added?>
	if(isNaN(text)==true)
	{
		adminnotice(text,0,5000);
	}
	<?php }elseif($_SESSION['siteconfig']!=3){?>//from main, edit by ahsan 14/02/2012
	if(text=='brand')
	{
		adminnotice("Item saved successfully.",0,5000);
		jQuery('#subsection').load('viewinstances.php?id=<?php print"$brandid";?>&param=brand');
		document.getElementById('bc').focus();
	}
	else if(isNaN(text)==true)
	{
		adminnotice(text,0,5000);
	}
	<?php }?>//end edit
	else
	{
			adminnotice("Item saved successfully.",0,5000);
			//jQuery('#instdiv').load('addinstance.php?id=<?php print"$productid";?>');
			//jQuery('#viewinstance').load('viewinstances.php?id=<?php //print"$productid";?>'+'&barcode=<?php //echo $barcodeid;?>');
			<?php if($siteconfit==3){//edit by ahsan 14/02/2012, if condition added?>
				jQuery('#items').load('viewinstances.php?id=<?php print"$productid";?>');
			<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
				jQuery('#subsection').load('viewinstances.php?id=<?php print"$productid";?>');
			<?php }//end edit?>
			document.getElementById('bc').focus();
			
	}
			
}
function attributesnew()
{
	options	=	{	
					url : 'insertnewattribute.php',
					type: 'POST',
					success: attresponse
				}
	jQuery('#frmattributes').ajaxSubmit(options);
}
function attresponse(text)
{
	if(text.indexOf('=')!='-1')
	{
		text	=	text.split('=');
		adminnotice("Attribute added successfully.",0,5000);
		//here is where we load the existing attributes 
		jQuery('#existingoptions').load('itemattributes.php?id='+'<?php echo $productid;?>'+'&barcode='+text[1]);
		jQuery('#xattributes').load('attributes.php?id='+'<?php echo $productid;?>'+'&barcode='+text[1]);
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function newoptions()
{
	options	=	{	
					url : 'insertnewattribute.php',
					type: 'POST',
					success: optionresponse
				}
	jQuery('#frmoptions').ajaxSubmit(options);
}
function optionresponse(text)
{
	if(text.indexOf('=')!='-1')
	{
		text	=	text.split('=');
		adminnotice("Attribute added successfully.",0,5000);
		//now we'd need to add a few more options here..
		jQuery('#xattributes').load('attributes.php?id='+'<?php echo $productid;?>'+'&barcode='+text[1]);
		jQuery('#existingoptions').load('itemattributes.php?id='+'<?php echo $productid;?>'+'&barcode='+text[1]);
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function oldoptions()
{
	options	=	{	
					url : 'insertnewattribute.php',
					type: 'POST',
					success: oldoptresponse
				}
	jQuery('#frmexoptions').ajaxSubmit(options);
}
function oldoptresponse(text)
{
	if(text.indexOf('=')!='-1')
	{
		text	=	text.split('=');
		adminnotice("Attribute added successfully.",0,5000);
		jQuery('#xattributes').load('attributes.php?id='+'<?php echo $productid;?>'+'&barcode='+text[1]);
		jQuery('#existingoptions').load('itemattributes.php?id='+'<?php echo $productid;?>'+'&barcode='+text[1]);
	}
	else
	{
		adminnotice(text,0,5000);
	}
}
function hideform2()
{
	document.getElementById('instdiv').style.display='none';
}
function showitem()
{
	document.getElementById('itemdiv').style.display	=	'block';
}
function hideitem()
{
	document.getElementById('itemdiv').style.display	=	'none';
}
function checkbarcode()
{
	options	=	{	
					url : 'checkbarcodeinstance.php',
					type: 'POST',
					success: chkbarcoderesponse
				}
	jQuery('#instanceform').ajaxSubmit(options);
//checkbarcodeinstance.php
}
function chkbarcoderesponse(text)
{
	if(text!='')
	{
		adminnotice(text,0,5000);
		return false;	
	}
	else
	{
		document.getElementById('brand').focus();
		return false;
	}
}
</script>
    </div>
	
<!--    <div id="instanceitems">
		<input type="checkbox" value="1" name="showhideinstance" id="showhideinstance" onclick="loadinstanceitems(this.value)"/>
			<font style="size:16px; font-weight:bold">Show/Hide Items</font> 
		</div>-->
    <div id="canceledit">
    <div id="viewinstance"></div>
    <div id="instdiv">
    <table>
			<tr>
                <td valign="top">    
   	 <form enctype="multipart/form-data" name="instanceform" id="instanceform" style="width:460;" onSubmit="submitinstance(); return false;" class="form">
      <fieldset>
      <legend>
      <?php
        if($barcodeid)
        {
            $action 	= "Edit";
        }
        else
        {
            $action 	= "Add";
        }
      ?>
        <?php echo $action; ?> Item for <?php echo $productname;?>
      </legend>
      <div style="float:right">
      <span class="buttons">
            <button type="button" class="positive" onclick="submitinstance();">
                <img src="../images/tick.png" alt=""/> 
                Save
            </button>
             <a href="javascript:void(0);" onclick="hidediv('instdiv');" class="negative">
                <img src="../images/cross.png" alt=""/>
                Cancel
            </a>
          </span>
      </div>
        <?php
        if($barcodeid)
        {
            $bcodes 			=	$AdminDAO->getrows("barcode","*"," pkbarcodeid='$barcodeid'");
            $barcode			=	$bcodes[0]['barcode'];
			if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
				$shortdesc			=	$bcodes[0]['shortdescription'];
			}//end edit
			$boxquantity		=	$bcodes[0]['boxquantity'];
			$boxbarcode			=	$bcodes[0]['boxbarcode'];
            $selected_brands	=	$AdminDAO->getrows("brand, barcodebrand","*","pkbrandid = fkbrandid AND fkbarcodeid = '$barcodeid'"); 
			$selected_brand 	= 	$selected_brands[0]['pkbrandid'];
			$selected_brand 	= 	array($selected_brand);
        }
		if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012
			for($b=0;$b<sizeof($brands_array);$b++)
			{
				$parentid		=	$brands_array[$b]['fkparentbrandid'];
				$pbrand			=	$AdminDAO->getrows("brand","brandname","pkbrandid='$parentid'");
				$parentbrand	=	$pbrand[0]['brandname'];
				if($parentbrand)
				{
					$newbrands_array[$b]['brandname']	=	$brands_array[$b]['brandname']." (".$parentbrand.")";
				}
				else
				{
					$newbrands_array[$b]['brandname']	=	$brands_array[$b]['brandname'];
				}
				$newbrands_array[$b]['pkbrandid']	=	$brands_array[$b]['pkbrandid'];
			}
		}//end edit
       		 $brands				=	$Component->makeComponent('d','brand',$brands_array,'pkbrandid','brandname',1,$selected_brand);
			$pack			=	"<select name=\"boxitem\" id=\"boxitem\" style=\"width:180px;\">";
			for($j=0;$j<sizeof($itemresult);$j++)
			{
				if(in_array($boxbarcode,$itemresult[$j]))
				{
					$selected	=	" selected=\"selected\"";
				}
				$pack2			.=	"<option value = \"".$itemresult[$j]['pkbarcodeid'].$selected."\">".$itemresult[$j]['PRODUCTNAME']."</option>";
			}
			$packitem		=	$pack.$pack2."</select>";
		?>
                    <table cellpadding="0" cellspacing="2" width="100%">
                    <tbody>
                    <tr align="center">
                        <td><strong>Barcode</strong><?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php } //end edit?></td>
                        <td><strong>Brand</strong><?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php }//end edit?></td>
                    </tr>
                    <tr>
                      <td>
					 <!-- <input name="bc" id="bc" type="text" value="<?php //echo $barcode;?>" onkeydown="javascript:if(event.keyCode==13) {document.getElementById('brand').focus(); return false;}" autocomplete="off" >-->
					  <input name="bc" id="bc" type="text" value="<?php echo $barcode;?>" onkeydown="javascript:if(event.keyCode==13) {checkbarcode(this.value); return false;}" autocomplete="off" ></td>
                      
					  <td><?php echo $brands ?></td>
                      </tr>
                      <?php
                      if(sizeof($itemresult)>0)
					  {
						  ?>
                      <tr align="center">
                        <td><strong>Boxed Item</strong></td>
                        <td>Yes<input type="radio" name="boxstatus" value="1" onclick="showitem()" <?php if($boxbarcode != ''){ echo "checked=checked";} ?> />No<input type="radio" name="boxstatus" value="0" onclick="hideitem()" <?php if($boxbarcode == ''){ echo "checked=checked"; }?> /></td>
                      </tr>
                      <tr>
                      	<td colspan="2">
                        <?php if($boxbarcode=='') {$display	=	"none";} else {$display	=	"block";}?>
                          <div id="itemdiv" style="display:<?php echo $display; ?>">
                              <table width="100%" cellpadding="0" cellspacing="2">
                              <tr>
                                <td width="45%">Boxed Item</td>
                                <td width="55%">
								<?php 
								echo $packitem;
								?>
                                </td>
                              </tr>
                              <tr>
                                <td>Quantity</td>
                                <td align="left"><input type="text" value="<?php echo $boxquantity; ?>" name="boxqty" onkeydown="javascript:if(event.keyCode==13) {return false;}" autocomplete="off" onkeypress="return isNumberKey(event);" /></td>
                              </tr>
                              </table>
                          </div>
                          </td>
                      </tr>
                      <?php
					  }
					?>
                    <div id="xattributes"></div>
                    <tr >
                      <td colspan="4" align="left">
                       <div class="buttons">
                        <button type="button" class="positive" onclick="submitinstance();">
                            <img src="../images/tick.png" alt=""/> 
                            Save
                        </button>
                         <a href="javascript:void(0);" onclick="hidediv('instdiv');" class="negative">
                            <img src="../images/cross.png" alt=""/>
                            Cancel
                        </a>
                      </div>
                        </td>
                      </tr>
                    </tbody>
                    </table>
                      <input type="hidden" name="id" value ="<?php echo $productid; ?>" />
                      <input type="hidden" name="barcode" value="<?php echo $barcodeid;?>" />
                      <?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
                      	<input type="hidden" name="brandflag" value="<?php echo $flag;?>" />
					  <?php }//end edit?>
                      </fieldset>	
                    </form>
	               </td>
                <td valign="top">
                <div id="newattributes">
                <form id="frmattributes" class="form" style="width:460;" onSubmit="attributesnew(); return false;">
                <fieldset>
                <legend>
                	New Attributes
                </legend>
			<div style="float:right">
              <span class="buttons">
                    <button type="button" class="positive" onclick="attributesnew();">
                        <img src="../images/tick.png" alt=""/> 
                        Save
                    </button>
                     <a href="javascript:void(0);" onclick="hidediv('newattributes');" class="negative">
                        <img src="../images/cross.png" alt=""/>
                        Cancel
                    </a>
                  </span>
              </div><br /><br />
                  <table cellpadding="0" cellspacing="2" width="100%">
					<tr>
                        <td><strong>Attribute Name</strong><?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php }//end edit?></td>
                        <td><strong>Attribute Type</strong></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="attributename" value="" /></td>
                        <td>Single<input type="radio" name="attributetype" value="s" checked="checked" />Multiple<input type="radio" name="attributetype" value="m" /></td>
                        <input type="hidden" name="productid" value="<?php echo $productid; ?>" />
                        <input type="hidden" name="barcode" value="<?php echo $barcodeid;?>" />
                        <input type="hidden" name="newattribute" value="n" />
                    </tr>
                    <tr>
                    	<td colspan="2"><div class="buttons">
                        <button type="button" class="positive" onclick="attributesnew();">
                            <img src="../images/tick.png" alt=""/> 
                            Save
                        </button>
                         <a href="javascript:void(0);" onclick="hidediv('newattributes');" class="negative">
                            <img src="../images/cross.png" alt=""/>
                            Cancel
                        </a>
                      </div></td>
                    </tr>
                  </table>
                </fieldset>
				</form>
                </div>
                    <table cellpadding="0" cellspacing="2" width="100%">
                        <tr>
                            <td>
                            <div id="exoptions">
                            <form id="frmoptions" class="form" style="width:460;" onSubmit="newoptions(); return false;">
                            <fieldset>
                            <legend>
                                New Options
                            </legend>
                        <div style="float:right">
                          <span class="buttons">
                                <button type="button" class="positive" onclick="newoptions();">
                                    <img src="../images/tick.png" alt=""/> 
                                    Save
                                </button>
                                 <a href="javascript:void(0);" onclick="hidediv('exoptions');" class="negative">
                                    <img src="../images/cross.png" alt=""/>
                                    Cancel
                                </a>
                              </span>
                          </div><br /><br />
                              <table cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td><strong>Attribute Name</strong></td>
                                    <td><strong>Attribute Type</strong></td>
                                    <td><strong>Option Name</strong><?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?><span class="redstar" title="This field is compulsory">*</span><?php } //end edit?></td>                                    
                                </tr>
                                <tr>
                                    <td>
                                    <?php echo $newattributes; ?>
                                    </td>
                                    <td>Single<input type="radio" name="attributetype" value="s" checked="checked" />Multiple<input type="radio" name="attributetype" value="m" /></td>																											
                                    <td><input type="text" name="option[]" value="" /><br /><input type="text" name="option[]" value="" /><br /><input type="text" name="option[]" value="" /></td>
                                    <input type="hidden" name="productid" value="<?php echo $productid; ?>" />
                                    <input type="hidden" name="barcode" value="<?php echo $barcodeid;?>" />
                                    <input type="hidden" name="newattribute" value="n2" />                                    
                                    </tr>
                                <tr>
                                    <td colspan="2"><div class="buttons">
                                    <button type="button" class="positive" onclick="newoptions();">
                                        <img src="../images/tick.png" alt=""/> 
                                        Save
                                    </button>
                                     <a href="javascript:void(0);" onclick="hidediv('exoptions');" class="negative">
                                        <img src="../images/cross.png" alt=""/>
                                        Cancel
                                    </a>
                                  </div>
                                  </td>
                                </tr>
                              </table>
                            </fieldset>
                            </form>
                            </div>
                            </td>
                        </tr>
                        <tr>
                        	<td>
                            <div id="existingoptions">
                            </div>
                            </td>
                        </tr>
                    </table>
                </td>
          	</tr>
      </table>
      </div>
</div>
<script language="javascript">
<?php
if($barcodeid=='')
{
?>
document.instanceform.bc.focus();
	loading('Loading Form...');
</script>
<?php
}
?>