<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
//getting default currency
$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");
$defaultcurrency = $currency[0]['currencyname'];
//$brandid	=	$_REQUEST['id'];
$stockids	=	$_REQUEST['id'];
$nobrand	=	$_REQUEST['nobrand'];
$list		=	explode(',',$stockids);
//print_r($list);
foreach($list as $pkbarcodeid)
{
	
	$productname="";
	//$bc	=	$AdminDAO->getrows("stock","fkbarcodeid", " pkstockid= '$stockid'");
	//$pkbarcodeid	=	$bc[0]['fkbarcodeid'];
	//$barcode		=	filter($_POST['bc']);
	$bc	=	$AdminDAO->getrows("barcode","barcode", " pkbarcodeid= '$pkbarcodeid'");
	$barcode	=	$bc[0]['barcode'];
	
	$brandarray		=	$AdminDAO->getrows("barcodebrand b, brand br"," b.pkbarcodebrandid as id, br.pkbrandid, br.brandname as brand"," b.fkbarcodeid= '$pkbarcodeid' AND br.pkbrandid = b.fkbrandid");
	$product	= 	$AdminDAO->getrows("product p, barcode b","productname", " b.pkbarcodeid = '$pkbarcodeid' AND fkproductid=pkproductid ");
	$productname	=	$product[0]['productname'];
	if(!$brandarray && $nobrand !='-1')
	{
		// if no brand is attched to stock it means this imported data then attach to "Esajee brand"
		//$sql="insert into barcodebrand set fkbarcodeid='$pkbarcodeid', fkbrandid='1375'";
		//$AdminDAO->queryresult($sql);
		
	if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
		$fields		=	array('fkbarcodeid','fkbrandid');
		$values		=	array($pkbarcodeid,'1375');
		$table		=	"barcodebrand";
	
		$insertid 	=	$AdminDAO->insertrow($table,$fields,$values);		
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
		$sql="insert into barcodebrand set fkbarcodeid='$pkbarcodeid', fkbrandid='1375'";
		//$AdminDAO->queryresult($sql);
			$tblj		 = 	"barcodebrand";
			$field		 =	array('fkbarcodeid','fkbrandid');
			$value		 =	array(
						$pkbarcodeid,
						'1375'		
			);
			$AdminDAO->insertrow($tblj,$field,$value);	
	}//end edit
		
		$brandarray		=	$AdminDAO->getrows("barcodebrand b, brand br"," b.pkbarcodebrandid as id, br.pkbrandid, br.brandname as brand"," b.fkbarcodeid= '$pkbarcodeid' AND br.pkbrandid = b.fkbrandid");
		foreach($list as $sids)
		{
			//$sql="UPDATE stock set fkbrandid='1375' where pkstockid='$sids'";//atching stock to brand
			//$AdminDAO->queryresult($sql);
					
			if($_SESSION['siteconfig']!=1){//edit by ahsan 17/02/2012, added if condition
				$fields		=	array('fkbrandid');
				$values		=	array('1375');
				$table		=	"stock";
			
				$AdminDAO->updaterow($table,$fields,$values,"pkstockid='$sids'");			
			}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012
				$sql="UPDATE stock set fkbrandid='1375' where pkstockid='$sids'";//atching stock to brand
				//$AdminDAO->queryresult($sql);
				$tblj	= 	'stock';
				$field	=	array('fkbrandid');
				$value	=	array('1375');
				
				$AdminDAO->updaterow($tblj,$field,$value,"pkstockid='$sids'");			
			}//end edit
		}
		//print_r($list);
		//echo "<div class=notice>No Brand record found.</div>";
		//exit;
	}
?>
<div id="stockdetailsdiv1">
<div style="float:right">
 <a href="javascript:void(0)" onclick="hidediv('stockdetailsdiv1')"> <img src="../images/x.jpeg"></a>
</div>

 <div id="movestock"></div>
 <table cellpadding="0" cellspacing="0" width="100%">
    <thead>
    <tr class="even">
    	<th width="8%" align="left">
        	&nbsp;Barcode
        </th>
        <td align="left">
        	 &nbsp;<?php echo $barcode;?>
        </td>
    </tr>
    </thead>
