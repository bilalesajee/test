<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;
if($_REQUEST['oper']=='del'){
	echo "<pre>";
		//print_r($_POST);
	echo "</pre>";
}
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
$qstring			=	$_SESSION['qstring'];
// purhcase status
if($liststatusid==1)
{
	$sel1	=	"selected=\"selected\"";
	$status	=	" HAVING quantity>qty";
}
else if($liststatusid==2)
{
	$sel2	=	"selected=\"selected\"";
	$status	=	" HAVING quantity=qty";
}
else if($liststatusid==3)
{
	$sel3	=	"selected=\"selected\"";
	$status	=	" HAVING quantity<qty";
}
$purchasestatus	=	"<select name=\"purchasestatus\" id=\"purchasestatus\" style=\"width:100px;\" onchange=\"getpurchasestatus(this.value)\"><option value=\"\">All</option><option value=\"1\" $sel1>Pending</option><option value=\"2\" $sel2>Purchased</option><option value=\"3\" $sel3>More Purchased</option></select>";
// end purchase status
$dest 	= 	'orderpurchases.php';
$div	=	'subsection';
$form 	= 	"purchasefrm";
$query	=	"SELECT 
				pkorderpurchaseid,
				barcode,
				itemdescription,
				o.quantity orderquantity,
				p.quantity,
				concat(currencysymbol ,' ', p.purchaseprice) purchaseprice,
				exchangerate,
				p.weight,
				companyname,
				batch,
				DATE_FORMAT(expiry,'%d-%m-%Y') expiry,
				CONCAT(firstname,' ',lastname) addedby
			FROM
				`order` o,orderpurchase p LEFT JOIN supplier ON (fksupplierid	=	pksupplierid),shipment LEFT JOIN currency ON (shipmentcurrency	=	pkcurrencyid) , addressbook
			WHERE
				p.fkorderid	=	pkorderid AND
				p.fkaddressbookid	=	pkaddressbookid AND
				p.fkshipmentid	=	pkshipmentid AND
				p.fkshipmentid	=	'$id'
			";
$navbtn	="<a href=\"javascript: javascript:showpage(1,document.$form.checks,'orderaddpurchase.php','editpurchase','subsection','$param','$formtype') \" title=\"Edit Purchase\"><span class=\"editrecord\">&nbsp;</span></a>";
$labels	=	array('ID','Barcode','Item','Ordered Quantity','Purchased','Purchase Price','Weight','Supplier','Batch','Expiry','Added By');
$fields	=	array('pkorderpurchaseid','barcode','itemdescription','orderquantity','quantity','purchaseprice','weight','companyname','batch','expiry','addedby');
/**** end grid section */

?>
<div id="editpurchase"></div>
<div id="<?php echo $div;?>">	
	<div class="breadcrumbs" id="breadcrumbs">Purchased Items</div>
    
	<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
</div>`