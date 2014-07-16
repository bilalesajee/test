<?php
include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$condition	=	"";
//********sorting ***********
$sorting_field = $_GET['field'];
if(!$_GET['field'])
{
	$sorting_field ='pksupplierid';
}
$sorting_order = $_GET['order'];
if(!$sorting_order)
{
	$sorting_order = 'desc';
}
//*************delete
$delid			=	$_REQUEST['id'];
$oper			=	$_REQUEST['oper'];
//****serach***********************


$searchString	=	$_REQUEST['searchString'];
$searchField 	=	$_REQUEST['searchField'];
$search 		=	$_REQUEST['_search'];
$searchOper	 	=	$_REQUEST['searchOper'];
//fotm data
$code3		 	=	$_REQUEST['code3'];
$codeint	 	=	$_REQUEST['codeint'];
$brandid			 	=	$_REQUEST['brandid'];
$condition="";
//*******************************
switch($oper)
{
	case 'del':
	{
		$condition="";
		$ids	=	explode(",",$id);
		foreach($ids as $value)
		{
			$condition =" pkbrandid = '$value' ";
			$AdminDAO->deleterows('brand',$condition);
			//$sql	=	" DELETE FROM countries WHERE pkcountryid = '$value';";
			//mysql_query($sql);
		}
	
		break;
	}
	case 'edit':
	{
		$sql	="UPDATE 
						countries
					SET 
						code3='$code3',
						codeint='$codeint'
					WHERE
						  	pkcountryid='$id'
				";
		break;
	}
	case 'add':
	{
		$pkcountryid	=	$AdminDAO->pkey('countries','pkcountryid');
		$sql	="INSERT 
					INTO 
						countries
					SET
						pkcountryid	=	'$pkcountryid',
						code3='$code3',
						codeint='$codeint'
					
				";
		break;
	}
}
if ($oper!='')
{
	mysql_query($sql);
}

	$k	.=	$sql;
// search variables***********************

/*bw - begins with ( LIKE val% )
eq - equal ( = )
ne - not equal ( <> )
lt - little ( < )
le - little or equal ( <= )
gt - greater ( > )
ge - greater or equal ( >= )
ew - ends with (LIKE %val )
cn - contain (LIKE %val% )
*/

if($search!='false')
{
	switch($searchOper)
	{
		case 'bw': 
		{
			$searchOper	=	" LIKE '$searchString". "%' ";
			break;
		}
		case 'eq': 
		{
			$searchOper	=	" = '$searchString'";
			break;
		}
		case 'ne': 
		{
			$searchOper	=	" <> '$searchString'";
			break;
		}
		case 'lt': 
		{
			$searchOper	=	" < '$searchString'";
			break;
		}
		case 'le': 
		{
			$searchOper	=	" <= '$searchString'";
			break;
		}
		case 'gt': 
		{
			$searchOper	=	" > '$searchString'";
			break;
		}
		case 'ge': 
		{
			$searchOper	=	" >= '$searchString'";
			break;
		}
		case 'ew': 
		{
			$searchOper	=	"LIKE %"."'$searchString'";
			break;
		}
		case 'cn': 
		{
			$searchOper	=	" LIKE %"."'$searchString"."%'";
			break;
		}
	}
		if($searchField!='')
		{
			$condition	=	"  $searchField $searchOper ";
		}
	}
	

$page 	=	$_GET['page'];
if($page=="")
{
	$page=1;
}
$limit	=	$_GET['rows'];
//$limit	=	10;

if(!$sidx)
{
	$sidx =1;
}
$from=" supplieroragent s, countries c, brandsupplier bs ";
$fields=" s.* , c.countryname ";
if($condition!='')
{
	$condition.=" AND ";
}
$condition.=" s.fkcountryid	=	c.pkcountryid AND bs.fkbrandid = '$brandid' AND bs.fksupplierid = s.pksupplierid ";
$brands_total_array =	$AdminDAO->getrows($from,$fields,$condition,$sorting_field,$sorting_order,$start,$end);
$count	=	count($brands_total_array);
if(!$count)
{
?>
	<span class="notice">No Suppliers Found</span>
<?php
	exit;
}
$Paging->TotalResults = $count;
$Paging->ResultsPerPage = 10; 
$page  = $Paging->getCurrentPage();
if($page > 1)
{
	$start = ($page-1) * $Paging->ResultsPerPage;
}
else 
{
	$start = 0;
}
$end   = $Paging->ResultsPerPage;

