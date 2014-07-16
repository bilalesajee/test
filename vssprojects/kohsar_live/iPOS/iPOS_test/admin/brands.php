<?php

include("../includes/security/adminsecurity.php");
global $AdminDAO,$Component;
$condition		=	"";
//********sorting ***********
$qs	= $_SESSION['qs']	=	$_SERVER['QUERY_STRING'];
$sorting_field = $_GET['field'];
if(!$_GET['field'])
{
	$sorting_field ='pkbrandid';
}
$sorting_order = $_GET['order'];
if(!$sorting_order)
{
	$sorting_order = 'desc';
}
//*************delete************************
 $delid			=	$_REQUEST['delid'];
 $oper			=	$_REQUEST['oper'];
//****************search***********************
$searchString	=	filter($_REQUEST['searchString']);
$searchField 	=	$_REQUEST['searchField'];
$search 		=	$_REQUEST['_search'];
$searchOper	 	=	$_REQUEST['searchOper'];
$condition="";
//******************************************/
switch($oper)
{
	case 'del':
	{
		$condition="";
		$ids	=	explode(",",$delid);
		foreach($ids as $value)
		{
			if($value!='')
			{
				$delcondition =" pkbrandid = '$value' ";
				$AdminDAO->deleterows('brand',$delcondition);
			}
		}
		break;
	}
}
$k	.=	$sql;
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
			$searchOper	=	"LIKE '%"."$searchString'";
			break;
		}
		case 'cn': 
		{
			$searchOper	=	" LIKE '%"."$searchString"."%'";
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
/*$limit	=	$_GET['rows'];
$sidx	=	$_GET['sidx'];
$sord	=	$_GET['sord'];
if(!$sidx)
{
	$sidx =1;
}*/
$from		=	" brand b, countries c ";
$fields		=	" b.*,c.countryname ";
if($condition!='')
{
	$condition.=" AND ";
}
$condition				.=	" b.fkcountryid	=	c.pkcountryid";
$brands_total_array 	=	$AdminDAO->getrows($from,$fields,$condition,$sorting_field,$sorting_order,$start,$end);
$count					=	count($brands_total_array);

$Paging->TotalResults 	=	$count;
$Paging->ResultsPerPage =	10; 
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
$brands_array =	$AdminDAO->getrows($from,$fields,$condition,$sorting_field,$sorting_order,$start,$end);
if($Paging->TotalResults > $Paging->ResultsPerPage)
{ 
	$pagelinks = $Paging->pageHTML('javascript: call_ajax_paging("'.$_SERVER["QUERY_STRING"].'&page=~~i~~")');
}
if($pagelinks)
{
	$pagelinks	=	"<table width='100%'>
						<tr>
							<td align='right'>
								$pagelinks
							</td>
						</tr>
					</table>";
}
//
//function grid(Labels_array,database_fields_array,Dbresults_array,paging_Limit,totalrecord,Navigation_buttons_array,Navigation_btn_links array,csspath);

?>

<link rel="stylesheet" type="text/css" href="../includes/css/styles.css" />
<form name="brandformmain"  method="post" id="brandformmain">
	<?php
		print "$pagelinks";
	?>
	<table width="100%" border="0" cellspacing="0" cellpadding="1" >
		
		<thead>
			<tr>
			<th width="3%" height="25" align="left" >
				<input type="checkbox" name="chkAll" value="checkbox" id="chkAll" onClick="checkAll(this,document.brandformmain.brands)"/>			</th>
			<th width="22%" align="left">
			ID
			<a href="Javascript: call_ajax_sort('pkbrandid','asc',<?php echo $page;?>,'brands.php','brands')">
				<img src="<?php echo IMGPATH;?>active_sortup.gif" width="8" height="10" hspace="2" border="0" />			</a>
			<a href="Javascript: call_ajax_sort('pkbrandid','desc',<?php echo $page;?>,'brands.php','brands')">
				<img src="<?php echo IMGPATH;?>active_sortdown.gif" width="8" height="10" border="0" />			</a>			</th>
			<th width="47%"  align="left">
			Brand Name
			<a href="Javascript: call_ajax_sort('brandname','asc',<?php echo $page;?>,'brands.php','brands')">
				<img src="<?php echo IMGPATH;?>active_sortup.gif" width="8" height="10" hspace="2" border="0" />			</a>
			<a href="Javascript: call_ajax_sort('brandname','desc',<?php echo $page;?>,'brands.php','brands')">
				<img src="<?php echo IMGPATH;?>active_sortdown.gif" width="8" height="10" border="0" />			</a>			</th>
			<th width="14%"  align="left">
			Country
			<a href="Javascript: call_ajax_sort('countryname','asc',<?php echo $page;?>,'brands.php','brands')">
				<img src="<?php echo IMGPATH;?>active_sortup.gif" width="8" height="10" hspace="2" border="0" />			</a>
			<a href="Javascript: call_ajax_sort('countryname','desc',<?php echo $page;?>,'brands.php','brands')">
				<img src="<?php echo IMGPATH;?>active_sortdown.gif" width="8" height="10" border="0" />			</a>			</th>
			<th width="14%"  align="left"><!--<a href="javascript: showhide('mainbody','main-body')" id="main-body">Hide</a>--></th>
			</tr>
		</thead>
	<tbody id="mainbody">
		<?php
			if($count == 0)
			{
		?>
		<tr>
			<td class="notice" colspan="5" align="center">
				Sorry! But no record was found.			</td>
		</tr>
		<?php
			}
		?>
		<div id="loading" class="loading">Loading...</div>
		<?php
		for($b=0;$b<=$count;$b++)
		{
			
			if($brands_array[$b][pkbrandid]!='')
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
		<tr id="tr<?php echo $brands_array[$b]['pkbrandid'];?>" onmousedown="highlight('<?php echo $brands_array[$b]['pkbrandid'];?>','<?php print"$class"; ?>','row')"  class="<?php echo $class;?>">
			<td>
				<input onClick="highlight('<?php echo $brands_array[$b]['pkbrandid'];?>','<?php print"$class"; ?>','chk')" type="checkbox" name="brands" id="cb<?php echo $brands_array[$b]['pkbrandid'];?>" value="<?php echo $brands_array[$b]['pkbrandid'];?>"/>			</td>
			<td>
				<?php echo $brands_array[$b]['pkbrandid'];?></td>
			<td>
				
					<?php echo $brands_array[$b]['brandname'];?></td>
			<td colspan="2">
				<?php echo $brands_array[$b]['countryname'];?>			</td>
		</tr>
		<?php
			}//end of if
		}//enf of for
		?>
	</tbody>
