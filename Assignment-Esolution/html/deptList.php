<?php
require('conn.php');
$query = "SELECT * FROM department";
$run = mysql_query($query);
?>
<select  id="dept">
    <option  disabled="disabled" selected="selected">-please select Department-</option>
    <?php while ($row = mysql_fetch_array($run)) { ?>
    <option value="<?php echo $row['DEPT_CODE']; ?>" id=""><?php echo $row['DEPT_NAME']; ?></option>
    <?php } ?>
</select>
