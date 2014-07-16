<script language="javascript" type="text/javascript">
function duplicatebillprint(billid)
{
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=350,height=600,left=100,top=25';
 	window.open('generatebill.php?saleid='+billid,'Invice',display); 
}
function billtoprint()
{
	var k=0,val,len;
	len	=	document.frm1.checks.length;
	if(len>0)
	{
		for(var i=0;i<document.frm1.checks.length;i++)
		{
			if(document.frm1.checks[i].checked==true)
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
			for(var j=0;j<document.frm1.checks.length;j++)
			{
				if(document.frm1.checks[j].checked==true)
				{
					val	=	document.frm1.checks[j].value;
				}
			}
		}//else
	}
	else// outermost if length
	{
		val	=	document.frm1.checks.value;
	}
	if(!val)
	{
		alert('Please select at least one bill.');
		return false;
	}
	duplicatebillprint(val);
}
function customerbilltoprint()
{
	var k=0,val,len;
	len	=	document.frm1.checks.length;
	if(len>0)
	{
		for(var i=0;i<document.frm1.checks.length;i++)
		{
			if(document.frm1.checks[i].checked==true)
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
			for(var j=0;j<document.frm1.checks.length;j++)
			{
				if(document.frm1.checks[j].checked==true)
				{
					val	=	document.frm1.checks[j].value;
				}
			}
		}//else
	}
	else// outermost if length
	{
		val	=	document.frm1.checks.value;
	}
	if(!val)
	{
		
		alert('Please select at least one bill.');
		return false;
	}
	$('#customeriddiv').load('getcustomerid.php?saleid='+val);
}
</script>
<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(8);
$param		=	$_REQUEST['param'];
$billid		=	$_REQUEST['id'];
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 		= 	'billing.php';
$div		=	'mainpanel';
$form 		=	"frm1";	
define(IMGPATH,'images/');
/*if($param=="duplicatebill")
{
	echo "<script language=\"javascript\" type=\"text/javascript\">duplicatebillprint('$billid');</script>";
}*/
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
else if($param=="deliverychalan")
{
	$and	=	" AND status='4'";
}
else
{
	$fromDate	=	strtotime($_GET['fromDate']);
	if($fromDate=='')
	{
		$and	=	" AND fkclosingid='$closingsession' AND status='1'";
	}
}
//this block is for seraching the items in the bills
$bc				=	$_GET['barcode'];
$productname	=	filter(urldecode($_GET['productname']));
$arrFrDate		=	explode('-',$_GET['fromDate']);
$arrToDate		=	explode('-',$_GET['toDate']);
$fromDate		=	mktime(0,0,0,$arrFrDate[1],$arrFrDate[0],$arrFrDate[2]);
$toDate			=	mktime(23,59,59,$arrToDate[1],$arrToDate[0],$arrToDate[2]);
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
	*/	//changed $dbname_main to $dbname_detail on line 169, 170, 172 by ahsan 22/02/2012
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
			} else // added by Yasir 13-09-11
			{
				//status is set to fake status to avoid any wrong results in the grid
				$and2sales=" AND st.status=55 ";
				$_SESSION['and2sales']=$and2sales;
			}

}//this block is for seraching the items in the bills
//***********************sql for record set**************************
/*
	$query	=	"SELECT 
				pksaleid,
				printid,
				round(globaldiscount,2) as discount,
				from_unixtime(updatetime,'%Y-%m-%d %h:%m:%s') as datetime, 
				countername, 
				CONCAT(firstname,' ', lastname) employeename,
				(SELECT round(SUM(saleprice*quantity),2) as Total FROM $dbname_main.saledetail sdt,$dbname_main.sale st2 WHERE st2.pksaleid = sdt.fksaleid AND st2.pksaleid = st.pksaleid) AS total, 							
				round((SELECT IF(sum(amount)IS NULL,0,sum(amount))FROM $dbname_main.cashpayment cp WHERE cp.fksaleid=st.pksaleid)+(SELECT IF(sum(amount)IS NULL,0,sum(amount)) FROM $dbname_main.ccpayment WHERE fksaleid=st.pksaleid)+(SELECT IF(sum(amount*rate)IS NULL,0,sum(amount*rate)) FROM $dbname_main.fcpayment WHERE fksaleid=st.pksaleid)+(SELECT IF(sum(amount)IS NULL,0,sum(amount)) FROM $dbname_main.chequepayment WHERE fksaleid=st.pksaleid),2) as paid,
				(SELECT CONCAT(firstname ,' ', lastname)cn FROM $dbname_main.sale st1, $dbname_main.addressbook LEFT JOIN ($dbname_main.customer c) ON (pkaddressbookid= c.fkaddressbookid) WHERE st.pksaleid = st1.pksaleid AND pkcustomerid = fkcustomerid) AS cn,
				
				IF(round((SELECT (SUM(saleprice*quantity)) as Total FROM $dbname_main.saledetail sdt,$dbname_main.sale st2 WHERE st2.pksaleid = sdt.fksaleid AND st2.pksaleid = st.pksaleid)-((SELECT IF(sum(amount)IS NULL,0,sum(amount))FROM $dbname_main.cashpayment cp WHERE cp.fksaleid=st.pksaleid)+(SELECT IF(sum(amount)IS NULL,0,sum(amount)) FROM $dbname_main.ccpayment WHERE fksaleid=st.pksaleid)+(SELECT IF(sum(amount*rate)IS NULL,0,sum(amount*rate)) FROM $dbname_main.fcpayment WHERE fksaleid=st.pksaleid)+(SELECT IF(sum(amount)IS NULL,0,sum(amount)) FROM $dbname_main.chequepayment WHERE fksaleid=st.pksaleid))-(globaldiscount),2)<0,0,(round((SELECT (SUM(saleprice*quantity)) as Total FROM $dbname_main.saledetail sdt,$dbname_main.sale st2 WHERE st2.pksaleid = sdt.fksaleid AND st2.pksaleid = st.pksaleid)-((SELECT IF(sum(amount)IS NULL,0,sum(amount))FROM $dbname_main.cashpayment cp WHERE cp.fksaleid=st.pksaleid)+(SELECT IF(sum(amount)IS NULL,0,sum(amount)) FROM $dbname_main.ccpayment WHERE fksaleid=st.pksaleid)+(SELECT IF(sum(amount*rate)IS NULL,0,sum(amount*rate)) FROM $dbname_main.fcpayment WHERE fksaleid=st.pksaleid)+(SELECT IF(sum(amount)IS NULL,0,sum(amount)) FROM $dbname_main.chequepayment WHERE fksaleid=st.pksaleid))-(globaldiscount),2))) as credit
				FROM $dbname_main.sale st, addressbook
				LEFT JOIN (employee ) ON ( pkaddressbookid = fkaddressbookid)
				WHERE st.fkstoreid ='$storeid'  
				AND fkuserid = fkaddressbookid
				AND countername='$countername'
				AND $and 
				
";
*///changed $dbname_main to $dbname_detail on line 229, 230 by ahsan 22/02/2012
 	$query	=	"SELECT 
				st.pksaleid,
				printid,
				creditinvoiceno,
				round(globaldiscount,2) as discount,
				from_unixtime(datetime,'%Y-%m-%d %h:%i:%s') as datetime, 
				countername, 
				CONCAT(firstname,' ', lastname) employeename,
				(SELECT CONCAT(firstname ,' ', lastname)cn FROM $dbname_detail.sale st1, $dbname_detail.addressbook LEFT JOIN ($dbname_detail.account c) ON (pkaddressbookid= c.fkaddressbookid) WHERE st.pksaleid = st1.pksaleid AND id = fkaccountid) AS cn
				FROM $dbname_detail.sale st, addressbook
				LEFT JOIN (employee ) ON ( pkaddressbookid = fkaddressbookid)
				WHERE st.fkstoreid ='$storeid'  
				AND fkuserid = fkaddressbookid
				AND countername='$countername'
				 $and $and2sales
				
