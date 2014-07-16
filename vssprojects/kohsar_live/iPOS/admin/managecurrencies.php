<?php
session_start();
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity,$del;
$rights	 	=	$userSecurity->getRights(17);
$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//require_once("brandsmenu.php"); 
//*************delete************************
$deltype= "currency";
include_once("delete.php");
$query		=	"SELECT 
						pkcurrencyid, currencyname, currencysymbol,rate,if(defaultcurrency=0,'No','Yes') defaultcurrency
				FROM
						currency
				WHERE 	
						currencydeleted <> 1
						";

$dest 	= 	"managecurrencies.php";
$div	=	'maindiv';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');

$navbtn	=	"";
if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	if(in_array('46',$actions))
	{
		$navbtn .= "<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(0,'','addcurrency.php','subsection','maindiv','','$formtype')\" title='Add Currency'>
					<span class='addrecord'>&nbsp;</span>
				</a>&nbsp;";
	}
	if(in_array('47',$actions))
	{
		$navbtn .="	<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'addcurrency.php','subsection','maindiv','','$formtype') title=\"Edit Currency\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
	}
	if(in_array('48',$actions))
	{
		$navbtn .="<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Currencies\"><span class=\"deleterecord\">&nbsp;</span></a>";
	}
	$navbtn .= "<a class='button2' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(1,document.$form.checks,'setdefaultcurrency.php','subsection','maindiv')\" title='Set Default Currency'>
					<span class=''> &nbsp;&nbsp;|&nbsp; <b> Set as Default</b></span>
				</a>&nbsp;
				";
}//edit by Ahsan on 09/02/2012
/********** END DUMMY SET ***************/
?><head>
</head>

<div id='maindiv'>
	<div class="breadcrumbs" id="breadcrumbs">Currencies</div>
	<?php 
		grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
	?>
</div>
<br />
<br />
<div id="sugrid"></div>