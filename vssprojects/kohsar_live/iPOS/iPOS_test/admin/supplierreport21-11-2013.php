<?php

session_start();

////if($_SESSION['siteconfig']!=1 && strstr($_SERVER['HTTP_REFERER'],'sectionid=3')==true){//edit by ahsan 17/02/2012, added if condition //run this block for local(store) or global(main&store)
include_once("../export/exportdata.php");
	include("../includes/security/adminsecurity.php");

	global $AdminDAO;

	$id	=	$_REQUEST['ids'];

	$id	=	trim($id,',');

	$idarr	=	explode(',',$id);

	//getting default currency

	$currency = $AdminDAO->getrows('currency','currencyname',"`defaultcurrency`  = 1");

	$defaultcurrency = $currency[0]['currencyname'];

	

	$newid	=	$idarr[(sizeof($idarr)-1)];

	if($newid=='')

	{

		print"<b>No Invoice Selected</b>";

	//	exit;

		?>

		<script language="javascript">

			//window.close();

		</script>

		<?php

		exit;

	}

	$query		=	"SELECT 

							pksupplierinvoiceid,

							billnumber,

							FROM_UNIXTIME(datetime,'%d-%m-%y') datetime,
							

							description,

							image,

							companyname,

							fksupplierid

							

					FROM

							$dbname_detail.supplierinvoice,supplier

					WHERE 	

							fksupplierid=pksupplierid and 

							pksupplierinvoiceid='$newid'

					";

	$supplierinfo		=	$AdminDAO->queryresult($query);

	$pksupplierinvoiceid=	$supplierinfo[0]['pksupplierinvoiceid'];

	$billnumber			=	$supplierinfo[0]['billnumber'];

	$datetime			=	$supplierinfo[0]['datetime'];

	$image				=	$supplierinfo[0]['image'];

	$companyname		=	$supplierinfo[0]['companyname'];

	$fksupplierid		=	$supplierinfo[0]['fksupplierid'];

	$sql	=	"

								SELECT

									pkstockid,

									barcode,

									itemdescription,

									quantity,

									unitsremaining,

									IF(expiry='0','--------',FROM_UNIXTIME(expiry,'%d-%m-%y')) as expiry,
									
									IF(st.addtime='0','--------',FROM_UNIXTIME(st.addtime,'%d-%m-%y')) as addtime,

									st.purchaseprice,
									st.costprice,

									priceinrs,

									retailprice,

									fksupplierid,

									concat(firstname,' ',lastname) name,

									currencysymbol,

									pkcurrencyid,
									pd.saleprice

								FROM 

									$dbname_detail.stock st LEFT JOIN addressbook ON (fkemployeeid=pkaddressbookid) left join $dbname_detail.podetail pd on (pkstockid=pd.fkstockid)  left join shipment on (st.fkshipmentid=pkshipmentid) LEFT JOIN currency ON (shipmentcurrency=pkcurrencyid),barcode

								WHERE 

									fksupplierinvoiceid='$pksupplierinvoiceid' AND 

							

									st.fkbarcodeid=pkbarcodeid

								ORDER BY

									pkstockid DESC

								";

		$result		=	$AdminDAO->queryresult($sql);

		$currency	=	$result[0]['currencysymbol'];

		$currencyid	=	$result[0]['pkcurrencyid'];

	echo"<div align=center><b><h3> $storename</h3><br>Supplier Name: $companyname &nbsp;&nbsp;&nbsp;Date: ".$datetime."&nbsp;&nbsp;&nbsp;Invoice Number: $pksupplierinvoiceid&nbsp;&nbsp;&nbsp;Bill No: $billnumber</b>

	<b>Currency: $currency</b><br><div id=supplierdiv></div></div>

	";

	?>
