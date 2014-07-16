<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
/********************************COUNTRIES***********************************/
$productid			=	$_REQUEST['id'];
$barcodeid			=	$_REQUEST['barcode'];
$barcode			=	$_REQUEST['bc'];
$product_array		=	$AdminDAO->getrows("product","productname", " pkproductid='$productid'");
$productname		=	$product_array[0]['productname'];		
$brands_array		=	$AdminDAO->getrows('brand','*', ' branddeleted != 1');
$attributes_array		=	$AdminDAO->getrows("attribute a, productattribute pa","a.attributename, a.pkattributeid, pa.pkproductattributeid", " a.attributedeleted != 1 AND a.pkattributeid=pa.fkattributeid AND pa.fkproductid='$productid' GROUP BY a.attributename");
//$attributes				=	$Component->makeComponent('d','attribute',$attributes_array,'pkattributeid','attributename',1,$selected_brands,"onchange=getattributeoptions(this.value,'attributediv')");
?>

<script language="javascript" type="text/javascript">

function getattributeoptions(id,div)
{
	/*if(id!='')
	{
		jQuery('#'+div).load('getinstanceattributes.php'+'?id='+id);
	}*/
}
function submitinstance()
{
	loading('System is ....');
	
	options	=	{	
					url : 'insertinproductstance.php',
					type: 'POST',
					success: response
				}
	jQuery('#instanceform').ajaxSubmit(options);
}
function response(text)
{
	
	if(text!="")
	{

		//var a	=	document.getElementById('viewinstance').innerHTML='';
		var	bcstr=	text.split('__');
		if(bcstr[1]=='barcode')
		{
			//alert('pak');
			jQuery('#instancediv').load('addstock.php?code='+bcstr[0]);
			jQuery('#loaditemscript').load('getitemdata.php?bc='+bcstr[0]+'&item='+1);
			hidediv("productdiv");
		}
	}
}
</script>
<div id="error" class="notice" style="display:none"></div>
    <div id="instdiv">
   	 <form enctype="multipart/form-data" id="instanceform" class="form" onSubmit="submitinstance(); return false;">
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
        <?php echo $action; ?> Instance for <?php echo $productname;?>
      </legend>
        <?php
        if($barcodeid)
        {
            $bcodes 	=	$AdminDAO->getrows("barcode","*"," pkbarcodeid=$barcodeid");
            $barcode	=	$bcodes[0]['barcode'];
            $selected_brands	=	$AdminDAO->getrows("brand, barcodebrand","*","pkbrandid = fkbrandid AND fkbarcodeid = '$barcodeid'"); 
        $selected_brand = $selected_brands[0]['pkbrandid'];
        $selected_brand = array($selected_brand);
		//$attrib	=	$AdminDAO->getrows("productattribute","*"," fkproductid=$productid");
									
        //$attributes_array	=	$AdminDAO->getrows("productinstance p, attribute a, productattribute pa","*","p.fkbarcodeid = '$barcodeid' AND a.pkattributeid = pa.fkattributeid AND pa.pkproductattributeid = p.fkproductattributeid");
        }
        $brands				=	$Component->makeComponent('d','brand',$brands_array,'pkbrandid','brandname',1,$selected_brand);
        ?>
      <table cellpadding="0" cellspacing="2" width="100%">
        <tbody>
        <tr>
            <td width="17%">Barcode</td>
            <td width="67%">Brand</td>
        </tr>
        <tr>
          <td><input name="bc" id="bc" type="text" value="<?php echo $barcode;?>" onkeydown="javascript:if(event.keyCode==13) {document.getElementById('brand').focus(); return false;}" autocomplete="off"></td>
          <td><?php echo $brands ?></td>
          </tr>
          <?php