<?php   
foreach($brandarray as $brands)
{
	$brand 			=	$brands['brand'];
	$brandid		=	$brands['pkbrandid'];
	$barcodebrand	=	$brands['id'];
	$res			=	$AdminDAO->getrows("barcode b, productinstance pi, stock s, product p","p.productname as product,SUM(s.quantity) as quantity, SUM(s.unitsremaining) as units, IF(expiry='0','--------',MIN(s.expiry)) as expiry"," s.fkbarcodeid=b.pkbarcodeid AND p.pkproductid = b.fkproductid AND pi.fkbarcodeid = b.pkbarcodeid  AND b.barcode = '$barcode' AND s.fkbrandid = '$brandid' GROUP BY s.fkbarcodeid");

/*	echo "<pre>";
	print_r($res2);
	echo "</pre>";*/
	$quantity	=	$res[0]['quantity'];
	$units	=	$res[0]['units'];
	$expiry	=	$res[0]['expiry'];
	$expiry	=	date("d M Y", strtotime($expiry));	
	if(!$quantity)
	{
		$quantity	=	0;
	}	
	if(!$units)
	{
		$units	=	0;
	}
	if(!$expiry)
	{
		$expiry	=	"----------";
	}

	?>
    <thead>
    <tr id="tr" class="odd">
       <th align="left">
       	&nbsp;Brand
       </th>
       <td align="left">
		&nbsp;<?php echo $brand; ?>
       </td>
    </tr>
    </thead>    
    <tr>
    	<td height="20" colspan="5" valign="top">
					<?php
                    if($barcodebrand)//displayes the brand barcode stock details
                    {
                        $ids	=	$AdminDAO->getrows("barcodebrand","fkbarcodeid, fkbrandid", " pkbarcodebrandid = '$barcodebrand'");
                       	$barcodeid	=	$ids[0]['fkbarcodeid'];
                        $brandid	=	$ids[0]['fkbrandid'];
                      // print"-------------------------------------------------------<br>";
					    $row	=	$AdminDAO->getrows("stock s, store st","st.pkstoreid, st.storename, s.updatetime, SUM(s.quantity) as quantity, SUM(s.unitsremaining) as unitsremaining, IF(MIN(s.expiry)='0','--------',MIN(s.expiry)) as expiry", " fkbrandid = '$brandid' AND fkbarcodeid = '$barcodeid' AND s.fkstoreid = st.pkstoreid GROUP BY pkstoreid");
						//print"-------------------------------------------------------<br>";
						//print_r($row);                   
                        if(!$row)
                        {
                            echo "<div class=notice>No record found for this brand.</div>";
                            exit;
                        }//end of if row
                    ?>
                    <div class="topimage" style="height:6px;"><!-- --></div>
                  
                    <table cellpadding="0" cellspacing="0" style="width:100%;" >
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Location</th>
                            <th>Last Update</th>
                            <th>Units Sent</th>
                            <th>Remaining Units</th>        
                            <th>Nearest Expiry</th>                
                        </tr>
                    </thead>    
                        <?php
                        foreach($row as $data)
                        {
                            $location	=	$data['storename'];
                            $time		=	$data['updatetime'];
                            $time		=	date('d-m-y', $time);
                            $quantity	=	$data['quantity'];		
                            $units		=	$data['unitsremaining'];
                            $expiry		=	$data['expiry'];
                            $expiry		=	date("d-m-y", $expiry);
                            $storeid	=	$data['pkstoreid'];		
                        
						?>
                         <tr id="tr<?php echo $barcodeid; ?>viewinstances"  class="even" align="center">
                            <td>
                            <img src="../images/max.GIF" width="12" height="12" onclick="viewstoredetails('tr<?php echo $barcodeid.$storeid; ?>')" id="tr<?php echo $barcodeid.$storeid;?>_link" title="Click here to view stock details."/>
                           </td>
                            <td><?php echo $location; ?></td>
                            <td><?php echo $time;?></td>
                            <td><?php echo $quantity;?></td>
                            <td><?php echo $units;?></td>
                            <td><?php echo $expiry;?></td>                
                        </tr>
                        <tr>
                        	<td colspan="6" width="100%" valign="top">
                            	<?php
								//$id	=	$_REQUEST['id'];
								//$barcode	=	$_REQUEST['barcode'];
								//$brandid	=	$_REQUEST['brandid'];
									$store	=	$AdminDAO->getrows("store","storename"," pkstoreid = '$storeid'");
									$res	=	$AdminDAO->getrows("stock","quantity,unitsremaining,IF (expiry='0', '--------',expiry)"," fkstoreid = '$storeid' AND fkbarcodeid = '$barcodeid' AND fkbrandid = '$brandid' ORDER BY expiry asc");
									if(!$res)
									{
										echo "<div class=notice>No record found.</div>";
										exit;
									}
									/****************** the start ********************/
									$labels = array("ID","Units Sent","Remaining Units","Retail Price","Cost Price","Trade Price $defaultcurrency","Expiry");
									$fields = array("pkstockid","quantity","unitsremaining","retailprice","costprice","priceinrs","expiry");
									$dest 	= 	'viewinstancestock.php';
									$div	=	"sugrid";
									$form 	= 	"frmdetails".$barcodeid.$storeid;
									/*$css 	= 	'<link rel="stylesheet" type="text/css" href="../includes/css/all.css">';
$jsrc 	= 	'<script language="javascript" src="../includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="../includes/js/jquery.form.js" type="text/javascript"></script>';*/
									define(IMGPATH,'../images/');
									$query 	= 	"SELECT 
													pkstockid,
													quantity,
													unitsremaining,
													round(retailprice,2) as retailprice,
													round(costprice,2) as costprice,
													round(priceinrs,2) as priceinrs,
													IF(expiry='0','--------',FROM_UNIXTIME(expiry,'%d-%m-%y')) as expiry
												FROM 
													stock
												WHERE
													fkstoreid = '$storeid' AND 
													fkbarcodeid = '$barcodeid' AND 
													fkbrandid = '$brandid'
												";
									
														
									$navbtn="<a class=\"button2\" id=\"editstock\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'editstock.php','movestock','sugrid') title=\"Edit Stock\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			&nbsp;  <a href=\"javascript: loadsubgrid('movestock',document.$form.checks,'movestock.php','sugrid') \" title=\"Move Stock\"><b>Move Stock</b></a>&nbsp;|
			&nbsp;  <a href=\"javascript: showpage(1,document.$form.checks,'loadunits.php','movestock','sugrid') \" title=\"View stock Damages\"><b>View Damages</b></a>&nbsp;";
									/********** END DUMMY SET ***************/
									?>
                                    <p>
                                    <div style="display:none" id="tr<?php echo $barcodeid.$storeid; ?>_detail">
									<?php
									grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
                                   
									?>
                                    </div>
                                    </p>
                                    
                          </td>
                        </tr>
                        <?php
                     
						}//end of for each
                        ?>
                        </table>
                        <?php
                    }
                    else
                    {
                        echo "No Result found";
                    }//end of row else
                    ?>	
                  
                    </td>
   </tr>
        
                <?php
                }//end of for each
          
		   
		   }//end of for each
		?>
</table>
</div>