<?php
include("includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$rights	 	=	$userSecurity->getRights(25);
$closingid	=	$_GET['id'];

$labels	 	=	$rights['labels'];
$fields		=	$rights['fields'];
$actions 	=	$rights['actions'];
//$H->dump($rights,1);
$dest 		= 	'pricechangetasks.php';
$div		=	'mainpanel';
$form 		=	"frm1";	
define(IMGPATH,'images/');
//***********************sql for record set**************************
/* Replaced By Yasir -- 08-07-11. Replaced FROM_UNIXTIME(pc.updatetime,'%d-%m-%Y') as updatetime with IF(pc.updatetime='0','-',FROM_UNIXTIME(pc.updatetime,'%d-%m-%Y')) as updatetime*/

 $query	=	"SELECT 
					pc.pkpricechangetaskid,
					FROM_UNIXTIME(pc.datetime,'%d-%m-%Y') as datetime,
					IF(pc.updatetime='0','-',FROM_UNIXTIME(pc.updatetime,'%d-%m-%Y')) as updatetime,
					barcode,
					taskdescription,
					note,
					price,
					
					(select CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=pc.assignedby) as assignedby,
					(select CONCAT(firstname,' ',lastname) from addressbook where pkaddressbookid=pc.assignedto) as assignedto	
				FROM 
					$dbname_detail.pricechangetask pc
				WHERE 
					taskstatus=0
				
				";
/************* DUMMY SET ***************/
$labels = array("ID","Barcode","Description","Note","New Price","Employee","Manager","Created On","Updated On");
$fields = array("pkpricechangetaskid","barcode","taskdescription","note","price","assignedto","assignedby","datetime","updatetime");

$navbtn	=	"<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:showpage(1,'','pricechangetaskupdate.php','childdiv','mainpanel')\" title='Add New Customer'>
				<span class='editrecord'>&nbsp;</span>
			</a>&nbsp;";
$navbtn .=	"<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)' href=\"javascript:printgrid('Price_Change_Tasks')\" title='Print'>
				<span class='print'>Print</span>
			</a>&nbsp;";
/*$navbtn	=	"<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'closinginfo.php','main-content','mainpanel','printclosing') title=\"Print Closing Details\"><span class=\"\">Print</span></a>&nbsp;
			";
$navbtn	.=	"&nbsp;|&nbsp; <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:void(0) onclick=loadclosingfrm(); title=\"Proccess Closing (CTRL+Z)\"><span class=\"\">Proccess Closing</span></a>&nbsp;
			";
$navbtn	.=	"&nbsp;|&nbsp; <a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(0,document.$form.checks,'closinginfostore.php','main-content','mainpanel') title=\"Closing Report\"><span class=\"\">Closing Report</span></a>&nbsp;
			";		*/				
//$navbtn="";
?>

<div id="mainpanel">
<div id="childdiv">
</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form,$type,$optionarray," pkpricechangetaskid DESC ");
?>
</div>