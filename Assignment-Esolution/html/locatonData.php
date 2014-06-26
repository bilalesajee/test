<?php
require_once('conn.php');
?>
<table width="70%"  align="center" id="tlocation" ><tbody>
        <tr>
            <th width="18" scope="col"  bgcolor="#FFFFFF">ID</th>
            <th width="40" scope="col" bgcolor="#FFFFFF">Code</th>
            <th width="27" scope="col" bgcolor="#FFFFFF">Detail</th>
            <th width="55" scope="col" bgcolor="#FFFFFF">Country</th>
            <th width="38" scope="col" bgcolor="#FFFFFF">City</th>
            <th width="80" scope="col" bgcolor="#FFFFFF">Action</th>
        </tr>
        <?php
        $select = "SELECT l.ID, l.CODE, l.DETAIL, l.COUNTRY, l.CITY FROM location l";
        ob_start();
        $run = mysql_query($select);
        while ($row = mysql_fetch_array($run)) {
            ?>
            <tr id="row<?php echo $row['ID']; ?>" height="30">
                <td align="center"><?php echo $row['ID']; ?></td>
                <td align="center"><?php echo $row['CODE']; ?></td>
                <td align="center"><?php echo $row['DETAIL']; ?></td>
                <td align="center"><?php echo $row['COUNTRY']; ?></td>
                <td align="center"><?php echo $row['CITY']; ?></td>
                
      </td>
      <td  align="center"><a href="#" onclick="delLocation('<?php echo $row['ID']; ?>')" >Delete</a><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $row['ID']; ?>"></td>
       

            </tr>
            <?php
        }
        echo $html = ob_get_clean();
        ?>
    </tbody>
</table>

