<script type="text/javascript">
var open_obj = new Array()
function Expand(obj,checkbox) {

    var obj2 = obj;
	//alert(obj+','+checkbox)
    var img_obj = "img_" + obj;
    var div_obj = "div_" + obj;
    var check_obj = "check_" + obj;

	if (open_obj[obj] == true) {

		if (checkbox != "1") 
		{
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

$parents	=	array();

function ajax_tree($enable_checkboxes = "0", $enable_tables = "0",$form=0,$m=array()) {
global $AdminDAO, $parents,$cat;
$enable_tables	=	0;
if(sizeof($m) > 0)
{
	$itself		=	$m[0];//the category whose children are going to be show should not be show as well
	$p_query	=	"SELECT fkparentid FROM subcategory WHERE fkcategoryid = '$itself'";
	$p_res		=	$AdminDAO->queryresult($p_query);
	if(sizeof($p_res) > 0 )
	{
		foreach($p_res as $p_row)
		{
			$parents_query	=	"SELECT pksubcatid FROM subcategory WHERE fkcategoryid = '$p_row[fkparentid]'";
			$parents_res	=	$AdminDAO->queryresult($parents_query);
			if(sizeof($parents_res) > 0)
			{
				foreach($parents_res as $prow)
				{
					$parents[]		=	$prow['pksubcatid'];	
				}
			}//if
		}
	}//if
	
	foreach($m as $mm)
	{
		$mmx.=","."'$mm'";	
	}
	//$m	=	trim(implode(",",$m),",");//all children and children to children including this category itself should not be included in menu
	$m	=	trim($mmx,",");
}
else
{
	$m	=	"";
}
$query_cat	=	" SELECT pksubcatid as id,
						name,
						IF (subcategory.fkparentid IS NULL,0,subcategory.fkparentid) pid,
						(SELECT name FROM category WHERE pkcategoryid = pid )as parent
					FROM
						`category` 
					LEFT JOIN 	subcategory	 ON ( pkcategoryid = fkcategoryid)
					";
if($m!="")
{
	$query_cat	.="	WHERE pksubcatid NOT IN($m) AND fkcategoryid != '$itself'";
}
$query_cat	.= " ORDER BY name ASC";
$catres		=	$AdminDAO->queryresult($query_cat);
$options	=	array();
if(sizeof($catres)>0)
{
	foreach($catres as $catrow)
	{
		$arr			=	array();
		$arr['url']		=	"";
		$arr['name']	=	$catrow['name'];
		if($catrow['parent'] =="")
		{
		//	$catrow['parent']	=	0;	
		}
		$arr['parent']	=	$catrow['parent'];
		$arr['id']		=	$catrow['id'];
		array_push($options,$arr);
	}
}//if


//dump($options);

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
	$menu = array();
	return $ajax_tree;

}

function options($option_depth = "0", $options, $enable_checkboxes, $enable_tables, $parent = "", $tier = "0") {
	global $parents, $cat;
	$tier = $tier + 2;
	$output = "";

	global $ajax_tree_table;

	if (!isset($ajax_tree_table)) {

		$ajax_tree_table['0'] = "D5EAFF";
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

				$output .= "<table  cellpadding=\"1\" cellspacing=\"0\" border=\"0\">";
				$output .= "<tr><td  bgcolor=\"#".$ajax_tree_table[$option_depth]."\">\n";

			}

			for ($l = 0; $l < $tier-2; $l++) {

				$output .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

			}

			if ($enable_checkboxes=="1")
			{
				$catid	=	$options[$k][id];
				//$output .= "<input type=\"checkbox\" name=\"categories[]\"  value=\"1\" onclick=\"Expand('".name_to_id($options[$k]['name'])."','1')\">";
				$output	.=" <input name=\"categories[]\" value=\"$catid\" type=\"checkbox\"  ";
				if(@in_array($catid,$parents) || @in_array($catid,$cat))
				{
					$output .= "checked=checked";
				}//if
			
                  $output .=" onclick=\"Expand('".name_to_id($options[$k]['id'])."','1')\" /> ";
		}
			if ($options[$k]['url']!="") {

				$option_link = "<a href=\"".$options[$k]['url']."\" class=\"ajax_tree$option_depth\">";

			} else {

				$option_link = "<a href=\"javascript:;\" onclick=\"Expand('".name_to_id($options[$k]['id'])."')\" class=\"ajax_tree$option_depth\">";

			}

			if ("$sub_options"!="") {

				$output .= "<a href=\"javascript:;\" onclick=\"Expand('".name_to_id($options[$k]['id'])."')\"><img src=\"../OpenCrypt/collapsed.gif\" alt=\"Click to Expand/Collapse Option\" title=\"Click to Expand/Collapse Option\" id=\"img_".name_to_id($options[$k]['id'])."\" border=0 hspace=8 style=\"outline=none\"></a>$option_link".$options[$k]['name']."</a>\n";

				if ("$enable_tables"=="1") {

					$output .= "</td></tr></table>\n";

				}

				$output .= "<div style=\"display:none;\" id=\"div_".name_to_id($options[$k]['id'])."\">\n".$sub_options."</div>\n";

			} else {

				$output .= "<img src=\"../OpenCrypt/expanded.gif\" alt=\"Click to Expand/Collapse Option\" title=\"Click to Expand/Collapse Option\" id=\"expand_".name_to_id($options[$k]['id'])."\" border=0 hspace=8></a>$option_link".$options[$k]['name']."</a>\n";

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
function getchildern($id)
{
	global $menu;
	$q	="	SELECT 
					pksubcatid,
					fkcategoryid,
					name
				FROM
					category,
					subcategory
				WHERE
					pkcategoryid = subcategory.fkcategoryid AND
					subcategory.fkparentid = '$id'  
			";
	$r	=	mysql_query($q);
	if(mysql_num_rows($r) > 0)
	{
		while($obj1	=	mysql_fetch_object($r))
		{
			$menu[$obj1->pksubcatid]	=	$obj1->pksubcatid;
			getchildern($obj1->fkcategoryid);//recursive call
		}//while
	}//if
}
function getparents($childid)
{
	//print"$childid....";
	//global $parentids;
	$parentids		=	array();
	$q1			=	"SELECT fkparentid
							FROM 
								tblsubcategory
							WHERE
								fkcategoryid ='$childid'
								
					";
	$r1	=	mysql_query($q1);
	if(mysql_num_rows($r1) > 0)
	{
		while($obj1	=	mysql_fetch_object($r1))
		{
			//print"$obj1->fkparentid...";
			$parentids[]	=	$obj1->fkparentid;
			getparents($obj1->fkparentid);
		}
	}
	//print"<br>";
	return($parentids);
}//getparents
?>