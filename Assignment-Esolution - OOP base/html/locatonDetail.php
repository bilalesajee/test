<?php
require_once('dbManager.php');
$mydb = new dbManager();
$select = "SELECT l.ID, l.CODE,c.COUNTRY as cnt, l.DETAIL, l.COUNTRY, l.CITY FROM location l "
        . "left join country c on c.ID=l.COUNTRY";
$array = $mydb->fetch_result($select);
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
        foreach ($array as $location) {
                ?>
                <tr id="row<?php echo $location['ID']; ?>" height="30">
                    <td align="center"><?php echo $location['ID']; ?></td>
                    <td align="center"><?php echo $location['CODE']; ?></td>
                    <td align="center"><?php echo $location['DETAIL']; ?></td>
                    <td align="center"><?php echo $location['cnt']; ?></td>
                    <td align="center"><?php echo $location['CITY']; ?></td>

                    </td>
                    <td  align="center"><a href="#" onclick="RowDelete_location('<?php echo $location['ID']; ?>')" >Delete</a><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $location['ID']; ?>"></td>


                </tr>
                <?php
            }
            ?>
    </tbody>
</table>

