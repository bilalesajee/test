<?php 
session_start();
include("../includes/security/adminsecurity.php");
include_once("detailscomponent.php");
//include_once("dbgrid.php");
global $AdminDAO,$Component;
$id		=	$_REQUEST['id'];
/************* DUMMY SET ***************/

  $sql=" SELECT 
				shc.fkchargesid,
				ch.chargesname,
				round(shc.chargesinrs,2) as chargesinrs 
			FROM 
				shipmentcharges shc,
				charges ch 
			WHERE 
				ch.pkchargesid=shc.fkchargesid
				AND shc.fkshipmentid='$id'
				AND ch.chargesdeleted<>1
			";
$data_array =	$AdminDAO->queryresult($sql);
if(count($data_array)>0)
{
	foreach($data_array as $value=>$k)
	{
		$labels[] =$k['chargesname'];
		$fields[] =$k['chargesinrs'];
	}
}
$type	=	'Charges Details';
$dest 	= 	'manageshipmentcharges.php';
$div	=	'maindiv33';
$form 	= 	"frm1";	
define(IMGPATH,'../images/');
//***********************sql for record set**************************
$navbtn = "";
	$navbtn .="	<a class=\"button2\" id=\"editbrands\" href=javascript:showeditcharge($id) title=\"Edit Shipment Charges\"><span class=\"editrecord\">&nbsp;</span></a>&nbsp;";
/********** END DUMMY SET ***************/
?><head>
</head>
<div id="subsection33"></div>
<div id='maindiv33'>
		<?php 
			$breadcrumb	=	'Shipment Charges';
			detail($labels,$fields,$navbtn,$jsrc,$dest,$div,$css,$breadcrumb);
			//grid($labels,$fields,$sql,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
		?>
</div>
<br />
<br />
<div id="sugrid"></div>
<script type="text/javascript">
function showeditcharge(id)
{
	$('#subsection33').load('editshipcharges.php?id='+id);//, { shipid:id },function(data) {
  		//alert(data);
	//});
}
</script>