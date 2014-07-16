<div class="top-bar">
<?php
global $button;

$button->makebutton("Delete","javascript: loadsection('center-column','deletestock.php')");
$button->makebutton("Edit","javascript: loadsection('center-column','addstock.php')");
$button->makebutton("Add Stock","javascript: loadsection('center-column','addstock.php')");
$button->makebutton("Stock Detail","javascript: loadsection('center-column','stockdetails.php')");
$button->makebutton("Home","javascript: loadsection('center-column','managestocks.php')");
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
			<input type="submit" name="Submit" value="Search Stocks" />
			</label>
		    <select name="select" id="select">
		      <option value="0">Country</option>
		      <option value="UK">UK</option>
		      <option>USA</option>
		      <option>Pakistan</option>
		      <option>Australia</option>
            </select>
            <select name="select2" id="select2">
              <option>Supplier</option>
              <option>ali &amp;co</option>
              <option>Usma &amp; co</option>
                        </select>
            <select name="select3" id="select3">
              <option>Brand</option>
              <option>Nestle</option>
              <option>Haleeb</option>
              <option>Almaraee</option>
                        </select>
            <select name="select4" id="select4">
              <option>Category</option>
              <option>Ice Cream</option>
              <option>Milk</option>
                        </select>
            <select name="select5" id="select5">
              <option>Expiry</option>
              <option>12-5-2009</option>
              <option>12-6-2009</option>
                        </select>
            <select name="select6" id="select6" >
              <option>Shipment</option>
              <option>D2(12-5-2009)</option>
              <option>P2(12-6-2009)</option>
            </select>
</div>