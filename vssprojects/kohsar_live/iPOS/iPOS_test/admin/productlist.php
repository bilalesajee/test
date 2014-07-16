<?php
include("../includes/security/adminsecurity.php");

 $barcode	=	$_REQUEST['barcode'];
 function productlist($barcode)
  {
	global $AdminDAO,$qs;
   $sql		=	"SELECT 
				CONCAT( productname, 
					   ' (', GROUP_CONCAT( IFNULL(attributeoptionname,'') 
												  ORDER BY attributeposition) ,')',
					   brandname
					   ) PRODUCTNAME, 
				
				
				b.pkbarcodeid as bc 
			FROM 
				productattribute pa RIGHT JOIN (product p, attribute a) ON ( pa.fkproductid = p.pkproductid AND pa.fkattributeid = a.pkattributeid ) , 
			attributeoption ao LEFT JOIN productinstance pi ON (pkattributeoptionid = pi.fkattributeoptionid), barcode b,brand br,barcodebrand bb 
			WHERE 
				pkproductid = pa.fkproductid 
				AND pkattributeid = pa.fkattributeid 
				AND pkproductattributeid = fkproductattributeid 
				AND pkattributeid = ao.fkattributeid 
				AND b.fkproductid = pkproductid 
				AND pi.fkbarcodeid = b.pkbarcodeid 
				AND br.pkbrandid=bb.fkbrandid
				AND bb.fkbarcodeid=b.pkbarcodeid
				AND b.barcode = '$barcode'
				
			GROUP BY bc";
	$listdata	=	$AdminDAO->queryresult($sql);	
?>
<select name="productname" id="productname" autoexpand=yes>
<?php
for($i=0;$i<count($listdata);$i++)
{
?>
    <option value="<?php echo $listdata[$i]['bc'];?>"><?php echo $listdata[$i]['PRODUCTNAME'];?></option>
<?php
}
?>
</select>
<?php
  }//end of function
productlist($barcode);
?>
