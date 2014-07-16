<div class="top-bar">
<?php
global $button;
$button->makebutton("Delete","javascript: loadsection('center-column','deletestore.php'')");
$button->makebutton("Edit","javascript: loadsection('center-column','addstore.php')");
$button->makebutton("Add Store","javascript: loadsection('center-column','addstore.php')");
$button->makebutton("Home","javascript: loadsection('center-column','managestores.php')");
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
			<input type="submit" name="Submit" value="Search Stores" />
			</label>
</div>