/*            foreach($attributes_array as $attributes)
            {
            ?>
           	 	<tr><td><?php echo $attributes['attributename']; ?></td><td>&nbsp;</td></tr>
            <?php
            }*/
                foreach($attributes_array as $attrib)
                {
								 
                    $pkattributeid	=	$attrib['pkattributeid'];
                    //echo $pkattributeid."is attribute id <br>";
                    	$options_array		=	$AdminDAO->getrows("attributeoption ao, attribute a","ao.pkattributeoptionid, ao.attributeoptionname", " ao.fkattributeid=a.pkattributeid AND a.pkattributeid='$pkattributeid'");
                        $productattributes	=	$AdminDAO->getrows("productattribute","fkproductid,fkattributeid,pkproductattributeid,attributetype","fkproductid='$productid'");
                        foreach($productattributes as $pattributes)
                        {
                            if($pattributes['fkattributeid']==$pkattributeid)
                            {
						?>
								<tr>
                                    <td><?php echo $attrib['attributename']; ?></td>
                                <?php
                                //getting selected option for edit
                                if($barcodeid)
                                {
                                    $selected_options	=	$AdminDAO->getrows("productinstance","*"," fkbarcodeid = '$barcodeid'");
									foreach($selected_options as $attoptions)
									{
										$selected_option[]	=	$attoptions['fkattributeoptionid'];
									}
                                }
                                if($pattributes['attributetype']=='s')
                                {
                                    //this is the section for single attributes
                                    ?>
                                    <td>
                                    <select name="attributeoptions[]">
                                    <option value="">None</option>
                                    <?php 
                                    foreach($options_array as $options)
                                    {
                                    ?>
                                    <option value="<?php echo $attrib['pkproductattributeid']."_".$options['pkattributeoptionid'];?>" <?php if(@in_array($options['pkattributeoptionid'],$selected_option)) {echo "selected=selected";}?> ><?php echo $options['attributeoptionname']; ?></option>
                                    <?php
                                    }//end foreach
                                    ?>
                                    </select>
                                    </td>
                                    <?php
                                }//end if inner $productattributes[0]['attributetype']
                                else if($pattributes['attributetype'] =='m')
                                {
									//this is the section for multiple attributes
                                    ?>
                                    <td width="16%">
                                    <select name="attributeoptions[]" multiple="multiple" size="4">
                                    <?php 
                                    foreach($options_array as $options)
                                    {
                                    ?>
                                    <option value="<?php echo $attrib['pkproductattributeid']."_".$options['pkattributeoptionid'];?>" <?php if(@in_array($options['pkattributeoptionid'],$selected_option)) {echo "selected=selected";}?>>
									<?php
									
										echo $options['attributeoptionname'];?>
                                    </option>
                                    
                                    
                                    <?php
									/*
										<input type="checkbox" name="attributeoptions[]" value="<?php echo $attrib['pkproductattributeid']."_".$options['pkattributeoptionid'];?>" <?php if(@in_array($options['pkattributeoptionid'],$selected_option)) {echo "checked=checked";}?> />
									*/
			                       }//end foreach
                                    ?>
                                    </select>
                                    </td>
                                    </tr>
                                    <?php
                                }//end else
                                //echo $productattributes[0]['attributetype']."<b> I M HERE</b>";
                            }//end if
                        }//end foreach inner
                }//end foreach outer
                ?>
          </tr>
        <tr>
          <td colspan="4" align="center"><input name="save" type="submit" id="save" value="Save">
            <input name="cancel" type="button" value="Cancel" onclick="hidediv('instdiv')" id="cancel" /></td>
          </tr>
        </tbody>
    </table>
      <input type="hidden" name="id" value ="<?php echo $productid; ?>" />
      <input type="hidden" name="barcode" value="<?php echo $barcodeid;?>" />
      
      </fieldset>	
    </form>
    <div id="viewinstance"></div>
    </div>
<?php if($_SESSION['siteconfig']!=3){//from main, edit by ahsan 14/02/2012?>
    <script language="javascript">
	focusfield('bc');
</script>
<?php } //end edit?>