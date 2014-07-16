<?php 
session_start();
//echo $_SERVER['QUERY_STRING'];
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$id			=	$_GET['id'];
$labels	 	=	array('ID','Barcode','Item','Store','By','Quantity','Damages','Type','Date');
$fields		=	array('pkshipliststockid','barcode','itemdescription','storename','name','quantity','damaged','damagetype','receivetime');
$dest 	= 	'receiveditems.php';
$div	=	'receiveditemsdiv';
$form 	= 	"receivedfrm";	
define(IMGPATH,'../images/');
//***********************sql for record set**************************
 $query 	= 	"	SELECT
 						pkshipliststockid,
						barcode,
						itemdescription,
						storename,
						CONCAT(firstname,' ',lastname) as name,
						ss.quantity as quantity,
						ss.damaged as damaged,
						damagetype,
						IF(receivetime='0','--------',FROM_UNIXTIME(receivetime, '%d-%m-%y')) as receivetime
					FROM
						shiplist,
						shiplistdetails,
						shipliststock ss LEFT JOIN addressbook ON(receivedby=pkaddressbookid) LEFT JOIN store ON (fkstoreid=pkstoreid) LEFT JOIN damagetype ON (fkdamagetypeid=pkdamagetypeid)
					WHERE
						fkshipmentid		=	'$id' AND
						pkshiplistid 		=	fkshiplistid AND 
						pkshiplistdetailsid	=	fkshiplistdetailsid
					";
				//	echo $query;
//*******************************************************************
//echo $query;
$navbtn	=	"";
/********** END DUMMY SET ***************/
?>
</head>
<div class="breadcrumbs" id="breadcrumbs">Shipments</div>
<?php 
	grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?>
</div>