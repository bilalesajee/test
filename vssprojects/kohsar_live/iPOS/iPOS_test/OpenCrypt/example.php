<?php
/*
include_once("../includes/security/adminsecurity.php");
global $AdminDAO;


*/

?>

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

	if (checkbox == "1") {

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

$tree_data = array(
	array(
	url => "",
	parent => "0",
	name => "Home"),
	array(
		url => "",
		parent => "Home",
		name => "About"),
		array(
		url => "",
		parent => "Home",
		name => "Contact"),
	array(
	url => "",
	parent => "0",
	name => "Our Products"),
		array(
		url => "http://www.OpenCrypt.com/",
		parent => "Our Products",
		name => "OpenCrypt"),
			array(
			url => "http://www.OpenCrypt.com/features.php",
			parent => "OpenCrypt",
			name => "Features and Benefits"),
			array(
			url => "http://demo.OpenCrypt.com/",
			parent => "OpenCrypt",
			name => "Demonstration"),
				array(
				url => "http://www.OpenCrypt.com/",
				parent => "Demonstration",
				name => "Another Tier"),
					array(
					url => "http://www.OpenCrypt.com/",
					parent => "Another Tier",
					name => "And Another"),
						array(
						url => "http://www.OpenCrypt.com/",
						parent => "And Another",
						name => "And Another Tier"),
		array(
		url => "http://www.Locked-Area.com/",
		parent => "Our Products",
		name => "Locked Area"),
		array(
		url => "http://www.DirectoryPass.com/",
		parent => "Our Products",
		name => "DirectoryPass"),
		array(
	url => "",
	parent => "0",
	name => "Home2"),
		array(
			url => "",
			parent => "Home2",
			name => "About"),
			array(
			url => "",
			parent => "Home",
			name => "Contact")		
);
/*
$tree_data = array();

$cats		=	array(	url => "",
						parent => "0",
						name => "Beverages"
					);
array_push($tree_data,$cats);


$cats		=	array(	url => "",
						parent => "Juices",
						name => "Orange Juice"
					);
array_push($tree_data,$cats);
$cats		=	array(	url => "",
						parent => "Juices",
						name => "Mango Juice"
					);
array_push($tree_data,$cats);

array_push($tree_data,$cats);
$cats		=	array(	url => "",
						parent => "Juices",
						name => "Banana Juice"
					);

array_push($tree_data,$cats);
$cats		=	array(	url => "",
						parent => "0",
						name => "Yougart"
					);
array_push($tree_data,$cats);
$cats		=	array(	url => "",
						parent => "Yougart",
						name => "Drinkable Yougart"
					);
array_push($tree_data,$cats);
$cats		=	array(	url => "",
						parent => "Yougart",
						name => "Nestle Yougart"
					);
array_push($tree_data,$cats);

$cats		=	array(	url => "",
						parent => "0",
						name => "Kitchen"
					);
array_push($tree_data,$cats);

$cats		=	array(	url => "",
						parent => "Kitchen",
						name => "Nestle Yougart"
					);
array_push($tree_data,$cats);

$cats		=	array(	url => "",
						parent => "Kitchen",
						name => "Recipes"
					);
array_push($tree_data,$cats);


$cats		=	array(	url => "",
						parent => "Beverages",
						name => "Juices"
					);

array_push($tree_data,$cats);
*/
require_once("../OpenCrypt/ajax_tree.php");
print ajax_tree($tree_data, 1, 1);
?>