<form id="reportdata" method="post">
<input type="hidden" name="data" id="data" />
	<link rel="stylesheet" type="text/css" href="../includes/css/style.css">

	<table class="simple">

	<tr>

		<th>S.No</th>

		<th>Barcode</th>

		<th>Item</th>

		<th>Units Recieved</th>
       <?php if($result[$i]['saleprice']>0){?> <th>PO Rate</th><?php  }?>

		<?php if($currencyid!=3){?><th>Trade Price</th><?php  }?>
        <th>Trade Price <?php echo $defaultcurrency;?></th>
        <th>Units x Trade Price in <?php echo $defaultcurrency;?></th>
		<th>Price <?php echo $defaultcurrency;?></th>

		<th>Units x Price in <?php echo $defaultcurrency;?></th>
      <!--  <th>Units x Trade Price in <?php //echo $defaultcurrency;?></th>-->
		<th>Retail Price</th>

		<th>Added by</th>
        
        <th>Date Added</th>

	</tr>

	<?php

		for($i=0;$i<count($result);$i++)

		{

			$supplierids[]	=	$result['fksupplierid'];

			$unitsprice		=	$result[$i]['quantity']*$result[$i]['priceinrs'];
			$unitscostprice		=	$result[$i]['quantity']*$result[$i]['costprice'];

			$addedby		=	$result[$i]['name'];
			
			$addtime		=	$result[$i]['addtime'];

			if(!$addedby)

			{

				//fetch employee info

				$stockid	=	$result[$i]['pkstockid'];

				$stockinfo	=	$AdminDAO->getrows("$dbname_detail.stock LEFT JOIN employee ON (fkemployeeid=pkemployeeid),addressbook","CONCAT(firstname,' ',lastname) name","fkaddressbookid=pkaddressbookid AND pkstockid='$stockid'");

				$addedby	=	$stockinfo[0]['name'];

			}

			?>

			<tr>

				<td><?php echo $i+1;?></td>

				<td><?php echo $result[$i]['barcode'];?></td>

				<td><?php echo $result[$i]['itemdescription'];?></td>

				<td><?php echo $result[$i]['quantity'];?></td>
              <?php if($result[$i]['saleprice']>0){?>  <td  bgcolor="#00CC33"><?php echo $result[$i]['saleprice'];?></td><?php }?>

				<?php if($currencyid!=3){?><td align="right"><?php echo $result[$i]['purchaseprice'];?></td><?php  }?>
                <td align="right" bgcolor="#FF3300"><?php echo number_format($result[$i]['costprice'],2);?></td>
                <td align="right" bgcolor="#FF3300"><?php echo number_format($unitscostprice,2);?></td>

				<td align="right" bgcolor="#3366FF"><?php echo number_format($result[$i]['priceinrs'],2);?></td>

				<td align="right" bgcolor="#3366FF"><?php echo number_format($unitsprice,2);?></td>
                <!-- <td align="right"><?php //echo number_format($unitscostprice,2);?></td>-->
				<td align="right" bgcolor="#99CC33"><?php echo number_format($result[$i]['retailprice'],2);?></td>

				<td><?php echo $addedby;?></td>
                
                <td><?php echo $addtime;?></td>

			</tr>

			

			<?php

	/*	$remunits+=$result['unitsremaining'];

		$qty+=$result['quantity'];

		$pprice+=$result['purchaseprice'];

		$priceinrs+=$result['priceinrs'];

		$rprice+=$result['retailprice'];*/

		

		$totalpprice	+=	$result[$i]['quantity']*$result[$i]['purchaseprice'];
        $totalcostprice	+=	$result[$i]['quantity']*$result[$i]['costprice'];
		$totalpriceinrs	+=	$result[$i]['quantity']*$result[$i]['priceinrs'];

		$totalrprice	+=	$result[$i]['quantity']*$result[$i]['retailprice'];

		

		}

	

	?>

	<tr>

			  <td colspan="4" align="right"><b>Total</b></td>
		     <?php if($currencyid!=3){?> <td align="right"><b><?php echo number_format($totalpprice,2);?></b></td><?php }?>
			  <td align="right">&nbsp;</td>
			  <td align="right" bgcolor="#FF3300"><b><?php echo number_format($totalcostprice,2);?></b></td>

			<td align="right">&nbsp;</td>

			  <td align="right" bgcolor="#3366FF"><b><?php echo number_format($totalpriceinrs,2);?></b></td>
              <!-- <td align="right"><b><?php //echo number_format($totalcostprice,2);?></b></td>-->
			  <td align="right"><b><?php //echo number_format($totalrprice,2);?></b></td>

	  </tr>

	<?php

	/*$supplierids	=	array_unique($supplierids);

	//print_r($supplierids);

	foreach($supplierids as $suppid)

	{

		$supids.=$suppid.",";

	}

	$supids	=	trim($supids,',');

	$supplierinfo	=	$AdminDAO->getrows("supplier","companyname","pksupplierid 	IN($supids)");

	for($i=0;$i<count($supplierinfo);$i++)

	{

		$companyname.=$supplierinfo[$i]['companyname'].', ';

	}

	$companyname	=	trim($companyname,', ');*/

	

	//$supplierids	=	 list($supplierids);

	?>

	</table>

	<script language="javascript">

	/*	document.getElementById('supplierdiv').innerHTML="<br><b>Supplier/Agent: <?php //echo $companyname;?></b>";*/

		window.print();

	</script>