</table> 
<table border="0" cellspacing="0" cellpadding="1" width="920">
	<thead>
	<tr height="20" valign="middle">
		<th width="896" colspan="5" align="left" style="padding-left:15px">
				<a class='button2' id='addbrands' onmouseover="buttonmouseover(this.id)" onmouseout="buttonmouseout(this.id)" href="javascript:showbrandform(0)" title="Add Brand"><span class="addrecord">&nbsp;</span></a>&nbsp;
				<a class='button2' id='editbrands' onmouseover="buttonmouseover(this.id)" onmouseout="buttonmouseout(this.id)" href="javascript:showbrandform(1)" title="Edit Brand"><span class="editrecord">&nbsp;</span></a>&nbsp;
				<a class='button2' id='deletebrands' onmouseover="buttonmouseover(this.id)" onmouseout="buttonmouseout(this.id)" href="javascript:deletebrands()" title="Delete Brands"><span class="deleterecord">&nbsp;</span></a>&nbsp;
				<a href="javascript: getsuppliers('sugrid','viewsuppliers.php')" title="View Suppliers">SUPPLIERS</a></th>
	</tr>
	<thead>
</table>
<?php
		print "$pagelinks";
?>
<input type="hidden" name="page" id="page" value="{$page}"/> 
<input type="hidden" name="sorting_field" id="sorting_field" value="{$sorting_field}"/> 
<input type="hidden" name="sorting_order" id="sorting_order" value="{$sorting_order}"/>      
</form>
<script language="javascript">
	var brandsfordelete='';
	function deletebrands()
	{
		var selectedbrands	=	getselected();
		if(selectedbrands.length > 1)
		{
			if(confirm('Are you sure to delete Selected brand(s)?'))
			{
				//var br1	=	document.getElementById('brandformmain').brands;
				//alert(br1);
				for (i = 0; i < selectedbrands.length; i++)
				{
				//	var v	=	br1[i].value;
					brandsfordelete	+=	','+selectedbrands[i];
				
					
				}//for
//				alert(brandsfordelete);
				jQuery('#brands').load('brands.php?oper=del&delid='+brandsfordelete+'&<?php echo $qs?>').$("#loading");
				//alert('a');
			}//if confirm
		}//if selected brands
		else
		{
			alert("Please select at least one brand to DELETE.");
		}
	}
	loading();
</script>