<?php
require('dbManager.php');
$id = $_POST['id'];
$query = "SELECT * FROM city WHERE CODE=$id";
$obj=new dbManager();
$array= $obj->fetch_result($query);
?>
<select name="city" id="city">
    <?php foreach ($array as $city) {
        
      ?>
        <option id="<?php echo $city['ID']; ?>"> <?php echo $city['CITY']; ?> </option>
    <?php } ?>
</select>