<?php
require('conn.php');
$query = "SELECT * FROM location";
$run = mysql_query($query);
?>
<select  id="loc">
    <option  disabled="disabled" selected="selected">-please select location-</option>
    <?php while ($row = mysql_fetch_array($run)) { ?>
        <option value="<?php echo $row['CODE']; ?>" id=""><?php echo $row['CITY']; ?></option> 	
    <?php } ?>
</select>