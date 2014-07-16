<?php 
	// ---------------------------------------------------------------- // 
	// DATABASE CONNECTION PARAMETER 									// 
	// ---------------------------------------------------------------- // 
	// directly below insted of include('adminsecurity.php'); 			//
	include('../security/adminsecurity.php'); 
	// ---------------------------------------------------------------- // 
	// SET PHP VAR - CHANGE THEM										// 
	// ---------------------------------------------------------------- // 
	// You can use these variables to define Query Search Parameters:	//
	
	// $SQL_FROM:	is the FROM clausule, you can add a TABLE or an 	//
	// 				expression: USER INNER JOIN DEPARTMENT...			//
	
	// $SQL_WHERE	is the WHER clausule, you can add an table 	 		//
	// 				attribute for ezample name or an 					//
	
	
	$SQL_FROM = 'barcode';//table name
	$SQL_WHERE = 'itemdescription';//item description from barcode


	$searchq		=	strip_tags($_GET['q']);
	$list			=	explode(' ',$searchq);
	foreach($list as $val)// preparing the search condition
	{
		$condition.="%$val%";
	}
	$tpmode				=	$_SESSION['tpmode'];
	$customerid			=	$_SESSION['customerid'];
	//echo "Tpmode=".$tpmode;
	if($tpmode==1)
	{
		$pricefield="costprice";
		//$subqry="  ";
	}
	else if($tpmode==2 && $customerid!='')
	{
		
		$subqry=" 	,(SELECT	
						quoteprice
					FROM
						$dbname_main.podetail,
						$dbname_main.purchaseorder
					WHERE
						fkbarcodeid	=	bcid AND 
						fkpurchaseorderid=pkpurchaseorderid AND
						fkcustomerid='$customerid' AND 
						expired=1 AND
						$dbname_main.purchaseorder.status=2
					LIMIT 0,1
					)as quoteprice ";	
		$pricefield="retailprice";
	}
	else
	{
		$pricefield="retailprice";
		$subqry=" 	,(SELECT	
						price
					FROM
						$dbname_main.pricechange
					WHERE
						fkbarcodeid	=	bcid
					
					LIMIT 0,1
					)as saleprice ";
	}	

	$condition	=	str_replace('%%','%',$condition);
	
	/*$getRecord_sql	=	"SELECT pkbarcodeid as bcid,barcode,itemdescription,(SELECT $pricefield from $dbname_main.stock where fkbarcodeid=pkbarcodeid order by pkstockid DESC limit 0,1) recenttradeprice
	,(SELECT MAX($pricefield)  from $dbname_main.stock where fkbarcodeid=pkbarcodeid) maxprice
						$subqry
						 FROM  $SQL_FROM  WHERE  $SQL_WHERE  LIKE '$condition' LIMIT 0,20";*/
						 
	/* Query changed by Yasir - 04-07-11, previous was "SELECT pkbarcodeid as bcid,barcode,itemdescription,(SELECT $pricefield from $dbname_main.stock,barcode where fkbarcodeid=pkbarcodeid order by pkstockid DESC limit 0,1) recenttradeprice	
						$subqry
						 FROM  $SQL_FROM  WHERE  $SQL_WHERE  LIKE '$condition' LIMIT 0,20";	*/				 
						 //(SELECT $pricefield from $dbname_main.stock where fkbarcodeid = bcid ORDER BY pkstockid DESC limit 0,1) recenttradeprice

	$getRecord_sql	=	"SELECT pkbarcodeid as bcid,barcode,itemdescription,(SELECT $pricefield from $dbname_main.stock,barcode where fkbarcodeid=pkbarcodeid order by pkstockid DESC limit 0,1) recenttradeprice	
						$subqry
						 FROM  $SQL_FROM  WHERE  $SQL_WHERE  LIKE '$condition' LIMIT 0,20";
						 
	
	 	/* $getRecord_sql	=	"SELECT pkbarcodeid as bcid,barcode,itemdescription,(SELECT MAX($pricefield)  from $dbname_main.stock where fkbarcodeid=pkbarcodeid) maxprice,(SELECT pkstockid  from $dbname_main.stock where fkbarcodeid=pkbarcodeid and $pricefield=maxprice LIMIT 0,1) as pkstockid
							$subqry
							
							 FROM  $SQL_FROM  WHERE  $SQL_WHERE  LIKE '$condition' LIMIT 0,20";*/

	$getRecord		=	$AdminDAO->queryresult($getRecord_sql);
	
	if(strlen($searchq)>0)
	{
	// ---------------------------------------------------------------- // 
	// AJAX Response													// 
	// ---------------------------------------------------------------- // 
	
	// Change php echo $row['name']; and $row['department']; with the	//
	// name of table attributes you want to return. For Example, if you //
	// want to return only the name, delete the following code			//
	// "<br /><?php echo $row['department'];></li>"//
	// You can modify the content of ID element how you prefer			//
	echo '<div id="scrolable">
		<ul id="autocompletelist" name="autocompletelist">';
	$i=1;
	for($d=0;$d<count($getRecord);$d++) // building the serached lists
	{
	$barcodeidlist	=	 $getRecord[$d]['barcode'];
	$barcodeid		=	 $getRecord[$d]['pkbarcodeid'];
	// change for last price changed
			$priceinrs		=	$getRecord[$d]['saleprice'];
			$quoteprice		=	$getRecord[$d]['quoteprice'];
			if($quoteprice!='')
			{
				$priceinrs	=	$quoteprice;
			}
			else if(!$priceinrs)
			{
				//$priceinrs			=	$getRecord[$d]['maxprice'];
				$recenttradeprice	=	$getRecord[$d]['recenttradeprice'];
				/*if($recenttradeprice && $tpmode!=2)
				{
					$str		=	' <font color=red title=\'Recent Price\'> ['.numbers($recenttradeprice).'] </font>';
				}
				else
				{
					$str		=	'';
				}*/
				$priceinrs		=	$recenttradeprice;
			}
	?>
		<li id="<?php echo $i;?>" value="<?php echo $barcodeidlist;?>">
              <a href="javascript:void(0)" onclick="getinstance('instancediv','<?php echo $barcodeidlist;?>');getlitext('<?php echo $i;?>')" id="<?php echo $i;?>_l" class="<?php echo $barcodeidlist;?>"><?php echo ' <font color=blue> '.$getRecord[$d]['barcode'].'  </font> <span class='.str_replace(' ','_',$getRecord[$d]['itemdescription']).' id='.$i.'_it>'.$getRecord[$d]['itemdescription'].' </span><font color=green title=\'Recent Price\'> ('.numbers($priceinrs).') </font>';?></a>
        </li>
	<?php 
		$i++;
	} 
	$recval	=	count($getRecord);
	echo '</ul>';
	echo "<input type=\"hidden\" name=\"recval\" id=\"recval\" value=\"$recval\" />";
}
exit; 
?>