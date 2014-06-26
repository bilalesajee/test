<?php
require('conn.php');
$query = "SELECT * FROM country";
$run = mysql_query($query);
?>
<select  id="cnt">
    <option  disabled="disabled" selected="selected">-please select country-</option>
    <?php while ($row = mysql_fetch_array($run)) { ?>
    <option value="<?php echo $row['ID']; ?>" id="<?php echo $row['COUNTRY']; ?>"><?php echo $row['COUNTRY']; ?></option>
    <?php } ?>
</select>