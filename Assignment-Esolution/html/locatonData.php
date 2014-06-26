<?php
require_once('conn.php');
?>
<table width="50%" border="0" align="center" id="tlocation" ><tbody>
        <tr>
            <th width="18" scope="col" bgcolor="#0000FF">ID</th>
            <th width="40" scope="col">Code</th>
            <th width="27" scope="col">Detail</th>
            <th width="55" scope="col">Country</th>
            <th width="38" scope="col">City</th>
            <th width="80" scope="col">Action</th>
        </tr>
        <?php
        $select = "SELECT l.ID, l.CODE, l.DETAIL, l.COUNTRY, l.CITY FROM location l";
        ob_start();
        $run = mysql_query($select);
        while ($row = mysql_fetch_array($run)) {
            ?>
            <tr id="row<?php echo $row['ID']; ?>">
                <td><?php echo $row['ID']; ?></td>
                <td><?php echo $row['CODE']; ?></td>
                <td><?php echo $row['DETAIL']; ?></td>
                <td><?php echo $row['COUNTRY']; ?></td>
                <td><?php echo $row['CITY']; ?></td>
                
           
      </td>
      <td><a href="#" onclick="delLocation('<?php echo $row['ID']; ?>')" >Delete</a></td>
                <td><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $row['ID']; ?>"></td>

            </tr>
            <?php
        }
        echo $html = ob_get_clean();
        ?>
    </tbody>
</table>

