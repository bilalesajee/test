<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
if($_SESSION['siteconfig']!=1){//edit by ahsan 16/02/2012, added if condition
	?>
	<script language="javascript" type="text/javascript">
	/*function print_item_summary_report()
	{
	var wid				=	800;
	var hig				=	600;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	window.open('itemsummaryreport.php','mywin',display);
	}*/
	function printlist()
	{
		var sel	=	getselected('maindiv');
		var sb;
		if (sel.length > 1)
		{
			for (i=1; i < sel.length; i++)
			{
				 sb	=	sel[i];
			} 
			var sb1	=	sb.split('maindiv');
			var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
		window.open('itemsummaryreport.php?id='+sb1,'mywin',display); 
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			
		}
	}
	function showitemhistory()
	{
		var sel	=	getselected('maindiv');
		var sb;
		if (sel.length > 1)
		{
			for (i=1; i < sel.length; i++)
			{
				 sb	=	sel[i];
			} 
			var sb1	=	sb.split('maindiv');
			var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
		window.open('itemhistory.php?id='+sb1,'mywin',display); 
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			
		}
	}
	function pricechangehistory()
	{
		var sel	=	getselected('maindiv');
		var sb;
		if (sel.length > 1)
		{
			for (i=1; i < sel.length; i++)
			{
				 sb	=	sel[i];
			} 
			var sb1	=	sb.split('maindiv');
			var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=650,height=600,left=100,top=25';
		window.open('pricechangehistory.php?id='+sb1,'mywin',display); 
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			
		}
	}

function bill_prev(){

	var sel	=	getselected('maindiv');

		var sb;

		if (sel.length > 1)

		{

			for (i=1; i < sel.length; i++)

			{

				 sb	=	sel[i];

			} 

			var sb1	=	sb.split('maindiv');

	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=300,height=400,left=100,top=25';

 	window.open('bill_prev.php?ids='+sb1,'Preview',display); 

}

}
	</script>
	
	<?php
	global $AdminDAO,$Component,$userSecurity;
	$rights	 	=	$userSecurity->getRights(49);
	//$labels	 	=	$rights['labels'];
	//$fields		=	$rights['fields'];
	$actions 	=	$rights['actions'];
	//print_r($rights);
	/************* DUMMY SET ***************/
	$labels = array("ID","Item Name","Barcode");
	$fields = array("pkbarcodeid","itemdescription","barcode");
	$dest 	= 	'manageitems.php';
	$div	=	'maindiv';
	$form 	= 	"itemsfrm";	
	define(IMGPATH,'../images/');
	$query 	= 	"SELECT 
					pkbarcodeid,
					barcode,
					itemdescription
				FROM
					barcode
				";
	$navbtn	=	"";
	//$sortorder	=	"pkbarcodeid DESC"; // takes field name and field order e.g. brandname DESC
	/********** END DUMMY SET ***************/
	//********ITEMS HISTORY*************************/
	if(in_array('106',$actions))
	{
		
	$navbtn .="	
		<a href=\"javascript: showitemhistory('')\" title='Item History '><span><b>Item History Report</b></span></a>";		
				
	}//if
	if(in_array('107',$actions))
	{
		$navbtn .=" | <a href=\"javascript: printlist('')\" title='Item Summary Report'><span><b>Item Summary Report</b></span></a>";	
				
	}
	if(in_array('109',$actions))
	{
		$navbtn .=" | <a href=\"javascript:pricechangehistory('')\" title='Price Change History'><span><b>Price Change History</b></span></a>";	
				
	}
	$navbtn .="&nbsp;<a href=\"javascript: bill_prev('')\" title=\"Purchase Return Report\"><b> Bill Preview </b></a>";
	?>
	<div id='maindiv'>
	<div id="sugrid"></div>
	<div class="breadcrumbs" id="breadcrumbs">Items</div>
	<?php 
	//$button->makebutton("All Attributes","javascript: showpage(0,'','manageattributes.php','maindiv')");
	//$AdminDAO->displayquery = 1;
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
	?>
	<br />
	</div>
<?php }elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 16/02/2012
global $AdminDAO;
$labels = array("ID","Code","Description","Short Description");
$fields = array("pkbarcodeid","barcode","itemdescription","shortdescription");
$dest 	= 	'manageitems.php';
$div	=	'items';
$form 	= 	"itemsfrm";	
$query	=	"SELECT 
					pkbarcodeid,
					barcode,
					itemdescription,
					shortdescription
				FROM 
					barcode 
				WHERE 
					barcodedeleted <> 1
			";
?>
<div id="susection"></div>
<div id='items'>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray);
?>
</div>
</form>
<script language="javascript" type="text/javascript">
	var selectedviewinstances	= '';
	loading('Loading Form...');
</script>
<?php }//end edit ?>