<?php

include_once("../includes/security/adminsecurity.php");

global $AdminDAO;

$Location=$_GET['loc'];
if($Location==4){
	
	echo file_get_contents("https://warehouse.esajee.com/admin/accounts/stockW.php?sdate=".$_REQUEST['sdate']."&edate=".$_REQUEST['edate']."&type=".$_REQUEST['type']);

exit;
	}
	if($Location==2){
	
	echo file_get_contents("https://gulberg.esajee.com/admin/accounts/stockG.php?sdate=".$_REQUEST['sdate']."&edate=".$_REQUEST['edate']."&type=".$_REQUEST['type']);

exit;
	}
if($Location==1){
	
	echo file_get_contents("https://dha.esajee.com/admin/accounts/stockD.php?sdate=".$_REQUEST['sdate']."&edate=".$_REQUEST['edate']."&type=".$_REQUEST['type']);

exit;
	}

/*************************DATE CHECKS**************************/

/*echo $sdate				=	strtotime($_GET['sdate'].'00:00:00'); 

echo "<br>";

echo $edate				=	strtotime($_GET['edate']."23:59:59");



/*$arr_from 	=	explode('-',$_GET['sdate']);

$fromdate	=	mktime(0,0,0,$arr_from[1],$arr_from[0],$arr_from[2]);



$arr_to	 	=	explode('-',$_GET['edate']);

$todate		=	mktime(23,59,59,$arr_to[1],$arr_to[0],$arr_to[2]);

echo "<br>";



echo "<br>";

echo $_GET['bname'];

echo "<br>";

echo $_GET['cname'];

echo "<br>";

echo $_GET['itemname'];

echo "<br>";



echo $prd_id	=	trim($_GET['prdnam'],',');


echo "<pre>";
print_r($_GET);
*///exit;

$sdate = strtotime($_GET['sdate'].'00:00:00'); 

$edate = strtotime($_GET['edate']."23:59:59");

//////////////////////////////////////////////////////////////////////////////////////



$Brand=$_GET['bname'];

/////////////////////////////////////////////////////////////////////////////////////



/*$arr_from 	=	explode('-',$_GET['sdate']);

$fromdate	=	mktime(0,0,0,$arr_from[1],$arr_from[0],$arr_from[2]);



$arr_to	 	=	explode('-',$_GET['edate']);

$todate		=	mktime(23,59,59,$arr_to[1],$arr_to[0],$arr_to[2]);*/




$Brand	=	trim($_GET['bname'],',');
$product	=	trim($_GET['prdnam'],',');
if($_GET['type']=='brand'){
  $query		=	"SELECT st.updatetime,brd.brandname as pname,sum(st.quantity) quantity ,st.price from $dbname_detail.stockmonitor st left join main.brand brd on (brand_id=pkbrandid)   where st.updatetime BETWEEN $sdate AND $edate and brand_id>0 group by brand_id  ";
$rpt="Brand Wise";

}else{
$query		=	"SELECT st.updatetime,brd.productname as pname,sum(st.quantity) quantity ,st.price  from $dbname_detail.stockmonitor st left join main.product brd on (product_id=pkproductid)   where st.updatetime BETWEEN $sdate AND $edate and product_id>0  group by product_id  ";	
	
	$rpt="Product Wise";
	}
$reportresult		=	$AdminDAO->queryresult($query);

$reportresult_sizeof = sizeof($reportresult);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>

<title>Stock Report</title>

<link rel="stylesheet" type="text/css" href="../includes/css/style.css" />

</head>

<body>

<div style="width:8.0in;padding:0px;font-size:17px;font-family:Arial, Helvetica, sans-serif; padding-left:230px;" align="left"><b>ESAJEE'S</b>

</div>

<div style="width:8.0in;font-size:11px;font-family:Comic Sans MS, cursive;padding-left:200px;" align="left"><b>Think globally shop locally</b></div><br />

<div style="width:8.0in;font-size:12px;padding-left:10px;" align="left"><br />

<br />

<?php echo $rpt;?> Stock Report <span style="font-weight:bold;"><?php if($reportresult[0]['pname']){echo ' of '.$reportresult[0]['pname'];}?> </span> AT  <span style="font-weight:bold;"><?php 			echo "Esajee &amp; Co. Kohsar Market";


 ?> </span></div>

<br />

<table style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;" class="simple">

  <tr>

    <td colspan="2">Date: <?php echo date('d-m-Y',time());?></td>

  </tr>

  <tr>

    <td width="88">From: <?php echo $_GET['sdate'];?></td>

    <td width="117">To: <?php echo $_GET['edate'];?></td>

  </tr>

</table>

<table width="558" class="simple" style="font-size:12px;margin-top:5px;font-family:Arial, Helvetica, sans-serif;padding:5px;">

  <tr>

  	<th width="83">Sr No</th>

    <th width="224"><?php if($Brand!=''){?>Brand<?php }?><?php if($Item!=''){?>Item<?php }?><?php if($prd_id!=''){?>Product<?php }?><?php if($Contry!=''){?>Country<?php }?> <?php if($high!=''){?>Item <?php }?><?php if($low!=''){?>Item <?php }?><?php if($high2!=''){?>Item <?php }?>Name</th>

    <th width="175">Stock</th>

    <th width="247">price (RS)</th>
    <th width="247">Value</th>

    </tr>

  <?php

  $quqn1=0;

  $quqn2=0;
  $quqn3=0;

for($i=0;$i<sizeof($reportresult);$i++)

{

	

	$Product_name	=	$reportresult[$i]['pname'];	

	

	$Quantity	=	$reportresult[$i]['quantity'];

	$Prc	=	$reportresult[$i]['price'];
	$Tval	=($Quantity*$Prc)	;

	//if($Quantity > 0 and $Prc > 0){

		$quqn1+=$Quantity;

		$quqn2+=$Prc;
        $quqn3+=$Tval;
		?>

  <tr>

    <td ><?php echo $i+1;?></td>

    <td align="left"><?php echo $Product_name; ?></td>

    <td align="left"><?php echo $Quantity; ?></td>

    <td align="left"><?php echo $Prc; ?></td>
    <td align="left"><?php echo $Tval; ?></td>

  </tr>

 

 <?php 

	//}

 }?>

 <tr>

<td colspan="2" align="right"><strong>Total</strong></td>

<td align="left"><b><?php echo $quqn1; ?></b></td>

<td align="left"><b><?php echo number_format($quqn2,2); ?></b></td>


<td align="left"><b><?php echo number_format($quqn3,2); ?></b></td>

</tr>
</table>
</body>
</html>