<?php

//}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 17/02/2012 //run this block for main or global(main & store)

?>

    <?php 

    

    

    

    function get_shipment_report_data(){

        global $AdminDAO,$id,$shipmentid,$supplierid,$brandid,$productid,$storeid,$to,$from,$status,$idarr;

    

        if($supplierid!=''){

            $where.=" and fksupplierid=$supplierid "	;		

        }if($brandid!=''){

            $where.=" and fkbrandid=$brandid "	;		

        }if($productid!=''){

            $where.=" and fkproductid=$productid "	;		

        }if($from!=''){

            $where.=" and p.purchaseprice >= $from "	;		

        }if($to!=''){

            $where.=" and p.purchaseprice <= $to "	;		

        }if($status=='Received' || $status=='Not Purchased'){

            // received = pkstatusid=7, Not Purchased = pkstatusid=10

            $where.=" and (statusname = '$status') ";

        }if($storeid!=''){

            $where.=" and s.fkstoreid <= $storeid "	;		

        }if($status=='Damages' || $status=='Expired'){		

            $groupby	=	" group by pkpurchaseid ";

            $join 		=	" LEFT JOIN receiving r ON p.pkpurchaseid = r.fkpurchaseid  ";

            $select		=	" ,returnqty, 	damageqty ";

            if($status=='Expired'){

                $where.=" and fkdamagetypeid = '1' "	;

            }

            $where.=" and damageqty > 0 "	;

        }if($status=='Return'){

            $groupby	=	" group by pkpurchaseid ";

            $join 		=	" LEFT JOIN receiving r ON p.pkpurchaseid = r.fkpurchaseid  ";

            $select		=	" , returnqty, damageqty ";

            $where.=" and (fkreturntypeid = '1' or fkreturntypeid = '2') and returnqty > 0 "	;	

        }

        if($status=='Extra'){

            $groupby	=	" ";

            $join 		=	" ";

            $select		=	" , (p.quantity - s.quantity) extra";

            $where		.=	" ";	

        }

        

        

        

        $query	=	"SELECT 

            pkpurchaseid,

            s.barcode,

            s.itemdescription,

            p.quantity,

            p.purchaseprice,

            currencysymbol,

            currencyrate,

            p.weight,

            companyname,

            batch,

            expiry,

            brandname,

            CONCAT(firstname,' ',lastname) addedby,

            productname,

            s.fkstoreid,

            fkstatusid,

            storename,

            statusname

            $select

        FROM

            shiplist as s

            LEFT JOIN purchase p  		ON p.fkshiplistid		=	pkshiplistid 

            LEFT JOIN currency 			ON p.fkcurrencyid		=	pkcurrencyid 

            LEFT JOIN supplier 			ON fksupplierid			=	pksupplierid 

            LEFT JOIN addressbook 		ON p.fkaddressbookid	=	pkaddressbookid

            LEFT JOIN barcode b 		ON pkbarcodeid			=	fkbarcodeid				

            LEFT JOIN product			ON b.fkproductid		=	pkproductid

            LEFT JOIN brand 			ON s.fkbrandid			=	pkbrandid

            LEFT JOIN store 			ON s.fkstoreid			=	pkstoreid

            LEFT JOIN statuses 			ON s.fkstatusid			=	pkstatusid

            $join

        WHERE p.fkshipmentid	=	'$shipmentid'

            $where 

            $groupby";//

        $res	=	mysql_query($query) or die("could not execute query".mysql_error());	

        return $res	;

    }		

    

    ////////////////////////////////////////////////Export to Excel///////////////////////////////////////////////////

        

    

    

    

    

    

        function exprot_XL($data){

            global  $cols;

            $curfilepath	=	"shipment_report.xls";

            $curfile 		= 	"../xl/$curfilepath";

            //$res=get_shipment_report_data();			

            if (!$handle1 = fopen($curfile, 'w+')) {

                     echo "Cannot open file ($curfile)";

                     exit;

            }		

            $somecontent=implode("\t",$cols)."\n";

            // Write $somecontent to our opened file.

            if (!fwrite($handle1, $somecontent)) {

                echo "Cannot write to file ($curfile)";

                //exit;

            }

            foreach($data as $data1){

                $somecontent=implode("\t",$data1)."\n";

                // Write $somecontent to our opened file.

                if (!fwrite($handle1, $somecontent)) {

                    echo "Cannot write to file ($curfile)";

                    //exit;

                }

            }

        }

            ////////////////////////////////////////////////Export to Excel///////////////////////////////////////////////////

    

    ?>

    <link rel="stylesheet" type="text/css" href="../includes/css/style.css">

    <?php include("../includes/security/adminsecurity.php");

    global $AdminDAO;

    $id			=	$_REQUEST['ids'];

    $shipmentid	=	$_REQUEST['shipmentid'];

    $supplierid	=	$_REQUEST['supplier'];

    $brandid	=	$_REQUEST['brand'];

    $productid	=	$_REQUEST['product'];

    $storeid	=	$_REQUEST['source'];

    

    $to			=	$_REQUEST['to'];

    $from		=	$_REQUEST['from'];

    $status		=	$_REQUEST['status'];

    

    $id			=	trim($id,',');

    $idarr		=	explode(',',$id);

    //print_r($idarr);

    //echo"------".sizeof($idarr);

    $newid	=	$idarr[(sizeof($idarr)-1)];

    $shupmentinfo	=	$AdminDAO->getrows("shipment LEFT JOIN currency ON shipmentcurrency=pkcurrencyid","pkshipmentid,currencysymbol,shipmentname,shipmentdate ","pkshipmentid='$shipmentid'");

    $shipmentname	=	$shupmentinfo[0]['shipmentname'];

    $currencyname	=	$shupmentinfo[0]['currencysymbol'];

    $shipmentdate	=	$shupmentinfo[0]['shipmentdate'];

    $pkshipmentid	=	$shupmentinfo[0]['pkshipmentid'];

    

  if($shipmentname!=''){  

    ?>	<table cellpadding="5" cellspacing="5" width="100%">

            <tr>

                <td align="center" style="border:#FFF solid 0px;">
  
                    <b>Shipment Name: <?php echo $shipmentname ." &nbsp;&nbsp;&nbsp; ". "Date: ". date('d-m-Y',$shipmentdate);?></b>

                </td>

            </tr>

        </table>

    

    <?php 
 
	$res=get_shipment_report_data();
	
  
	 ?>
  <table cellpadding="5" cellspacing="5" width="100%">

    <tr>

    <td align="center" style="border:#FFF solid 0px;">

    <table class="simple">

    <tr>

        <th>Barcode</th>

        <th>Item</th>

        <th>Brand</th> 

        <th>Product</th>

        <th>Supplier</th>        

        <th>Batch</th>

        <th>Store</th>

        <th>Status</th>

        <th>Expiry</th>

        <th>Weight</th>

        <th>QTY</th>

        <th>Price</th>

        <?php if($status=='Damages' || $status=='Return' || $status=='Expired'   || $status=='Extra'  ){ ?>

            <th><?php echo $status;?></th>    

        <?php }       

        

        $cols=array("Barcode","Item","Brand","Product","Supplier","Batch"   ,"Store","Status","Expiry","Weight" ,"QTY","Price",$status);

        

        ?>

    </tr>

     <tr>

            <th colspan="13" bgcolor="#FF3300">

                <?php //echo $storename;?>        

            </th>

        </tr>

        <?php

        while($result	=	mysql_fetch_assoc($res))

        {

            $supplierids[]=$result['fksupplierid'];

            $expiry=implode("-",array_reverse(explode("-",$result['expiry'])));

            //$extra=$result['extra'];

            

            $extra=($result['extra']>0)?($result['extra']):0;		

            if($status=='Damages' || $status=='Expired'){ 

                $laststatus	=	$result['damageqty'];    

            }elseif($status=='Return'){ 

                $laststatus	=	$result['returnqty'];                 

            }elseif($status=='Extra'){

                $laststatus	=	$extra;                

            }

            

            $barcode		=$result['barcode'];

            $itemdescription=$result['itemdescription'];

            $brandname		=$result['brandname'];

            $productname	=$result['productname'];

            $companyname	=$result['companyname'];

            $batch			=$result['batch'];

            $storename		=$result['storename'];

            $statusname		=$result['statusname'];

            $expiry			=$expiry;

            $weight			=$result['weight'];

            $quantity		=$result['quantity'];      

            $purchaseprice	=$result['purchaseprice'];

            //$currencyname	=$result['currencyname'];

            $price			=$currencyname."&nbsp;".$result['purchaseprice'];

            

            $data[]=array("$barcode","$itemdescription","$brandname","$productname","$companyname","$Batch"   ,"$storename","$statusname","$expiry","$weight" ,"$quantity","$price",$laststatus);

            

            ?>

            <tr>

                <td><?php echo $barcode;?></td>

                <td><?php echo $itemdescription;?></td>

                <td><?php echo $brandname;?></td>

                <td><?php echo $productname;?></td>

                <td><?php echo $companyname;?></td>

                <td><?php echo $batch;?></td>

                <td><?php echo $storename;?></td>

                <td><?php echo $statusname;?></td>

                <td><?php echo $expiry;?></td>

                <td><?php echo $weight;?></td>

                <td><?php echo $quantity;?></td>    

                          

                <td><?php echo $price;?></td>

                <?php if($laststatus){?>

                    <td><?php echo $laststatus;?></td>

                <?php }?>

            </tr>

            

            <?php

            $totalweight		+=	$weight;	

            $totalprice			+=	$purchaseprice;

            $totalquantity		+=	$quantity;

            $tlaststatus		+=	$laststatus;

        }

    ?>

        <tr>

          <td colspan="9"><b>Total</b></td>          

          <td><b><?php echo $totalweight;?></b></td>

          <td><b><?php echo $totalquantity;?></b></td>

          <td><b><?php echo $currencyname."&nbsp;".$totalprice;?></b></td>   

           <?php $data[]=array("","","","","","","","","","$totalweight","$totalquantity","$price",$tlaststatus);

              exprot_XL($data);

            if($tlaststatus){?> 

          <td><b><?php echo $tlaststatus;?></b></td> 

          <?php }?>  

        </tr>

        

        <tr>

          <td colspan="13">

          

              

        <div class="buttons">

            <button type="button" class="positive" onclick="print_report();"> 

                <img src="../images/tick.png" alt=""/>

                <?php {echo "Print";} ?>

            </button>

            <button type="button" class="positive" onclick="emailnow();"> 

                <img src="../images/tick.png" alt=""/>

                <?php {echo "Email";} ?>

            </button>

            <a href="../xl/shipment_report.xls" target="_blank"  onclick="javascript:void(0);" >Export Excel</a>

        </div>      

              

              

              </td>          

            </tr>

            

    <?php /*$supplierids	=	array_unique($supplierids);//print_r($supplierids);foreach($supplierids as $suppid){	$supids.=$suppid.",";}$supids	=	trim($supids,',');$supplierinfo	=	$AdminDAO->getrows("supplier","companyname","pksupplierid 	IN($supids)");for($i=0;$i<count($supplierinfo);$i++){	$companyname.=$supplierinfo[$i]['companyname'].', ';}$companyname	=	trim($companyname,', ');*///$supplierids	=	 list($supplierids);?>

    </table>

    </td>

    </tr>

    <tr>

    <td align="center" style="border:#FFF solid 0px;">

    

    <div id="emaildiv" style="display:none; ">

        <form name="emalfrm" id="emailfrm" method="post" action="">

          <table  class="simple" cellpadding="5" cellspacing="5" width="50%" border="0">

              <tr>

                <td height="22" colspan="2"><strong>Email This Report </strong>

                <div style="float:right">

                <span class="buttons">

                    <button type="button" class="positive" onclick="sendemail(-1);">

                        <img src="../images/email_go.png" alt=""/> 

                       Send

                    </button>

                     <a href="javascript:void(0);" onclick="hideclass('emaildiv');" class="negative">

                        <img src="../images/cross.png" alt=""/>

                        Cancel

                    </a>

                </span>

                </div>

                </td>

            </tr>

              <tr>

                <td width="127">From</td>

                <td width="599" valign="middle"><br />

                <?php 	$aid			=		$_SESSION['addressbookid'];

                    $sql="select 

                                pkaddressbookid,CONCAT(a.firstname,' ',a.lastname) as name,

                                 a.email

                                from 

                                    addressbook a

                                where pkaddressbookid='$aid'

                                

                                ";

                $addarray	=	$AdminDAO->queryresult($sql);

                //print_r($addarray);

                $ename				=	$addarray[0]['name'];

                $email				=	$addarray[0]['email'];

                ?>

                <input name="fromemails" type="text" id="fromemails"  size="58" readonly="readonly" value="<?php print "$ename <$email>";?>"/></td>

            </tr>

              <tr>

                <td>To</td>

                <td valign="middle">

                <select name="tolist" id="tolist" onchange="toemail('tolist')" style="width:375px;">

                    <?php

                    $sql="select 

                                pkaddressbookid,CONCAT(a.firstname,' ',a.lastname) as name,

                                 a.email

                                from 

                                    addressbook a

                                ";

                        $addarray	=	$AdminDAO->queryresult($sql);

                    for($a=0;$a<count($addarray);$a++)

                    {

                        $pkaddressbookid	=	$addarray[$a]['pkaddressbookid'];

                        $ename				=	$addarray[$a]['name'];

                        $email				=	$addarray[$a]['email'];

                    ?>

                    <option value="<?php echo $email;?>"><?php echo $ename;?></option>

                    <?php

                    }

                    ?>

                </select><br />

                <textarea name="toemails" id="toemails" cols="65" rows="1"></textarea></td>

            </tr>

            <tr>

                <td>Cc</td>

                <td><textarea name="ccemails" id="ccemails" cols="65" rows="1"></textarea></td>

            </tr>

            <tr>

                <td>Bcc</td>

                <td><textarea name="bccemails" id="bccemails" cols="65" rows="1"></textarea></td>

            </tr>

            <tr>

                <td>Subject</td>

                <td><input type="text" name="subject" id="subject" size="58" ></td>

            </tr>

            <tr>

                <td>Message</td>

                <td><textarea name="message" id="message" cols="65" rows="8"></textarea></td>

            </tr>

              <tr>

                <td colspan="2">

                             <div style="float:left">

                <span class="buttons">

                    <button type="button" class="positive" onclick="sendemail(-1);">

                        <img src="../images/email_go.png" alt=""/> 

                       Send

                    </button>

                     <a href="javascript:void(0);" onclick="hideclass('emaildiv');" class="negative">

                        <img src="../images/cross.png" alt=""/>

                        Cancel

                    </a>

                </span>

                </div>

                </td>

            </tr>

          </table>

        <input type="hidden" value="" name="mailtext" id="mailtext" />

        </form>

    </div>

    

    

    </td>

    </tr>

    </table>



    <p>
<?php //}//end edit?>
<?php 
 }
