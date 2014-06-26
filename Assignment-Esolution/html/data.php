<?php
require_once('conn.php');
?>
<table width="70%"   align="center" id="tgrid" bordercolor="#0000FF" ><tbody>
        <tr>
            <th width="30" scope="col" bgcolor="#FFFFFF" height="20">ID</th>
            <th width="150" scope="col" bgcolor="#FFFFFF">Name</th>
            <th width="50" scope="col" bgcolor="#FFFFFF">Age</th>
            <th width="150" scope="col" bgcolor="#FFFFFF">Address</th>
            <th width="100" scope="col" bgcolor="#FFFFFF">Email</th>
            <th width="100" scope="col" bgcolor="#FFFFFF">Status</th>
            <th width="100" scope="col" bgcolor="#FFFFFF" colspan="2">Action</th>
        </tr>
        <?php
        $select = "SELECT p.ID,p.NAME,p.AGE,p.ADDRESS,p.EMAIL,p.STATUS FROM person p";
        ob_start();
        $run = mysql_query($select);
        while ($row = mysql_fetch_array($run)) {
            ?>
            <tr id="row<?php echo $row['ID']; ?>" height="30">
                <td align="center"><?php echo $row['ID']; ?></td>
                <td align="center"><?php echo $row['NAME']; ?></td>
                <td align="center"><?php echo $row['AGE']; ?></td>
                <td align="center"><?php echo $row['ADDRESS']; ?></td>
                <td align="center"><?php echo $row['EMAIL']; ?></td>
                <td align="center"><?php if ($row['STATUS'] == 1) {echo "Active";}else {
        echo 'In Active';} ?> </td>
                <td  align="center"><a href="#" onclick="RowDelete('<?php echo $row['ID']; ?>')" >Delete</a>|<a href="#"  onClick="RowEdit('<?php echo $row['ID']; ?>')"> Edit</a><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $row['ID']; ?>"> </td>
                 

            </tr>
            <?php
        }
        echo $html = ob_get_clean();
        ?>
    </tbody>
</table>

