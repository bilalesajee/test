<?php
require('dbManager.php');
$query = "SELECT d.DEPT_CODE, d.DEPT_NAME FROM department d";
$obj = new dbManager();
$array = $obj->fetch_result($query);
?>
<select  id="departList">
    <option  disabled="disabled" selected="selected">-please select Department-</option>
    <?php foreach ($array as $dept) {
        ?>
        <option value="<?php echo $dept['DEPT_CODE']; ?>" id=""><?php echo $dept['DEPT_NAME']; ?></option>
    <?php } ?>
</select>
