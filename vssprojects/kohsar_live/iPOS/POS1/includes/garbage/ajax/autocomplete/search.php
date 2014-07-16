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
/*
onkeyup="javascript:if(event.keyCode==13) {getlitext('<?php echo $getRecord[$d]['itemdescription'];?>','<?php echo $barcodeidlist;?>');}"
*/
exit; 
?>