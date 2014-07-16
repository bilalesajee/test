<?php

include("../includes/security/adminsecurity.php");

include_once("dbgrid.php");

/************* DUMMY SET ***************/





$labels = array("ID","Product","Barcode","Quantity","Employee Name","Return Date","Status","Location");

$fields = array("pkreturnid","itemdescription","pkbarcodeid","quantity","name","returndate","returnstatus","location");

if($_GET['param']!='confirm')

{

	$stockid=	$_GET['id'];

}

else

{	$ids	=	$_GET['id'];

	$ids	=	explode(",",$ids);

	$field	=	array('damagestatus');

	for($i=0;$i<count($ids) ;$i++)

	{

		

		

		$id	=	$ids[$i];

		

		if($id!='')

		{

			$rowarray	=	$AdminDAO->getrows("$dbname_detail.damages","damagestatus"," pkdamageid='$id'");

			

			$damagestatus	=	$rowarray[0]['damagestatus'];

			if($damagestatus=='c')

			{

				$damagestatus='p';	

			}

			else

			{

				$damagestatus='c';	

			}

			//echo $damagestatus;

			$val	=	array($damagestatus);

			$AdminDAO->updaterow("$dbname_detail.damages",$field,$val," pkdamageid='$id'");

		}

	}

		$stockid='-1';	

}

$dest 	= 	'loadunits.php';

$form 	= 	"damagesfrm2";	

if($stockid == "-1")

{

	$div = 'maindiv';

}

else

{

	$div	=	'movestock';

}

if($stockid!='-1' )

{

	$and=" AND d.fkstockid = '$stockid' ";

}

$id	=	$_GET['id'];

define(IMGPATH,'../images/');









$query 	=	"SELECT pkreturnid, itemdescription, fkbarcodeid, storename location, concat( firstname, ' ', lastname ) name, r.quantity quantity, FROM_UNIXTIME(returndate,'%d-%m-%Y') returndate, IF(returnstatus='c','Confirmed','Pending') returnstatus ,pkbarcodeid

FROM main.barcode b, $dbname_detail.returns r, $dbname_detail.stock s, main.employee e, main.addressbook, main.store st

WHERE r.fkemployeeid = e.pkemployeeid

AND e.fkaddressbookid = pkaddressbookid

AND r.fkstoreid = st.pkstoreid

AND s.fkbarcodeid = pkbarcodeid

AND r.fkstockid = pkstockid

AND s.fkbarcodeid = '$id'";//pkemployeeid 	=	d.fkemployeeid AND 						employee,



$navbtn = "

<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','confirm') title=\"Select a record and click here to confirm\"><span ><b>Returns</b></span></a>&nbsp;

";

$totals		=	array('quantity','loss');//the fields in this array will be summed up at end of grid

/********** END DUMMY SET ***************/

?><head>

</head>

<div class="breadcrumbs" id="breadcrumbs">Returns</div>

<div id='<?php echo $div;?>'>

<?php 

grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);

?></div>

<br />

<br />

<div id="sugrid"></div>