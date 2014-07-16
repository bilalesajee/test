<?php
include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights();
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$delid		=	$_REQUEST['id'];
$oper		=	$_REQUEST['oper'];
$gid=$_SESSION['groupid'];
if($delid!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$checkstat	=	$AdminDAO->getrows("$dbname_detail.stock_adjustment","status","pkstockadjustmentid  = '$value'");
                 $stst	=	$checkstat[0]['status'];
				if($stst==0){
				$delcondition =" pkstockadjustmentid  = '$value'";
				$AdminDAO->deleterows("$dbname_detail.stock_adjustment",$delcondition,1);
				$delcondition2 =" fkstockadjustmentid  = '$value'";
				$AdminDAO->deleterows("$dbname_detail.stock_adjustment_detail",$delcondition2,1);
				}else{
					echo "<center><b>You Can Not Delete Closed Record</b></center>";
					
					}
			}
		
			
		}
}
//echo "$groupid!=$ownergroup";
/*if($groupid!=$ownergroup)
{
	//$where=" AND fkstoreid='$storeid' ";
}*/
/************* DUMMY SET ***************/
$dest 		= 	'managestock_adjustment_without_invoice.php';
$div		=	'maindiv';
$form 		= 	"frmquotes";	
//$tablename	=	'purchaseorder';
define(IMGPATH,'../images/');

$labels = array("ID", "Remarks","Date ","Status");
$fields = array("pkstockadjustmentid","remarks","addtime","status");
 $query	=	"SELECT
				s.pkstockadjustmentid,s.remarks,FROM_UNIXTIME(s.addtime,'%d-%m-%Y') as addtime,
				
				IF (s.status=1,'Close','Open') as status
			
			FROM
				$dbname_detail.stock_adjustment s
				
			";
			//echo $query;
$navbtn	=	"";

	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,document.$form.checks,'addstock_adjustment_without_invoice.php','subsection','maindiv','$param')\" title='Add Quote'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";


			$navbtn .="	
			<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addstock_adjustment_without_invoice.php','subsection','maindiv','$param') \" title=\"Edit Quote\"><span class=\"editrecord\">&nbsp;</span></a>";

$navbtn .="|&nbsp;<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: returnstatus('close')\" title=\"Mark As Close\"><b>Mark As Close </b></a>&nbsp;";

if($gid==6){$navbtn .="|&nbsp;<a class=\"n\" id=\"returns\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\"&nbsp;<a href=\"javascript: returnstatus('open')\" title=\"Open Invoice\"><b>Open Invoice </b></a>&nbsp;";
}
$navbtn .="|&nbsp;<a href=\"javascript: printsupllierreport('')\" title=\"Stock Adjustment Report\"><b> Stock Adjustment Report </b></a>";

/*$navbtn .="|&nbsp;<a href=\"javascript: printsupllierreport('')\" title=\"Shipment Report\"><b> Purchase Return Report </b></a>";*/
			
$navbtn .="|&nbsp;<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete \"><span class=\"deleterecord\">&nbsp;</span></a>";
/*	$navbtn .="	
			|&nbsp;<a href=\"javascript:void(0)\" onClick=\"printquote()\" title='Print Consignment'><span class=\"printrecord\">&nbsp;</span></a>&nbsp;";*/

/********** END DUMMY SET ***************/
?>
<script language="javascript">
function quote(id)
{
	//var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=500,height=600,left=100,top=25';
 	//window.open('printquote.php?id='+id,display); 
}
function printquote()
{
	var k=0,val,len;
	len	=	document.frmquotes.checks.length;
	if(len>0)
	{
		for(var i=0;i<document.frmquotes.checks.length;i++)
		{
			if(document.frmquotes.checks[i].checked==true)
			{
				k++;
			}//if
		}//for
		if(k>1)
		{
			alert('Please select only one record.');
			return false;
		}//if
		else
		{
			for(var j=0;j<document.frmquotes.checks.length;j++)
			{
				if(document.frmquotes.checks[j].checked==true)
				{
					val	=	document.frmquotes.checks[j].value;
				}
			}
		}//else
	}
	else// outermost if length
	{
		val	=	document.frmquotes.checks.value;
	}
	if(!val)
	{
		
		alert('Please select at least one record.');
		return false;
	}
	quote(val);
}

//////////////////////////////////////////////////////////////
function returnstatus(text)
{
	
	var ids	=	selectedstring;
	return_status = $("#return_status").val(); 
	$.ajax({
type: "GET",
url: 'changestockadjuststatus.php',
success: response,
data: 'ids='+ids+'&return_status='+text,


});
loadsection('maindiv','managestock_adjustment_without_invoice.php');
}

function response(text)

{
alert(text);
loadsection('maindiv','managestock_adjustment_without_invoice.php');
}
function printsupllierreport(text)
{
	var ids	=	selectedstring;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600,left=100,top=25';
 	window.open('print_stock_adj_report.php?ids='+ids+'&'+text,'Print Stock Adj Report',display); 
}
</script>
</head>
<div id="sugrid"></div>
<div id="stockdetailsdiv"></div>
<div id='<?php echo $div;?>'>
<div class="breadcrumbs" id="breadcrumbs"></div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,'','  pkstockadjustmentid DESC',$tablename);
?>
</div>
<br />
<br />
