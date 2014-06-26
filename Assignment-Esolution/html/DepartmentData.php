<?php
require_once('conn.php');
?>
<table width="50%" border="0" align="center" id="tgrid" ><tbody>
        <tr>
            <th width="18" scope="col" bgcolor="#0000FF">ID</th>
            
            <th width="27" scope="col">Name</th>
            <th width="55" scope="col">Code</th>
            <th width="38" scope="col">Head of Departmetn</th>
            <th width="80" scope="col">Action</th>
        </tr>
        <?php
        $select = "SELECT d.ID, d.DEPT_NAME, d.DEPT_CODE, d.DEPT_HEAD FROM department d";
        ob_start();
        $run = mysql_query($select);
        while ($row = mysql_fetch_array($run)) {
            ?>
            <tr id="row<?php echo $row['ID']; ?>">
                <td><?php echo $row['ID']; ?></td>
                <td><?php echo $row['DEPT_NAME']; ?></td>
                <td><?php echo $row['DEPT_CODE']; ?></td>
                <td><?php echo $row['DEPT_HEAD']; ?></td>
                
                
           
      </td>
                <td><a href="#" onclick="delDepartment('<?php echo $row['ID']; ?>')" >Delete</a></td>
                <td><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $row['ID']; ?>"></td>

            </tr>
            <?php
        }
        echo $html = ob_get_clean();
        ?>
    </tbody>
</table>

