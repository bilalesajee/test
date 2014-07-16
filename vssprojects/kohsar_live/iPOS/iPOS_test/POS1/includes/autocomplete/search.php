<?php 
if(strstr($_SERVER['REQUEST_URI'],'/admin/')==false){//added if condition by ahsan 16/02/2012, 

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
//	$list			=	explode(' ',$searchq);
//	foreach($list as $val)// preparing the search condition
//	{
//		$condition.="%$val%";
//	}
	$condition.="%$searchq%";
	$tpmode				=	$_SESSION['tpmode'];
	$customerid			=	$_SESSION['customerid'];
	//echo "Tpmode=".$tpmode;
	if($tpmode==1)
	{
		$pricefield="priceinrs";
		//$subqry="  ";
	}
	else if($tpmode==2 && $customerid!='')
	{
		
		$subqry=" 	,(SELECT	
						quoteprice
					FROM
						$dbname_detail.podetail,
						$dbname_detail.purchaseorder
					WHERE
						fkbarcodeid	=	bcid AND 
						fkpurchaseorderid=pkpurchaseorderid AND
						fkaccountid='$customerid' AND 
						expired=1 AND
						$dbname_detail.purchaseorder.status=2
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
						$dbname_detail.pricechange
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

	if($_SESSION['siteconfig']!=1){//edit by Ahsan on 10/02/2012, added if condition
	if($tpmode==1){
		$getRecord_sql	=	"SELECT pkbarcodeid as bcid,barcode,itemdescription	
							$subqry
							 FROM  $SQL_FROM  WHERE  $SQL_WHERE  LIKE '$condition' LIMIT 0,20";
	}else{
$getRecord_sql	=	"SELECT pkbarcodeid as bcid,barcode,itemdescription,(SELECT $pricefield from $dbname_detail.stock,barcode where fkbarcodeid=pkbarcodeid order by pkstockid DESC limit 0,1) recenttradeprice	
							$subqry
							 FROM  $SQL_FROM  WHERE  $SQL_WHERE  LIKE '$condition' LIMIT 0,20";
		
		
		}

	}elseif($_SESSION['siteconfig']!=3){//from main, start edit by Ahsan on 10/02/2012
			$getRecord_sql	=	"
							 SELECT itemdescription , 
							barcode as bc,
							pkbarcodeid,
							fkproductid
						FROM 
							$SQL_FROM
						WHERE
							itemdescription 
							 LIKE '$condition' OR shortdescription LIKE '$condition' LIMIT 0,20";

	}//end edit
	
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
	if($_SESSION['siteconfig']!=1){//edit by Ahsan on 10/02/2012, added if condition
		$barcodeidlist	=	 $getRecord[$d]['barcode'];
	}elseif($_SESSION['siteconfig']!=3){//from main, edit by ahsan on 10/02/2012
		$barcodeidlist	=	 $getRecord[$d]['bc'];
	}
	
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
        <?php if($_SESSION['siteconfig']!=1){ //edit by ahsan on 10/02/2012, if condition added
		
		if($tpmode==1){ ?>
		
	 <a href="javascript:void(0)" onclick="getinstance('instancediv','<?php echo $barcodeidlist;?>');getlitext('<?php echo $i;?>')" id="<?php echo $i;?>_l" class="<?php echo $barcodeidlist;?>"><?php echo ' <font color=blue> '.$getRecord[$d]['barcode'].'  </font> <span class='.str_replace(' ','_',$getRecord[$d]['itemdescription']).' id='.$i.'_it>'.$getRecord[$d]['itemdescription'].'</font>';?></a>
	
	<?php 	}else{ ?>
              <a href="javascript:void(0)" onclick="getinstance('instancediv','<?php echo $barcodeidlist;?>');getlitext('<?php echo $i;?>')" id="<?php echo $i;?>_l" class="<?php echo $barcodeidlist;?>"><?php echo ' <font color=blue> '.$getRecord[$d]['barcode'].'  </font> <span class='.str_replace(' ','_',$getRecord[$d]['itemdescription']).' id='.$i.'_it>'.$getRecord[$d]['itemdescription'].' </span><font color=green title=\'Recent Price\'> ('.numbers($priceinrs).') </font>';?></a>			
			
		<?php	}	?>

        <?php }elseif($_SESSION['siteconfig']!=3){ //from main, start edit by ahsan on 10/02/2012?>
              <a href="javascript:void(0)" onclick="getlitext('<?php echo $getRecord[$d]['itemdescription'];?>','<?php echo $barcodeid;?>','<?php echo $barcodeidlist;?>')"   id="<?php echo $i;?>_l" class="<?php echo $barcodeidlist;?>"><?php echo ' <font color=blue> '.$getRecord[$d]['bc'].'  </font> <span id=itemname_'.$i.' class="'.$getRecord[$d]['itemdescription'].'">'.$getRecord[$d]['itemdescription'].'</span>';?></a>
         <?php }//end edit?>
        </li>
	<?php 
		$i++;
	} 
	$recval	=	count($getRecord);
	echo '</ul>';
	echo "<input type=\"hidden\" name=\"recval\" id=\"recval\" value=\"$recval\" />";
}
}else{//edit by ahsan 20/20/2012
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
//	$list			=	explode(' ',$searchq);
//	foreach($list as $val)// preparing the search condition
//	{
//		$condition.="%$val%";
//	}
	
$condition.="%$searchq%";	
$condition	=	str_replace('%%','%',$condition);
	 $getRecord_sql	=	"
							 SELECT itemdescription , 
							barcode as bc,
							pkbarcodeid,
							fkproductid
						FROM 
							$SQL_FROM
						WHERE
							itemdescription 
							 LIKE '$condition' LIMIT 0,20";
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
		$barcodeidlist	=	 $getRecord[$d]['bc'];
		$barcodeid		=	 $getRecord[$d]['pkbarcodeid'];
	// change for last price changed
			
			
			
	?>
		<li id="<?php echo $i;?>" value="<?php echo $barcodeidlist;?>">
              <a href="javascript:void(0)" onclick="getlitext('<?php echo $getRecord[$d]['itemdescription'];?>','<?php echo $barcodeidlist;?>')"   id="<?php echo $i;?>_l" class="<?php echo $barcodeidlist;?>"><?php echo ' <font color=blue> '.$getRecord[$d]['bc'].'  </font> <span id=itemname_'.$i.' class="'.$getRecord[$d]['itemdescription'].'">'.$getRecord[$d]['itemdescription'].'</span>';?></a>
        </li>
	<?php 
		$i++;
	} 
	echo '</ul>';
}
}//end edit else
exit; 
?>