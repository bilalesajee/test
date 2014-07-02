<?php
require_once('dbManager.php');
$dbobj=new dbmanager();
$select = "SELECT d.ID, d.DEPT_NAME, d.DEPT_CODE, d.DEPT_HEAD FROM department d";
$array=$dbobj->fetch_result($select);
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
foreach ($array as $value) {
        ?>
            <tr id="row<?php echo $value['ID']; ?>" height="30">
                <td align="center"><?php echo $value['ID']; ?></td>
                <td align="center"><?php echo $value['DEPT_NAME']; ?></td>
                <td align="center"><?php echo $value['DEPT_CODE']; ?></td>
                <td align="center"><?php echo $value['DEPT_HEAD']; ?></td>
                
                
           
      <td width="2"></td >
                <td width="39"   align="center"><a href="#" onclick="delDepartment('<?php echo $value['ID']; ?>')" >Delete</a><input type="checkbox" name="chekboxDel" class="chkdel" value="<?php echo $value['ID']; ?>"></td>
                

            </tr>
            <?php
        }
        ?>
    </tbody>
</table>

