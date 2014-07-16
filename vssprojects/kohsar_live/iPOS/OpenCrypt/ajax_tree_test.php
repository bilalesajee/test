
<style type="text/css">
.text					{ font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 13px; color : #000000; }
a.text			{ color: #0000aa; text-decoration: none; }
a.text:hover		{ color: #0000aa; text-decoration: underline; }

.ajax_tree0		{ font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 18px; color: #D21A20; text-decoration: none; }
a.ajax_tree0		{ color: #D21A20; text-decoration: none; }
a.ajax_tree0:hover        { color: #D21A20; text-decoration: underline; }

.ajax_tree1		{ font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 17px; color: #D21A20; text-decoration: none; }
a.ajax_tree1		{ color: #D21A20; text-decoration: none; }
a.ajax_tree1:hover        { color: #D21A20; text-decoration: underline; }

.ajax_tree2		{ font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 16px; color: #D21A20; text-decoration: none; }
a.ajax_tree2		{ color: #D21A20; text-decoration: none; }
a.ajax_tree2:hover        { color: #D21A20; text-decoration: underline; }

.ajax_tree3		{ font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 15px; color: #D21A20; text-decoration: none; }
a.ajax_tree3		{ color: #D21A20; text-decoration: none; }
a.ajax_tree3:hover        { color: #D21A20; text-decoration: underline; }

.ajax_tree4		{ font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 14px; color: #D21A20; text-decoration: none; }
a.ajax_tree4		{ color: #D21A20; text-decoration: none; }
a.ajax_tree4:hover        { color: #D21A20; text-decoration: underline; }

.ajax_tree5		{ font-family : Verdana, Arial, Helvetica, sans-serif; font-size : 13px; color: #D21A20; text-decoration: none; }
a.ajax_tree5		{ color: #D21A20; text-decoration: none; }
a.ajax_tree5:hover        { color: #D21A20; text-decoration: underline; }
</style>
<script type="text/javascript">
var open_obj = new Array()
function Expand(obj,checkbox) {

    var obj2 = obj;
    var img_obj = "img_" + obj;
    var div_obj = "div_" + obj;
    var check_obj = "check_" + obj;

	if (open_obj[obj] == true) {

		if (checkbox != "1") {

			document.getElementById(div_obj).style.display = "none";
			document.getElementById(img_obj).src = "../OpenCrypt/collapsed.gif";
			open_obj[obj] = false;

		}

	} else {

		document.getElementById(div_obj).style.display = "block";
		document.getElementById(img_obj).src = "../OpenCrypt/expanded.gif";
		open_obj[obj] = true;

	}

	
	if (checkbox == "1")
	{

		var checkboxes = document.forms["form"].elements[check_obj];
		for (var i = 0; i < checkboxes.length; i++) {

			if (checkboxes[i].checked == true) {

				checkboxes[i].checked = false;

			} else {

				checkboxes[i].checked = true;

			}
		}

	}

}

</script>

</head>
<body bgcolor="#ffffff">
<?PHP
$ajax_tree_table['0'] = "ffffff";
$ajax_tree_table['1'] = "eeeeee";
$ajax_tree_table['2'] = "e5e5e5";
$ajax_tree_table['3'] = "dddddd";
$ajax_tree_table['4'] = "d5d5d5";
$ajax_tree_table['5'] = "cccccc";
?>
<?PHP
/**
Copyright (C) 2008 ionix Limited
http://www.ionix.ltd.uk/

This script was written by ionix Limited, and was distributed
via the OpenCrypt.com Blog.

AJAX Tree Menu with PHP
http://www.OpenCrypt.com/blog.php?a=23

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of
the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

GNU GPL License
http://www.opensource.org/licenses/gpl-license.php
*/


//ini_set('error_reporting', E_ALL | E_STRICT);
//ini_set('display_errors', 'On');
#ini_set('log_errors', 'On');
#ini_set('error_log', 'C:\XAMPP\xampp\htdocs\i\errors.log');


function ajax_tree($enable_checkboxes = "0", $enable_tables = "0",$form=0) {
global $AdminDAO;
/*
	 SELECT pkcategoryid, name, subcategory.fkparentid fkparentid
FROM `category`
LEFT JOIN subcategory ON ( pkcategoryid = fkcategoryid )
LIMIT 0 , 30 
*/
$query_cat	=	"  SELECT pkcategoryid as id, name, IF (subcategory.fkparentid IS NULL,0,subcategory.fkparentid) fkparentid
FROM `category`
LEFT JOIN subcategory ON ( pkcategoryid = fkcategoryid )
					";
$catres	=	$AdminDAO->queryresult($query_cat);
$options	=	array();
foreach($catres as $catrow)
{
	$arr		=	array();
	$arr['url']	=	"";
	$arr['name']=	$catrow['name'];
	$arr['id']	=	$catrow['id'];
	if($catrow['fkparentid'] == 0)
	{
		$arr['parent']	=	"";
		array_push($options,$arr);
	}
	else
	{
		$parent_query	=	"select fkparentid from subcategory WHERE fkcategoryid =$catrow['id'];";
		$p_res	=	$AdminDAO->queryresult($parent_query);
		foreach($p_res as $prow)
		{
			$parent_name_query	=	"SELECT name FROM categotry WHERE pkcategory = '$prow['fkparentid']'";	
			$pn_res	=	$AdminDAO->queryresult($parent_query);
			foreach($pn_res as $pn_row )
			{
				$arr['parent']	=	$pn_row['name'];
				array_push($options,$arr);
			}
		}
	}
	
}
dump($options);

# $tree_data must be a mutli-dimensional array containing the elements, name, url and parent.  Parent reflects the 'name' of the record this record should be listed under.  If set as '0', the record will be shown at the top level.  If the 'url' value is not present, the link/name text will be used to expand and collapse sub-links.
# $enable_checkboxes controls whether or not the checkboxes should be dislayed, enter '1' for yes and '0' for no.
# $enable_tables controls whether or not the link should be dislayed within tables, enter '1' for yes and '0' for no.

	

	if ("$enable_checkboxes"!="1") {

		$enable_checkboxes = "0";

	}

	if ("$enable_tables"!="1") {

		$enable_tables = "0";

	}

	if ("$enable_checkboxes"=="1")
	{
		if($form=='1')
		{
			$ajax_tree = "<form id=\"ajax_tree\" style=\"margin:0px\">";
		}
	} 
	else
	{

		$ajax_tree = "";

	}

	$option_depth = "0";

	$ajax_tree .= options($option_depth, $options, $enable_checkboxes, $enable_tables);

	if ("$enable_checkboxes"=="1")
	{
		if($form=='1')
		{
			$ajax_tree .= "</form>";
		}

	}

	return $ajax_tree;

}

function options($option_depth = "0", $options, $enable_checkboxes, $enable_tables, $parent = "0", $tier = "0") {
	$tier = $tier + 2;
	$output = "";

	global $ajax_tree_table;

	if (!isset($ajax_tree_table)) {

		$ajax_tree_table['0'] = "ffffff";
		$ajax_tree_table['1'] = "eeeeee";
		$ajax_tree_table['2'] = "e5e5e5";
		$ajax_tree_table['3'] = "dddddd";
		$ajax_tree_table['4'] = "d5d5d5";
		$ajax_tree_table['5'] = "cccccc";

	}

	$depth[$option_depth] = $option_depth;

	for ($k = 0; $k < count($options); $k++) {

		if ($options[$k]['parent']==$parent) {

			if (("$output"!="") && ("$enable_tables"!="1")) {

				$output .= "<br>";

			}

			$sub_options = options(($option_depth+1), $options, $enable_checkboxes, $enable_tables, $options[$k]['name'], $tier);

			if ("$enable_tables"=="1") {

				$output .= "<table width=\"100%\" cellpadding=\"4\" cellspacing=\"0\" border=\"0\">";
				$output .= "<tr><td bgcolor=\"#".$ajax_tree_table[$option_depth]."\">\n";

			}

			for ($l = 0; $l < $tier-2; $l++) {

				$output .= "&nbsp;";

			}

			if ($enable_checkboxes=="1")
			{
				$catid	=	$options[$k][id];
				//$output .= "<input type=\"checkbox\" name=\"categories[]\"  value=\"1\" onclick=\"Expand('".name_to_id($options[$k]['name'])."','1')\">";
				$output	.=" <input name=\"categories[]\" value=$catid type=\"checkbox\"  ";
				if(@in_array($category['pkcategoryid'],$cat))
				{
					$output .= "checked=checked";
				}//if
			
                  $output .=" onclick=\"Expand('".name_to_id($options[$k]['name'])."','1')\" /> ";
		}
			if ($options[$k]['url']!="") {

				$option_link = "<a href=\"".$options[$k]['url']."\" class=\"ajax_tree$option_depth\">";

			} else {

				$option_link = "<a href=\"javascript:;\" onclick=\"Expand('".name_to_id($options[$k]['name'])."')\" class=\"ajax_tree$option_depth\">";

			}

			if ("$sub_options"!="") {

				$output .= "<a href=\"javascript:;\" onclick=\"Expand('".name_to_id($options[$k]['name'])."')\"><img src=\"../OpenCrypt/collapsed.gif\" alt=\"Click to Expand/Collapse Option\" title=\"Click to Expand/Collapse Option\" id=\"img_".name_to_id($options[$k]['name'])."\" border=0 hspace=2></a>$option_link".$options[$k]['name']."</a>\n";

				if ("$enable_tables"=="1") {

					$output .= "</td></tr></table>\n";

				}

				$output .= "<div style=\"display:none;\" id=\"div_".name_to_id($options[$k]['name'])."\">\n".$sub_options."</div>\n";

			} else {

				$output .= "<img src=\"../OpenCrypt/expanded.gif\" alt=\"Click to Expand/Collapse Option\" title=\"Click to Expand/Collapse Option\" id=\"expand_".name_to_id($options[$k]['name'])."\" border=0 hspace=2></a>$option_link".$options[$k]['name']."</a>\n";

				if ("$enable_tables"=="1") {

					$output .= "</td></tr></table>\n";

				}

			}

		}
	}

	return $output;

}

function name_to_id($name) {

	$name = preg_replace("/[^a-zA-Z0-9s]/", "", $name);

	return $name;

}

?>