<div class="top-bar">
<?php
global $button;
$button->makebutton("Delete","javascript: loadsection('center-column','managedemands.php'')");
$button->makebutton("Edit","javascript: loadsection('center-column','demandgeneration.php')");
$button->makebutton("Receive","javascript: loadsection('center-column','receivedemands.php')");
$button->makebutton("Fulfill","javascript: loadsection('center-column','fulfillment.php')");
$button->makebutton("Generate","javascript: loadsection('center-column','demandgeneration.php')");
$button->makebutton("Home","javascript: loadsection('center-column','managedemands.php')");
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
			<input type="submit" name="Submit" value="Search Demands" />
			</label>
</div>