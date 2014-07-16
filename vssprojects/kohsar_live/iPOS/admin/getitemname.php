<?php
include_once("../includes/security/adminsecurity.php");
include_once("dbgrid.php");
global $AdminDAO,$Component,$userSecurity;
$bcid	=	$_GET['bcid'];
$query	=	"SELECT 
					CONCAT( productname, ' (', 
											(SELECT GROUP_CONCAT(attributeoptionname) FROM 	
												attribute a,
												attributeoption ao,
												productinstance pi,
												barcode bc
											WHERE 
											 	a.pkattributeid 		=	ao.fkattributeid AND
											 	pkattributeoptionid 	=	pi.fkattributeoptionid AND 
												pi.fkbarcodeid			=	bc.pkbarcodeid AND
												bc.pkbarcodeid			=	'$bcid'
											 ORDER BY attributeposition) ,
										') ',
						brandname)	PRODUCTNAME
				FROM 
					product,
					barcode,
					brand,
					barcodebrand
				WHERE
					pkproductid = fkproductid AND
					pkbrandid	=	fkbrandid AND
					pkbarcodeid	=	fkbarcodeid AND 
					pkbarcodeid = '$bcid'
						
";
$result	=	$AdminDAO->queryresult($query);
//echo $result[0]['PRODUCTNAME'];
?>
<script language="javascript">
	alert('<?php echo html_entity_decode($result[0]['PRODUCTNAME']);?>');
</script>