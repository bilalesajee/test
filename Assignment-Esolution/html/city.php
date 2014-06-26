<?php
require('conn.php');
$id = $_POST['id'];
$query = "SELECT * FROM city WHERE CODE=$id";
$run = mysql_query($query);
?>
<select name="city" id="city">
    <?php while ($row = mysql_fetch_array($run)) { ?>
        <option id="<?php echo $row['ID']; ?>"> <?php echo $row['CITY']; ?> </option>
    <?php } ?>
</select>