////////////////////////////////////ad by wajid from line 1117 to 1197/////////////////////////////////////

 $query2 	= 	"SELECT 
				pkreturnid,
				barcode,
				itemdescription,
				r.quantity qty,
				s.quantity quantity,
				s.unitsremaining,
				returntype,
				IF(returnstatus='p','Pending','Confirmed') status
			FROM 
				$dbname_detail.returns r,$dbname_detail.stock s,returntype,barcode
			WHERE
				r.fkstockid				=	pkstockid AND
				r.fkreturntypeid		=	pkreturntypeid AND
				fkbarcodeid				=	pkbarcodeid AND
				s.fksupplierinvoiceid	=	'$newid' and r.issclose=1
			";

$reportresult2		=	$AdminDAO->queryresult($query2);
if(sizeof($reportresult2)>0)
{
?>
    </p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <table style="font-size:12px;font-family:Arial, Helvetica, sans-serif;padding:5px;">
      <tr>
        <th>Sr #</th>
        <th>Barcode</th>
        <th>Item Description</th>
        <th>Total Units</th>
        <th>Returned</th>
        <th>Remaining</th>
        <th>Reasons</th>
        <th>Status</th>
      </tr>
      <?php
 // $totalamut=0;
  for($i=0;$i<sizeof($reportresult2);$i++)
  {
	  //fetching recordsarray("pkreturnid","barcode","itemdescription","quantity","qty","unitsremaining","returntype","status");
	  $barcode		=	$reportresult2[$i]['barcode'];
	  $itemdescription			=	$reportresult2[$i]['itemdescription'];
	  $quantity		=	$reportresult2[$i]['quantity'];
	  $qty	=	$reportresult2[$i]['qty'];
	  $returntype	=	$reportresult2[$i]['returntype'];
	  $unitsremaining	=	$reportresult2[$i]['unitsremaining'];
	  $status=	$reportresult2[$i]['status'];
	  // $totalamut+=$purchaseprice;
/*	  
	  if($reportresult[$i]['invoice_status'] =='0')
	  {
		  $status = "Open";
		  }
		  else if($reportresult[$i]['invoice_status'] =='1')
		  {
			   $status = "Close";
			  }
			   else if($reportresult[$i]['invoice_status'] =='2')
			   {
				      $status = "Void";
				   }*/
	 
  ?>
      <tr>
        <td><?php echo $i+1;?></td>
        <td><?php echo $barcode; ?></td>
        <td align="center"><?php echo $itemdescription;?></td>
        <td align="right"><?php echo  $quantity;?></td>
        <td align="right"><?php echo  $qty;?></td>
        <td align="right"><?php echo $unitsremaining;?></td>
        <td align="left"><?php echo $returntype;?></td>
        <td align="left"><?php echo $status;?></td>
      </tr>
      <?php
  }// end for
  ?>
    
    </table>
    <?php } ?>
    <p>&nbsp; </p>
	<script language="javascript">

    

        function exportxl()

        {

            var wid, hig, qrystr, msg=	'';

            var shipmentid		=	document.getElementById('shipmentid').value;

            var param			=	document.getElementById('param').value;

            var paramid			=	document.getElementById('paramid').value;

            

            if(param=='price'){		

                var to			=	document.getElementById('to').value;

                var from		=	paramid;

                if(to==''){

                    msg+="Please enter to.\n";	

                }if(from==''){

                    msg+="Please enter from.\n";	

                }if(msg!=''){

                    alert(msg);	

                    return false;

                }

                qrystr			=	'to='+to+'&from='+from;	

            }else{				

                qrystr			=	param+'='+paramid;

            }

            

            

            var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';

            window.open('supplierreport.php?'+qrystr+'&shipmentid='+shipmentid ,'Shipment Report by Supplier',display); 	 

        }

        function emailnow()

        {

            document.getElementById('emaildiv').style.display='block';

            //document.getElementById('tolist').focus();

        }

        function print_report(){

            document.getElementById('emaildiv').style.display='none';

           window.print();

        }

    </script>
	</form> <!--end form-->
<?php 
//////////////////////add by wajid for excel export/////////////////////////
echo $exporactions;
//////////////////////////////////////////////////////////////////////////