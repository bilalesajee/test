<?php
require('dbManager.php');
$query = "SELECT * FROM  location";
$obj=new dbManager();
$array= $obj->fetch_result($query);
?>
<select  id="loc">
    <option  disabled="disabled" selected="selected">-please select location-</option>
    <?php foreach ($array as $location) {
        
    } { ?>
        <option value="<?php echo $location['CODE']; ?>" id=""><?php echo $location['CITY']; ?></option> 	
    <?php } ?>
</select>