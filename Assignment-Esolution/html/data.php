<?php
require_once('conn.php');
?>
<table width="50%" border="0" align="center" id="tgrid" ><tbody>
        <tr>
            <th width="18" scope="col" bgcolor="#0000FF">ID</th>
            <th width="40" scope="col">Name</th>
            <th width="27" scope="col">Age</th>
            <th width="55" scope="col">Address</th>
            <th width="38" scope="col">Email</th>
            <th width="42" scope="col">Status</th>
            <th width="80" scope="col">Action</th>
        </tr>
        <?php
        $select = "SELECT p.ID,p.NAME,p.AGE,p.ADDRESS,p.EMAIL,p.STATUS FROM person p";
        ob_start();
        $run = mysql_query($select);
        while ($row = mysql_fetch_array($run)) {
            ?>
            <tr id="row<?php echo $row['ID']; ?>">
                <td><?php echo $row['ID']; ?></td>
                <td><?php echo $row['NAME']; ?></td>
                <td><?php echo $row['AGE']; ?></td>
                <td><?php echo $row['ADDRESS']; ?></td>
                <td><?php echo $row['EMAIL']; ?></td>
                <td><input type="checkbox" name="chk" value="1" <?php if ($row['STATUS'] == 1) {
            echo "checked";
        } ?>  /></td>
                <td><a href="#" onclick="RowDelete('<?php echo $row['ID']; ?>')" >Delete</a>|<a href="#"  onClick="RowEdit('<?php echo $row['ID']; ?>')"> Edit</a></td>
                <td><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $row['ID']; ?>"></td>

            </tr>
            <?php
        }
        echo $html = ob_get_clean();
        ?>
    </tbody>
</table>

