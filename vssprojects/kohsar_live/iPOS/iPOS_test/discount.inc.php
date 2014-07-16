<?php
session_start();
include_once("includes/security/adminsecurity.php");
include_once("includes/classes/discount.class.php");
global $AdminDAO;
$saleid	=	$_SESSION['tempsaleid'];
// step 1 retrieve products
if($saleid)
{//changed $dbname_main to $dbname_detail on line 10 by ahsan 22/02/2012
	$salerows	=	$AdminDAO->getrows("$dbname_detail.saledetail,$dbname_detail.stock,barcode","pkbarcodeid,barcode,itemdescription,pksaledetailid,fkstockid,saleprice, sum( saledetail.quantity ) AS quantity"," fksaleid='$saleid' AND fkstockid=pkstockid AND fkbarcodeid=pkbarcodeid AND fkdiscountid=0 group by fkstockid,saleprice ORDER BY timestamp DESC");
	if(sizeof($salerows)>0)
	{
		//fetch sale total for Amount on Amount //changed $dbname_main to $dbname_detail on line 14 by ahsan 22/02/2012
		$saletotalres	=	$AdminDAO->getrows("$dbname_detail.saledetail","sum(saleprice*quantity) total","fksaleid='$saleid' AND fkdiscountid=0");
		$subtotal		=	$saletotalres[0]['total'];
		$table			=	'<table width="300" align="left" class="epos"><tr><th>Discounted Item</th><th>Free Item</th><th>Discount Effect</th></tr>';
		$set	=	0;
        for($i=0;$i<sizeof($salerows);$i++)
        {
            $fkbarcodeid	=	$salerows[$i]['pkbarcodeid'];
            $barcode		=	$salerows[$i]['barcode'];
            $item			=	$salerows[$i]['itemdescription'];
            $quantity		=	$salerows[$i]['quantity'];
            $price			=	$salerows[$i]['saleprice'];
            //$subtotal		=	$salerows[$i]['subtotal'];
            $disc			=	"disc".$i;
            // step 2 fetch total amount
            $$disc			=	new Discount($AdminDAO);
            $disc			=	$$disc;
            $disc->setDiscount($fkbarcodeid,$quantity,$price,$subtotal,$storeid);
			if($disc->PoP || $disc->QoQ || $disc->AoQ || $disc->AoA && $set==0)
			{
				$set	=	1;
				//echo $table;
			}
            for($pro=0;$pro<sizeof($disc->PoP);$pro++)
            {
                $bcid		=	$disc->PoP[$pro]['pkbarcodeid'];
                //selecting free product
                $itemdesc	=	$AdminDAO->getrows("barcode","itemdescription","pkbarcodeid='$bcid'");
                $newdesc	=	$itemdesc[0]['itemdescription'];
                
                $table	.=	'<tr class="rowcolor3"><td>PoP:'.$item.'['.$barcode.']</td><td>'.$newdesc.'</td><td>'.$disc->PoP[$pro]['qty'].'</td></tr>';//." ".$disc->PPstat['combine']." ".$disc->PPstat['priority'];
                
                // setting up values for use in product on product
                $product[]['pkbarcodeid']	=	$bcid;
                $qty[]['quantity']			=	$disc->PoP[$pro]['qty'];
                $type[]['type']				=	$disc->PPstat['discountid'];
            }
            // all other discount types
            if($disc->QoQ)
            {
            
            $table	.=	'<tr class="rowcolor3"><td>QoQ:'.$item.'['.$barcode.']</td><td></td><td>Quantity '.$disc->QoQ.' free</td></tr>'; //echo $disc->QQstat['combine']." ".$disc->QQstat['priority'];
            
                $product[]['pkbarcodeid']	=	$disc->QQstat['pkbarcodeid'];
                $qty[]['quantity']			=	$disc->QoQ;
                $type[]['type']				=	$disc->QQstat['discountid'];
            }
            if($disc->AoQ)
            {
            
            $table	.=	'<tr class="rowcolor3"><td>AoQ:'.$item.'['.$barcode.']</td><td></td><td>Rs. '.$disc->AoQ.' Off</td></tr>'; //echo $disc->AQstat['combine']." ".$disc->AQstat['priority'];
            
            $totalamount['amountoff'][]	=	$disc->AoQ;
            $totalamount['type'][]	=	2;
            }
        }
        if($disc->AoA)
        {
        
        $table	.=	'<tr class="rowcolor3"><td>AoA:'.$item.'['.$barcode.']</td><td></td><td>Rs. '.$disc->AoA.' Off</td></tr>';//echo $disc->AAstat['combine']." ".$disc->AAstat['priority'];
        
        $totalamount['amountoff'][]	=	$disc->AoA;
        $totalamount['type'][]	=	3;
        }
        $totaloff	=	@array_sum($totalamount['amountoff']);
        //calling discount implementation method 1 adding products in sales
        $disc->addPopSales($product,$dbname_detail,$saleid,$qty,$closingsession,$type);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
        //calling discount implementation method 2 adding amounts in sales
        $disc->addAoqSales($totalamount,$totaloff,$saleid,$dbname_detail,$closingsession);//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
    }
    
	$table	.=	'</table>'; ?>
  <script>
	document.getElementById('discount_details').innerHTML	=	'<?php echo $table  ?>';
  </script>  
<?php    
}
?>