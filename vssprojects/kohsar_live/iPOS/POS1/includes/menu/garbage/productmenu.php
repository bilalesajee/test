<div class="top-bar">
<?php
global $button;
$button->makebutton("Delete","javascript: loadsection('center-column','deletetype.php')");
$button->makebutton("Edit Type","javascript: loadsection('center-column','addtype.php')");
$button->makebutton("Images","javascript: loadsection('center-column','productimages.php')");
$button->makebutton("Categories","javascript: loadsection('center-column','attachcategory.php')");
$button->makebutton("View Types","javascript: loadsection('center-column','manageinstances.php')");
$button->makebutton("Add Type","javascript: loadsection('center-column','addtype.php')");
$button->makebutton("Add Product","javascript: loadsection('center-column','addproduct.php')");
$button->makebutton("Home","javascript: loadsection('center-column','manageproducts.php')");
?>
<!--<h1>Products</h1>-->
<div class="breadcrumbs" id="breadcrumbs">
	<a href="#">Attributes</a>/ <a href="#">Options</a></div>
</div>
<br />
<div class="select-bar">
		    <label>
		    <input type="text" name="search" />
		    </label>
		    <label>
			<input type="submit" name="Submit" value="Search Products" />
			</label>
</div>