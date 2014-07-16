<div class="top-bar">
<?php
global $button;
$button->makebutton("Groups","javascript: loadsection('center-column','manageshipmentgroups.php'')");
$button->makebutton("Delete","javascript: loadsection('center-column','manageshipmentgroups.php')");
$button->makebutton("Edit","javascript: loadsection('center-column','addshipmentgroup.php')");
$button->makebutton("Add","javascript: loadsection('center-column','addshipmentgroup.php')");
$button->makebutton("Home","javascript: loadsection('center-column','manageshipment.php')");
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
			<input type="submit" name="Submit" value="Search Shipment Groups" />
			</label>
</div>