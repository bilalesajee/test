<?php
include("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
/************* DUMMY SET ***************/
$labels = array("ID","Product","Barcode","Quantity","Loss Amount","Employee Name","Damage Date", "Damage Type","Status","Location");
$fields = array("pkdamageid","productname","barcode","quantity","loss","name","damagedate","damagetype","damagestatus","location");
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

define(IMGPATH,'../images/');
if($_SESSION['siteconfig']!=1){//edit by ahsan 15/02/2012, if condition added
	$query 	= 	"SELECT 
						pkdamageid,
						d.quantity,
						s.fkbarcodeid as barcodeid,
						d.fkstoreid,
						FROM_UNIXTIME(damagedate,'%d-%m-%y') as damagedate,
						damagetype,
						bc.barcode,
						round((s.priceinrs*d.quantity),2) as loss,
						(SELECT storename FROM store WHERE pkstoreid = d.fkstoreid) as location,
						IF(damagestatus = 'c' ,'Confirmed','Pending') as damagestatus,
						concat(firstname,' ',lastname) as name,
						bc.itemdescription as productname
					FROM
						$dbname_detail.damages d,
						damagetype,
						$dbname_detail.stock s,
						barcode bc,
						addressbook
					WHERE
						pkdamagetypeid	=	fkdamagetypeid AND
						pkstockid 		=	fkstockid AND
						pkaddressbookid =	d.fkemployeeid AND
						bc.pkbarcodeid	=	s.fkbarcodeid
						$and
							";//pkemployeeid 	=	d.fkemployeeid AND 						employee,
}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan 15/02/2012
	 $query 	= 	"SELECT 
						pkdamageid,
						d.quantity,
						s.fkbarcodeid as barcodeid,
						d.fkstoreid,
						FROM_UNIXTIME(damagedate,'%d-%m-%y') as damagedate,
						FROM_UNIXTIME(damagedate,'%Y-%m-%d') as sortingdate,
						damagetype,
						bc.barcode,
						round((s.priceinrs*d.quantity),2) as loss,
						(SELECT storename FROM store WHERE pkstoreid = d.fkstoreid) as location,
						IF(damagestatus = 'c' ,'Confirmed','Pending') as damagestatus,
						concat(firstname,' ',lastname) as name,
						bc.itemdescription as productname
					FROM
						$dbname_detail.damages d,
						damagetype,
						$dbname_detail.stock s,
						barcode bc,
						employee,
						addressbook
					WHERE
						pkdamagetypeid	=	fkdamagetypeid AND
						pkstockid 		=	fkstockid AND
						pkaddressbookid =	employee.fkaddressbookid AND
						pkemployeeid 	=	d.fkemployeeid AND
						bc.pkbarcodeid	=	s.fkbarcodeid
						$and
							";
}//end edit

if($_SESSION['siteconfig'] != 3){//edit by ahsan on 09/02/2012
	$navbtn = "
	<a class=\"button2\" id=deletebrands onmouseover=\"buttonmouseover(this.id)\" onmouseout=\"buttonmouseout(this.id)\" href=javascript:deleterecords('$dest','$div','$_SESSION[qs]','confirm') title=\"Select a record and click here to confirm\"><span ><b>Confirm Damage</b></span></a>&nbsp;
	";
}//edit by Ahsan on 09/02/2012
$totals		=	array('quantity','loss');//the fields in this array will be summed up at end of grid
/********** END DUMMY SET ***************/
?><head>
</head>
<div class="breadcrumbs" id="breadcrumbs">Damages</div>
<div id='<?php echo $div;?>'>
<?php 
grid($labels,$fields,$query,$limit,$navbtn,$jsrc,$dest, $div, $css, $form);
?></div>
<br />
<br />
<div id="sugrid"></div>