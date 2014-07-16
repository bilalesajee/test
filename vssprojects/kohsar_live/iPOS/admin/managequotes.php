<?php
include_once("../includes/security/adminsecurity.php");
//$smarty->display("managestocks.html");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(41);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//echo "$groupid!=$ownergroup";
if($groupid!=$ownergroup)
{
	//$where=" AND fkstoreid='$storeid' ";
}
/************* DUMMY SET ***************/
$dest 		= 	'managequotes.php';
$div		=	'maindiv';
$form 		= 	"frmquotes";	
$tablename	=	'purchaseorder';
define(IMGPATH,'../images/');

 $query	=	"SELECT
				pkpurchaseorderid,
				quotetitle,
				IF (status=1,'Quote','Purchase Order') as statusname,
				terms as description,
				IF (expired=1,'No','Yes') as expired,
				FROM_UNIXTIME(addtime,'%d-%m-%Y') as addtime,
				FROM_UNIXTIME(deadline,'%d-%m-%Y') as deadline,
				ponum,
				(select CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=fkaddressbookid) as createdby,
				(select CONCAT(firstname,' ',lastname) from customer where fkaccountid=pkcustomerid ) as customername
			FROM
				$dbname_detail.purchaseorder
			";
			//echo $query;
$navbtn	=	"";
if(in_array('94',$actions))
{
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,document.$form.checks,'addquote.php','subsection','maindiv')\" title='Add Quote'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(in_array('95',$actions))
{
			$navbtn .="	
			<a href=\"javascript: javascript:showpage(1,document.$form.checks,'addquote.php','subsection','maindiv') \" title=\"Edit Quote\"><span class=\"editrecord\">&nbsp;</span></a>";
}
if(in_array('96',$actions))
{
	$navbtn .="	
			&nbsp;<a href=\"javascript: showpage(1,document.$form.checks,'addquoteitem.php','subsection','maindiv') \" title=\"Add Items\"><b>Items</b></a>";
			
}//if
if(in_array('97',$actions))
{
	$navbtn .="	
			|&nbsp;<a href=\"javascript:void(0)\" onClick=\"printquote()\" title='Print Consignment'><span class=\"printrecord\">&nbsp;</span></a>&nbsp;";
}//if
/********** END DUMMY SET ***************/
?>
<script language="javascript">
function quote(id)
{
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width=500,height=600,left=100,top=25';
 	window.open('printquote.php?id='+id,display); 
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
</script>
</head>
<div id="sugrid"></div>
<div id="stockdetailsdiv"></div>
<div id='<?php echo $div;?>'>
<div class="breadcrumbs" id="breadcrumbs">Quotations</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,'','  pkpurchaseorderid DESC',$tablename);
?>
</div>
<br />
<br />
