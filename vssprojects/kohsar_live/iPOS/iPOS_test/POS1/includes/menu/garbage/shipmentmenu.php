<div class="top-bar">
<?php
global $button;
$button->makebutton("Groups","javascript: loadsection('center-column','manageshipmentgroups.php'')");
$button->makebutton("Delete","javascript: loadsection('center-column','manageshipment.php')");
$button->makebutton("Shipment Details","javascript: loadsection('center-column','shipmentdetails.php')");
$button->makebutton("Edit","javascript: loadsection('center-column','addshipment.php')");
$button->makebutton("Add Invoice","javascript: loadsection('center-column','addinvoice.php')");
$button->makebutton("Add Shipment","javascript: loadsection('center-column','addshipment.php')");
$button->makebutton("Home","javascript: loadsection('center-column','manageshipment.php')");
?>
<!--<h1>Products</h1>-->
<div class="breadcrumbs" id="breadcrumbs">
	<a href="#">Attributes</a>/ <a href="#">Options</a></div>
</div>
<br />
<div class="select-bar">
		    <label>
			</label>  <label>
		    <input type="text" name="search" />
		    </label>
		    <label>
			<input type="submit" name="Submit" value="Search Shipments" />
			</label>
		    <label>		    </label><label>			 Country<span class="last">
            <select name="select">
              <option>Pakistan</option>
              <option>USA</option>
              <option>UK</option>
              <option>Dubai</option>
            </select>
		    </span>Agent </label>
  <span class="last">
  <select name="select2">
              <option>Agent 1</option>
              <option>Agent 2</option>
            </select>
</span>
  <label>
<input type="radio" name="btn" />
Open </label>
<label>
<input type="radio" name="btn" checked="checked"/>
</label>
Closed 
<input type="submit" name="Submit2" value="Go" />
</div>
