<?php
require('dbManager.php');
$query = "SELECT * FROM department";
$obj=new dbManager();
$array= $obj->fetch_result($query);
?>
<select  id="dept">
    <option  disabled="disabled" selected="selected">-please select Department-</option>
    <?php foreach ($array as $dept) {
        
      ?>
    <option value="<?php echo $dept['DEPT_CODE']; ?>" id=""><?php echo $dept['DEPT_NAME']; ?></option>
    <?php } ?>
</select>
