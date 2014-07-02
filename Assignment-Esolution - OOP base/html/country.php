<?php
require('dbManager.php');
$query = "SELECT * FROM country";
$obj=new dbManager();
$array= $obj->fetch_result($query);
?>
<select  id="cnt">
    <option  disabled="disabled" selected="selected">-please select country-</option>
    <?php foreach ($array as $country) {     ?>
    <option value="<?php echo $country['ID']; ?>" id="<?php echo $country['COUNTRY']; ?>" ><?php echo $country['COUNTRY']; ?></option>
    <?php } ?>
</select>