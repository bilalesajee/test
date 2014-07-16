<div class="top-bar">
<?php
global $button;
$button->makebutton("Delete","javascript: loadsection('center-column','deletestore.php'')");
$button->makebutton("Edit","javascript: loadsection('center-column','addsupplier.php')");
$button->makebutton("Add Supplier","javascript: loadsection('center-column','addsupplier.php')");
$button->makebutton("Home","javascript: loadsection('center-column','managesuppliers.php')");
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
			<input type="submit" name="Submit" value="Search Suppliers" />
			</label>
</div>