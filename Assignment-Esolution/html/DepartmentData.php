<?php
require_once('conn.php');
?>
<table width="70%" border="0" align="center" id="tdept" ><tbody>
        <tr>
            <th width="18" scope="col"  bgcolor="#FFFFFF">ID</th>
            
            <th width="40" scope="col"  bgcolor="#FFFFFF">Name</th>
            <th width="35" scope="col"  bgcolor="#FFFFFF">Code</th>
            <th width="80" scope="col"  bgcolor="#FFFFFF">Head of Departmetn</th>
            <th  bgcolor="#FFFFFF" colspan="2">Action</th>
        </tr>
        <?php
        $select = "SELECT d.ID, d.DEPT_NAME, d.DEPT_CODE, d.DEPT_HEAD FROM department d";
        ob_start();
        $run = mysql_query($select);
        while ($row = mysql_fetch_array($run)) {
            ?>
            <tr id="row<?php echo $row['ID']; ?>" height="30">
                <td align="center"><?php echo $row['ID']; ?></td>
                <td align="center"><?php echo $row['DEPT_NAME']; ?></td>
                <td align="center"><?php echo $row['DEPT_CODE']; ?></td>
                <td align="center"><?php echo $row['DEPT_HEAD']; ?></td>
                
                
           
      <td width="2"></td >
                <td width="39"   align="center"><a href="#" onclick="delDepartment('<?php echo $row['ID']; ?>')" >Delete</a><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $row['ID']; ?>"></td>
                

            </tr>
            <?php
        }
        echo $html = ob_get_clean();
        ?>
    </tbody>
</table>

