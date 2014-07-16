<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 		=	$userSecurity->getRights(32);
$labels	 		=	$rights['labels'];
$fields			=	$rights['fields'];
$actions 		=	$rights['actions'];
$page			=	$_GET['page'];
$countrylist	=	$_GET['countryid'];
// countries
$srccountries		=	$AdminDAO->getrows("countries","*","countriesdeleted<>1");
$countrysel			=	"<select name=\"country\" id=\"country\" style=\"width:100px;\" onchange=\"getcountrylist(this.value)\"><option value=\"\">Select Country</option>";
for($i=0;$i<sizeof($srccountries);$i++)
{
	$countryname	=	$srccountries[$i]['countryname'];
	$countryid		=	$srccountries[$i]['pkcountryid'];
	$select		=	"";
	if($countryid == $countrylist)
	{
		$select = "selected=\"selected\"";
	}
	$countrysel2	.=	"<option value=\"$countryid\" $select>$countryname</option>";
}
$countries			=	$countrysel.$countrysel2."</select>";
// end countries

if($countrylist)
{
	$country	=	"AND fkcountrylist	=	'$countrylist'";
}
if($page)
{
	$_SESSION['qstring']='';
	$qstring	= $_SERVER['QUERY_STRING'];
	$_SESSION['qstring']=$qstring;
}
$dest 	= 	'managecountrylist.php';
$div	=	'maindiv';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');
$query 	= 	"SELECT 
				pkshiplistid,
				barcode,
				itemdescription,
				quantity,
				lastpurchaseprice,
				weight,
				code3,
				(SELECT GROUP_CONCAT(companyname) FROM supplier,shiplistsupplier sl WHERE sl.fksupplierid=pksupplierid AND  fkshiplistid=pkshiplistid) as companyname,
				storename,
				DATE_FORMAT(deadline,'%d-%m-%y') as deadline,
				CONCAT(firstname,' ',lastname) as name
			FROM
				shiplist LEFT JOIN countries ON(fkcountrylist=pkcountryid) LEFT JOIN store on (fkstoreid=pkstoreid) LEFT JOIN addressbook ON (shiplist.fkaddressbookid=pkaddressbookid)
			WHERE
				fkstatusid = 5 
				$country
			GROUP BY
				pkshiplistid
			";
$navbtn	=	"";
if(in_array('83',$actions))
{
$navbtn .="	<a class=\"button2\" id=selrecords onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:displayrecords('receivelist.php','subsection','$div','$_SESSION[qs]') title=\"Receive List\"><b>Receive List</b></a>&nbsp;";
}
?>
<script language="javascript" type="text/javascript">
function getcountrylist(id)
{
	jQuery('#maindiv').load('managecountrylist.php?countryid='+id);
}
</script>
<div id="sugrid"></div>
<div id='maindiv'>
<div class="breadcrumbs" id="breadcrumbs">Country List</div>
<?php
	echo $countries."<br/>";
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
<br />
<br />
</div>