<?php

include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
/************* DUMMY SET ***************/
$labels = array("ID","BarCode","Item Name","Units","Deadline","Comments");
$fields = array("pkdemanddetailid","barcode","productname","units","deadline","comments");
$dest 	= 	'demanddetails.php';
$div	=	'sugrid';
$form 	= 	"frm2";	
$demandid		=	$_REQUEST['id'];
$delid			=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
if($delid!='' && $oper=='del')
{
		$condition="";
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" pkdemanddetailid = '$value' ";
				$AdminDAO->deleterows('demanddetails',$delcondition);
			}
		}
}
$_SESSION['demandid'] =	$demandid;
$id		=	$_GET['id'];
define(IMGPATH,'../images/');
/*(SELECT CONCAT(productname, ' (', GROUP_CONCAT( IFNULL(attributeoptionname,'') ORDER BY attributeposition),')',brandname) PRODUCTNAME
			FROM 
						productattribute pa RIGHT JOIN (product p, attribute a) ON ( pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid ), attributeoption ao LEFT JOIN productinstance pi ON (pkattributeoptionid = pi.fkattributeoptionid), barcode bd,brand br,barcodebrand bb 
			WHERE 
				pkproductid = pa.fkproductid 
				AND pkattributeid = pa.fkattributeid 
				AND pkproductattributeid = fkproductattributeid 
				AND pkattributeid = ao.fkattributeid 
				AND bd.fkproductid = pkproductid 
				AND pi.fkbarcodeid = bd.pkbarcodeid 
				AND br.pkbrandid=bb.fkbrandid
				AND bb.fkbarcodeid=bd.pkbarcodeid
				AND bd.barcode = b.barcode
				
			GROUP BY PRODUCTNAME) as*/
$query 	= 	"SELECT 
						distinct(d.pkdemanddetailid) as pkdemanddetailid,
						itemdescription as productname,
						b.barcode,
						d.comments,
						d.units,
						from_unixtime(d.deadline,'%d-%m-%Y') as deadline
					FROM
						$dbname_detail.demanddetails d,
						barcode b
					WHERE
						d.fkdemandid 	=	'$demandid' AND
						b.pkbarcodeid 	=	d.fkbarcodeid AND
						demanddetailsdeleted <>1";
/*<a class='button2' id='addbrands' onmouseover='buttonmouseover(this.id)' onmouseout='buttonmouseout(this.id)'
			href=\"javascript:showpage(0,'','adddemand.php','subsection')\" title='Add Details'>
				<span class='addrecord'>&nbsp;</span>
			</a>&nbsp;
			<a class=\"button2\" id=\"editbrands\" onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:showpage(1,document.$form.checks,'adddetails.php','subsection') title=\"Edit Details\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;
			<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]') title=\"Delete Details\"><span class=\"deleterecord\">&nbsp;</span></a>&nbsp;
	*/							
$navbtn = "
			
			 <a href=\"javascript: showpage(1,document.$form.checks,'fulfilldemand.php','sugridchild','maindiv') \" title=\"Fullfill Demand\"><b>Fullfill Demand</b></a>";
//<a href=\"javascript: loadsubgrid('sugridchild',document.$form.checks,'instancedemanddetails.php','sugrid')
//<a href=\"javascript:showpage(2,document.$form.checks,'instancedemanddetails.php','sugridchild','')
//<a href=\"javascript:showpage(2,document.$form.checks,'additem.php','sugrid','maindiv')
/********** END DUMMY SET ***************/
?><head>
</head>
<div id="sugridchild"></div>
<a href='#' class='basic'>
	
</a>
<div class="breadcrumbs" id="breadcrumbs">Demand Details</div>
<div id='maindiv'>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?></div>