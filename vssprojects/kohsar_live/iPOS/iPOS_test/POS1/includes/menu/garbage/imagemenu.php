<div class="top-bar">
<?php
global $button;
$button->makebutton("Delete","javascript: loadsection('center-column','deleteimage.php')");
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
			<input type="submit" name="Submit" value="Search Images" />
			</label>
</div>