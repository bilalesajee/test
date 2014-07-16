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
/************* DUMMY SET ***************/
$labels = array("ID","Customer Name","Email","Company","Address","Phone","NIC","NTN");
$fields = array("pkcustomerid","name","email","title","address","phone","nic","ntn");

$dest 	= 	'customers.php';
$div	=	'mainpanel';
$form 	= 	"frm1cutomers";	
$css 	= 	'<link rel="stylesheet" type="text/css" href="includes/css/style.css">';
$jsrc 	= 	'<script language="javascript" src="includes/js/common.js"></script><script language="javascript" src="../includes/js/jquery.js"></script><script src="includes/js/jquery.form.js" type="text/javascript"></script>';
define(IMGPATH,'images/');
//***********************sql for record set**************************//changed $dbname_main to $dbname_detail on line 33 by ahsan 22/02/2012
  $query 	= 	"SELECT 
					id as pkcustomerid,
					CONCAT(firstname,' ',lastname) as name,
					CONCAT(address1,' ',address2) as address,
					email,
					title,					
					taxnumber,
					ntn,
					CONCAT(phone ,' ',mobile) as phone,
					nic
        		FROM
					$dbname_detail.account c LEFT JOIN $dbname_detail.addressbook  ON (c.fkaddressbookid = pkaddressbookid)
				WHERE
					isdeleted <> 1  
				GROUP BY 
					id
					";
// added Collection Details by Yasir - 20-07-11  

$navbtn	=	"<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addnewcoutomer.php','childdiv','mainpanel')\" title='Add New Customer'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addnewcoutomer.php','childdiv','mainpanel','') title=\"Edit Customer\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			
			
			
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'collections.php','childdiv','mainpanel','') title=\"Collections\"><span class=\"\">Collections</span></a>&nbsp;
			
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'customercollections.php','childdiv','mainpanel','') title=\"Customer Collection Details\"><span class=\"\">Collection Details</span></a>&nbsp;
			
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'customersaccount.php','childdiv','mainpanel','') title=\"Collections\"><span class=\"\">Customer Account</span></a>&nbsp;	
			
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'customerbilling.php','childdiv','mainpanel','') title=\"Customer Bill Details\"><span class=\"\">Customer Bills</span></a>&nbsp;
			
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'demands.php','childdiv','mainpanel','') title=\"Demands\"><span class=\"\">Demands</span></a>&nbsp;			
		
		<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddemand.php','childdiv','mainpanel','') title=\"Add Demands\"><span class=\"\">Add Demand</span></a>&nbsp;					
			";
			$navbtn .=	"<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:printgrid('Customers')\" title='Print'>
				<span class='print'>Print</span>
			</a>&nbsp;";
?>
<div id="collections"></div>
<div id="childdiv" style="clear:both;float:left;width:100%;"></div>
<div id="mainpanel" style="clear:both;float:left;width:100%;">
  <?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<!--Added by Yasir - 08-07-11-->
<script language="javascript" type="text/javascript">
	document.getElementById('searchFieldmainpanel').focus();	
</script>