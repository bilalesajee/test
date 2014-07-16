<?php session_start();
include_once("../../includes/security/adminsecurity.php");
include_once("../dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(63);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 	= 	'accounts/reports.php';
$div	=	'maindiv';
$form 	= 	"reportsfrm";	
define(IMGPATH,'../images/');
$query	= "SELECT 
				id,
				name,
				description
			FROM 
				report
			";
$i=0;
if(in_array('136',$actions))
{
	/*$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(1,'','displayreport.php','sugrid','$div')\" title='Select a record and Click to View Report'>
				<b>View</b>
			</a>&nbsp;";*/
	  $navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showReport('$div','')\" title='Select a record and Click to View Report'>
				<img src='../images/printer.png' border='0' />
			</a>&nbsp;";		
}
/********** END DUMMY SET ***************/
?>
<div id="menudiv"></div>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">View Reports</div>
<!--<div class="breadcrumbs" id="breadcrumbs">New Arrivals</div>-->
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray,$orderby);
?>
</div>
<script language="javascript" type="text/javascript">
function showReport(cdiv, cbfield)
{	//alert(billid);
    
	var id;
	var selectedbrands	=	getselected(cdiv);
	var sb;
	
	if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
				//alert(i+'---'+selectedbrands[i]);
				sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
			
		    /*var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=800,height=800,left=100,top=25';
		 	window.open('accounts/displayreport.php?id='+id,'Invice',display);*/
			showpage(1,'','accounts/creditorreport.php','sugrid','<?php echo $div; ?>')
		}
		else
		{
			alert("Please make sure that you have selected at least one row.");
			
		}//else	
	
	}