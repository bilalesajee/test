<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(39);
$param		=	$_REQUEST['param'];
$billid		=	$_REQUEST['id'];
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 		= 	'managecollections.php';
$div		=	'maindiv';
$form 		=	"collectionfrm";	
$param		=	$_GET['param'];
$employeeid				=	$_SESSION['addressbookid'];

if($param=="cancelled")
{
	$and	=	" AND status='2'";
}
else if($param=="heldsales")
{
	$and	=	" AND status='3'";
}
else if($param=="allsales")
{
	$and	=	" AND status='1'";
}
else
{
	$fromDate	=	strtotime($_GET['fromDate']);
	if($fromDate=='')
	{
		if($param=='closingbills')
		{
			$closingsession	=	$_GET['id'];
			$and	=	" AND st.fkclosingid='$closingsession' AND st.status='1'";
		}
		else
		{
				$and	=	"  AND st.status='1'";//shows the completed sales
		}
	}
	
}

$fromDate	=	strtotime($_GET['fromDate']);
	
	if($_GET['id']!=''){
		$and	=	" AND st.fkclosingid='{$_GET['id']}' AND st.status='1'";
		}
//this block is for seraching the items in the bills
$bc				=	$_GET['bc'];
$productname	=	filter($_GET['productname']);
$fromDate		=	strtotime($_GET['fromDate']);
$toDate			=	strtotime($_GET['toDate']);


if($employeeid==1928)
{
	$andchk=" and updatetime > 1395646473";
	//$and='';
}



if($_GET['page']>1)
{
	$and2sales=$_SESSION['and2sales'];
	//$and='';
}
else
{
	$_SESSION['and2sales']='';
}
if($bc!='' || $productname!='' || $fromDate!='' || $toDate!='')
{
	if($bc!='')
	{
		$andslqry=" AND bc.barcode='$bc' ";
	}
	elseif($productname!='')
	{
		$andslqry=" AND bc.itemdescription LIKE '%$productname%' ";
	}
	//$and2=" AND st.pksaleid='$billid' ";
	/*
			bc.pkbarcodeid,
			bc.barcode,
			bc.itemdescription,
			sd.quantity,
			sd.saleprice,
			sd.timestamp,
	*/
	 $sql="SELECT 
			DISTINCT(sd.fksaleid) as fksaleid 
		FROM 
			$dbname_detail.saledetail sd,
			$dbname_detail.stock s,
			barcode bc ,
			$dbname_detail.sale sl
		WHERE 
			sl.status='1' AND
			sl.pksaleid=sd.fksaleid and 
			s.fkbarcodeid=bc.pkbarcodeid AND
			s.pkstockid=sd.fkstockid 
			$andslqry  AND `timestamp` BETWEEN '$fromDate' AND '$toDate' LIMIT 0,100";
			$item_array	=	$AdminDAO->queryresult($sql);
			for($a=0;$a<count($item_array);$a++)
			{
				$fksaleid		=	$item_array[$a]['fksaleid'];
				$salesids.=",$fksaleid";
			}
			$salesids=trim($salesids,',');
			if($salesids!='')
			{
				$and2sales=" AND st.pksaleid IN($salesids) ";
				$_SESSION['and2sales']=$and2sales;
			}
}//this block is for seraching the items in the bills
//***********************sql for record set**************************
  $query	=	"SELECT 
				st.pksaleid,
				printid,
				round(globaldiscount,2) as discount,
				from_unixtime(updatetime,'%Y-%m-%d %h:%i:%s') as datetime, 
				countername, 
				CONCAT(firstname,' ', lastname) employeename,
				(SELECT CONCAT(firstname ,' ', lastname)cn FROM $dbname_detail.sale st1, customer  WHERE st.pksaleid = st1.pksaleid AND pkcustomerid = fkaccountid) AS cn,
				if(totalamount=0,(SELECT sum(quantity*saleprice) totalamount FROM $dbname_detail.saledetail sad   WHERE st.pksaleid = sad.fksaleid ),totalamount) totalamount
				FROM $dbname_detail.sale st, addressbook
				LEFT JOIN (employee ) ON ( pkaddressbookid = fkaddressbookid)
				WHERE st.fkstoreid ='$storeid'  
				AND fkuserid = fkaddressbookid
				 $and $and2sales $andchk
				
";

$sortorder	=	" ORDER BY updatetime DESC";
//$sortorder	=	" ORDER BY updatetime DESC";
							
/************* DUMMY SET ***************/
$labels = array("ID","Transaction","Bill #","Date","Counter","Cashier","Customer","Amount");
$fields = array("pksaleid","pksaleid","printid","datetime","countername","employeename","cn","totalamount");
$navbtn	=	"";
$navbtn .="	
			<a class=\"printrecord\" id=\"addbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void(0)\" onClick=\"billtoprint()\" title=\"Duplicate Bill\"><span class=\"\">&nbsp;Print</span></a>&nbsp;
			";
			//<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(1,'','creditorreport.php','sugrid','$div')\" title='Creditor Reports'>				<span class='printrecord'>&nbsp;</span>			</a>&nbsp;
/********** END DUMMY SET ***************/
if(in_array('206',$actions))
{
$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'edit_sale.php','subsection','$div') title=\"Edit Bill\"><span class=\"editrecord\">&nbsp;</span></a>";
}
if($_SESSION['addressbookid']== '1888')
	{
	$navbtn .="&nbsp; | <a href=\"javascript: printhistory('')\" title=\"View History\"><b> View History </b></a>";
	}
?>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Billing</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionarray,"  updatetime DESC ");
?>
<br />
<br />
</div>
<script language="javascript" type="text/javascript">
function duplicatebillprint(billid)
{
	//alert(billid);
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
 	window.open('generatebill.php?saleid='+billid+'&area=store','Invice',display); 
}
function billtoprint()
{
	var k=0,val,len;
	var frm='<?php echo $form;?>';
	//alert(frm);
	len	=	document.<?php echo $form;?>.checks.length;
	//alert(len);
	if(len>0)
	{
		for(var i=0;i<document.<?php echo $form;?>.checks.length;i++)
		{
			if(document.<?php echo $form;?>.checks[i].checked==true)
			{
				k++;
			}//if
		}//for
		if(k>1)
		{
			alert('Please select only one bill.');
			return false;
		}//if
		else
		{
			for(var j=0;j<document.<?php echo $form;?>.checks.length;j++)
			{
				if(document.<?php echo $form;?>.checks[j].checked==true)
				{
					val	=	document.<?php echo $form;?>.checks[j].value;
				}
			}
		}//else
	}
	else// outermost if length
	{
		val	=	document.<?php echo $form;?>.checks.value;
	}
	if(!val)
	{
		
		alert('Please select at least one bill.');
		return false;
	}
	duplicatebillprint(val);
}
function printhistory(text)
{
	var ids	=	selectedstring;
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=1000,height=600,left=100,top=25';
 	window.open('history_print.php?screen=bill&ids='+ids+'&'+text,'Cusromer History Report',display); 
}
</script>