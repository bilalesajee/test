<div class="top-bar">
<?php
global $button;
$button->makebutton("Delete","javascript: loadsection('center-column','deleteattribute.php'')");
$button->makebutton("Edit","javascript: loadsection('center-column','manageattributes.php')");
$button->makebutton("View Options","javascript: loadsection('center-column','manageattributeoptions.php')");
$button->makebutton("Home","javascript: loadsection('center-column','manageattributes.php')");
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
			<input type="submit" name="Submit" value="Search Attributes" />
			</label>
</div>