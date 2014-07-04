<?php
require_once('dbManager.php');
$obj = new dbmanager();
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
        $array = $obj->fetch_result($select);
        foreach ($array as $p) {
            ?>
            <tr id="row<?php echo $p['ID'] ?>" height="30">
                <td align="center"><?php echo $p['ID'] ?></td>
                <td align="center"><?php echo $p['NAME'] ?></td>
                <td align="center"><?php echo $p['AGE'] ?></td>
                <td align="center"><?php echo $p['ADDRESS'] ?></td>
                <td align="center"><?php echo $p['EMAIL'] ?></td>
                <td align="center"><?php if ($p['STATUS'] == 1) {
            echo "Active";
        } else {
            echo 'In Active';
        }
        ?> </td>
                <td><a href="#" onclick="RowDelete_employee('<?php echo $p['ID']; ?>')"> Delete</a>|<a href="#"  onClick="rowEdit_employee('<?php echo $p['ID']; ?>')"> Edit</a><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $p['ID']; ?>"> </td>
            </tr>
    <?php
}
?>    </tbody>
</table>