";

$sortorder	=	"  st.pksaleid DESC";
							
/************* DUMMY SET ***************/
// selecting counter type to see what type of bill fields should be shown 
// retrieving counter info
$counterinfo	=	$AdminDAO->getrows("$dbname_detail.counter","countertype","countername='$countername'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
$countertype	=	$counterinfo[0]['countertype'];
// countertype = 1 means normal pos; countertype = 2 means hotels
if($countertype==2)
{
	$labels = array("ID","Serial #","Date","Counter","Cashier","Customer");
	$fields = array("pksaleid","creditinvoiceno","datetime","countername","employeename","cn");
}
else
{
	$labels = array("ID","Transaction","Bill #","Date","Counter","Cashier","Customer");
	$fields = array("pksaleid","pksaleid","printid","datetime","countername","employeename","cn");
}
$navbtn	=	"
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'billing.php','main-content','mainpanel','allsales') title=\"All Time Sales\"><span class=\"\">All Time Sales</span></a>&nbsp;|&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'billing.php','main-content','mainpanel','heldsales') title=\"View Sales on Hold\"><span class=\"\">Sales on Hold</span></a>&nbsp;|&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'billing.php','main-content','mainpanel','billing') title=\"View Completed Sales\"><span class=\"\">Completed Sales</span></a>&nbsp;|&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'billing.php','main-content','mainpanel','cancelled') title=\"View Cancelled Sales\"><span class=\"\">Cancelled Sales</span></a>&nbsp;|&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'codeitem.php','main-content','mainpanel','billing') title=\"Sale Details\"><span class=\"\">Sale Detail</span></a>&nbsp;|&nbsp;
			";
// selecting counter type for duplicate bill print
$counter		=	$_SESSION['countername'];
$counterinfo	=	$AdminDAO->getrows("$dbname_detail.counter","countertype","countername='$counter'");//changed $dbname_main to $dbname_detail by ahsan 22/02/2012
$countertype	=	$counterinfo[0]['countertype'];
if($countertype==2)
{
	$navbtn.=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void(0)\" onClick=\"customerbilltoprint()\" title=\"Duplicate Bill\"><span class=\"\">Duplicate Bill Print</span></a>&nbsp;
				";
	$navbtn.=	"&nbsp;|&nbsp;<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void(0)\" onClick=\"showpage(1,document.$form.checks,'sale.php','main-content','mainpanel','delievrychalan&invmode=1')\" title=\"Edit Invoice\"><span class=\"\">Edit Invoice</span></a>&nbsp;
			";
}
else
{
	$navbtn.=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void(0)\" onClick=\"billtoprint()\" title=\"Duplicate Bill\"><span class=\"\">Duplicate Bill Print</span></a>&nbsp;
			";
}
if($param=="deliverychalan")
{
	$navbtn.=	"&nbsp;|&nbsp;<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=\"javascript:void(0)\" onClick=\"showpage(1,document.$form.checks,'sale.php','main-content','mainpanel','delievrychalan')\" title=\"Confirm Order\"><span class=\"\">Confirm Order</span></a>&nbsp;
			";

}
//echo "<pre>".$query;
?>
<div id="customeriddiv" style="display:none;"></div>
<div id="mainpanel">
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,'','',$sortorder);
?>
</div>
<!--Added by Yasir - 08-07-11-->
<script language="javascript" type="text/javascript">
	document.getElementById('searchFieldmainpanel').focus();	
</script>