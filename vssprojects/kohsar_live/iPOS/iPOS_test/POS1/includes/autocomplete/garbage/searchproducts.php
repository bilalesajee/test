<?php 
	include('../security/adminsecurity.php'); 	
	$SQL_FROM = 'barcode';//table name
	$SQL_WHERE = 'productname';//item description from barcode
	$searchq		=	strip_tags($_GET['q']);
	$list			=	explode(' ',$searchq);
	// preparing the search condition
	foreach($list as $val){
		$condition.="%$val%";
	}	
	$condition	=	str_replace('%%','%',$condition);
	$getRecord_sql	=	"SELECT  productname , 
							barcode as bc,
							pkproductid,
							fkproductid
						FROM 
							product
						WHERE
							productname 
							 LIKE '$condition' LIMIT 0,20";
	$getRecord		=	$AdminDAO->queryresult($getRecord_sql);
	if(strlen($searchq)>0){
	echo '<div id="scrolable"><ul id="autocompletelist" name="autocompletelist">';
	$i=1;
	for($d=0;$d<count($getRecord);$d++) // building the serached lists
	{
		$barcodeidlist	=	 $getRecord[$d]['bc'];
		$barcodeid		=	 $getRecord[$d]['pkproductid'];	?>
		<li id="<?php echo $i;?>" value="<?php echo $barcodeidlist;?>">
          <a href="javascript:void(0)" 
          onclick="getlitext('<?php echo $getRecord[$d]['productname'];?>','<?php echo $barcodeidlist;?>')"   
          id="<?php echo $i;?>_l" class="<?php echo $barcodeidlist;?>">
          <?php echo ' <font color=blue> '.$getRecord[$d]['bc'].'  </font> <span id=itemname_'.$i.' class="'.$getRecord[$d]['productname'].'"></span>';?>
          </a>
        </li>
	<?php $i++;	
	} 
	echo '</ul>';
}
exit; 
?>