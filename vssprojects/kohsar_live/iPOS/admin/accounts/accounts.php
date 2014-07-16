<?php session_start();
include_once("../../includes/security/adminsecurity.php");
include_once("../dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
if($_GET['na']!=''){
	print"<div style=\"font-size:14px; font-weight:bold;\">New Account is Successfully Created</div>";
	}
	
if($_GET['nu']!=''){
	
	print"<div style=\"font-size:14px; font-weight:bold;\">Account is Successfully Updated</div>";
	
	}
$rights	 	=	$userSecurity->getRights(60);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
$dest 	= 	'accounts/accounts.php';
$div	=	'maindiv';
$form 	= 	"accountfrm";	
define(IMGPATH,'../images/');
$query	= "SELECT 
				a.id as id,
				a.id as accountid,
				title,
				code , 
				c.name category, 
				t.name type,
				(if (status =  1, 'Active','In Active')) as status 

			FROM 
				$dbname_detail.account a LEFT JOIN type as t ON(t.id = type_id),
				accountcategory c
			WHERE 
				a.category_id 	=	c.id
			";
$i=0;
if(in_array('130',$actions))
{
	$navbtn .= "<a class='button2' id='addaccount' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','accounts/account.php','sugrid','$div')\" title='Add Account'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;";
}
if(in_array('131',$actions))
{
	$navbtn .="	<a class=\"button2\" id=\"editmenus\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'accounts/account.php','sugrid','$div') title=\"Edit Account\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
}
if(in_array('132',$actions))
{
	//$navbtn .="	<a class=\"button2\" id=deletemenus onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Account\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;";
}
//if(in_array('21',$actions))
//{
	$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(1,'','accounts/creditorreport.php','sugrid','$div','account_id')\" title='Select an account and Click to View Ledger'>
				<b>Show Ledger</b>
			</a>&nbsp;";
//}

/********** END DUMMY SET ***************/
?>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Manage Accounts</div>
<!--<div class="breadcrumbs" id="breadcrumbs">New Arrivals</div>-->
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionsarray,$orderby);
?>
</div>