$suppliers_array =	$AdminDAO->getrows($from,$fields,$condition,$sorting_field,$sorting_order,$start,$end);
//print_r($suppliers_array);
if($Paging->TotalResults > $Paging->ResultsPerPage)
{ 
	$pagelinks = $Paging->pageHTML('javascript: call_ajax_paging("'.$_SERVER["QUERY_STRING"].'&page=~~i~~")');
}
if($pagelinks)
{
	echo $pagelinks;
}
?>
<link rel="stylesheet" type="text/css" href="../includes/css/styles.css" />
<form name="bloform"  method="post" >
	<table width="100%" border="0" cellspacing="0" cellpadding="1" >
		<thead>
			<tr>
			<th width="3%" height="25" align="left" >
				<input type="checkbox" name="checkbox" id="chkAll" value="chkAll" />			</th>
			
			<th width="41%" align="left">
			ID
			<a href="Javascript: call_ajax_sort('pksupplierid','asc',<?php echo $page;?>,'viewsuppliers.php?brandid=<?php echo $brandid?>','sugrid')">
				<img src="<?php echo IMGPATH;?>active_sortup.gif" width="8" height="10" hspace="2" border="0" />			</a>
			<a href="Javascript: call_ajax_sort('pksupplierid','desc',<?php echo $page;?>,'viewsuppliers.php?brandid=<?php echo $brandid?>','sugrid')">
				<img src="<?php echo IMGPATH;?>active_sortdown.gif" width="8" height="10" border="0" />			</a>			</th>
			<th width="28%"  align="left">Company <a href="Javascript: call_ajax_sort('countryname','asc',<?php echo $page;?>,'viewsuppliers.php?brandid=<?php echo $brandid?>','sugrid')"> <img src="<?php echo IMGPATH;?>active_sortup.gif" width="8" height="10" hspace="2" border="0" /> </a> <a href="Javascript: call_ajax_sort('countryname','desc',<?php echo $page;?>,'viewsuppliers.php?brandid=<?php echo $brandid?>','sugrid')"> <img src="<?php echo IMGPATH;?>active_sortdown.gif" width="8" height="10" border="0" /></a></th>
			<th width="28%"  align="left">Contact Person
			<a href="Javascript: call_ajax_sort('contactperson1','asc',<?php echo $page;?>,'viewsuppliers.php?brandid=<?php echo $brandid?>','subgrid')"> <img src="<?php echo IMGPATH;?>active_sortup.gif" width="8" height="10" hspace="2" border="0" /> </a> <a href="Javascript: call_ajax_sort('contactperson1','desc',<?php echo $page;?>,'viewsuppliers.php?brandid=<?php echo $brandid?>','sugrid')"> <img src="<?php echo IMGPATH;?>active_sortdown.gif" width="8" height="10" border="0" /> </a></th>
			</tr>
		</thead>
	<tbody>
		<?php
		for($b=0;$b<=$count;$b++)
		{
			
			if($suppliers_array[$b][pksupplierid]!='')
			{
				if($b%2==0)
				{
					 $class="even";
				}
				else
				{
					 $class="odd";
				}
				
		?>
		<tr id="tr<?php echo $suppliers_array[$b]['pksupplierid'];?>" onclick="highlight('<?php echo $suppliers_array[$b]['pksupplierid'];?>','<?php print"$class"; ?>')"  class="<?php echo $class;?>">
			<td>
				<input type="checkbox" name="<?php echo $suppliers_array[$b]['pksupplierid'];?>" id="cb<?php echo $suppliers_array[$b]['pksupplierid'];?>" value="<?php echo $brands_array[$b]['pkbrandid'];?>"/>			</td>
			<td>
				<?php echo $suppliers_array[$b]['pksupplierid'];?>			</td>
			<td><?php echo $suppliers_array[$b]['suppliername'];?></td>
			<td><?php echo $suppliers_array[$b]['contactperson1'].', '.$suppliers_array[$b]['contactperson2'];?></td>
		</tr>
		<?php
			}//end of if
		}//enf of for
		?>
		<tr>
			<th height="25" colspan="5">
				<a href="javascript: showaddform('string')">
					ADD				</a>
					|
				<a href="javascript: showaddform('32')">
					EDIT				</a>
					|
				<a href="#">
					DELET				</a>
					|
				<a href="javascript: getsuppliers('brands','viewsuppliers.php','1')">
					SUPPLIERS				</a>			</th>
		</tr>
	</tbody>
</table> 
<input type="hidden" name="responder_1" id="responder_1" value=""/> 
<input type="hidden" name="page" id="page" value="{$page}"/> 
<input type="hidden" name="sorting_field" id="sorting_field" value="{$sorting_field}"/> 
<input type="hidden" name="sorting_order" id="sorting_order" value="{$sorting_order}"/>      
</form>