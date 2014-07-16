<?php
include("includes/security/adminsecurity.php");
include_once("includes/classes/bill.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$Bill;
$Bill		=	new Bill($AdminDAO);
$rights	 	=	$userSecurity->getRights(8);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$lid	=	$_REQUEST['loc'];
if($lid!='')
{
	if($lid==6){
		$loc_and	=	" ";
		}else{
 $loc_and	=	" AND location='$lid' ";	
		}
}else{
	$loc_and=" AND location=3 ";
	
	}
/************* DUMMY SET ***************/
$labels = array("ID","Loc","Customer Name ","Email","Address","Phone","NIC","Balance Amount","Status");
$fields = array("pkcustomerid","location","name","email","address","phone","nic","balance","customer_status");

$dest 	= 	'customers.php';
$div	=	'mainpanel';
$form 	= 	"frm1cutomers";	
$css 	= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 	= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************//changed $dbname_main to $dbname_detail on line 33 by ahsan 22/02/2012 
$query 	= 	"SELECT 
					pkcustomerid,
					IF(location=1,'DHA',IF(location=2,'Gulberg',IF(location=3,'Kohsar','Pharma')))    location,
					CONCAT(firstname,' ', lastname) name,
					CONCAT(address1,' ',address2) as address,
					email,
					taxnumber,
					IF(isdeleted=1,'Deactive','Active') customer_status,
					CONCAT(phone ,' ',mobile) as phone,
					nic,CONCAT( '<span class=\"balance\" id=\"',pkcustomerid,'\" >Show Balance </span>') as balance
        		FROM
					customer
				WHERE
					 ctype in (2) $loc_and ";

$locations			=	"<select name=\"group\" id=\"group\" style=\"width:100px;\" onchange=\"listusers(this.value)\"><option value=\"6\">All</option>";
$listlocs	=	$AdminDAO->getrows("store","*");
// pkgroupid 	groupname
for($i=0;$i<sizeof($listlocs);$i++)
{
	$locid	=	$listlocs[$i]['pkstoreid'];
	$groupname		=	$listlocs[$i]['storename'];
	$select		=	"";
	if($lid==''){
		$lid=3;
		}
	if($locid == $lid)
	{
		$select = "selected=\"selected\"";
	}
	$locations	.=	"<option value=\"$locid\" $select>$groupname</option>";
}
$locations			.=	"</select>";
// added Collection Details by Yasir - 20-07-11  
//$orderby = "name desc";

$navbtn	=	"<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addnewcoutomer.php','childdiv','mainpanel')\" title='Add New Customer'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addnewcoutomer.php','childdiv','mainpanel','') title=\"Edit Customer\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";			
			
				$navbtn .=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'collections.php','childdiv','mainpanel','') title=\"Collections\"><span class=\"\">Collections</span></a>&nbsp;";
			
			/*<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'customercollections.php','childdiv','mainpanel','') title=\"Customer Collection Details\"><span class=\"\">Collection Details</span></a>&nbsp;";*/

					
		//$navbtn .=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'customersaccount.php','childdiv','mainpanel','') title=\"Collections\"><span class=\"\">Customer Account</span></a>&nbsp;	";
			
		$navbtn .=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'customerbilling.php','childdiv','mainpanel','') title=\"Customer Bill Details\"><span class=\"\">Customer Bills</span></a>&nbsp;
			
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'demands.php','childdiv','mainpanel','') title=\"Demands\"><span class=\"\">Demands</span></a>&nbsp;			
		
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'adddemand.php','childdiv','mainpanel','') title=\"Add Demands\"><span class=\"\">Add Demand</span></a>&nbsp;					
			";
			$navbtn .=	"<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:printgrid('Customers')\" title='Print'>
				<span class='print'>Print</span>
			</a>&nbsp;";
			
?>
<div id="collections"></div>
<div id="childdiv" style="clear:both;float:left;width:100%;"></div>
<div id="mainpanel" style="clear:both;float:left;width:100%;">
<b> Select Location </b> <?php echo $locations;?>
<style type="text/css">
.balance
{
	cursor:pointer;
	text-align:right;
	float:right;
}
</style>
  <?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
		
	?>
    
</div>
<!--Added by Yasir - 08-07-11-->
<script language="javascript" type="text/javascript">

	document.getElementById('searchFieldmainpanel').focus();	
	function listusers(groupid)
	{
		jQuery('#mainpanel').load('customers.php?loc='+groupid);	
	}
	function show_balance()
	{
		var customer = (this).id;
		
		$('#'+customer).load('customer_balance.php?customer='+customer);
		
	}
	
	$('.balance').click(show_balance);

	
</script>