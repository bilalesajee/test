<?php
/********************* function grid ******************************/
/* accepts parameters
	1. $labels 		=	Field Labels 
	2. $fields 		=	Database Fields
	3. $res			=	Database Resultset
	4. $limit 		=	Paging Limit
	5. $totalrows 	= 	Total Results for paging
	6. $navbtn 		=	Button Image or Text
	7. $navbtnac	=	Button action
	8. $dest		= 	Destination Page
	9. $div		= 	Div of the grid
	10. $css 		=	Grid CSS path
*******************************************************************/
function grid($labels='',$fields='',$res='',$limit=10,$totalrows=0,$navbtn='',$navbtnac='',$dest='', $div='', $css='')
{
$labels = array("ID","Brand Name","Country");
$fields = array("pkbrandid","brandname","countryname");

	$labelsize	=	sizeof($labels);
	$fieldsize	= 	sizeof($fields);
	if($labelsize!=$fieldsize)
	{
		echo "Label count does not match with database Fields.";
		exit;
	}//end if
//building table...
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1" >
		<thead>
			<tr>
			<th width="3%" height="25" align="left" >
				<input type="checkbox" name="chkAll" value="checkbox" id="chkAll" onClick="checkAll(this,document.brandformmain.brands)"/>			</th>
			<?php 
			for($i=0;$i<$labelsize;$i++)
			{
			?>
			<th width="22%" align="left">
				<?php echo $labels[$i];?>
				<a href="Javascript: call_ajax_sort('<?php echo $fields[$i];?>','asc',<?php echo $page;?>,'<?php echo $dest;?>','<?php echo $div;?>')">
					<img src="<?php echo IMGPATH;?>active_sortup.gif" width="8" height="10" hspace="2" border="0" />
				</a>
				<a href="Javascript: call_ajax_sort('<?php echo $fields[$i];?>','desc',<?php echo $page;?>,'<?php echo $dest;?>','<?php echo $div;?>')">
					<img src="<?php echo IMGPATH;?>active_sortdown.gif" width="8" height="10" border="0" />
				</a>
			</th>
			<?php
			}//end for
			?>
			
			
			
			<?php /*?><th width="47%"  align="left">
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
			<th width="14%"  align="left"><!--<a href="javascript: showhide('mainbody','main-body')" id="main-body">Hide</a>--></th><?php */?>

			</tr>
		</thead>	
</table>
<?php

}//end function grid
grid();
?>