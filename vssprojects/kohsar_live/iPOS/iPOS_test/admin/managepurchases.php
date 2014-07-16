<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO;

if($_REQUEST['oper']=='del'){
	echo "<pre>";
		//print_r($_POST);
	echo "</pre>";
	echo "called";
}
	//exit;

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
$dest 	= 	'managepurchases.php';
$div	=	'subsection';
$form 	= 	"purchasefrm";
$query	=	"SELECT 
				pkpurchaseid,
				barcode,
				itemdescription,
				p.quantity,
				concat(currencysymbol ,' ', p.purchaseprice) purchaseprice,
				currencyrate,
				p.weight,
				companyname,
				batch,
				expiry,
				CONCAT(firstname,' ',lastname) addedby
			FROM
				shiplist,purchase p LEFT JOIN currency ON (fkcurrencyid	=	pkcurrencyid) LEFT JOIN supplier ON (fksupplierid	=	pksupplierid), addressbook
			WHERE
				p.fkshiplistid	=	pkshiplistid AND
				p.fkaddressbookid	=	pkaddressbookid AND
				p.fkshipmentid	=	'$id'
			";
$navbtn	="<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addpurchase.php','eidtpurchase','subsection','$param') \" title=\"Edit Purchase\"><span class=\"editrecord\">&nbsp;</span></a>";
//$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('updatepurchase.php','subsection','$_SESSION[qs]') title=\"Delete purchase\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";

$labels	=	array('ID','Barcode','Item','Quantity','Purchase Price','Weight','Supplier','Batch','Expiry','Added By');
$fields	=	array('pkpurchaseid','barcode','itemdescription','quantity','purchaseprice','weight','companyname','batch','expiry','addedby');
/**** end grid section */

?>

<div id="eidtpurchase"></div>
<div id="<?php echo $div;?>">	
	<div class="breadcrumbs" id="breadcrumbs">Purchased Items</div>
    
	<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
</div>`